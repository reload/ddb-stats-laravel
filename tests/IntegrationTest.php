<?php

namespace DDB\Stats;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Orchestra\Testbench\TestCase;
use DDB\Stats\StatisticsCollector;
use Illuminate\Testing\PendingCommand;
use DDB\Stats\ServiceProviders\StatisticsServiceProvider;

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

    public function testEvents(): void
    {
        $now = Carbon::now();
        Carbon::setTestNow($now);

        /** @var \DDB\Stats\StatisticsCollector $collector */
        $collector = $this->app->get(StatisticsCollector::class);
        $collector->event('guid', 'event', 'object', 'item', 42, ['item1', 'item2']);

        $response = $this->json('PATCH', 'statistics');
        /* @var array<string, mixed> $statistics */
        $statistics = (array) $response->json();

        $this->assertEquals(
            [
                'date' => $now->toIso8601String(),
                'guid' => 'guid',
                'event' => 'event',
                'collectionId' => 'object',
                'itemId' => 'item',
                'totalCount' => 42,
                'content' => ['item1', 'item2'],
            ],
            $statistics[0]
        );
    }

    public function testSince(): void
    {
        $now = CarbonImmutable::now();
        $yesterday = $now->subDay();

        // Event is created yesterday
        Carbon::setTestNow($yesterday);
        /** @var \DDB\Stats\StatisticsCollector $collector */
        $collector = $this->app->get(StatisticsCollector::class);
        $collector->event('guid', 'event', 'object', 'item', 42, ['item1', 'item2']);

        // Ensure that the event is available.
        $response = $this->json('PATCH', 'statistics');
        $this->assertEquals(1, count((array) $response->json()));

        // Retrieve events occurred in the last 6 hours. Event should not be
        // available since it occurred yesterday.
        // This should also delete the event.
        $time = $now->subHours(6);
        $response = $this->json('PATCH', 'statistics?since=' . urlencode($time->toIso8601String()));
        $this->assertEquals(0, count((array) $response->json()));

        // No event should be available now as it has been deleted.
        $response = $this->json('PATCH', 'statistics');
        $this->assertEquals(0, count((array) $response->json()));
    }
}
