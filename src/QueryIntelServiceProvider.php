<?php

namespace Kishan\QueryIntel;

use Illuminate\Support\ServiceProvider;
use Kishan\QueryIntel\Collectors\QueryCollector;
use Kishan\QueryIntel\Support\QueryIntelTracker;
use Kishan\QueryIntel\Middleware\QueryIntelMiddleware;

class QueryIntelServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/query-intel.php',
            'query-intel'
        );

        $this->app->singleton(QueryIntelTracker::class, fn() => new QueryIntelTracker());

    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        /**
         * ---------------------------------------------------------
         * Load package migrations automatically
         * ---------------------------------------------------------
         * These migrations will run when user executes `php artisan migrate`
         */
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        /**
         * ---------------------------------------------------------
         * Publish config & migrations (console only)
         * ---------------------------------------------------------
         */
        if ($this->app->runningInConsole()) {

            // Publish config
            $this->publishes([
                __DIR__ . '/../config/query-intel.php' =>
                    config_path('query-intel.php'),
            ], 'query-intel-config');

            // Publish migrations (optional override)
            $this->publishes([
                __DIR__ . '/../database/migrations/' =>
                    database_path('migrations/query-intel'),
            ], 'query-intel-migrations');
        }

        /**
         * ---------------------------------------------------------
         * Register collector & middleware AFTER app boot
         * ---------------------------------------------------------
         */
        $this->app->booted(function () {

            if (!config('query-intel.enabled')) {
                return;
            }

            if ($this->app->runningInConsole()) {
                return;
            }

            // Register DB query collector
            app(QueryCollector::class)->register();

            // Attach middleware to web group
            $this->app['router']->pushMiddlewareToGroup(
                'web',
                QueryIntelMiddleware::class
            );
        });
    }
}
