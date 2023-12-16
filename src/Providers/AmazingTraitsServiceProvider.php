<?php

namespace AmazingTraits\Providers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class AmazingTraitsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $pathToNewFolder = app_path('Http/Controllers/API');

        // Periksa apakah folder sudah ada sebelum membuatnya
        if (!File::exists($pathToNewFolder)) {
            File::makeDirectory($pathToNewFolder, 0777, true, true);
        }

        $will_be_delete = [
            'app/Http/Controllers/API/AuthController.php',
            'app/Exceptions/Handler.php',

            'app/Models/ApiModel.php',
            'app/Models/ApiModelNoTrait.php',
            'app/Models/configs.php',
            'app/Models/d_example.php',
            'app/Models/e_approval.php',
            'app/Models/e_approval_d.php',
            'app/Models/e_approval_logs.php',
            'app/Models/e_counter.php',
            'app/Models/m_approval.php',
            'app/Models/m_approval_d_configs.php',
            'app/Models/m_approval_d_excludes.php',
            'app/Models/m_approval_d_rules.php',
            'app/Models/m_colors.php',
            'app/Models/m_general.php',
            'app/Models/m_menu.php',
            'app/Models/m_models.php',
            'app/Models/m_roles.php',
            'app/Models/m_roles_d_api_permissions.php',
            'app/Models/m_roles_d_ui_mb_permissions.php',
            'app/Models/m_roles_d_ui_pc_permissions.php',
            'app/Models/m_roles_d_users.php',
            'app/Models/m_users.php',
            'app/Models/m_users_d_device_histories.php',
            'app/Models/m_users_d_login_histories.php',
            'app/Models/User.php',

            'app/Mail/SendOtpCode.php',
            'app/Providers/BroadcastServiceProvider.php',

            'config/auth.php',
            'config/model-trait.php',

            'database/migrations/2014_10_12_000000_create_users_table.php',
            'database/migrations/0000_00_00_000000_create_websockets_statistics_entries_table.php',
            'database/migrations/2014_10_12_000000_create_m_users_table.php',
            'database/migrations/2014_10_12_000001_create_m_users_d_device_histories_table.php',
            'database/migrations/2014_10_12_000002_create_m_users_d_login_histories_table.php',
            'database/migrations/2022_11_18_003957_create_m_generals_table.php',
            'database/migrations/2022_12_04_165821_create_e_counters_table.php',
            'database/migrations/2022_12_07_054519_create_m_models_table.php',
            'database/migrations/2022_12_07_054520_create_m_roles_table.php',
            'database/migrations/2022_12_07_054521_create_m_roles_d_users_table.php',
            'database/migrations/2022_12_07_054522_create_m_roles_d_ui_pc_permissions_table.php',
            'database/migrations/2022_12_07_054523_create_m_roles_d_ui_mb_permissions_table.php',
            'database/migrations/2022_12_07_054524_create_m_roles_d_api_permissions_table.php',
            'database/migrations/2022_12_30_154920_create_m_colors_table.php',
            'database/migrations/2022_12_31_100032_create_configs_table.php',
            'database/migrations/2023_02_04_231115_create_d_examples_table.php',
            'database/migrations/2023_05_14_100956_create_m_approvals_table.php',
            'database/migrations/2023_05_14_101001_create_m_approval_d_rules_table.php',
            'database/migrations/2023_05_14_101002_create_m_approval_d_configs_table.php',
            'database/migrations/2023_05_14_101003_create_m_approval_d_excludes_table.php',
            'database/migrations/2023_05_14_101008_create_e_approvals_table.php',
            'database/migrations/2023_05_14_101012_create_e_approval_ds_table.php',
            'database/migrations/2023_05_14_101016_create_e_approval_logs_table.php',
            'database/migrations/2023_05_14_102112_create_m_menus_table.php',

            'database/seeders/configs_seeder.php',
            'database/seeders/d_example_seeder.php',
            'database/seeders/DatabaseSeeder.php',
            'database/seeders/m_colors_seeder.php',
            'database/seeders/m_general_register_recom_seeder.php',
            'database/seeders/m_general_scheduler_select_dom_seeder.php',
            'database/seeders/m_general_seeder.php',
            'database/seeders/m_general_subscriptions_seeder.php',
            'database/seeders/m_roles_seeder.php',
            'database/seeders/m_users_seeder.php',

            'database/administratives_202305211059.csv',

            'routes/api.php',
            'routes/web.php',
            'resources/views/vendor/amazing-traits',
            '.env.amazing-traits',
            '.editorconfig',
            '.eslintrc.js',
            '.prettierrc',
            'postcss.config.js',
            'server.php',
            'tailwind.config.js',
            'tsconfig.json',
            'vite.config.ts',
        ];

        foreach ($will_be_delete as $migration) {
            $check = base_path($migration);
            if (File::exists($check)) {
                File::delete($check);
            }
        }



        // File::copy(dirname(__DIR__).'/../resources/Exceptions/Handler.php', base_path('app/Exceptions/Handler.php'));
        $will_copied = [
            [
                'from' => dirname(__DIR__).'/../resources/Exceptions/Handler.php',
                'to' => base_path('app/Exceptions/Handler.php'),
                'is_directory' => false,
            ],
            [
                'from' => dirname(__DIR__).'/../resources/Controllers/API',
                'to' => app_path('Http/Controllers/API'),
                'is_directory' => true,
            ],
            [
                'from' => dirname(__DIR__).'/../resources/Resources/vue',
                'to' => resource_path('vue'),
                'is_directory' => true,
            ],
            [
                'from' => dirname(__DIR__).'/../resources/Models',
                'to' => app_path('Models'),
                'is_directory' => true,
            ],
            [
                'from' => dirname(__DIR__).'/../resources/Database/migrations',
                'to' => base_path('database/migrations'),
                'is_directory' => true,
            ],
            [
                'from' => dirname(__DIR__).'/../resources/Database/seeders',
                'to' => base_path('database/seeders'),
                'is_directory' => true,
            ],
            [
                'from' => dirname(__DIR__).'/../resources/Database/administratives_202305211059.csv',
                'to' => base_path('database/administratives_202305211059.csv'),
                'is_directory' => false,
            ],
            [
                'from' => dirname(__DIR__).'/../resources/Mail/SendOtpCode.php',
                'to' => app_path('Mail/SendOtpCode.php'),
                'is_directory' => false,
            ],
            [
                'from' => dirname(__DIR__).'/../resources/Providers/BroadcastServiceProvider.php',
                'to' => app_path('Providers/BroadcastServiceProvider.php'),
                'is_directory' => false,
            ],
            [
                'from' => dirname(__DIR__).'/../resources/Resources/views',
                'to' => resource_path('views/vendor/amazing-traits'),
                'is_directory' => true,
            ],
            [
                'from' => dirname(__DIR__).'/../resources/.editorconfig',
                'to' => base_path('.editorconfig'),
                'is_directory' => false,
            ],
            [
                'from' => dirname(__DIR__).'/../resources/.eslintrc.js',
                'to' => base_path('.eslintrc.js'),
                'is_directory' => false,
            ],
            [
                'from' => dirname(__DIR__).'/../resources/.prettierrc',
                'to' => base_path('.prettierrc'),
                'is_directory' => false,
            ],
            [
                'from' => dirname(__DIR__).'/../resources/postcss.config.js',
                'to' => base_path('postcss.config.js'),
                'is_directory' => false,
            ],
            [
                'from' => dirname(__DIR__).'/../resources/server.php',
                'to' => base_path('server.php'),
                'is_directory' => false,
            ],
            [
                'from' => dirname(__DIR__).'/../resources/tailwind.config.js',
                'to' => base_path('tailwind.config.js'),
                'is_directory' => false,
            ],
            [
                'from' => dirname(__DIR__).'/../resources/tsconfig.json',
                'to' => base_path('tsconfig.json'),
                'is_directory' => false,
            ],
            [
                'from' => dirname(__DIR__).'/../resources/vite.config.ts',
                'to' => base_path('vite.config.ts'),
                'is_directory' => false,
            ],
            [
                'from' => dirname(__DIR__).'/../resources/Routes/api.php',
                'to' => base_path('routes/api.php'),
                'is_directory' => false,
            ],
            [
                'from' => dirname(__DIR__).'/../resources/Routes/web.php',
                'to' => base_path('routes/web.php'),
                'is_directory' => false,
            ],
            [
                'from' => dirname(__DIR__).'/../resources/Controllers/API/MainDocsController.php',
                'to' => app_path('Http/Controllers/API/MainDocsController.php'),
                'is_directory' => false,
            ],
            [
                'from' => dirname(__DIR__).'/../resources/.env',
                'to' => base_path('.env.amazing-traits'),
                'is_directory' => false,
            ],
            [
                'from' => dirname(__DIR__).'/../resources/model-trait.php',
                'to' => base_path('config/model-trait.php'),
                'is_directory' => false,
            ],
            [
                'from' => dirname(__DIR__).'/../resources/auth.php',
                'to' => base_path('config/auth.php'),
                'is_directory' => false,
            ]
        ];

        foreach ($will_copied as $item) {
            if ($item['is_directory'] === true) {
                File::copyDirectory($item['from'], $item['to']);
            } else {
                File::copy($item['from'], $item['to']);
            }
        }

        $projectPackageJsonPath = base_path('package.json');
        $projectPackageJson = json_decode(file_get_contents($projectPackageJsonPath), true);

        $projectPackageJson['scripts']['init'] = 'php artisan migrate:fresh --seed  && php artisan passport:install && php artisan update:m_models';
        $projectPackageJson['dependencies']['@codemirror/lang-json'] = '^6.0.1';
        $projectPackageJson['dependencies']['@codemirror/theme-one-dark'] = '^6.1.2';
        $projectPackageJson['dependencies']['@highlightjs/vue-plugin'] = '^2.1.0';
        $projectPackageJson['dependencies']['@selectize/selectize'] = '^0.15.2';
        $projectPackageJson['dependencies']['@vueuse/core'] = '^10.2.1';
        $projectPackageJson['dependencies']['@yaireo/tagify'] = '^4.17.7';
        $projectPackageJson['dependencies']['chart.js'] = '^4.2.1';
        $projectPackageJson['dependencies']['click-outside-vue3'] = '^4.0.1';
        $projectPackageJson['dependencies']['codemirror'] = '^6.0.1';
        $projectPackageJson['dependencies']['crypto-js'] = '^4.1.1';
        $projectPackageJson['dependencies']['daisyui'] = '^3.1.0';
        $projectPackageJson['dependencies']['flatpickr'] = '^4.6.13';
        $projectPackageJson['dependencies']['highcharts'] = '^11.0.1';
        $projectPackageJson['dependencies']['highcharts-vue'] = '^1.4.1';
        $projectPackageJson['dependencies']['highlight.js'] = '^11.7.0';
        $projectPackageJson['dependencies']['leaflet'] = '^1.9.3';
        $projectPackageJson['dependencies']['moment'] = '^2.29.4';
        $projectPackageJson['dependencies']['notyf'] = '^3.10.0';
        $projectPackageJson['dependencies']['phone-formatter'] = '^0.0.2';
        $projectPackageJson['dependencies']['pinia'] = '^2.0.33';
        $projectPackageJson['dependencies']['read-excel-file'] = '^5.6.1';
        $projectPackageJson['dependencies']['select2'] = '^4.1.0-rc.0';
        $projectPackageJson['dependencies']['slugify'] = '^1.6.5';
        $projectPackageJson['dependencies']['string-strip-html'] = '^13.2.1';
        $projectPackageJson['dependencies']['sweetalert2'] = '^11.7.3';
        $projectPackageJson['dependencies']['viewerjs'] = '^1.11.6';
        $projectPackageJson['dependencies']['vue'] = '^3.2.47';
        $projectPackageJson['dependencies']['vue-codemirror'] = '^6.1.1';
        $projectPackageJson['dependencies']['vue-good-table-next'] = '^0.2.1';
        $projectPackageJson['dependencies']['vue-i18n'] = '^9.2.2';
        $projectPackageJson['dependencies']['vuedraggable'] = '^4.1.0';

        $projectPackageJson['devDependencies']['@intlify/vite-plugin-vue-i18n'] = '^7.0.0';
        $projectPackageJson['devDependencies']['@types/daterangepicker'] = '^3.1.5';
        $projectPackageJson['devDependencies']['@types/highcharts'] = '^7.0.0';
        $projectPackageJson['devDependencies']['@types/jquery'] = '^3.5.16';
        $projectPackageJson['devDependencies']['@types/leaflet'] = '^1.9.3';
        $projectPackageJson['devDependencies']['@types/node'] = '^20.2.5';
        $projectPackageJson['devDependencies']['@types/select2'] = '^4.0.57';
        $projectPackageJson['devDependencies']['@types/selectize'] = '^0.12.35';
        $projectPackageJson['devDependencies']['@types/summernote'] = '^0.8.7';
        $projectPackageJson['devDependencies']['@types/tailwindcss'] = '^3.1.0';
        $projectPackageJson['devDependencies']['@types/yaireo__tagify'] = '^4.16.1';
        $projectPackageJson['devDependencies']['@typescript-eslint/eslint-plugin'] = '^4.25.0';
        $projectPackageJson['devDependencies']['@typescript-eslint/parser'] = '^4.25.0';
        $projectPackageJson['devDependencies']['@vitejs/plugin-vue'] = '^4.1.0';
        $projectPackageJson['devDependencies']['@vue/eslint-config-prettier'] = '^6.0.0';
        $projectPackageJson['devDependencies']['@vue/eslint-config-typescript'] = '^7.0.0';
        $projectPackageJson['devDependencies']['autoprefixer'] = '^10.4.14';
        $projectPackageJson['devDependencies']['axios'] = '^1.3.4';
        $projectPackageJson['devDependencies']['eslint'] = '^7.27.0';
        $projectPackageJson['devDependencies']['eslint-plugin-prettier'] = '^3.4.0';
        $projectPackageJson['devDependencies']['eslint-plugin-vue'] = '^7.10.0';
        $projectPackageJson['devDependencies']['laravel-echo'] = '^1.15.1';
        $projectPackageJson['devDependencies']['laravel-vite-plugin'] = '^0.7.2';
        $projectPackageJson['devDependencies']['postcss'] = '^8.4.21';
        $projectPackageJson['devDependencies']['prettier'] = '2.8.6';
        $projectPackageJson['devDependencies']['pusher-js'] = '^8.1.0';
        $projectPackageJson['devDependencies']['sass'] = '^1.53.0';
        $projectPackageJson['devDependencies']['sass-loader'] = '^13.0.2';
        $projectPackageJson['devDependencies']['tailwindcss'] = '^3.2.7';
        $projectPackageJson['devDependencies']['typescript'] = '^4.9.3';
        $projectPackageJson['devDependencies']['vite'] = '^4.2.0';
        $projectPackageJson['devDependencies']['vue-router'] = '^4.1.6';
        $projectPackageJson['devDependencies']['vue-tsc'] = '^1.2.0';

        file_put_contents($projectPackageJsonPath, json_encode($projectPackageJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        $projectComposerPath = base_path('composer.json');
        $projectComposer = json_decode(file_get_contents($projectComposerPath), true);

        // Menambahkan dependencies yang baru
        $projectComposer['require']['laravel/passport'] = '^11.8';
        $projectComposer['require']['beyondcode/laravel-websockets'] = '^1.14';
        $projectComposer['require']['matanyadaev/laravel-eloquent-spatial'] = '^3.1';
        $projectComposer['require']['pusher/pusher-php-server'] = '^7.2';

        file_put_contents($projectComposerPath, json_encode($projectComposer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
