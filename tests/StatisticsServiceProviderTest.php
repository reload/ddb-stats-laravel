<?php

use DDB\Stats\Collector;
use DDB\Stats\StatisticsCollector;
use DDB\Stats\StatisticsServiceProvider;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\ConnectionInterface;
use Laravel\Lumen\Routing\Router;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

// phpcs:ignore PSR1.Classes.ClassDeclaration.MissingNamespace
class StatisticsServiceProviderTest extends TestCase
{
    public function testRegistration()
    {
        $app = $this->prophesize(Application::class);

        $router = $this->prophesize(Router::class);
        $app->make('router')->willReturn($router);
        // The /statistics route should be registered.
        $router->get('/statistics', '\\DDB\\Stats\\StatisticsController@get')->shouldBeCalled();
        // StatsServiceProvider will only register routes if they're not cached.
        $app->routesAreCached()->willReturn(false)->shouldBeCalled();

        // This is the effect of $this->loadMigrationsFrom. We assume that
        // this will do the right thing with the migrations. Else we'd
        // basically be testing the internals of ServiceProvider and
        // Application, and that's not what we want.
        $app->afterResolving('migrator', Argument::any())->shouldBeCalled();

        $db = $this->prophesize(ConnectionInterface::class);
        $app->make('db')->willReturn($db);

        // Check that the register method registers StatisticsCollector in the app container.
        $callable = null;
        $app->singleton(StatisticsCollector::class, Argument::any())->will(function ($args) use (&$callable) {
            $callable = $args[1];
        })->shouldBeCalled();

        $provider = new StatisticsServiceProvider($app->reveal());
        $provider->register();

        $this->assertTrue(is_callable($callable));

        // Try using the factory closure and check that it returns a Collector.
        $collector = $callable($app->reveal());
        $this->assertInstanceOf(Collector::class, $collector);
    }
}
