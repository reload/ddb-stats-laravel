<?php

namespace DDB\Stats\ServiceProviders;

use DDB\Stats\Collector;
use DDB\Stats\StatisticsCollector;
use Illuminate\Support\ServiceProvider;

class StatisticsServiceProvider extends ServiceProvider
{
    protected function loadRoutesFrom($path)
    {
        if ($router = $this->app->router ?? $this->app->make('router')) {
            require $path;
        }
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../migrations');
        $this->loadRoutesFrom(__DIR__ . '/../../routes/web.php');
    }

    public function register()
    {
        $this->app->singleton(StatisticsCollector::class, function ($app) {
            return new Collector($app->make('db'));
        });

        $this->app->register(StatisticsEventsServiceProvider::class);
    }
}
