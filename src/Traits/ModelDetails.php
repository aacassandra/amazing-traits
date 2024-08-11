<?php

namespace AmazingTraits\Traits;

use AmazingTraits\Rules\PointRule;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

trait ModelDetails
{
    /**
     * @param $createdModel
     * @param array $rules
     * @param mixed $reqArr
     * @param array $details
     * @return void
     */
    public function creatinDetails($createdModel, array $rules, mixed $reqArr, array $details)
    {
        foreach ($reqArr as $key => $val) {
            if (method_exists($this, $key) && is_array($val)) {
                //  jika pakai hasMany
                $createdModel->$key()->createMany($val);
            } elseif (strpos(json_encode($details), $key)) {
                $allow = false;
                if (isset($rules[$key]) && str_contains($rules[$key], 'nullable')) {
                    if ($val && is_array($val)) {
                        $allow = true;
                    }
                } else if (is_array($val)) {
                    $allow = true;
                }

                if ($allow) {
                    foreach ($val as $dtlIdx => $dtlRow) {
                        $urutan = ((int)$dtlIdx) + 1;
                        $modelDtl = $this->getModel($key);

                        if (!$modelDtl) {
                            trigger_error("Detail of $key is not found");
                        }

                        $fk = $createdModel->getOnlyTable() . "_id";
                        foreach ($details as $idx => $dtl) {
                            if (str_starts_with($dtl, $key) && strpos($dtl, ':')) {
                                $fk = explode(':', $dtl)[1];
                            }
                        }

                        if (isset($rules[$key . ".*"])) {
                            //  cast data joinan FK ke decrypted id
                            foreach ($dtlRow as $currentCol => $val2) {
                                if (Str::endswith($currentCol, '_id') && $val2 && !is_numeric($val2)) {
                                    $dtlRow[$currentCol] = $this->decrypt($val2) ?? $val2;
                                }
                            }

                            $detailRules = $rules[$key . ".*"];
                            $detailsSub = @$modelDtl->details ?? [];
                            $fixedRules = array_filter($detailRules, function ($rule) {
                                return !is_array($rule);
                            });

                            foreach ($fixedRules as $index => $rule) {
                                if (strpos($rule, 'point')) {
                                    $split = explode('|', $rule);
                                    $fixedRules[$index] = [];
                                    foreach ($split as $sp) {
                                        if ($sp === 'point') {
                                            $fixedRules[$index][] = new PointRule;
                                        } else {
                                            $fixedRules[$index][] = $sp;
                                        }
                                    }
                                }
                            }

                            $dtlValidator = Validator::make($dtlRow, $fixedRules);

                            if ($dtlValidator->fails()) {
                                config(['errors' => $dtlValidator->errors()->all()]);
                                trigger_error("Sorry, the detail data in line $urutan is not valid.");
                            }

                            //  penambahan id FK parent
                            $fixedRowDtl = array_merge($dtlRow, [
                                $fk => $this->decrypt($createdModel->id)
                            ]);

                            if (env('DB_CONNECTION') === 'pgsql') {
                                $schm = $this->getSchema($key);
                                $columns = $this->getColumns("$schm.{$modelDtl->getOnlyTable()}");
                            } else {
                                $columns = $this->getColumns("{$modelDtl->getOnlyTable()}");
                            }

                            // pengecekan active_flag, jika tidak ada akan di set default = true
                            $active_flag_dtl_available = false;
                            if (isset($rules[$key . ".*"]['active_flag'])) {
                                $active_flag_dtl_available = true;
                            }

                            $fixedRowDtl = Arr::only($fixedRowDtl, $columns);

                            if ($active_flag_dtl_available === true && !isset($fixedRowDtl['active_flag'])) {
                                $fixedRowDtl['active_flag'] = true;
                            }

                            $fixedRowDtl = $this->onTransform($fixedRowDtl);
                            $createdModelDetail = $modelDtl->create($fixedRowDtl);
                            if (count($detailsSub)) {
                                $this->creatinDetails($createdModelDetail, $detailRules, $dtlRow, $detailsSub);
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * @param $createdModel
     * @param array $rules
     * @param mixed $reqArr
     * @param array $details
     * @param mixed $id
     * @param mixed $id2
     * @param bool $header
     * @return void
     */
    public function updatinDetails($createdModel, array $rules, mixed $reqArr, array $details, mixed $id, mixed $id2, bool $header = false, bool $debug = false)
    {
        foreach ($reqArr as $key => $val) {
            if (method_exists($this, $key) && is_array($val)) {
                //  jika pakai hasMany
                $createdModel->$key()->delete();
                $createdModel->$key()->createMany($val);
            } elseif (strpos(json_encode($details), $key)) {
                $allow = false;
                $modelDtl = $this->getModel($key);
                if (isset($rules[$key]) && str_contains($rules[$key], 'nullable')) {
                    if ($val && is_array($val)) {
                        $allow = true;
                    } else {
                        $parentModel = $createdModel->getOnlyTable();
                        $fk = $parentModel . "_id";
                        $dtls = $modelDtl->where($fk, $id)->get();

                        foreach ($dtls as $dtl) {
                            $modelDtl->deletin($this->autoDecrypt($dtl->id));
                        }
                    }
                } else if (is_array($val)) {
                    $allow = true;
                }

                if ($allow && $reqArr[$key] && is_array($reqArr[$key]) && count($reqArr[$key])) {
                    if (!$modelDtl) {
                        trigger_error("Detail of $key is not found");
                    }
                    $fk = $createdModel->getOnlyTable() . "_id";
                    foreach ($details as $idx => $dtl) {
                        if (str_starts_with($dtl, $key) && strpos($dtl, ':')) {
                            $fk = explode(':', $dtl)[1];
                        }
                    }
                    $dts = $modelDtl->where($fk, $id)->get();
                    //  hapus dahulu data detail lama
                    //  hati2 dengan data detail lama yg tidak boleh dihapus (next)
                    foreach ($dts as $dtlIdxPrev => $val2) {
                        $this->withoutEvents(function () use ($dts, $dtlIdxPrev) {
                            return $dts[$dtlIdxPrev]->forceDelete();
                        });
                    }

                    foreach ($val as $dtlIdx => $dtlRow) {
                        $urutan = ((int)$dtlIdx) + 1;
                        $modelDtl = $this->getModel($key);

                        if (isset($rules[$key . ".*"])) {
                            //  cast data joinan FK ke decrypted id
                            foreach ($dtlRow as $currentCol => $val) {
                                if (Str::endswith($currentCol, '_id') && $val && !is_numeric($val)) {
                                    $dtlRow[$currentCol] = $this->decrypt($val) ?? $val;
                                }
                            }

                            $detailRules = $rules[$key . ".*"];
                            $detailsSub = @$modelDtl->details ?? [];
                            $fixedRules = array_filter($detailRules, function ($rule) {
                                return !is_array($rule);
                            });

                            foreach ($fixedRules as $index => $rule) {
                                if (strpos($rule, 'point')) {
                                    $split = explode('|', $rule);
                                    $fixedRules[$index] = [];
                                    foreach ($split as $sp) {
                                        if ($sp === 'point') {
                                            $fixedRules[$index][] = new PointRule;
                                        } else {
                                            $fixedRules[$index][] = $sp;
                                        }
                                    }
                                }
                            }

                            $dtlValidator = Validator::make($dtlRow, $fixedRules);

                            if ($dtlValidator->fails()) {
                                config(['errors' => $dtlValidator->errors()->all()]);
                                trigger_error("Sorry, the detail data in line $urutan is not valid.");
                            }

                            //  penambahan id FK parent
                            $fixedRowDtl = array_merge($dtlRow, [
                                $fk => $header ? $id : $id2
                            ]);
                            //  create detail
                            if (env('DB_CONNECTION') === 'pgsql') {
                                $schm = $this->getSchema($key);
                                $columns = $this->getColumns("$schm.{$modelDtl->getOnlyTable()}");
                            } else {
                                $columns = $this->getColumns("{$modelDtl->getOnlyTable()}");
                            }

                            $fixedRowDtl = Arr::only($fixedRowDtl, $columns);
                            $fixedRowDtl = $this->onTransform($fixedRowDtl);
                            $createdModelDetail = $modelDtl->create($fixedRowDtl);

                            if (count($detailsSub)) {
                                $this->updatinDetails(
                                    $createdModelDetail,
                                    $detailRules,
                                    $dtlRow,
                                    $detailsSub,
                                    isset($dts[$dtlIdx]->id) ? $this->autoDecrypt($dts[$dtlIdx]->id) : null,
                                    $this->autoDecrypt($createdModelDetail->id),
                                    false,
                                    $dtlIdx === 1
                                );
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * @param bool $softDelete
     * @param mixed $m
     * @param array $details
     * @param mixed $id
     * @param bool $header
     * @return void
     */
    public function deletinDetails(bool $softDelete, mixed $m, array $details, mixed $id, bool $header = false)
    {
        foreach ($details  as $key) {
            if (method_exists($this, $key)) {
                //  jika pakai hasMany
            } else {
                //  hapus dahulu detail
                $modelDtl = $this->getModel($key);
                if (!$modelDtl) {
                    trigger_error("Detail of $key is not found");
                }
                $mDt = $modelDtl;
                $itemsDtl = $modelDtl->withoutEvents(function () use ($m, $mDt, $id) {
                    return $mDt->where($m->getOnlyTable() . "_id", $id)->get();
                });

                $detailsSub = @$modelDtl->details ?? [];
                foreach ($itemsDtl as $itemDtl) {
                    if (count($detailsSub)) {
                        $this->deletinDetails($softDelete, $modelDtl, $detailsSub, $this->autoDecrypt($itemDtl->id), false);
                    }
                    if ($softDelete) {
                        // Untuk soft deleting
                        $itemDtl->delete();
                    } else {
                        // Untuk force deleting
                        $itemDtl->forceDelete();
                    }
                }
            }
        }
    }

    /**
     * @param array $details
     * @param string $tableName
     * @param mixed $decryptedId
     * @param array $data
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function detailinDetails(array $details, string $tableName, mixed $decryptedId, array $data, $debug = false)
    {
        $req = app()->request;
        foreach ($details as $detailName) {
            if (strpos($detailName, ':')) {
                $detailName = explode(':', $detailName);
                $detailName = $detailName[0];
            }
            $model = $this->getModel($detailName);
            $dtlModel = $model->scopes($this->scopes);

            if (app()->request->has('all')) {
                $dtlModel = $dtlModel->withTrashed();
            }

            if (!$dtlModel) {
                return response()->json([
                    'message' => "Resource of $detailName is not found",
                    "errors" => ["Resource of $detailName is not found"]
                ], 404);
            }

            $fk = $tableName . "_id";
            foreach ($details as $idx => $dtl) {
                if (str_starts_with($dtl, $detailName) && strpos($dtl, ':')) {
                    $fk = explode(':', $dtl)[1];
                }
            }

            // variable handle recursive details
            $detailsSub = @$model->details ?? [];

            $data[$detailName] = $dtlModel
                ->where($fk, $decryptedId)
                ->orderBy('id')->get();




            if ($debug && count($data[$detailName])) {
                foreach ($data[$detailName] as $item) {
                    foreach ($item->toArray() as $key => $val) {
                        if (Str::endsWith($key, '_id') && is_numeric($val)) {
                            $item->$key = $this->encrypt($val);
                        } else if (strpos($key, '.') > -1) {
                            $exp = explode('.', $key);
                            if (Str::endsWith($exp[count($exp) - 1], '_id') && is_numeric($val)) {
                                $item->$key = $this->encrypt($val);
                            } else if (Str::is('id', $exp[count($exp) - 1]) && is_numeric($val)) {
                                $item->$key = $this->encrypt($val);
                            }
                        }
                    }
                }
            }

            foreach ($data[$detailName] as $dtl) {
                if (count($detailsSub)) {
                    foreach ($dtl->details as $subdetailName) {
                        $subdata = [];
                        if (strpos($subdetailName, ':')) {
                            $subdetailName = explode(':', $subdetailName)[0];
                        }
                        $subdata = $this->detailinDetails($dtl->details, $dtl->getOnlyTable(), $this->autoDecrypt($dtl->id), $subdata, true);
                        if (isset($subdata->original)) {
                            return response()->json($subdata->original, $subdata->status());
                        }
                        $dtl->$subdetailName = $subdata[$subdetailName];
                    }
                }
                if ($req->encrypt === true || $req->encrypt === 'true') {
                    foreach ($dtl->toArray() as $dkey => $dval) {
                        if (Str::endsWith($dkey, '_id') && is_numeric($dval)) {
                            $dtl[$dkey] = $this->encrypt($dval);
                        } else if (strpos($dkey, '.') > -1) {
                            $exp = explode('.', $dkey);
                            if (Str::endsWith($exp[count($exp) - 1], '_id') && is_numeric($dval)) {
                                $dtl[$dkey] = $this->encrypt($dval);
                            } else if (Str::is('id', $exp[count($exp) - 1]) && is_numeric($dval)) {
                                $dtl[$dkey] = $this->encrypt($dval);
                            }
                        }
                    }
                }
            }
        }
        return $data;
    }
}
