<?php

namespace DDB\Stats;

use Carbon\Carbon;
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

    public function testPatch(): void
    {
        $now = Carbon::now();

        $result = new Collection();
        $result->add((object) [
          'timestamp' => $now,
          'guid' => 'guid',
          'event' => 'event',
          'collection_id' => 'collection_id',
          'item_id' => 'item_id',
          'total_count' => 42,
          'content' => json_encode(['item1', 'item2']),
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
           'date' => $now->toIso8601String(),
           'guid' => 'guid',
           'event' => 'event',
           'collectionId' => 'collection_id',
           'itemId' => 'item_id',
           'totalCount' => 42,
           'content' => ['item1', 'item2'],
        ]]);
        $this->assertEquals(
            $response,
            $controller->patch($request->reveal())
        );
    }

    public function testSince(): void
    {
        $now = Carbon::now();

        $database = \Mockery::mock(DatabaseManager::class);
        $database->shouldReceive('table')
            ->with('statistics')
            ->andReturnSelf()
            ->shouldReceive('orderBy')
            ->with('timestamp', 'ASC')
            ->andReturnSelf()
            ->shouldReceive('where')
            ->withArgs(function (string $column, string $operator, Carbon $value) use ($now) {
                return $column === 'timestamp' &&
                    $operator === ">=" &&
                    $value->getTimestamp() == $now->getTimestamp();
            })
            ->once()
            ->andReturnSelf()
            ->shouldReceive('get')
            ->andReturn(new Collection());

        $dispatcher = \Mockery::mock(Dispatcher::class);
        $dispatcher->shouldReceive('dispatch')
            ->withArgs(function (StatisticsClaimed $event) use ($now) {
                $since = $event->getSince();
                return $since !== null && $since->getTimestamp() === $now->getTimestamp();
            });

        $controller = new StatisticsController($database, $dispatcher);

        $request = $this->prophesize(Request::class);
        $request->has('since')->willReturn(true);
        $request->get('since')->willReturn($now->toIso8601String());

        $response = $controller->patch($request->reveal());
        $this->assertInstanceOf(Collection::class, $response);
    }

    public function testInvalidSince(): void
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
