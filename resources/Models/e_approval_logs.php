<?php

namespace App\Models;

use AmazingTraits\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class e_approval_logs extends Model
{
    use HasFactory, ModelTrait, SoftDeletes;

    protected $guarded = ['id','created_at','updated_at'];
    protected $table;
    public $hide = true;

    public function __construct($attr = array(), $exists = false) {
        parent::__construct($attr, $exists);
        $this->table = env('TABLE_PREFIX') . 'e_approval_logs';
    }

    /**
     * Untuk auto join saat get data
     */
    protected $joins = [
        'created_id' => [m_users::class, 'username,name'],
        'updated_id' => [m_users::class, 'username,name'],
        'deleted_id' => [m_users::class, 'username,name'],
    ];

    /**
     * Fungsi untuk validation saat create
     */
    public function creatingRules(): array
    {
        return [
            env('TABLE_PREFIX')."e_approval_id" => "required|integer",
            "trx_id" => "required|integer",
            "trx_name" => "required|string",
            "trx_no" => "required|string",
            "trx_table" => "required|string",
            "trx_date" => "required|date",
            "trx_created_id" => "required|integer",
            "status" => "required|string"
        ];
    }

    /**
     * Fungsi untuk validation saat update
     */
    public function updatingRules( int $id ): array
    {
        return $this->creatingRules();
    }

    public function onCreating( $model )
    {
        $model->created_id = $model->getUserId();
    }

    /**
     * Tambahan saat update (fitur trait)
     */
    public function onUpdating( $model )
    {
        $model->updated_id = $model->getUserId();
    }

    /**
     * Tambahan saat read (fitur trait)
     */
    public function onRetrieved( $model )
    {
        //
    }

    public function onDeleting($model)
    {
        $model->deleted_id = $model->getUserId();
    }
}
