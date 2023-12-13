<?php

namespace AmazingTraits\Helpers;
use AmazingTraits\Helpers\Cryptor;
use App\Models\m_approval;
use App\Models\m_roles;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class Approval
{
    const TABLE_HEADER = 'cp_e_approval';
    const TABLE_DETAIL = 'cp_e_approval_d';
    const TABLE_LOG = 'cp_e_approval_logs';

    private $timestamp = null;
    private $table_prefix = null;
    function __construct(){
        $this->timestamp = \Carbon\Carbon::now();
        $this->table_prefix = getTablePrefix();
    }

    private function getCurrentUser()
    {
        if (app()->request->user('api') !== null) {
            return app()->request->user('api');
        } else {
            return null;
        }
    }

    private function getCurrentRole(string $roleName) {
        return (new m_roles())->where('name', $roleName)->first();
    }

    protected function getFilter ( array $additional = [] )
    {
        $user = $this->getCurrentUser();

        $data = array_merge([
            // 'user_id' => $user->id,
            'type' => 'MENGAJUKAN',
        ], $additional);

        $fixedFilter = [];
        foreach($data as $key => $val){
            if($val){
                $fixedFilter[] = [ $key, '=', $val];
            }
        }
        return $fixedFilter;
    }

    public function getRawConfig()
    {
        $table_prefix = getTablePrefix();
        return "SELECT
                app_d_rules.*,
                app_d_configs.is_full_approve,
                app_d_configs.is_skippable,
                app_d_configs.is_able_to_skip,
                app_d_configs.send_wa_notif,
                app_d_configs.send_email_notif,
                app_d_configs.min_value,
                app_d_configs.max_value,
                app_d_configs.id as config_id,
                app_d_excludes.id as exclude_id
            FROM {$table_prefix}m_approval_d_rules app_d_rules
            JOIN {$table_prefix}m_approval app ON app.id = app_d_rules.{$table_prefix}m_approval_id
            JOIN {$table_prefix}m_approval_d_configs app_d_configs
                ON (app_d_rules.{$table_prefix}m_approval_id = app_d_configs.{$table_prefix}m_approval_id AND app_d_rules.level = app_d_configs.level)
            LEFT JOIN {$table_prefix}m_approval_d_excludes app_d_excludes
                ON (app_d_rules.{$table_prefix}m_approval_id = app_d_excludes.{$table_prefix}m_approval_id AND app_d_rules.level = app_d_excludes.level)
            ";
    }

    protected function getTemplate( bool $isDetail = false, array $data = [] ) :array
    {
        return !$isDetail ? [
            'trx_id'=> null,
            'trx_name'=> null,
            'trx_no'=> null,
            'trx_table'=> null,
            'trx_date'=> null,

            "{$this->table_prefix}m_approval_id" => null,
            'status'=> 'PROGRESS',
            'trx_creator_id'=> app()->request->user('api')->id,
            'created_at'=> $this->timestamp
        ] : [
            //  copas only
            'level' => $data['level'],
            'level_order' => $data['level_order'],
            'type' => $data['type'],
            'action_role_id' => $data['action_role_id'],
            'action_user_id' => $data['action_user_id'],

            //  config
            'is_full_approve' => $data['is_full_approve'],
            'is_skippable' => $data['is_skippable'],
            'is_able_to_skip' => $data['is_able_to_skip'],
            'send_wa_notif' => $data['send_wa_notif'],
            'send_email_notif' => $data['send_email_notif'],
            'min_value' => $data['min_value'],
            'max_value' => $data['max_value'],
            'is_completed' => $data['is_completed'],
            'is_assigned' => $data['is_assigned'],

            //  generate
            // 'assigned_at' => $data['assigned_at'],

            //  action
            'action' => null,
            'action_at' => null,
            'action_note' => null,

            getTablePrefix().'m_approval_d_rules_id' => $data[getTablePrefix().'m_approval_d_rules_id'],
            getTablePrefix().'m_approval_d_configs_id' => $data[getTablePrefix().'m_approval_d_configs_id'],
            getTablePrefix().'m_approval_d_excludes_id' => $data[getTablePrefix().'m_approval_d_excludes_id'],
            getTablePrefix().'e_approval_id' => $data[getTablePrefix().'e_approval_id'],
            'created_at'=> $this->timestamp
        ];
    }

    public function start( array $config, bool $errorOnFailed = false )
    {
        $user = $this->getCurrentUser();
        $role = $this->getCurrentRole($user->role);
        $transactionName = $config['trx_name'];
        $table_prefix = getTablePrefix();
        $whereArr = $this->getFilter([
            'name' => $transactionName,
            'level_order' => 0,
            'role_id' => (new Cryptor())->decrypt($role->id),
        ]);

        $rules_mdl = $foundApproval = getModel("{$table_prefix}m_approval_d_rules");
        $config['trx_id'] = $rules_mdl->autoDecrypt($config['trx_id']);

        //  penemuan detail yang MENGAJUKAN
        $foundApproval = getModel("{$table_prefix}m_approval_d_rules")
            ->join("{$table_prefix}m_approval as app","app.id","=","{$table_prefix}m_approval_id")
            ->where( $whereArr )
            ->where('app.active_flag', 1)
            ->first();

        // jika master approval terdapat user id, maka m_approval_d.user_id harus sama dengan current_user_id
        if ($foundApproval && $foundApproval->user_id && $foundApproval->user_id !== $user->id) {
            $foundApproval = null;
        }

        if (!$foundApproval) {
            trigger_error("Maaf data approval tidak ditemukan untuk attribut(user) anda");
        }

        $approval_id = $foundApproval->{"{$table_prefix}m_approval_id"};
        //  penemuan detail, config, sampe header
        $rawConfig = $this->getRawConfig();
        $fixedConfig = "$rawConfig WHERE app.id=$approval_id AND type <> 'MENGAJUKAN' ORDER BY app_d_rules.level,level_order ASC";
        $details = DB::select( $fixedConfig );

        $tempArr = $this->getTemplate();
        $tempArr["{$this->table_prefix}m_approval_id"] =  $foundApproval->{"{$this->table_prefix}m_approval_id"};
        $tempArr['created_id'] = $user->id;
        $headerData = array_merge($tempArr, $config);

        $detailData = [];
        DB::beginTransaction();
        try {
            $headerId = DB::table(self::TABLE_HEADER)->insertGetId( $headerData );

            // insert approval log
            DB::table("{$table_prefix}e_approval_logs")->insert([
                "{$table_prefix}e_approval_id" => $headerId,
                "trx_id" => $config['trx_id'],
                "trx_name" => $config['trx_name'],
                "trx_no" => $config['trx_no'],
                "trx_table" => $config['trx_table'],
                "trx_date" => $config['trx_date'],
                "trx_creator_id" => $config['trx_creator_id'],
                "action" => 'PROGRESS',
                "action_at" => now(),
                "action_note" => null,
                "action_user_id" => $config['trx_creator_id'],
                "created_id" => $config['trx_creator_id'],
                "created_at" => now(),
            ]);

            foreach ($details AS $idx => $dtl) {
                $data = array_merge((array)$dtl, [
                    getTablePrefix().'e_approval_id' => $headerId,
                ]);
                $data['is_assigned'] = $idx === 0 ? 1 : 0;
                $data['is_completed'] = 0;
                $data['action_role_id'] = $dtl->role_id;
                $data['action_user_id'] = $dtl->user_id;
                $data[getTablePrefix().'m_approval_d_rules_id'] = $dtl->id;
                $data[getTablePrefix().'m_approval_d_configs_id'] = $dtl->config_id;
                $data[getTablePrefix().'m_approval_d_excludes_id'] = $dtl->exclude_id;
                $tempArr = $this->getTemplate(true, $data);
                $tempArr['created_id'] = $user->id;
                if ($tempArr['type'] === 'MENGETAHUI') {
                    // tambahkan notification disini, seperti wa atau email
                    if ($tempArr['send_wa_notif']) {
                        //
                    }

                    if ($tempArr['send_email_notif']) {
                        //
                    }
                }
                $detailData[] = $tempArr;
            }
            DB::table(self::TABLE_DETAIL)->insert( $detailData );

            DB::commit();
        } catch(\Exception $e){
            DB::rollback();
            if( $errorOnFailed ){
                trigger_error( $e->getMessage() );
            }
            return false;
        }
    }

    public function saveLog(array $data)
    {
        //
    }
}
