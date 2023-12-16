<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Schema
    |--------------------------------------------------------------------------
    |
    | Konfigurasi ini bertujuan untuk memberi tahu pada sistem kami
    | Jika suatu table tidak di daftarkan ke konfigurasi schema
    | maka sistem akan secara otomatis menggunakan nama schema dibawah ini
    |
    */
    'default' => env('DB_SCHEMA', 'public'),

    /*
    |--------------------------------------------------------------------------
    | Markdown DB Schema of Table Settings
    |--------------------------------------------------------------------------
    |
    | Konfigurasi ini bertujuan untuk memberi tahu pada sistem kami
    | agar sistem mengetahui, di schema mana suatu table berada
    | Dengan adanya ini, kita tidak perlu men define nama schema di tiap file model dan file migration
    | kalian hanya perlu mendaftarkan nama table disini
    |
    */

    'schema' => [
        'public' => []
    ],

    /*
    | List semua table dari trait generator
    | ini digunakan untuk memberi tahu trait docs generator
     */
    'list-table' => [
        "configs",
        "d_example",
        "e_approval",
        "e_approval_d",
        "e_approval_logs",
        "e_counter",
        "m_menu",
        "m_approval",
        "m_approval_d_rules",
        "m_approval_d_configs",
        "m_approval_d_excludes",
        "m_colors",
        "m_general",
        "m_users",
        "m_users_d_device_histories",
        "m_users_d_login_histories",
        "m_roles",
        "m_roles_d_users",
        "m_roles_d_ui_pc_permissions",
        "m_roles_d_ui_mb_permissions",
        "m_roles_d_api_permissions",
        "m_models",
    ]
];
