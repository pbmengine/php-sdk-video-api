<?php

namespace Pbmengine\VideoApiClient;

use Illuminate\Support\ServiceProvider;

class ClientServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('pbm-video-api.php'),
            ], 'config');
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'pbm-video-api');

        $this->app->singleton('pbm-video-api', function ($app) {
            return new PbmVideoApi(
                $app['config']['pbm-video-api']['base_path'],
                $app['config']['pbm-video-api']['api_key'],
                $app['config']['pbm-video-api']['access_key'],
                $app['config']['pbm-video-api']['secret_key']
            );
        });
    }
}
