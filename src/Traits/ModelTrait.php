<?php

namespace AmazingTraits\Traits;

use AmazingTraits\Events\ModelEvent;
use AmazingTraits\Helpers\Cryptor;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use AmazingTraits\Rules\PointRule;
use App\Traits\ModelCustom;

trait ModelTrait
{
    use ModelHelper, ModelScopes, ModelDetails, ModelCustom, UserRole;

    protected static function booted()
    {
        /**
         *
         * The retrieved event will dispatch when an existing model is retrieved from the database.
         * When a new model is saved for the first time, the creating and created events will dispatch.
         * The updating / updated events will dispatch when an existing model is modified and the save method is called.
         * The saving / saved events will dispatch when a model is created or updated
         *
         * https://laravel.com/docs/9.x/eloquent#events
         *
         */
        parent::boot();
        static::creating(function ($model) {
            if (method_exists($model, 'onCreating')) $model->onCreating($model);
        });

        self::created(function ($model) {
            if (method_exists($model, 'onCreated')) $model->onCreated($model);

            if (env('PUSHER_ENABLED')) {
                event(new ModelEvent($model->getTable(), $model->getDetailForEvent($model), 'onCreated'));
            }
        });

        self::saving(function ($model) {
            if (method_exists($model, 'onSaving')) $model->onSaving($model);
        });

        self::saved(function ($model) {
            if (method_exists($model, 'onSaved')) $model->onSaved($model);

            if (env('PUSHER_ENABLED')) {
                event(new ModelEvent($model->getTable(), $model->getDetailForEvent($model), 'onSaved'));
            }
        });

        self::updating(function ($model) {
            foreach ($model->getAttributes() as $key => $val) {
                if (!in_array($key, $model->getColumns($model->getTable()))) {
                    $model->offsetUnset($key);
                }
            }

            if (method_exists($model, 'onUpdating')) $model->onUpdating($model);
        });

        self::updated(function ($model) {
            if (method_exists($model, 'onUpdated')) $model->onUpdated($model);

            if (env('PUSHER_ENABLED')) {
                event(new ModelEvent($model->getTable(), $model->getDetailForEvent($model), 'onUpdated'));
            }
        });

        self::deleting(function ($model) {
            if (method_exists($model, 'onDeleting')) {
                $model->onDeleting($model);
                $model->saveQuietly();
            };
        });

        self::deleted(function ($model) {
            if (method_exists($model, 'onDeleted')) $model->onDeleted($model);

            if (env('PUSHER_ENABLED')) {
                event(new ModelEvent($model->getTable(), $model, 'onDeleted'));
            }
        });

        self::retrieved(function ($model) {
            if (method_exists($model, 'onRetrieved')) {
                $model->onRetrieved($model);

                //  pas PUT/DELETE by id harus dikecualikan agar tidak diencrypt kolom2nya

                if( !app()->request->isMethod('GET') ) return;

                if (isset(app()->request->encrypt) && app()->request->encrypt === 'false') {
                    // abaikan
                } else {
                    foreach ($model->toArray() as $key => $val) {
                        if (Str::endsWith($key, '_id') && is_numeric($val)) {
                            $model[$key] = $model->encrypt($val);
                        } else if (strpos($key, '.') > -1) {
                            $exp = explode('.', $key);
                            if (Str::endsWith($exp[count($exp) - 1], '_id') && is_numeric($val)) {
                                $model[$key] = $model->encrypt($val);
                            } else if (Str::is('id', $exp[count($exp) - 1]) && is_numeric($val)) {
                                $model[$key] = $model->encrypt($val);
                            }
                        }
                    }
                }
            }
        });
    }

    public function readin(mixed $id) {
        $request = app()->request;
        $decryptedId = $this->autoDecrypt($id);
        $availableScopesForById = array_filter($this->scopes, function($scope){
            return !in_array( $scope, [
                'wherin','whereadnotin','whereadin','distinctin','orderin','filterin','searchin'
            ]) ;
        });

        $data = $this->scopes( $availableScopesForById );
        if ($request->has('all')) {
            $data = $data->withTrashed();
        }
        $data = $data->find( $decryptedId );
        if ( $data ){
            $data = $data->toArray();
        } else {
            return response()->json([
                'message' => t('message.data_not_found'),
                'errors'=> ['No Data']
            ],404);
        }

        if ($this->details ){
            $data = $this->detailinDetails($this->details, $this->getOnlyTable(), $decryptedId, $data);
            if (isset($data->original)){
                return response()->json($data->original, $data->status());
            }
        }

        if ( !$data ){
            return response()->json([
                'message'   => t('message.data_not_found'),
                'data'      => null
            ], 404);
        }
        return [
            'success' => true,
            'data' => $data
        ];
    }

    //  Cara pakai: harus di dalam try catch, setelah validasi manual
    //  ->creatin();
    public function creatin($arrData = null, $customRules = [], $debug = false)
    {
        DB::beginTransaction();

        try {
            $reqArr = $arrData ?? app()->request->all();

            //  cast data joinan FK ke decrypted id
            if (isset($this->joins)) {
                foreach ($this->joins as $currentCol => $val) {
                    if (@$reqArr[$currentCol] && !is_numeric($reqArr[$currentCol])) {
                        $reqArr[$currentCol] = $this->decrypt($reqArr[$currentCol]) ?? $reqArr[$currentCol];
                    }
                }
            }

            foreach ($reqArr as $currentCol => $val) {
                if (Str::endswith($currentCol, '_id') && $val && !is_numeric($val)) {
                    $reqArr[$currentCol] = $this->decrypt($val) ?? $val;
                }
            }

            $rules = method_exists($this, 'creatingRules') ? $this->creatingRules() : [];
            $details = @$this->details ?? [];
            $fixedRules = [];
            if (count($customRules)) {
                $fixedRules = array_filter($customRules, function ($rule) {
                    return !is_array($rule);
                });
            } else {
                $fixedRules = array_filter($rules, function ($rule) {
                    return !is_array($rule);
                });
            }

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

            $validator = Validator::make($reqArr, $fixedRules);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    "message" => t('message.data_invalid'),
                    "errors" => $validator->errors()->all()
                ], 422);
            }

            $columns = $this->getColumns($this->getTable());
            $reqArrFinal = Arr::only($reqArr, $columns);
            $reqArrFinal = $this->onTransform($reqArrFinal);

            $active_flag_available = false;
            foreach ($columns as $col) {
                if ($col === 'active_flag') {
                    $active_flag_available = true;
                }
            }

            // pengecekan active_flag, jika tidak ada akan di set default = true
            if ($active_flag_available === true && !isset($reqArrFinal['active_flag'])) {
                $reqArrFinal['active_flag'] = true;
            }

            $createdModel = $this->create($reqArrFinal);
            if (count($details)) {
                $this->creatinDetails($createdModel, $rules, $reqArr, $details);
            }

            foreach($createdModel->attributes as $key => $val){
                if(str_ends_with($key, "_id") && $val){
                    $createdModel[$key] = $this->encrypt($val);
                }
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => t('message.data_saving_success'),
                'data' => $createdModel
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            if (str_contains($e->getMessage(), 'use_smart_trigger_error')) {
                $decodeError = json_decode($e->getMessage(), true);
                return response()->json([
                    'success' => false,
                    'message' => $decodeError['message'],
                    'errors' => $decodeError['errors']
                ], 422);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'errors' => @config('errors') ?? [$e->getMessage()],
                    'errorDebug' => @config('errors') ?? [$e->getMessage() . "-" . $e->getLine() . "-" . $e->getFile()],
                ], 422);
            }
        }
    }

    public function creatins($multiArrData = null, $customRules = []) {
        try {
            foreach ($multiArrData as $arrData) {
                $creatin = $this->creatin($arrData, $customRules);
                if ($creatin->original['success'] === false) {
                    throw new \Exception(json_encode([
                        'message' => $creatin->original['message'],
                        'errors' => $creatin->original['errors']
                    ]));
                }
            }
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => t('message.data_saving_success'),
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            $response = json_decode($e->getMessage(), true);
            return response()->json([
                'success' => false,
                'message' => $response['message'],
                'errors' => $response['errors']
            ], 422);
        }
    }

    //  Cara pakai: harus di dalam try catch, setelah validasi manual
    //  ->updatin( id: 1 );
    public function updatin(int $id, $arrData = null, $customRules = [], $successMessage = null)
    {
        $createdModel = $this->find(
            $id
        /** Harus sudah didecrypt */
        );

        if (!$createdModel) {
            return response()->json([
                'success' => false,
                'message' => t('message.data_not_found_for_update'),
                'errors' => ["No Data"]
            ], 404);
        }

        DB::beginTransaction();

        try {
            $reqArr = $arrData ?? app()->request->all();
            $rules = method_exists($this, 'updatingRules') ? $this->updatingRules($id) : [];
            $details = @$this->details ?? [];

            $fixedRules = [];
            if (count($customRules)) {
                $fixedRules = array_filter($customRules, function ($rule) {
                    return !is_array($rule);
                });
            } else {
                $fixedRules = array_filter($rules, function ($rule) {
                    return !is_array($rule);
                });
            }

            //  cast data joinan FK ke decrypted id
            if (isset($this->joins)) {
                foreach ($this->joins as $currentCol => $val) {
                    if (@$reqArr[$currentCol] && !is_numeric($reqArr[$currentCol])) {
                        $reqArr[$currentCol] = $this->decrypt($reqArr[$currentCol]) ?? $reqArr[$currentCol];
                    }
                }
            }

            foreach ($reqArr as $currentCol => $val) {
                if (Str::endswith($currentCol, '_id') && $val && !is_numeric($val)) {
                    $reqArr[$currentCol] = $this->decrypt($val) ?? $val;
                }
            }

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

            $validator = Validator::make($reqArr, $fixedRules);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    "message" => t('message.data_invalid'),
                    "errors" => $validator->errors()->all()
                ], 422);
            }

            $columns = $this->getColumns($this->getTable());
            $reqArrFinal = Arr::only($reqArr, $columns);
            $reqArrFinal = $this->onTransform($reqArrFinal);
            // $reqArr = Arr::except($reqArr, ['id']);
            $createdModel->update($reqArrFinal);

            if (count($details)) {
                $this->updatinDetails($createdModel, $rules, $reqArr, $details, $id, null, true);
            }

            foreach($createdModel->attributes as $key => $val){
                if ($key === 'id') {
                    // $createdModel[$key] = $this->encrypt($val); // ini tidak perlu mas afif yg ganteng, sudah encryptan
                } else if(str_ends_with($key, "_id") && $val){
                    $createdModel[$key] = $this->encrypt($val);
                }
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => $successMessage ?? t('message.data_updating_success'),
                'data'  => $createdModel
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            if (str_contains($e->getMessage(), 'use_smart_trigger_error')) {
                $decodeError = json_decode($e->getMessage(), true);
                return response()->json([
                    'success' => false,
                    'message' => $decodeError['message'],
                    'errors' => $decodeError['errors']
                ], 422);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'errors' => @config('errors') ?? [$e->getMessage() . "-" . $e->getLine() . "-" . $e->getFile()]
                ], 422);
            }
        }
    }

    //  Cara pakai: harus di dalam try catch
    //  ->deletin( id: 1, softDelete: true );
    public function deletin(int $id, bool $softDelete = true)
    {
        DB::beginTransaction();
        try {
            if (isset($this->children)) {
                foreach ($this->children as $child => $column) {
                    $childClass = new $child;
                    if ($childClass->where($column, $id)->exists()) {
                        trigger_error("Resource is still referenced in " . $childClass->getTable());
                    }
                }
            }

            $m = $this->getModel($this->getOnlyTable());
            $items = $this->withoutEvents(function () use ($m, $id) {
                return $m->where($m->getKeyName(), $id)->get();
            });

            if (count($items)) {
                foreach ($items as $item) {
                    if ($softDelete) {
                        // Untuk soft deleting
                        $item->delete();
                    } else {
                        // Untuk force deleting
                        $item->forceDelete();
                    }
                }

                $details = @$this->details ?? [];
                $this->deletinDetails($softDelete, $m, $details, $id, true);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => t('message.data_not_found')
                ], 404);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => t('message.data_deleting_success')
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            if (str_contains($e->getMessage(), 'use_smart_trigger_error')) {
                $decodeError = json_decode($e->getMessage(), true);
                return response()->json([
                    'success' => false,
                    'message' => $decodeError['message'],
                    'errors' => $decodeError['errors']
                ], 422);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Sorry, something went wrong!',
                    'errors' => [$e->getMessage()]
                ], 422);
            }
        }
    }
}
