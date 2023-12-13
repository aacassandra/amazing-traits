<?php

namespace App\Models;

use AmazingTraits\Controllers\API\ApiController;
use App\Http\Controllers\API\AuthController;
use AmazingTraits\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class e_approval extends Model
{
    use HasFactory, ModelTrait, SoftDeletes;

    protected $guarded = ['id','created_at','updated_at'];
    protected $table;
    public $hide = true;

    /**
     * Untuk auto mengurusi detail saat Create/Update/Delete data, mirip hasMany tapi versi simple
     */
    public $details = [];

    /**
     * Untuk auto join saat get data
     */
    protected $joins = [];

    public function __construct($attr = array(), $exists = false) {
        parent::__construct($attr, $exists);
        $this->table = env('TABLE_PREFIX') . 'e_approval';
        $this->details[] = env('TABLE_PREFIX') . "e_approval_d";
        $this->details[] = env('TABLE_PREFIX') . "e_approval_logs";

        $this->joins['created_id'] = [m_users::class, 'username,name'];
        $this->joins['updated_id'] = [m_users::class, 'username,name'];
        $this->joins['deleted_id'] = [m_users::class, 'username,name'];
        $this->joins[getTablePrefix().'m_approval_id'] = [m_approval::class, 'menu_id'];
        $this->joins[getTablePrefix().'m_approval.menu_id'] = [m_menu::class, 'path_url,callback_approval_url'];

        $isLandingRequest = false;
        $req = app()->request;
        if (isset($req->columnFilters) && isset($req->page) && isset($req->perPage) && isset($req->sort)) {
            $isLandingRequest = true;
        }
        if (!app()->request->isMethod('GET') && !$isLandingRequest) return;

        $user = $this->getCurrentUser();
        if ($user) {
            $clusterId = $this->getClusterId($user->id);
            $current_role = $this->getCurrentRole($user->role);
            $current_role_id = $this->autoDecrypt($current_role->id);

            /**
             * 1. harus berada dalam cluster yg sama [done]
             * 2. harus sesuai urutan e_approval_d dmn is_complete = false [done]
             * 3. harus sesuai role yg sama [done]
             * 4. harus sesuai user_id yg sama jika value <> null [done]
             */
            if ($user->role !== 'SUPERADMIN' && $user->role !== 'INTERNAL - ADMIN') {
                $this->addGlobalScope('global', function ($q) use ($clusterId, $current_role_id, $user) {
                    $q
                        ->join('cp_m_approval as m_app', 'm_app.id', '=', 'cp_e_approval.cp_m_approval_id')
                        ->join('cp_e_approval_d as e_app_d', 'e_app_d.cp_e_approval_id', '=', 'cp_e_approval.id')
                        // yang muncul hanya sesuai cluster nya
                        ->where('m_app.company_id', $clusterId)
                        // yang muncul di table approval hanya yg sesuai role nya
                        ->where('e_app_d.action_role_id', $current_role_id)
                        ->where('e_app_d.is_assigned', 1)
                        // query ketika column user = null dan ketika ada isinya wajib mirip dgn user id nya
                        ->where([['e_app_d.action_user_id', NULL]])
                        ->orWhere([['e_app_d.action_user_id', $user->id]])
                    ;
                });
            }
        }
    }

    /**
     * Fungsi untuk validation saat create
     */
    public function creatingRules(): array
    {
        return [
            "trx_id" => "required|integer",
            "trx_name" => "required|string",
            "trx_no" => "required|string",
            "trx_table" => "required|string",
            "trx_date" => "required|date",
            "trx_created_id" => "required|integer",
            "modul" => "required|string",
            "form" => "required|string",
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
        $req = app()->request;

        if ($req->parent_id) {
            $trx = $this->getModel($model->trx_table);
            $data = $trx->scopes($this->scopes)->find($this->autoDecrypt($model->trx_id));

            if ( $data ) {
                $data = $data->toArray();
            } else {
                return response()->json(['message'=>'Data tidak ditemukan','errors'=>['No Data']],404);
            }

            if ( @$trx->details ) {
                $data = $trx->detailinDetails($trx->details, $model->trx_table, $this->autoDecrypt($model->trx_id), $data);
            }

            $model->trx_detail = $data;
        }
    }

    public function onDeleting($model)
    {
        $model->deleted_id = $model->getUserId();
    }

    public function customChange_status() {
        $request = app()->request;
        $table_prefix = getTablePrefix();

        $validator = Validator::make($request->all(), [
            'trx_id' => 'required|string',
            'trx_name' => 'required|string',
            'trx_table' => 'required|string',
            'action' => 'required|string',
            'action_note' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors()->all(), 400);
        }

        $trx_id = $this->autoDecrypt($request->trx_id);
        $action = $request->action;

        DB::beginTransaction();
        try {
            // get header
            $header = DB::table($this->getTable())
                ->where('trx_id', $trx_id)
                ->where('trx_name', $request->trx_name)
                ->where('trx_table', $request->trx_table)
                ->where('status', '!=', 'APPROVED')
                ->where('status', '!=', $action)
                ->first();

            if (!$header) {
                return sendError('Approval is not found',[],404);
            }

            // insert approval log
            DB::table("{$table_prefix}e_approval_logs")->insert([
                "{$table_prefix}e_approval_id" => $header->id,
                "trx_id" => $trx_id,
                "trx_name" => $header->trx_name,
                "trx_no" => $header->trx_no,
                "trx_table" => $header->trx_table,
                "trx_date" => $header->trx_date,
                "trx_creator_id" => $header->trx_creator_id,
                "action" => $action,
                "action_at" => now(),
                "action_note" => $request->action_note ?? null,
                "action_user_id" => $this->getUserId(),
                "created_id" => $this->getUserId(),
                "created_at" => now(),
            ]);

            // update header
            DB::table($this->getTable())
                ->where('id', $header->id)
                ->update([
                    'status' => $action,
                    'updated_id' => $this->getUserId(),
                    'updated_at' => now(),
                ]);

            // update main table
            DB::table($request->trx_table)
                ->where('id', $trx_id)
                ->update([
                    'status' => $action,
                    'updated_id' => $this->getUserId(),
                    'updated_at' => now(),
                ]);

            if ($action === 'APPROVED') {
                $user = $this->getCurrentUser();
                $role = $this->getCurrentRole($user->role);
                $role_id = $this->autoDecrypt($role->id);
                $cek = DB::table("{$table_prefix}e_approval_d")
                    ->where([
                        ['is_completed', '=', 0],
                        ['is_assigned', '=', 1],
                        ['type', '=', 'MENYETUJUI'],
                        ['action_role_id', '=', $role_id]
                    ])
                    ->where([['action_user_id', NULL]])
                    ->orWhere([['action_user_id', $user->id]])
                    ->update([
                        'is_completed' => 1,
                        'updated_id' => $user->id,
                        'updated_at' => now(),
                    ])
                ;
            }

            DB::commit();

            $message = "";
            if ($action === 'REVISED') {
                $message = "alert.revised_data.success_response";
            } elseif ($action === 'REJECTED') {
                $message = "alert.rejected_data.success_response";
            } elseif ($action === 'APPROVED') {
                $message = "alert.approved_data.success_response";
            }

            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        } catch(\Exception $e){
            DB::rollback();
            trigger_error($e->getMessage());
        }
    }

    public function customIn_approval()
    {
        $request = app()->request;
        $table_prefix = getTablePrefix();

        $validator = Validator::make($request->all(), [
            'trx_id' => 'required|string',
            'trx_name' => 'required|string',
            'trx_table' => 'required|string',
            'action' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors()->all(), 400);
        }

        $trx_id = $this->autoDecrypt($request->trx_id);
        $action = $request->action;

        /**
         * 0. pengecekan status, status != APPROVED & REJECTED
         * 1. update main_table status
         * 2. update e_table status
         * 3. insert e_table_logs
         */

        // get header
        $header = DB::table($this->getTable())
            ->where('trx_id', $trx_id)
            ->where('trx_name', $request->trx_name)
            ->where('trx_table', $request->trx_table)
            ->where('status', '!=', 'APPROVED')
            ->where('status', '!=', 'REJECTED')
            ->first();

        if (!$header) {
            return sendError('Approval is not found',[],404);
        }

        DB::beginTransaction();
        try {
            // insert approval log
            DB::table("{$table_prefix}e_approval_logs")->insert([
                "{$table_prefix}e_approval_id" => $header->id,
                "trx_id" => $trx_id,
                "trx_name" => $header->trx_name,
                "trx_no" => $header->trx_no,
                "trx_table" => $header->trx_table,
                "trx_date" => $header->trx_date,
                "trx_creator_id" => $header->trx_creator_id,
                "action" => 'PROGRESS',
                "action_at" => now(),
                "action_note" => null,
                "action_user_id" => $this->getUserId(),
                "created_id" => $this->getUserId(),
                "created_at" => now(),
            ]);

            // update header
            DB::table($this->getTable())
                ->where('id', $header->id)
                ->update([
                    'status' => $action,
                    'updated_id' => $this->getUserId(),
                    'updated_at' => now(),
                ]);

            // update main table
            DB::table($request->trx_table)
                ->where('id', $trx_id)
                ->update([
                    'status' => $action,
                    'updated_id' => $this->getUserId(),
                    'updated_at' => now(),
                ]);

            DB::commit();
            return response()->json([
               'success' => true,
               'message' => 'Success'
            ]);
        } catch(\Exception $e){
            DB::rollback();
            return $this->sendError($e->getMessage(), [], 500);
        }
    }
}
