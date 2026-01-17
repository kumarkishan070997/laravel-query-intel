<?php

namespace Kishan\QueryIntel;

use Illuminate\Support\ServiceProvider;
use Kishan\QueryIntel\Collectors\QueryCollector;
use Kishan\QueryIntel\Middleware\QueryIntelMiddleware;
use Kishan\QueryIntel\Support\QueryIntelTracker;

class QueryIntelServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/query-intel.php',
            'query-intel'
        );

        $this->app->singleton(QueryIntelTracker::class);
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/query-intel.php' =>
            config_path('query-intel.php'),
        ], 'query-intel-config');

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        if (config('query-intel.enabled') && !$this->app->runningInConsole()) {
            app(QueryCollector::class)->register();

            $this->app['router']->pushMiddlewareToGroup(
                'web',
                QueryIntelMiddleware::class
            );
        }

        if (config('query-intel.dashboard.enabled')) {
            $this->loadRoutesFrom(__DIR__ . '/../routes/dashboard.php');
            $this->loadViewsFrom(
                dirname(__DIR__) . '/resources/views',
                'query-intel'
            );
        }
    }
}
