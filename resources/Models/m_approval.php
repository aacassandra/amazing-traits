<?php

namespace App\Models;

use AmazingTraits\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class m_approval extends Model
{
    use HasFactory, ModelTrait, SoftDeletes;
    /**
     * @var string[] // ['default', 'get', 'find', 'store', 'update', 'destroy', 'custom*', 'getfunc*']
     */
    public $auths = ['default'];
    protected $guarded = ['id','created_at','updated_at'];
    protected $table;
    public $hide = true;
    public $details = [];

    public function __construct($attr = array(), $exists = false) {
        parent::__construct($attr, $exists);
        $this->table = env('TABLE_PREFIX') . 'm_approval';
        $this->details[] = env('TABLE_PREFIX') . "m_approval_d_rules";
        $this->details[] = env('TABLE_PREFIX') . "m_approval_d_configs";
        $this->details[] = env('TABLE_PREFIX') . "m_approval_d_excludes";
    }

    /**
     * Untuk auto join saat get data
     */
    protected $joins = [
        'created_id' => [m_users::class, 'username,name'],
        'updated_id' => [m_users::class, 'username,name'],
        'deleted_id' => [m_users::class, 'username,name'],
        'company_id' => [cp_m_clusters::class, 'name'],
        'menu_id' => [m_menu::class, 'name']
    ];

    /**
     * Fungsi untuk validation saat create
     */
    public function creatingRules(): array
    {
        return [
            'company_id' => 'nullable|integer',
            'menu_id' => 'required|integer',
            'information' => 'nullable|string',
            'active_flag' => 'nullable|boolean',

            env('TABLE_PREFIX') . 'm_approval_d_rules' => 'required|array',
            env('TABLE_PREFIX') . 'm_approval_d_rules.*' => [
                'level' => 'required|integer',
                'level_order' => 'required|integer',
                'type' => 'required|in:MENGAJUKAN,MENGETAHUI,MENYETUJUI',
                'role_id' => 'required|integer',
                'user_id' => 'nullable|integer',
            ],

            env('TABLE_PREFIX') . 'm_approval_d_configs' => 'nullable|array',
            env('TABLE_PREFIX') . 'm_approval_d_configs.*' => [
                'level' => 'required|integer',
                'is_full_approve' => 'required|boolean',
                'is_skippable' => 'required|boolean',
                'is_able_to_skip' => 'required|boolean',
                'send_wa_notif' => 'required|boolean',
                'send_email_notif' => 'required|boolean',
                'min_value' => 'nullable|integer',
                'max_value' => 'nullable|integer',
            ],

            env('TABLE_PREFIX') . 'm_approval_d_excludes' => 'nullable|array',
            env('TABLE_PREFIX') . 'm_approval_d_excludes.*' => [
                'level' => 'required|integer',
                'user_id' => 'required|boolean',
            ],
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

    public function scopeFilter_by_cluster($model)
    {
        $clusterId = $this->getClusterId($this->getUserId());

        $model->where('company_id', $clusterId);

        return $model;
    }
}
