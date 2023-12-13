on config/app.php, add this
```
    'providers' => ServiceProvider::defaultProviders()->merge([
        /*
         * Package Service Providers...
         */
        AmazingTraits\Providers\AmazingTraitsServiceProvider::class,
```
php artisan vendor:publish="AmazingTraits\Providers\AmazingTraitsServiceProvider"