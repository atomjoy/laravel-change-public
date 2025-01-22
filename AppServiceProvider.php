<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;
use URL;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Create symlinks from public to public_html directory in config/filesystems.php
        config(['filesystems.links' => [
            public_path('storage') => storage_path('app/public'),
            base_path('public_html') => base_path('public')
        ]]);

        // Rewrite public dir to public_html shared hosting
        $this->app->usePublicPath(app()->basePath('public_html'));

        // Or rewrite public dir to public_html shared hosting
        // $this->app->bind('path.public', function () {
        //     return base_path() . '/public_html';
        // });
    }

    public function boot(): void
    {
        // Laravel force https
        // if (App::environment(['staging', 'production'])) {
        //     URL::forceScheme('https');
        // }
    }
}
