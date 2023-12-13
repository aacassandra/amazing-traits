<?php

namespace AmazingTraits\Traits;

use AmazingTraits\Helpers\Approval as ApprovalEngine;
use AmazingTraits\Helpers\Cryptor;
use App\Models\ApiModel;
use App\Models\m_roles;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

trait ModelHelper
{
    public $useEncryption  = true;

    public function encrypt(string $plainText): mixed
    {
        return (new Cryptor)->encrypt($plainText);
    }

    public function decrypt(string $encryptedText): mixed
    {
        return (new Cryptor)->decrypt($encryptedText);
    }

    private function getColumns($table)
    {
        $columns = config("columns_$table");    // ambil dari config jika ada agar tidak berkali2 getColumnListing
        if (!$columns) {
            $db = DB::connection()->getPdo();
            $rs = $db->query("SELECT * FROM $table LIMIT 0");
            for ($i = 0; $i < $rs->columnCount(); $i++) {
                $col = $rs->getColumnMeta($i);
                $columns[] = $col['name'];
            }

            // tidak support untuk multischema
            // $columns = Schema::getColumnListing('set_mas_gen');
            config(["columns_$table" => $columns]);
        }
        return $columns;
    }

    public function getIdAttribute(mixed $val)
    {

        if (app()->request->has('encrypt') && Str::lower(app()->request->encrypt) == "false") {
            return $val;
        }

        if (!$this->useEncryption) {
            return (int)$val;
        }

        return $this->encrypt($val);
    }

    private function getSchema(string $modelOrTable)
    {
        $schemas = config('model-trait.schema');

        // Pengecekan schema
        $match = false;
        $selectedSchema = "";
        foreach ($schemas as $schema => $tables) {
            foreach ($tables as $tbl) {
                if ($tbl === $modelOrTable) {
                    $match = true;
                    $selectedSchema = $schema;
                }
            }
        }

        // Gunakan default schema jika table/model tidak di daftarkan ke konfigurasi schema
        // konfigurasi ada di file ./config/model-trait.php
        if (!$match) {
            $selectedSchema = config('model-trait.default');
        }

        return $selectedSchema;
    }

    private function getModel(string $modelOrTable)
    {
        $fixModel = $this->onCheckSysModel($modelOrTable);
        $selectedSchema = '';
        if (env("DB_CONNECTION") === "pgsql") {
            $selectedSchema = $this->getSchema($modelOrTable);
        }

        if (class_exists("App\Models\\$fixModel")) {
            $className = "App\Models\\$fixModel";
            $model = new $className;
            if (env("DB_CONNECTION") === "pgsql") {
                return $model->setTable("$selectedSchema.{$model->getTable()}");
            } else {
                return $model->setTable("{$model->getTable()}");
            }
        } elseif (Schema::hasTable($modelOrTable)) {
            return $this->makeModel( env("DB_CONNECTION") === "pgsql" ? $selectedSchema : "", $modelOrTable );
        }

        return false;
    }

    private function makeModel(string $selectedSchema, string $tableName)
    {
        $model = new ApiModel;
        if (env("DB_CONNECTION") === "pgsql") {
            $model->setTable("$selectedSchema.$tableName");
        } else {
            $model->setTable("$tableName");
        }
        return $model;
    }

    /**
     * @override Laravel function
     */
//    public function getCasts()
//    {
//        $casts = $this->casts;
//        return array_merge([
//            'created_at' => 'date:d/m/Y H:i',
//            'updated_at' => 'date:d/m/Y H:i'
//        ], $casts);
//    }

    public function serializeDate(\DateTimeInterface $date)
    {
        return $date->timezone(config('app.timezone'))->format('d/m/Y H:i');
    }

    public function getDeletedAtAttribute(mixed $value)
    {
        return $value ? Carbon::create($value)->format('d/m/Y H:i') : $value;
    }

    public function arr2json($arr)
    {
        return json_decode(json_encode($arr));
    }

    public function json2arr($json)
    {
        return json_decode(json_encode($json), true);
    }

    // Untuk manual validation di controller menggunakan rules yang ada di model
    public function getValidation(string $get, string $method)
    {
        $reqArr = app()->request->all();
        $rules = [];

        if (method_exists($this, $method)) {
            if ($method === 'create') {
                $rules = $this->creatingRules();
            } elseif ($method === 'update') {
                $rules = $this->updatingRules();
            } elseif ($method === 'delete') {
                $rules = $this->deletingRules();
            }
        }

        $details = @$this->details ?? [];
        $fixedRules = array_filter($rules, function ($rule) {
            return !is_array($rule);
        });

        foreach ($reqArr as $currentCol => $val) {
            if (Str::endswith($currentCol, '_id') && $val && !is_numeric($val)) {
                $reqArr[$currentCol] = $this->autoDecrypt($val) ?? $val;
            }
        }

        $validator = Validator::make($reqArr, $fixedRules);

        if ($get === 'status') {
            if ($validator->fails()) {
                return 'error';
            } else {
                foreach ($reqArr as $key => $val) {
                    if (method_exists($this, $key) && is_array($val)) {
                        //  jika pakai hasMany
                    } elseif (in_array($key, $details)) {
                        foreach ($val as $dtlIdx => $dtlRow) {
                            if (isset($rules[$key . ".*"])) {

                                foreach ($dtlRow as $currentCol => $val) {
                                    if (Str::endswith($currentCol, '_id') && $val && !is_numeric($val)) {
                                        $dtlRow[$currentCol] = $this->autoDecrypt($val) ?? $val;
                                    }
                                }

                                $dtlValidator = Validator::make($dtlRow, $rules[$key . ".*"]);

                                if ($dtlValidator->fails()) {
                                    return 'error';
                                }
                            }
                        }
                    }
                }
            }
        } elseif ($get === 'output') {
            if ($validator->fails()) {
                return $this->arr2json([
                    "message" => "Sorry, data is invalid.",
                    "errors" => $validator->errors()->all()
                ]);
            } else {
                foreach ($reqArr as $key => $val) {
                    if (method_exists($this, $key) && is_array($val)) {
                        //  jika pakai hasMany
                    } elseif (in_array($key, $details)) {
                        foreach ($val as $dtlIdx => $dtlRow) {
                            if (isset($rules[$key . ".*"])) {

                                foreach ($dtlRow as $currentCol => $val) {
                                    if (Str::endswith($currentCol, '_id') && $val && !is_numeric($val)) {
                                        $dtlRow[$currentCol] = $this->autoDecrypt($val) ?? $val;
                                    }
                                }

                                $dtlValidator = Validator::make($dtlRow, $rules[$key . ".*"]);

                                if ($dtlValidator->fails()) {
                                    return $this->arr2json([
                                        "message" => "Sorry, data is invalid.",
                                        "errors" => $dtlValidator->errors()->all()
                                    ]);
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public function getUserId()
    {
        if (app()->request->user('api') !== null) {
            return app()->request->user('api')->id;
        } else {
            return null;
        }
    }

    /**
     * Melakukan listing available scope di model saat get data
     */
    public function getAvailableScopes()
    {
        $model = $this;
        $scopes = array_filter(get_class_methods($model), function ($dt) use ($model) {
            $dt = Str::lower($dt);
            return Str::startsWith($dt, 'scope') && !in_array(Str::replace('scope', '', $dt), $model->scopes);
        });

        return array_map(function ($dt) {
            return lcfirst(Str::replace('scope', '', $dt));
        }, array_values($scopes));
    }

    /**
     * Melakukan listing available scope di model saat get data
     */
    public function getAvailableMethods()
    {
        $model = $this;
        $methods = array_filter(get_class_methods($model), function ($dt) {
            $dt = Str::lower($dt);
            return Str::startsWith($dt, ['custom','getfunc']) && !Str::endsWith($dt, 'validation') && !Str::endsWith($dt, 'configs');
        });

        return array_map(function ($dt) {
            if(Str::startsWith($dt, 'custom')){
                return lcfirst(Str::replace('custom', '', $dt))." :POST";
            }else if(Str::startsWith($dt, 'getfunc')){
                return lcfirst(Str::replace('getfunc', '', $dt))." :GET";
            }
        }, array_values($methods));
    }

    /**
     * Melakukan generesasi number otomatis
     *
     * @param $length
     * @return string
     */
    public function getRandomNumber($length) {
        $result = '';

        for($i = 0; $i < $length; $i++) {
            $result .= mt_rand(0, 9);
        }

        return $result;
    }

    /**
     * Untuk get table dan schema
     * Ini juga dianjurkan untuk pemakaian di file migrations
     *
     * @param string $table
     * @return string
     */
    public function getTableWithSchema(string $table) {
        $schm = $this->getSchema($table);
        return "$schm.$table";
    }

    public function autoDecrypt($val)
    {
        return $val === null ? null : (is_numeric($val) && isset(app()->request->encrypt) && app()->request->encrypt === 'false' ?
            $val :
            (is_numeric($val) ? $val : $this->decrypt($val)));
    }

    public function autoDecryptAllNested($reqArr)
    {
        if (is_string($reqArr)) $reqArr = json_decode($reqArr, true);

        foreach ($reqArr as $currentCol => $val) {
            if (is_array($val)) {
                // is detail
                $reqArr[$currentCol] = $this->autoDecryptAllNested($val);
            } else if ((Str::endswith($currentCol, '_id') && $val && !is_numeric($val))) {
                $reqArr[$currentCol] = $this->autoDecrypt($val) ?? $val;
            } else if ($currentCol === 'id') {
                $reqArr[$currentCol] = $this->autoDecrypt($val) ?? $val;
            }
        }

        return $reqArr;
    }

    public function has_custom()
    {
        return $this->is_custom ?? false;
    }

    public function getOnlyTable()
    {
        $b = $this->getTable();
        if (env('DB_CONNECTION') === 'pgsql') {
            $schemas = config('model-trait.schema');
            $tblName = $b;

            // Pengecekan schema
            foreach ($schemas as $schema => $tables) {
                if (str_contains($b, "$schema.")) {
                    $tblName = str_replace("$schema.","",$b);
                }
            }

            return $tblName;
        } else {
            return $b;
        }
    }

    public function getScopes()
    {
        $relatedScope = ['joinin','filterin','selectin'];
        return $relatedScope;
    }

    public function getFixRules($rules)
    {
        return array_filter($rules, function ($rule) {
            return !is_array($rule);
        });
    }

    /**
     * Validator include
     * 1. auto check untuk field yang perlu di decrypt
     * 2. penghapusan rules yang ada detailnya
     * @param array $data
     * @param array $rules
     * @param bool $debug
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator(array $data, array $rules, $debug = false)
    {
        foreach ($data as $currentCol => $value) {
            if ($currentCol === 'id' && $value && !is_numeric($value)) {
                $data[$currentCol] = $this->decrypt($value) ?? $value;
            } else if (Str::endswith($currentCol, '_id') && $value && !is_numeric($value)) {
                $data[$currentCol] = $this->decrypt($value) ?? $value;
            }
        }
        if ($debug === true) {
            // masukan variable apa yang ingi kalian debug
            // ...
        }
        return Validator::make($data, $this->getFixRules($rules));
    }

    /**
     * Mengurusi auto encrypt untuk semua field dalam object/array
     * @param $data
     * @param $inArray
     * @return mixed
     */
    public function encryptAllFields($data, $inArray = false)
    {
        if (isset(app()->request->encrypt) && app()->request->encrypt === 'false') {
            //
        } else {
            if ($inArray === true) {
                foreach ($data as $index => $dt)
                {
                    foreach ($dt as $key => $val) {
                        if ($key === 'id') {
                            if (is_object($dt)) {
                                $dt->$key = $this->encrypt($val);
                            } else {
                                $dt[$key] = $this->encrypt($val);
                            }
                        } else if (Str::endsWith($key, '_id') && is_numeric($val)) {
                            if (is_object($dt)) {
                                $dt->$key = $this->encrypt($val);
                            } else {
                                $dt[$key] = $this->encrypt($val);
                            }
                        } else if (strpos($key, '.') > -1) {
                            $exp = explode('.', $key);
                            if (Str::endsWith($exp[count($exp) - 1], '_id') && is_numeric($val)) {
                                if (is_object($dt)) {
                                    $dt->$key = $this->encrypt($val);
                                } else {
                                    $dt[$key] = $this->encrypt($val);
                                }
                            } else if (Str::is('id', $exp[count($exp) - 1]) && is_numeric($val)) {
                                if (is_object($dt)) {
                                    $dt->$key = $this->encrypt($val);
                                } else {
                                    $dt[$key] = $this->encrypt($val);
                                }
                            }
                        }
                    }
                    $data[$index] = $dt;
                }
            } else {
                foreach ($data as $key => $val) {
                    if ($key === 'id') {
                        $data[$key] = $this->encrypt($val);
                    } else if (Str::endsWith($key, '_id') && is_numeric($val)) {
                        $data[$key] = $this->encrypt($val);
                    } else if (strpos($key, '.') > -1) {
                        $exp = explode('.', $key);
                        if (Str::endsWith($exp[count($exp) - 1], '_id') && is_numeric($val)) {
                            $data[$key] = $this->encrypt($val);
                        } else if (Str::is('id', $exp[count($exp) - 1]) && is_numeric($val)) {
                            $data[$key] = $this->encrypt($val);
                        }
                    }
                }
            }
        }

        return $data;
    }

    /**
     * Mengurusi auto decrypt untuk semua field dalam object/array
     * @param $data
     * @param $inArray
     * @return mixed
     */
    public function decryptAllFields($data, $inArray = false)
    {
        if ($inArray === true) {
            foreach ($data as $index => $dt) {
                $tempData = $this->arr2json($dt);
                if (is_string($tempData)) {
                    $data[$index] = $this->autoDecrypt($tempData) ?? $tempData;
                } else if (is_integer($tempData)) {
                    $data[$index] = $tempData;
                } else {
                    foreach ($tempData as $currentCol => $value) {
                        if ($currentCol === 'id' && $value && !is_numeric($value)) {
                            $data[$index][$currentCol] = $this->autoDecrypt($value) ?? $value;
                        } else if (Str::endswith($currentCol, '_id') && $value && !is_numeric($value)) {
                            $data[$index][$currentCol] = $this->autoDecrypt($value) ?? $value;
                        }
                    }
                }
            }
        } else {
            $tempData = $this->arr2json($data);
            foreach ($tempData as $currentCol => $value) {
                if ($currentCol === 'id' && $value && !is_numeric($value)) {
                    $data[$currentCol] = $this->autoDecrypt($value) ?? $value;
                } else if (Str::endswith($currentCol, '_id') && $value && !is_numeric($value)) {
                    $data[$currentCol] = $this->autoDecrypt($value) ?? $value;
                }
            }
        }

        return $data;
    }

    public function onGenerateRandomString($prefix = '', $length = 10, $hasNumber = true, $hasLowercase = true, $hasUppercase = true): string
    {
        $string = '';
        if ($hasNumber)
            $string .= '0123456789';
        if ($hasLowercase)
            $string .= 'abcdefghijklmnopqrstuvwxyz';
        if ($hasUppercase)
            $string .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return $prefix . substr(str_shuffle(str_repeat($x = $string, ceil($length / strlen($x)))), 1, $length);
    }

    public function onCheckSysModel($model)
    {
        $sysTbls = config('model-trait.list-table');
        foreach ($sysTbls as $sys) {
            if (Str::endsWith($model, $sys)) {
                $model = str_replace(env('TABLE_PREFIX'), '', $model);
            }
        }

        return $model;
    }

    public function onTransform($reqArr)
    {
        foreach ($reqArr as $index => $ra) {
            if (Str::endsWith( $index, '_point')) {
                if ($ra) {
                    if (is_array($ra) && $ra['type'] === 'Point') {
                        if (isset($ra['coordinates']['lat'])) {
                            $reqArr[$index] = DB::raw("POINT({$ra['coordinates']['lng']}, {$ra['coordinates']['lat']})");
                        } else {
                            $reqArr[$index] = DB::raw("POINT({$ra['coordinates'][0]}, {$ra['coordinates'][1]})");
                        }
                    } else {
                        if (is_string($ra)) {
                            $explode = explode(',', $ra);
                            $reqArr[$index] = DB::raw("POINT($explode[0], $explode[1])");
                        } else {
                            $reqArr[$index] = $ra;
                        }
                    }
                }
            } else {
                $reqArr[$index] = $ra;
            }
        }

        return $reqArr;

    }

    public function checkPermissions(string $permission_name = '')
    {
        $tbl_prefix = env('TABLE_PREFIX');
        $connection = env('DB_CONNECTION');
        $default_schema = config('model-trait.default');

        /**
         * if permission is not set or empty array
         */
        if (!isset($this->permissions)) {
            return true;
        } else if (!count($this->permissions)) {
            return true;
        }

        $is_custom = false;
        if (Str::startsWith($permission_name, 'custom')) {
            $is_custom = true;
        }

        if (Str::startsWith($permission_name, 'getfunc')) {
            $is_custom = true;
        }


        if (!$this->getUserId()) {
            if ($is_custom) {
                if (!isset($this->permissions['customs'])) {
                    // jika customs tidak di set atau = null
                    return true;
                } else if (!count($this->permissions['customs'])) {
                    // jika value customs = [] / array kosong
                    return true;
                } else {
                    foreach ($this->permissions['customs'] as $custom) {
                        if (strtolower($custom) == strtolower($permission_name)) {
                            return false;
                        }
                    }
                }
            } else {
                if (!isset($this->permissions[$permission_name])) {
                    // jika permission tidak di set
                    return true;
                } else if ($this->permissions[$permission_name] === null || $this->permissions[$permission_name] === undefined) {
                    // jika permission value tidak ada
                    return true;
                } else {
                    // jika permission value ada
                    return false;
                }
            }
        }


        if (!$is_custom && !isset($this->permissions[$permission_name])) {
            return true;
        }


        // di isikan permission name yg bukan custom
        $permission_target = '';

        if ($is_custom) {
            // jika sudah login dan permission is custom
            if (!isset($this->permissions['customs'])) {
                // jika customs tidak di set atau = null
                return true;
            } else if (!count($this->permissions['customs'])) {
                // jika value customs = [] / array kosong
                return true;
            } else {
                foreach ($this->permissions['customs'] as $custom) {
                    if (strtolower($custom) == strtolower($permission_name)) {
                        $permission_target = strtolower($custom);
                    }
                }
            }
        } else {
            // permission biasa
            $permission_target = $this->permissions[$permission_name] ?? $this->permissions[strtolower($permission_name)];
            $permission_target = strtolower($permission_target);
        }

        // Cek permision pada model, jika tidak ada, maka auto insert by system
        $permission = null;
        if ($connection === 'mysql') {
            $permission = DB::table("{$tbl_prefix}m_permissions");
        } else if ($connection === 'pgsql') {
            $permission = DB::table("{$default_schema}.{$tbl_prefix}m_permissions");
        }
        $permission = $permission->where('name', $permission_target);
        if (!$permission->exists()) {
            // jika permission tidak ada, maka auto insert
            $permission->insert([
                'name' => strtolower($permission_target),
                'created_id' => $this->getUserId(),
                'created_at' => now()
            ]);
        }
        // END

        $user = null;
        if ($connection === 'mysql') {
            $user = DB::table("{$tbl_prefix}m_users")->find($this->getUserId());
        } else if ($connection === 'pgsql') {
            $user = DB::table("{$default_schema}.{$tbl_prefix}m_users")->find($this->getUserId());
        }

        $roles_d_user = null;
        if ($connection === 'mysql') {
            $roles_d_user = DB::table("{$tbl_prefix}m_roles_d_users as role_d_user");
        } else if ($connection === 'pgsql') {
            $roles_d_user = DB::table("{$default_schema}.{$tbl_prefix}m_roles_d_users as role_d_user");
        }

        $roles_d_user = $roles_d_user
            ->selectRaw('role_d_user.*')
            ->join("{$tbl_prefix}m_roles as roles", "roles.id", "=", "role_d_user.{$tbl_prefix}m_roles_id")
            ->where('role_d_user.user_id', $this->getUserId())
            ->where('roles.deleted_at', '=', null)
            ->where('roles.name', $user->role)
            ->get()
        ;

        $has_match = false;
        foreach ($roles_d_user as $ru) {
            $permissions = null;
            if ($connection === 'mysql') {
                $permissions = DB::table("{$tbl_prefix}m_roles_d_permissions as main");
            } else if ($connection === 'pgsql') {
                $permissions = DB::table("{$default_schema}.{$tbl_prefix}m_roles_d_permissions as main");
            }

            $permissions = $permissions->selectRaw('main.*, pms.name as "name"')
                ->join("{$tbl_prefix}m_permissions as pms", 'pms.id', '=', 'main.permission_id')
                ->where("{$tbl_prefix}m_roles_id", $ru->{"{$tbl_prefix}m_roles_id"})
                ->get();

            foreach ($permissions as $pms) {
                if (strtolower($pms->name) === $permission_target || $pms->name === 'full-access') {
                    $has_match = true;
                }
            }
        }

        return $has_match;
    }

    public function getPermissions($userId)
    {
        $user = DB::table(env('TABLE_PREFIX').'m_users')->find($userId);
        // cek active role nya apa, karena use memungkinkan untuk multiple role
        $role = DB::table(env('TABLE_PREFIX').'m_roles')->where('name', $user->role)->first();

        $roles_d_users = DB::table(env('TABLE_PREFIX').'m_roles_d_users as users')
            ->selectRaw('mpm.name as "permission_name"')
            ->join(env('TABLE_PREFIX').'m_roles as roles', 'roles.id', '=', "users.".env('TABLE_PREFIX')."m_roles_id")
            ->join(env('TABLE_PREFIX').'m_roles_d_permissions as pm', "pm.".env('TABLE_PREFIX')."m_roles_id", '=', 'roles.id')
            ->join(env('TABLE_PREFIX').'m_permissions as mpm', 'mpm.id', '=', 'pm.permission_id')
            ->where('users.user_id', $userId)
            ->where('users.'.env('TABLE_PREFIX').'m_roles_id', $role->id)
            ->get();

        $permissions = [];
        foreach ($roles_d_users as $pm) {
            $permissions[] = $pm->permission_name;
        }
        return $permissions;
    }

    public function checkPermissionsV2(string $permission_key = '', string $table_name)
    {
        $tbl_prefix = env('TABLE_PREFIX');
        $connection = env('DB_CONNECTION');
        $version_role = "";
        if ($connection === 'pgsql') {
            $tbl_prefix = config('model-trait.default') . "." . $tbl_prefix;
        }

        $current_role = auth('api')->check() ? $this->getUser()->role : 'GUEST';
        $req = app()->request;
        if (isset($req->force_permissions) && $req->force_permissions == true && env('APP_ENV') === 'local') {
            return true;
        }

        // get role detail
        if ($current_role !== 'GUEST') {
            $role = DB::table("{$tbl_prefix}m_roles{$version_role} as main")
                ->selectRaw('main.*')
                ->join("{$tbl_prefix}m_roles_d_users{$version_role} as d_users", "d_users.{$tbl_prefix}m_roles{$version_role}_id", "=", "main.id")
                ->where('main.name', $current_role)
                ->where('main.deleted_at', null)
                ->where('d_users.user_id', $this->getUserId())
                ->first();

            if (!$role) {
                return true;
            }
        } else {
            $role = DB::table("{$tbl_prefix}m_roles{$version_role} as main")
                ->where('main.name', $current_role)
                ->where('main.deleted_at', null)
                ->first();
        }

        if (!$role) {
            return false;
        }

        $permission = DB::table("{$tbl_prefix}m_roles_d_api_permissions{$version_role} as main")
            ->selectRaw('main.*, models.name as model')
            ->join("{$tbl_prefix}m_models{$version_role} as models", 'models.id', '=', 'main.model_id')
            ->where('main.deleted_at', null)
            ->where('models.name', $table_name)
            ->where("{$tbl_prefix}m_roles{$version_role}_id", $role->id)
            ->first()
        ;

        if (!$permission) {
            return true;
        }

        $isCustom = in_array($permission_key, ['get', 'find', 'store', 'update', 'destroy']);
        if (!$isCustom) {
            $custom_permissions = json_decode($permission->customs);
            if (!count($custom_permissions)) {
                return true;
            }

            foreach ($custom_permissions as $pms) {
                if ($pms->method === 'GET') {
                    if ("getfunc{$pms->name}" === strtolower($permission_key)) {
                        return $pms->allow;
                    }
                } else {
                    if ("custom{$pms->name}" === strtolower($permission_key)) {
                        return $pms->allow;
                    }
                }
                return true;
            }
        } else {
            return $permission->{$permission_key} === 1;
        }
    }

    public function getPermissionsV2($userId)
    {
        $tbl_prefix = env('TABLE_PREFIX');
        $version_role = '';
        $user = DB::table($tbl_prefix.'m_users')->find($userId);
        // cek active role nya apa, karena use memungkinkan untuk multiple role
        $role = DB::table($tbl_prefix.'m_roles'.$version_role)
            ->where('name', $user->role)->first();

        $roles_d_users = DB::table("{$tbl_prefix}m_roles_d_users{$version_role} as users")
            ->selectRaw('pm.*, menu.uid as "menu.uid", menu.name as "menu.name", menu.module as "menu.module", menu.submodule as "menu.submodule"')
            ->join("{$tbl_prefix}m_roles{$version_role} as roles", 'roles.id', '=', "users.{$tbl_prefix}m_roles{$version_role}_id")
            ->join("{$tbl_prefix}m_roles_d_ui_pc_permissions{$version_role} as pm", "pm.{$tbl_prefix}m_roles{$version_role}_id", '=', 'roles.id')
            ->join("{$tbl_prefix}m_menu as menu", 'menu.id', '=', 'pm.menu_id')
            ->where('users.user_id', $userId)
            ->where("users.{$tbl_prefix}m_roles{$version_role}_id", $role->id)
            ->where('pm.deleted_at', '=', null)
            ->where('users.deleted_at', '=', null)
            ->where('roles.deleted_at', '=', null)
            ->get();

        $permissions = [];
        $default_permissions = ['view', 'preview', 'create', 'edit', 'delete'];
        foreach ($roles_d_users as $pm) {
            $customs = json_decode($pm->customs);
            $module = $pm->{"menu.module"} ? str_replace(' ', '_', strtolower($pm->{"menu.module"})) : '';
            $submodule = $pm->{"menu.submodule"} ? str_replace(' ', '_', strtolower($pm->{"menu.submodule"})) : '';
            $name = $pm->{"menu.name"} ? str_replace(' ', '_', strtolower($pm->{"menu.name"})) : '';
            $uid = $pm->{"menu.uid"};

            foreach ($default_permissions as $dm) {
                if ($pm->{$dm} === 1) {
                    // if ($name && $module && $submodule) {
                    //     $permissions[] = "{$module}-{$submodule}-{$name}-{$dm}";
                    // } else if ($name && $module && !$submodule) {
                    //     $permissions[] = "{$module}-{$name}-{$dm}";
                    // } else if ($name && !$module && !$submodule) {
                    //     $permissions[] = "{$name}-{$dm}";
                    // }

                    $permissions[] = "{$uid}-{$dm}";
                }
            }

            foreach ($customs as $custom) {
                if ($custom->allow) {
                    $customName = str_replace(' ', '_', strtolower($custom->name));
                    if ($name && $module && $submodule) {
                        $permissions[] = "{$module}-{$submodule}-{$name}-{$customName}";
                    } else if ($name && $module && !$submodule) {
                        $permissions[] = "{$module}-{$name}-{$customName}";
                    } else if ($name && !$module && !$submodule) {
                        $permissions[] = "{$name}-{$customName}";
                    }
                }
            }
        }
        return $permissions;
    }

    public function getPermissionsV2_mb($userId)
    {
        $tbl_prefix = env('TABLE_PREFIX');
        $version_role = '';
        $user = DB::table($tbl_prefix.'m_users')->find($userId);
        // cek active role nya apa, karena use memungkinkan untuk multiple role
        $role = DB::table($tbl_prefix.'m_roles'.$version_role)
            ->where('name', $user->role)->first();

        $roles_d_users = DB::table("{$tbl_prefix}m_roles_d_users{$version_role} as users")
            ->selectRaw('pm.*')
            ->join("{$tbl_prefix}m_roles{$version_role} as roles", 'roles.id', '=', "users.{$tbl_prefix}m_roles{$version_role}_id")
            ->join("{$tbl_prefix}m_roles_d_ui_mb_permissions{$version_role} as pm", "pm.{$tbl_prefix}m_roles{$version_role}_id", '=', 'roles.id')
            ->where('users.user_id', $userId)
            ->where("users.{$tbl_prefix}m_roles{$version_role}_id", $role->id)
            ->where('pm.deleted_at', '=', null)
            ->where('users.deleted_at', '=', null)
            ->where('roles.deleted_at', '=', null)
            ->get();

        $permissions = [];
        $default_permissions = ['view', 'preview', 'create', 'edit', 'delete'];
        foreach ($roles_d_users as $pm) {
            $customs = json_decode($pm->customs);
            $module = $pm->module ? str_replace(' ', '_', strtolower($pm->module)) : '';
            $submodule = $pm->submodule ? str_replace(' ', '_', strtolower($pm->submodule)) : '';
            $name = $pm->name ? str_replace(' ', '_', strtolower($pm->name)) : '';
            foreach ($default_permissions as $dm) {
                if ($pm->{$dm} === 1) {
                    if ($name && $module && $submodule) {
                        $permissions[] = "{$module}-{$submodule}-{$name}-{$dm}";
                    } else if ($name && $module && !$submodule) {
                        $permissions[] = "{$module}-{$name}-{$dm}";
                    } else if ($name && !$module && !$submodule) {
                        $permissions[] = "{$name}-{$dm}";
                    }
                }
            }

            foreach ($customs as $custom) {
                if ($custom->allow) {
                    $customName = str_replace(' ', '_', strtolower($custom->name));
                    if ($name && $module && $submodule) {
                        $permissions[] = "{$module}-{$submodule}-{$name}-{$customName}";
                    } else if ($name && $module && !$submodule) {
                        $permissions[] = "{$module}-{$name}-{$customName}";
                    } else if ($name && !$module && !$submodule) {
                        $permissions[] = "{$name}-{$customName}";
                    }
                }
            }
        }
        return $permissions;
    }

    public function getMe()
    {
        $data = DB::table(env('TABLE_PREFIX').'m_users')->find($this->getUserId());
        $data = $this->arr2json($data);
        $data->permissions = $this->getPermissionsV2($data->id);
        $data->permissions_mb = $this->getPermissionsV2_mb($data->id);
        $data->id = app()->request->encrypt === 'false' ? $data->id : $this->encrypt($data->id);
        $data->roles = json_decode($data->roles);

        return $data;
    }

    public function getDetailForEvent($model)
    {
        $data = $model->readin($model->id);
        $all = app()->request->all();
        foreach ($all as $key => $dt) {
            if (is_array($dt) && count($dt)) {
                $data['data'][$key] = $dt;
            }
        }

        return $data['data'];
    }

    public function getCurrentUser()
    {
        if (app()->request->user('api') !== null) {
            return app()->request->user('api');
        } else {
            return null;
        }
    }

    private function getCurrentRole(string $roleName) {
        return (new m_roles())->where('name', $roleName)->first();
    }

    public function getApprovalPermissions(int $trx_id, array $data)
    {
        DB::beginTransaction();
        try {
            $table_prefix = getTablePrefix();
            $user = $this->getCurrentUser();
            $e_app = DB::table($table_prefix.'e_approval')->where('trx_id', $trx_id)->first();
            $current_role = $this->getCurrentRole($user->role);
            $current_role_id = $this->autoDecrypt($current_role->id);
            $action_is_allowed = true;
            // get e_approval detail
            $e_app_d = DB::table($table_prefix.'e_approval_d')
                ->where([
                    ["{$table_prefix}e_approval_id", '=', $e_app->id],
                    ['is_completed', '=', 0],
                ])
                ->first();

            if ($e_app_d) {
                // pengecekan role
                if ($e_app_d->action_role_id != $current_role_id) {
                    $action_is_allowed = false;
                }
                // pengecekan user
                if (
                    $e_app_d->action_user_id !== null &&
                    $e_app_d->action_user_id !== $user->id) {
                    $action_is_allowed = false;
                }
                if ($action_is_allowed) {
                    if ($e_app_d->type === 'MENGETAHUI') {
                        $action_is_allowed = false;

                        if ($e_app_d->send_wa_notif) {
                            //
                        }

                        if ($e_app_d->send_email_notif) {
                            //
                        }

                        DB::table($table_prefix.'e_approval_d')
                            ->where('id', $e_app_d->id)
                            ->update([
                                'is_completed' => true
                            ]);

                        // mencari detail last
                        $det_last = DB::table($table_prefix.'e_approval_d')
                            ->where([
                                ["{$table_prefix}e_approval_id", '=', $e_app->id],
                                ['is_completed', '=', 0],
                                ['is_assigned', '=', 0],
                            ])
                            ->orderBy('id', 'ASC')
                            ->first();
                        ;

                        // update detail last untuk menandai kalau sudah siap assignment
                        DB::table($table_prefix.'e_approval_d')
                            ->where('id', $det_last->id)
                            ->update([
                                'is_assigned' => 1
                            ]);

                        // insert approval log
                        DB::table("{$table_prefix}e_approval_logs")->insert([
                            "{$table_prefix}e_approval_id" => $e_app->id,
                            "trx_id" => $e_app->trx_id,
                            "trx_name" => $e_app->trx_name,
                            "trx_no" => $e_app->trx_no,
                            "trx_table" => $e_app->trx_table,
                            "trx_date" => $e_app->trx_date,
                            "trx_creator_id" => $e_app->trx_creator_id,
                            "action" => 'KNOW',
                            "action_at" => now(),
                            "action_note" => null,
                            "action_user_id" => $this->getUserId(),
                            "created_id" => $this->getUserId(),
                            "created_at" => now(),
                        ]);
                    }

                    if ($e_app_d->type === 'MENYETUJUI') {
                        if ($e_app->status === 'REVISED' || $e_app->status === 'REJECTED') {
                            $action_is_allowed = false;
                        }
                    }
                }
            } else {
                $action_is_allowed = false;
            }

            $data['approval'] = [
                'reject' => $action_is_allowed,
                'revise' => $action_is_allowed,
                'approve' => $action_is_allowed,
            ];

            $data['approval']['logs'] = DB::table("{$table_prefix}e_approval_logs as logs")
                ->selectRaw('logs.*, users.name as "action_user.name", users.phone as "action_user.phone", users.email as "action_user.email"')
                ->join("{$table_prefix}m_users as users", 'users.id', '=', 'logs.action_user_id')
                ->where([
                    ["{$table_prefix}e_approval_id", '=', $e_app->id],
                ])
                ->get();

            DB::commit();
            return $data;
        } catch(\Exception $e){
            DB::rollback();
            trigger_error( $e->getMessage() );
        }
    }

    /**
     * @param $get
     * @return array
     */
    public function chartQueryGroupByDays($get)
    {
        $get = $get->groupBy(fn($pv) => $pv->created_at->format('l'))
            ->map(fn($date) => count($date));

        $visitors = [
            'total' => 0,
            'days' => [],
            'values' => [],
        ];

        foreach ($get as $day => $value) {
            $visitors['days'][] = $day;
            $visitors['values'][] = $value;
            $visitors['total'] = $visitors['total'] + $value;
        }

        return $visitors;
    }

    /**
     * @param $get
     * @param string $groupBy
     * @param string $styleChart 'barchart' | 'piechart'
     * @return array
     */
    public function chartQueryGroupByCustomColumns($get, string $groupBy, string $styleChart)
    {
        $rows = $get;
        $get = $get->groupBy(fn($pv) => $pv->$groupBy)
            ->map(fn($val) => count($val));

        if ($styleChart === 'barchart') {
            $response = [
                'total' => 0,
                'groups' => [],
                'values' => [],
            ];

            foreach ($get as $group => $value) {
                $response['groups'][] = $group;
                $response['values'][] = $value;
                $response['total'] = $response['total'] + $value;
            }
            return $response;
        } else {
            $response = [
                'data' => $rows,
                'total' => 0,
                'values' => [],
            ];

            foreach ($get as $group => $value) {
                $response['values'][] = [
                    'name' => $group,
                    'y' => $value,
                    'sliced' => false,
                    'selected' => false,
                ];
                $response['total'] = $response['total'] + $value;
            }
            return $response;
        }
    }

    public function point_decoder($point)
    {
        $before = json_decode($point);
        $lat = $before->coordinates[1];
        $lon = $before->coordinates[0];
        return "$lat, $lon";
    }
}
