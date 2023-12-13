<?php

namespace App\Models;

use AmazingTraits\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class m_general extends Model
{
    use HasFactory, ModelTrait, SoftDeletes;

    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $table;

    public function __construct($attr = array(), $exists = false)
    {
        parent::__construct($attr, $exists);
        $this->table = env('TABLE_PREFIX') . 'm_general';
    }
    /**
     * @var string[] // ['default', 'get', 'find', 'store', 'update', 'destroy', 'custom*', 'getfunc*']
     */
    public $auths = ['store', 'update', 'destroy'];

    protected $casts = [
        'active_flag' => 'boolean',
    ];

    /**
     * Untuk auto join saat get data
     */
    protected $joins = [
        'created_id' => [m_users::class, 'username,name'],
        'updated_id' => [m_users::class, 'username,name'],
        'deleted_id' => [m_users::class, 'username,name'],
    ];

    /**
     * Untuk auto mengurusi detail saat Create/Update/Delete data, mirip hasMany tapi versi simple
     */
    public $details = [];

    /**
     * Fungsi untuk validation saat create
     */
    public function creatingRules(): array
    {
        return [];
    }

    /**
     * Fungsi untuk validation saat update
     */
    public function updatingRules(int $id): array
    {
        return $this->creatingRules();
    }

    /**
     * Untuk sementara ini Lifecycle yang tersedia:
     *
     * onCreating = Sebelum proses menambahkan data
     * onCreated = Sesudah proses menambahkan data
     * onUpdating = Sebelum proses memperbarui data
     * onUpdated = Setelah proses memperbarui data
     * onSaved = Ke trigger ketika propses menambahkan data atau memperbarui data
     * onRetrieved = Ke trigger ketika proses membaca data
     * onDeleting = Sebelum menghapus data
     * onDeleted = Sesudah menghapus data
     *
     * readmore: https://laravel.com/docs/10.x/eloquent#events
     */

    public function onCreating($model)
    {
        // jika ingin mengembalikan error pada lifecycle
        // return trigger_error('error at oncreating');

        $model->created_id = $model->getUserId();
    }

    /**
     * Tambahan saat update (fitur trait)
     */
    public function onUpdating($model)
    {
        // jika ingin mengembalikan error pada lifecycle
        // return trigger_error('error at onUpdating');

        $model->updated_id = $model->getUserId();
    }

    /**
     * Tambahan saat read (fitur trait)
     */
    public function onRetrieved($model)
    {
        //
    }

    public function onDeleting($model)
    {
        $model->deleted_id = $model->getUserId();
    }

    /**
     * @return void
     * Custom function dengan method GET
     */
    public function getfunctesting()
    {
        $req = app()->request;
        dd($req->all());

        // jika ingin mengembalikan error pada custom function
        // sendError(error = required, errors = optional, errorCode = optional)
        // return sendError('Contoh error', ['error 1', 'error 2'], 500);
        /**
         * Contoh respon
         * {
         *  "success": false,
         *  "message": "Contoh error",
         *  "errors": [
         *    "error 1",
         *    "error 2"
         *  ]
         * }
         */

        // jika ingin mengembalikan respons sukses
        // return response()->json([
        //     'data' => 'contoh'
        // ], 200)
    }

    /**
     * @return mixed
     * Custom function untuk mendapatkan option select
     */
    public function getfuncoption_select()
    {
        $request = app()->request;
        $type = $request->type;
        if (in_array($type, ['PROVINCES', 'REGENCIES', 'DISTRICTS', 'VILLAGES', 'RECOMMENDATIONS'])) {
            $options = m_general::where('group', "SELECT $type");

            if ($request->filled('relation')) $options->where('value_2', $this->autoDecrypt($request->relation));

            $options = $options->get();
            return $options;
        } else {
            return sendError('Options Not Found');
        }
    }

    /**
     * @return void
     * Custom function dengan method POST
     */
    public function customSubmit_order()
    {
        $req = app()->request;
        dd($req->all());

        // jika ingin mengembalikan error pada custom function
        // sendError(error = required, errors = optional, errorCode = optional)
        // return sendError('Contoh error', ['error 1', 'error 2'], 500);
        /**
         * Contoh respon
         * {
         *  "success": false,
         *  "message": "Contoh error",
         *  "errors": [
         *    "error 1",
         *    "error 2"
         *  ]
         * }
         */

        // jika ingin mengembalikan respons sukses
        // return response()->json([
        //     'data' => 'contoh'
        // ], 200)
    }
}
