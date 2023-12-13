<?php

namespace App\Models;

use App\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class m_roles_d_api_permissions extends Model
{
    use HasFactory, ModelTrait, SoftDeletes;

    protected $guarded = ['id','created_at','updated_at'];
    protected $table;
    public $hide = true;
    /**
     * @var string[] // ['default', 'get', 'find', 'store', 'update', 'destroy', 'custom*', 'getfunc*']
     */
    public $auths = ['default'];

    public function __construct($attr = array(), $exists = false) {
        parent::__construct($attr, $exists);
        $this->table = env('TABLE_PREFIX') . 'm_roles_d_api_permissions';
    }

    /**
     * Untuk auto join saat get data
     */
    protected $joins = [
        'created_id' => [m_users::class, 'username,name'],
        'updated_id' => [m_users::class, 'username,name'],
        'deleted_id' => [m_users::class, 'username,name'],
        'model_id' => [m_models::class, 'name']
    ];

    protected $casts = [
        'get' => 'boolean',
        'find' => 'boolean',
        'store' => 'boolean',
        'update' => 'boolean',
        'destroy' => 'boolean',
        'customs' => 'array',
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
