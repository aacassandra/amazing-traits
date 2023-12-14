<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class m_roles_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table(env('TABLE_PREFIX').'m_roles')->insert([
            [
                "name" => "SUPERADMIN",
                "active_flag" => true,
                'created_at' => Carbon::now(),
                'created_id' => 1
            ],
            [
                "name" => "INTERNAL - ADMIN",
                "active_flag" => true,
                'created_at' => Carbon::now(),
                'created_id' => 1
            ],
            [
                "name" => "ADMIN",
                "active_flag" => true,
                'created_at' => Carbon::now(),
                'created_id' => 1
            ],
            [
                "name" => "STAFF",
                "active_flag" => true,
                'created_at' => Carbon::now(),
                'created_id' => 1
            ],
            [
                "name" => "RW",
                "active_flag" => true,
                'created_at' => Carbon::now(),
                'created_id' => 1
            ],
            [
                "name" => "RT",
                "active_flag" => true,
                'created_at' => Carbon::now(),
                'created_id' => 1
            ],
            [
                "name" => "SECURITY",
                "active_flag" => true,
                'created_at' => Carbon::now(),
                'created_id' => 1
            ],
            [
                "name" => "GUEST",
                "active_flag" => true,
                'created_at' => Carbon::now(),
                'created_id' => 1
            ]
        ]);
    }
}
