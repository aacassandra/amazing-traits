<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class configs_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table(env('TABLE_PREFIX').'configs')->insert([
          ['params' => 'is_maintenance', 'value' => 'false', 'created_id' => 1, 'created_at' => Carbon::now()],
          ['params' => 'deadline_of_realization', 'value' => '3', 'created_id' => 1, 'created_at' => Carbon::now()],
          ['params' => 'styles', 'value' => '{
            "logo": {
                "text": {
                    "light":"text-emerald-700",
                    "dark":"text-gray-200"
                }
            },
            "sidebar": {
                "bg": {
                    "light":"bg-slate-50",
                    "dark":"bg-base-100"
                },
                "text": {
                    "light":"text-gray-800",
                    "dark":"text-gray-200"
                },
                "border": {
                    "light":"border-gray-200",
                    "dark":"border-gray-600"
                }
            },
            "header": {
                "bg": {
                    "light":"bg-slate-50",
                    "dark":"bg-base-100"
                },
                "text": {
                    "light":"text-gray-800",
                    "dark":"text-gray-200"
                }
            }
          }', 'created_id' => 1, 'created_at' => Carbon::now()]
        ]);
    }
}
