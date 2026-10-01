<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL; // 1. Import Facade URL

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 2. Paksa semua URL asset & route menjadi HTTPS saat diakses via Ngrok/Proxy
        if (request()->server('HTTP_X_FORWARDED_PROTO') === 'https' || request()->isSecure()) {
            URL::forceScheme('https');
        }
    }
}