<?php

namespace Kishan\QueryIntel;

use Illuminate\Support\ServiceProvider;

class QueryIntelServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/query-intel.php',
            'query-intel'
        );
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/query-intel.php' =>
            config_path('query-intel.php'),
        ], 'query-intel-config');

        if ($this->app->runningInConsole()) {
            $this->commands([
                Commands\QueryIntelCommand::class,
            ]);
        }
        if (config('query-intel.enabled')) {
            app(\Kishan\QueryIntel\Collectors\QueryCollector::class)->register();
        }
    }
}
