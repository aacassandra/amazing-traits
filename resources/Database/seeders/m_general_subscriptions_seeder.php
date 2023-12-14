<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class m_general_subscriptions_seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table(env('TABLE_PREFIX') . 'm_general')->insert([
            [
                'code' => null,
                'group' => 'SUBSCRIPTION',
                'key' => 'ADMIN FEE',
                'value_1' => '1000',
                'value_2' => null,
                'value_3' => null,
                'value_4' => null,
                'description' => null,
                'active_flag' => true,
                'created_id' => 1,
                'updated_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'code' => null,
                'group' => 'SUBSCRIPTION',
                'key' => 'MAX ORDER TIME',
                'value_1' => '24',
                'value_2' => 'hours',
                'value_3' => null,
                'value_4' => null,
                'description' => 'value_2=minutes,hours,days',
                'active_flag' => true,
                'created_id' => 1,
                'updated_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'code' => null,
                'group' => 'SUBSCRIPTION',
                'key' => 'MAX APPROVAL TIME',
                'value_1' => '7',
                'value_2' => 'days',
                'value_3' => null,
                'value_4' => null,
                'description' => 'value_2=minutes,hours,days,weeks',
                'active_flag' => true,
                'created_id' => 1,
                'updated_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'code' => null,
                'group' => 'SUBSCRIPTION',
                'key' => 'MAX TRIAL',
                'value_1' => '1',
                'value_2' => 'months',
                'value_3' => null,
                'value_4' => null,
                'description' => null,
                'active_flag' => true,
                'created_id' => 1,
                'updated_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
        ]);
    }
}
