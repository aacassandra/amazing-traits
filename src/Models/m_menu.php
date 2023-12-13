<?php

namespace App\Models;

use App\Traits\ModelTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;

class m_menu extends Model
{
    use HasFactory, ModelTrait, SoftDeletes;

    protected $guarded = ['id','created_at','updated_at'];
    protected $table;
    public $hide = true;

    public function __construct($attr = array(), $exists = false) {
        parent::__construct($attr, $exists);
        $this->table = env('TABLE_PREFIX') . 'm_menu';
    }
    /**
     * @var string[] // ['default', 'get', 'find', 'store', 'update', 'destroy', 'custom*', 'getfunc*']
     */
    public $auths = ['default'];

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
        return [
            'name' => 'required|string',
            'module' => 'nullable|string',
            'submodule' => 'nullable|string',
            'sequence' => 'nullable|string',
            'path_url' => 'required|string',
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

    public function customSync_menu()
    {
        $menus = app()->request->menus;
        $worktask = app()->request->worktask;



        DB::beginTransaction();
        try {
            foreach ($menus as $key => $value) {
                $menu = DB::table(env('TABLE_PREFIX').'m_menu')
                    ->where([
                        ['uid', '=', $value['uid']],
                        ['deleted_at', '=', null]
                    ]);
                if ($menu->exists()) {
                    // if available
                    $menu_f = $menu->first();
                    if (
                        $menu_f->uid === $value['uid'] &&
                        (
                            $menu_f->name !== $value['name'] ||
                            $menu_f->module !== $value['module'] ||
                            $menu_f->submodule !== $value['submodule'] ||
                            $menu_f->path_url !== $value['path_url']
                        )
                    ) {
                        // if need update
                        $menu->update([
                            'name' => $value['name'],
                            'module' => $value['module'],
                            'submodule' => $value['submodule'],
                            'path_url' => $value['path_url'],
                            'callback_approval_url' => $value['path_url'] === '/subscriptions/transactions/packages' ? '' : '/form',
                            'active_flag' => $value['active_flag'],
                            'updated_id' => 1,
                            'updated_at' => Carbon::now(),
                        ]);
                    }
                } else {
                    // if new
                    DB::table(env('TABLE_PREFIX').'m_menu')
                        ->insert([
                            'uid' => $value['uid'],
                            'name' => $value['name'],
                            'module' => $value['module'],
                            'submodule' => $value['submodule'],
                            'path_url' => $value['path_url'],
                            'callback_approval_url' => $value['path_url'] === '/subscriptions/transactions/packages' ? '' : '/form',
                            'active_flag' => $value['active_flag'],
                            'created_id' => 1,
                            'created_at' => Carbon::now(),
                        ]);
                }
            }

            $menus_db = null;
            $table_prefix = env('TABLE_PREFIX');
            if ($worktask) {
                $menus_db = DB::table("{$table_prefix}m_menu")
                    ->whereRaw("{$table_prefix}m_menu.uid LIKE '%worktask-menu%'")
                    ->get();
            } else {
                $menus_db = DB::table("{$table_prefix}m_menu")->get();
            }

            foreach ($menus_db as $menu_f) {
                $match = false;
                foreach ($menus as $key => $value) {
                    if (
                        $menu_f->uid === $value['uid']
                    ) {
                        $match = true;
                    } else if (!$worktask && strpos($menu_f->uid, 'worktask-menu') !== false) {
                        $match = true;
                    }
                }

                if (!$match) {
                    DB::table(env('TABLE_PREFIX').'m_menu')
                        ->where('id', $menu_f->id)
                        ->delete();
                }
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Has been updated succesfull'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->sendError('Register failed.', [], 500);
        }

    }
}
