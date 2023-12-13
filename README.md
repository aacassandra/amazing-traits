at config/app.php file, you must add this
```
    'providers' => ServiceProvider::defaultProviders()->merge([
        /*
         * Package Service Providers...
         */
        AmazingTraits\Providers\AmazingTraitsServiceProvider::class,
```

and then publish with the service provider <br />
```php artisan vendor:publish --provider="AmazingTraits\Providers\AmazingTraitsServiceProvider"```