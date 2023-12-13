<?php

namespace AmazingTraits\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use AmazingTraits\Helpers\Cryptor;

trait ModelScopes
{
    public $scopes = [
        'wherejsoncontainsin',
        'wherin',
        'whereadin',
        'whereadnotin',
        'distinctin',
        'joinin',
        'filterin',
        'searchin',
        'orderin',
        'selectin'
    ];

    public $tampunganColumns = [];

    public function scopeSearchin($model)
    {
        $searchText = app()->request->has('search') && app()->request->search !== "null" ? app()->request->search : '';

        /**
         * special characters remove from query
         */
        $searchText = str_replace(["'", "?"], "", $searchText);
        $searchText = str_replace("(", "\(", $searchText);
        $searchText = str_replace(")", "\)", $searchText);
        if ($searchText) {
            $searchable = @$this->searchable ?? $this->tampunganColumns;
            $searchable = array_filter($searchable, function ($key) {
                return !in_array($key, ['id']) && !Str::contains($key, "_id") && !Str::contains($key, ".id") && !Str::endsWith($key, "_at");
            });
            $table = $this->getTable();
            $model->where(function ($q) use ($searchable, $searchText, $table) {
                foreach ($searchable as $kolom) {
                    $kolomFixed = Str::contains($kolom, ".") ? $kolom : "$table.$kolom";
                    if (!Str::contains($kolom, ".")) {
                        $kolom = "$table.$kolom";
                    }
                    $q->orWhereRaw("$kolom::text ~* '$searchText'");
                }

            });
        }
        return $model;
    }

    public function scopeJoinin($model)
    {
        $currentTable = $this->getTable();
        $this->tampunganColumns = $this->getColumns($currentTable);
        if (app()->request->has('join') && strtolower(app()->request->join) === 'false') {
            return $model;
        }

        $joins = @$this->joins ?? [];
        if (app()->request->has('joins')) {
            $joinsArr = explode(",", app()->request->joins);
            foreach ($joinsArr as $joinSingle) {
                $colArr = explode("=", $joinSingle);
                $valsplit = explode('.', $colArr[1]);
                if (count($valsplit) < 2) {
                    $joins[$colArr[0]] = $valsplit[0];
                } else {
                    $tbl = $valsplit[0];
                    $cols = '';
                    foreach ($valsplit as $index => $val) {
                        if ($index) {
                            if ($index === count($valsplit) - 1) {
                                $cols .= "$val";
                            } else {
                                $cols .= "$val,";
                            }
                        }
                    }
                    $mdl = $this->onCheckSysModel($tbl);
                    $joins[$colArr[0]] = ["App\Models\\$mdl", $cols];
                }
            }
        }

        foreach ($joins as $joinColumn => $parentClass) {
            $selectedColumns = null;
            if (is_array($parentClass)) {
                $selectedColumns = explode(",", $parentClass[1]);
                $parentClass = $parentClass[0];
            }

             if(strpos($joinColumn, ':')){
                $explode = explode(':', $joinColumn);
                $joinColumn = $explode[0];
                $fk = $explode[1];
            }else{
                $fk = null;
            }

            $currentJoin = "$currentTable.$joinColumn";
            if (strpos($joinColumn, ".") !== false) {
                $currentJoin = "$joinColumn";
                $parentArr = explode(".", $joinColumn);
                $parentAlias = str_replace("_id", "", end($parentArr));
            } else {
                $parentAlias = str_replace("_id", "", $joinColumn);
            }

            $parentTable =  Str::contains($parentClass, ".") ? explode(".", $parentClass)[0] : $parentClass;

            if (class_exists($parentClass)) {
                $parent = new $parentClass;
                $parentTable = $parent->getTable();
            }

            $columns = $selectedColumns ?? $this->getColumns($parentTable);
            $fixedColumns = array_map(function ($column) use ($joinColumn, $parentAlias) {
                return "$parentAlias.$column";
            }, $columns);

            $idPk = $fk ?? 'id';

            $this->tampunganColumns = array_merge($this->tampunganColumns, $fixedColumns);
            $model->leftJoin("$parentTable as $parentAlias", Str::contains($parentClass, ".") ?
            $parentAlias . "." . explode(".", $parentClass)[1]
            : "$parentAlias.".$idPk, "=", $currentJoin);
        }

        return $model;
    }

    public function scopeSelectin($model)
    {
        $selectField = app()->request->has('selectfield') ? app()->request->selectfield : '';
        $currentTable = $this->getTable();

        if ($selectField) {
            $selectsArr = explode(",", str_replace("this.", $this->getTable() . ".", $selectField));
            $selectsArr = array_map(function ($selectColumn) use ($currentTable) {
                if (!Str::contains($selectColumn, ".") && !Str::contains($selectColumn, " as ")) {
                    return "$currentTable.$selectColumn";
                }
                return $selectColumn;
            }, $selectsArr);

            $model->selectRaw(implode(",", $selectsArr));
        } else {
            $fixedColumns = array_map(function ($column) use ($currentTable) {
                if (!Str::contains($column, ".")) {
                    return "$currentTable.$column as $column";
                }
                return "$column as $column";
            }, $this->tampunganColumns);
            $model->select($fixedColumns);
        }

        if (app()->request->has('addselect')) {
            $model->addSelect(DB::raw(str_replace("this.", $this->getTable() . ".", app()->request->addselect)));
        }

        return $model;
    }

    public function scopeFilterin($model)
    {
        $modelClass = $this;
        $model->where(function($q)use($modelClass){
            $table = $modelClass->getTable();
            $reqArr = (array)req('data_filter_%');
            foreach($reqArr as $key => $val){
                if(!$val) continue;
                $key = Str::replace("data_filter_", "", $key);
                if( !Str::contains($key, '.') ){
                    $key = "$table.$key";
                }
                $q->where(DB::raw("$key::text"), "~*", $val);
            }
        });
    }

    public function scopeOrderin($model)
    {
        if (app()->request->has('orderby')) {
            $model = $model->orderBy(str_replace("this.", $this->getTable() . ".", app()->request->orderby), @app()->request->ordertype ?? 'DESC');
        } else {
            // $model = $model->orderBy( $this->getTable().".id", 'DESC' );
        }
        return $model;
    }

    public function scopeWherin($model)
    {
        if (!app()->request->route('parent_id') && app()->request->has('where')) {
            $preparedQuery = app()->request->where;
            $preparedQuery = str_replace('this.' , "{$model->getModel()->getTable()}.", $preparedQuery);
            preg_match_all("/(:\w+:)/", $preparedQuery, $m);

            foreach( $m[0] as $encrypted ){
                // trigger_error($encrypted);
                $decrypted = (new Cryptor)->decrypt(str_replace( ":", "", $encrypted ));
                $preparedQuery = str_replace($encrypted, $decrypted, $preparedQuery);
            }

            $model = $model->whereRaw( $preparedQuery );
        }
        return $model;
    }

    public function scopeWhereAdin($model)
    {
        if (app()->request->has('wherein')) {
            $d = app()->request->wherein;
            $d = explode("=", $d);
            $key = $d[0];
            $val = json_decode($d[1]);

            foreach ($val as $key2 => $vl) {
                if (
                    (Str::is('id', $key) && !is_numeric($vl)) ||
                    (Str::endswith($key, '_id') && !is_numeric($vl))
                ) {
                    $val[$key2] = $this->decrypt($vl);
                }
            }

            $tbl = $this->getTable();
            $model = $model->whereIn("$tbl.$key", $val);
        }
        return $model;
    }

    public function scopeWhereAdNotin($model)
    {
        if (app()->request->has('wherenotin')) {
            $d = app()->request->wherenotin;
            $d = explode("=", $d);
            $key = $d[0];
            $val = json_decode($d[1]);
            foreach ($val as $key2 => $vl) {
                if (
                    (Str::is('id', $key) && !is_numeric($vl)) ||
                    (Str::endswith($key, '_id') && !is_numeric($vl))
                ) {
                    $val[$key2] = $this->decrypt($vl);
                }
            }

            $tbl = $this->getTable();
            if (strpos($key, $tbl) !== false) {
                // jika key sudah terdpat nama table nya
                $model = $model->whereIn("$key", $val, 'and', true);
            } else {
                // jika belum perlu di tambahankan tablename dot
                $model = $model->whereIn("$tbl.$key", $val, 'and', true);
            }
        }
        return $model;
    }

    /**
     * @param $model
     * @return mixed
     * ex:
     *  1. ?wherejsoncontainsin=roles="ADMIN"
     *  2. ?wherejsoncontainsin=roles=["ADMIN", "INTERNAL - ADMIN"]
     */
    public function scopeWhereJsonContainsin($model)
    {
        if (app()->request->has('wherejsoncontainsin')) {
            $d = app()->request->wherejsoncontainsin;
            $d = explode("=", $d);
            $key = $d[0];
            $val = json_decode($d[1]);
            $tbl = $this->getTable();
            $petik = '"';
            if (is_array($val)) {
                foreach ($val as $vl) {
                    // old version
                    // $valFix = "'%{$petik}$vl{$petik}%'";
                    // $model = $model->orWhereRaw("$tbl.$key LIKE $valFix");

                    // new version
                    $model = $model->orWhereJsonContains("$tbl.$key", $vl);
                }
            } else {
                $valFix = "'%{$petik}$val{$petik}%'";
                $model = $model->whereRaw("$tbl.$key LIKE $valFix");
            }
        }
        return $model;
    }

    public function scopeDistinctin($model)
    {
        if (app()->request->has('distinct')) {
            $model = $model->distinct()->pluck(app()->request->distinct);
        }
        return $model;
    }
}
