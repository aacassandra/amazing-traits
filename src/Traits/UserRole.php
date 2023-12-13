<?php

namespace AmazingTraits\Traits;

use AmazingTraits\Helpers\Cryptor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait UserRole
{
    /**
     * Get User Roles
     */
    public function getRoles($id = null, $roleName = '')
    {
        $userId = $id ?? $this->getUserId();
        $table_prefix = env('TABLE_PREFIX');
        $role = DB::table($table_prefix."m_roles as roles")
            ->selectRaw('roles.*')
            ->join($table_prefix.'m_roles_d_users as roles_d_users', "roles_d_users.{$table_prefix}m_roles_id", '=', 'roles.id')
            ->where('roles.deleted_at','=',null)
            ->where('roles_d_users.user_id', $userId)
            ->where('roles_d_users.deleted_at', '=', null)
        ;

        if ($roleName) {
            return $role->where('roles.name', $roleName)->get();
        } else {
            return $role->get();
        }
    }

    public function getUser(){
        $userId = $this->getUserId();
        $table_prefix = env('TABLE_PREFIX');
        return DB::table($table_prefix.'m_users')
            ->where('id',  $userId)
            ->where('active_flag', true)
            ->where('deleted_at', '=', null)
            ->first();
    }

}
