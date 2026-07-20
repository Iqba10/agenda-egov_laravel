<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

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
        // Force HTTPS in production (Railway, etc.)
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        // Force the configured local URL scheme to avoid asset tags generating HTTPS
        // when the local dev server is accessed over HTTP.
        if (config('app.env') === 'local') {
            $appUrl = config('app.url');

            if ($appUrl) {
                URL::forceRootUrl($appUrl);

                $scheme = parse_url($appUrl, PHP_URL_SCHEME) ?: 'http';
                URL::forceScheme($scheme);
            }

            Vite::createAssetPathsUsing(fn ($path, $secure = null) => asset($path, false));
        }
    }
}
