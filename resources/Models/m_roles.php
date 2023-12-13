<?php

namespace App\Models;

use AmazingTraits\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class m_roles extends Model
{
    use HasFactory, ModelTrait, SoftDeletes;

    protected $guarded = ['id','created_at','updated_at'];
    protected $table;

    /**
     * @var bool Jika false maka tidak muncul di apidocs
     */
    public $hide = false;
    /**
     * @var string[] // ['default', 'get', 'find', 'store', 'update', 'destroy', 'custom*', 'getfunc*']
     */
    public $auths = ['default'];

    protected $casts = [
        'active_flag' => 'boolean',
    ];

    /**
     * Untuk auto mengurusi detail saat Create/Update/Delete data, mirip hasMany tapi versi simple
     */
    public $details = [];

    public function __construct($attr = array(), $exists = false) {
        parent::__construct($attr, $exists);
        $this->table = env('TABLE_PREFIX') . 'm_roles';
        $this->details[] = env('TABLE_PREFIX') . 'm_roles_d_users';
        $this->details[] = env('TABLE_PREFIX') . 'm_roles_d_ui_pc_permissions';
        $this->details[] = env('TABLE_PREFIX') . 'm_roles_d_ui_mb_permissions';
        $this->details[] = env('TABLE_PREFIX') . 'm_roles_d_api_permissions';
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
            'name' => 'required|string',
            'note' => 'nullable|string',
            'active_flag' => 'required|boolean',

            env('TABLE_PREFIX').'m_roles_d_users' => 'nullable|array',
            env('TABLE_PREFIX').'m_roles_d_users.*' => [
                'user_id' => 'integer'
            ],

            env('TABLE_PREFIX').'m_roles_d_api_permissions' => 'nullable|array',
            env('TABLE_PREFIX').'m_roles_d_api_permissions.*' => [
                'model_id' => 'integer',
                'get' => 'required|boolean',
                'find' => 'required|boolean',
                'store' => 'required|boolean',
                'update' => 'required|boolean',
                'destroy' => 'required|boolean',
                'customs' => 'nullable|array',
            ],

            env('TABLE_PREFIX').'m_roles_d_ui_pc_permissions' => 'nullable|array',
            env('TABLE_PREFIX').'m_roles_d_ui_pc_permissions.*' => [
                'menu_id' => 'integer',
                'view' => 'required|boolean',
                'preview' => 'required|boolean',
                'create' => 'required|boolean',
                'edit' => 'required|boolean',
                'delete' => 'required|boolean',
                'customs' => 'nullable|array',
            ],

            env('TABLE_PREFIX').'m_roles_d_ui_mb_permissions' => 'nullable|array',
            env('TABLE_PREFIX').'m_roles_d_ui_mb_permissions.*' => [
                'name' => 'required|string|max:60',
                'module' => 'nullable|string|max:60',
                'submodule' => 'nullable|string|max:60',
                'view' => 'required|boolean',
                'preview' => 'required|boolean',
                'create' => 'required|boolean',
                'edit' => 'required|boolean',
                'delete' => 'required|boolean',
                'customs' => 'nullable|array',
            ]
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
