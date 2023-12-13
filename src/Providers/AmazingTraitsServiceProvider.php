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
        $pathToAuthController = app_path('Http/Controllers/API/AuthController.php');
        if (File::exists($pathToAuthController)) {
            File::delete($pathToAuthController);
        }

        $pathToExceptionHandler = app_path('Exceptions/Handler.php');
        if (File::exists($pathToExceptionHandler)) {
            File::delete($pathToExceptionHandler);
        }

        File::cleanDirectory(app_path('Models'));

        $pathToOtpCode = app_path('Mail/SendOtpCode.php');
        if (File::exists($pathToOtpCode)) {
            File::delete($pathToOtpCode);
        }

        $pathToBroadcastServiceProvider = app_path('Providers/BroadcastServiceProvider.php');
        if (File::exists($pathToBroadcastServiceProvider)) {
            File::delete($pathToBroadcastServiceProvider);
        }

        $pathToApiRoute = base_path('routes/api.php');
        if (File::exists($pathToApiRoute)) {
            File::delete($pathToApiRoute);
        }

        $pathToApiWeb = base_path('routes/web.php');
        if (File::exists($pathToApiWeb)) {
            File::delete($pathToApiWeb);
        }

        $pathToResViews = resource_path('views/vendor/amazing-traits');
        if (File::exists($pathToResViews)) {
            File::delete($pathToResViews);
        }

        $pathToEnvFile = base_path('.env.amazing-traits');
        if (File::exists($pathToEnvFile)) {
            File::delete($pathToEnvFile);
        }

        $pathToEditorConfig = base_path('.editorconfig');
        if (File::exists($pathToEditorConfig)) {
            File::delete($pathToEditorConfig);
        }

        $pathToEslintRC = base_path('.eslintrc.js');
        if (File::exists($pathToEslintRC)) {
            File::delete($pathToEslintRC);
        }

        $pathToPrettierRC = base_path('.prettierrc');
        if (File::exists($pathToPrettierRC)) {
            File::delete($pathToPrettierRC);
        }

        $pathToPostcss = base_path('postcss.config.js');
        if (File::exists($pathToPostcss)) {
            File::delete($pathToPostcss);
        }

        $pathToServerPhp = base_path('server.php');
        if (File::exists($pathToServerPhp)) {
            File::delete($pathToServerPhp);
        }

        $pathToTailwindConfigJS = base_path('tailwind.config.js');
        if (File::exists($pathToTailwindConfigJS)) {
            File::delete($pathToTailwindConfigJS);
        }

        $pathToTypescriptJson = base_path('tsconfig.json');
        if (File::exists($pathToTypescriptJson)) {
            File::delete($pathToTypescriptJson);
        }

        $pathToViteConfigJs = base_path('vite.config.ts');
        if (File::exists($pathToViteConfigJs)) {
            File::delete($pathToViteConfigJs);
        }

        $this->publishes([
            dirname(__DIR__).'/../resources/Controllers/API' => app_path('Http/Controllers/API'),
            dirname(__DIR__).'/../resources/Resources/vue' => resource_path('vue'),
            dirname(__DIR__).'/../resources/Exceptions/Handler.php' => app_path('Exceptions/Handler.php'),
            dirname(__DIR__).'/../resources/Models' => app_path('Models'),
            dirname(__DIR__).'/../resources/Mail/SendOtpCode.php' => app_path('Mail/SendOtpCode.php'),
            dirname(__DIR__).'/../resources/Providers/BroadcastServiceProvider.php' => app_path('Providers/BroadcastServiceProvider.php'),
            dirname(__DIR__).'/../resources/Resources/views' => resource_path('views/vendor/amazing-traits'),

            dirname(__DIR__).'/../resources/.editorconfig' => base_path('.editorconfig'),
            dirname(__DIR__).'/../resources/.eslintrc.js' => base_path('.eslintrc.js'),
            dirname(__DIR__).'/../resources/.prettierrc' => base_path('.prettierrc'),
            dirname(__DIR__).'/../resources/postcss.config.js' => base_path('postcss.config.js'),
            dirname(__DIR__).'/../resources/server.php' => base_path('server.php'),
            dirname(__DIR__).'/../resources/tailwind.config.js' => base_path('tailwind.config.js'),
            dirname(__DIR__).'/../resources/tsconfig.json' => base_path('tsconfig.json'),
            dirname(__DIR__).'/../resources/vite.config.ts' => base_path('vite.config.ts'),
        ]);

        if (!File::exists(base_path('routes/api.php'))) {
            File::copy(dirname(__DIR__).'/../resources/Routes/api.php', base_path('routes/api.php'));
        }

        if (!File::exists(base_path('routes/web.php'))) {
            File::copy(dirname(__DIR__).'/../resources/Routes/web.php', base_path('routes/web.php'));
        }

        if (!File::exists(app_path('Http/Controllers/API/MainDocsController.php'))) {
            File::copy(dirname(__DIR__).'/../resources/Controllers/API/MainDocsController.php', app_path('Http/Controllers/API/MainDocsController.php'));
        }

        if (!File::exists(base_path('.env.amazing-traits'))) {
            File::copy(dirname(__DIR__).'/../resources/.env', base_path('.env.amazing-traits'));
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
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
