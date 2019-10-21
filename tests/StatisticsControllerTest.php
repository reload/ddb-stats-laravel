<?php

namespace DDB\Stats;

use DDB\Stats\Controllers\StatisticsController;
use DDB\Stats\Events\StatisticsClaimed;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\DatabaseManager;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\HttpException;

class StatisticsControllerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testPatch()
    {
        $now = time();

        $result = new Collection();
        $result->add((object) [
          'timestamp' => $now,
          'guid' => 'guid',
          'event' => 'event',
          'collection_id' => 'collection_id',
          'item_id' => 'item_id',
          'details' => json_encode(['some' => 'value']),
        ]);

        $database = \Mockery::mock(DatabaseManager::class);
        $database->shouldReceive('table->orderBy->get')->andReturn($result);

        $dispatcher = \Mockery::mock(Dispatcher::class);
        $dispatcher->shouldReceive('dispatch')
            ->withArgs(function (StatisticsClaimed $event) {
                return $event->getSince() === null;
            });

        $controller = new StatisticsController($database, $dispatcher);

        $request = $this->prophesize(Request::class);

        $response = new Collection([[
           'date' => date('c', $now),
           'guid' => 'guid',
           'event' => 'event',
           'collectionId' => 'collection_id',
           'itemId' => 'item_id'
        ]]);
        $this->assertEquals(
            $response,
            $controller->patch($request->reveal())
        );
    }

    public function testSince()
    {
        $now = time();

        $database = \Mockery::mock(DatabaseManager::class);
        $database->shouldReceive('table')
            ->with('statistics')
            ->andReturnSelf()
            ->shouldReceive('orderBy')
            ->with('timestamp', 'ASC')
            ->andReturnSelf()
            ->shouldReceive('where')
            ->with('timestamp', '>=', $now)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('get')
            ->andReturn(new Collection());

        $dispatcher = \Mockery::mock(Dispatcher::class);
        $dispatcher->shouldReceive('dispatch')
            ->withArgs(function (StatisticsClaimed $event) use ($now) {
                $since = $event->getSince();
                return ($since !== null) && ($since->getTimestamp() === $now);
            });

        $controller = new StatisticsController($database, $dispatcher);

        $request = $this->prophesize(Request::class);
        $request->has('since')->willReturn(true);
        $request->get('since')->willReturn(date('c', $now));

        $response = $controller->patch($request->reveal());
        $this->assertInstanceOf(Collection::class, $response);
    }

    public function testInvalidSince()
    {
        $database = \Mockery::mock(DatabaseManager::class);
        $database->shouldReceive('table->orderBy');

        $dispatcher = \Mockery::mock(Dispatcher::class);

        $controller = new StatisticsController($database, $dispatcher);

        $now = time();

        $request = $this->prophesize(Request::class);
        $request->has('since')->willReturn(true);
        $request->get('since')->willReturn(date('r', $now));

        $this->expectException(HttpException::class);
        $controller->patch($request->reveal());
    }
}
