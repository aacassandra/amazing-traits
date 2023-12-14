<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        $this->call([
            m_general_seeder::class,
            m_users_seeder::class,
            m_roles_seeder::class,
            m_colors_seeder::class,
            configs_seeder::class,
            d_example_seeder::class,
            m_general_register_recom_seeder::class,
            m_general_subscriptions_seeder::class,
            m_general_scheduler_select_dom_seeder::class,
        ]);
    }
}
