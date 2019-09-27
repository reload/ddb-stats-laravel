<?php

namespace DDB\Stats;

use Illuminate\Support\ServiceProvider;

class StatisticsServiceProvider extends ServiceProvider
{

    protected function loadRoutesFrom($path)
    {
        if (! $this->app->routesAreCached()) {
            $router = $this->app->make('router');
            require $path;
        }
    }

    public function register(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../migrations');
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        $this->app->singleton(StatisticsCollector::class, function ($app) {
            return new Collector($app->make('db'));
        });
    }
}
