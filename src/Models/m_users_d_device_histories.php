<?php

namespace App\Models;

use App\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class m_users_d_device_histories extends Model
{
    use HasFactory, ModelTrait, SoftDeletes;

    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $table;
    public $hide = true;

    public function __construct($attr = array(), $exists = false)
    {
        parent::__construct($attr, $exists);
        $this->table = env('TABLE_PREFIX') . 'm_users_d_device_histories';
    }
    /**
     * @var string[] // ['default', 'get', 'find', 'store', 'update', 'destroy', 'custom*', 'getfunc*']
     */
    public $auths = ['default'];

    protected $casts = [
        'current' => 'boolean',
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

    public function onCreating($model)
    {
        $model->created_id = $model->getUserId();
    }

    /**
     * Tambahan saat update (fitur trait)
     */
    public function onUpdating($model)
    {
        $model->updated_id = $model->getUserId();
    }

    /**
     * Tambahan saat read (fitur trait)
     */
    public function onRetrieved($model)
    {
        if ($model->current == 1) {
            $model->online = "Aktif";
        } else {
            $model->online = "Tidak Aktif";
        }
    }

    public function onDeleting($model)
    {
        $model->deleted_id = $model->getUserId();
    }
}
