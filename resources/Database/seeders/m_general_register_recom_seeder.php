<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class m_general_register_recom_seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table(env('TABLE_PREFIX') . 'm_general')->insert([
            [
                'code' => null,
                'group' => 'SELECT RECOMMENDATIONS',
                'key' => null,
                'value_1' => 'Teman / Keluarga',
                'value_2' => null,
                'value_3' => null,
                'active_flag' => true,
                'created_id' => 1,
                'updated_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'code' => null,
                'group' => 'SELECT RECOMMENDATIONS',
                'key' => null,
                'value_1' => 'Pencarian Di Internet',
                'value_2' => null,
                'value_3' => null,
                'active_flag' => true,
                'created_id' => 1,
                'updated_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'code' => null,
                'group' => 'SELECT RECOMMENDATIONS',
                'key' => null,
                'value_1' => 'Iklan',
                'value_2' => null,
                'value_3' => null,
                'active_flag' => true,
                'created_id' => 1,
                'updated_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'code' => null,
                'group' => 'SELECT RECOMMENDATIONS',
                'key' => null,
                'value_1' => 'Youtube',
                'value_2' => null,
                'value_3' => null,
                'active_flag' => true,
                'created_id' => 1,
                'updated_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'code' => null,
                'group' => 'SELECT RECOMMENDATIONS',
                'key' => null,
                'value_1' => 'Social Media',
                'value_2' => null,
                'value_3' => null,
                'active_flag' => true,
                'created_id' => 1,
                'updated_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
        ]);
    }
}
