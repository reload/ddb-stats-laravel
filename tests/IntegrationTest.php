<?php

namespace DDB\Stats;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\PendingCommand;
use Orchestra\Testbench\TestCase;

class IntegrationTest extends TestCase
{

    public function getPackageProviders($app)
    {
        return [StatisticsServiceProvider::class];
    }

    public function setUp(): void
    {
        parent::setUp();

        $command = $this->artisan('migrate:fresh');
        if ($command instanceof PendingCommand) {
            $command->run();
        }
    }

    public function testEvents()
    {
        $now = Carbon::now();
        Carbon::setTestNow($now);

        $collector = $this->app->get(StatisticsCollector::class);
        $collector->event('guid', 'event', 'object', 'item', ['extra1', 'extra2']);

        $response = $this->json('GET', 'statistics');
        $statistics = $response->json();
        $this->assertEquals(
            [
                'date' => $now->toIso8601String(),
                'guid' => 'guid',
                'event' => 'event',
                'collectionId' => 'object',
                'itemId' => 'item'
            ],
            $statistics[0]
        );
    }
}
