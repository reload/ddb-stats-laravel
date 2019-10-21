<?php

namespace DDB\Stats;

use Carbon\Carbon;
use Illuminate\Database\DatabaseManager;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Illuminate\Database\Query\Builder;

class CollectorTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * Test that adding events sends it to the DB.
     */
    public function testEvent()
    {
        $now = Carbon::now();
        Carbon::setTestNow($now);

        $builder = \Mockery::mock(Builder::class);
        $builder->shouldReceive('insert')->with([
            'timestamp' => $now->timestamp,
            'guid' => 'guid',
            'event' => 'event',
            'collection_id' => 'collection_id',
            'item_id' => 'item_id',
            'details' => json_encode(['some' => 'value']),
        ])->once();
        $db = \Mockery::mock(DatabaseManager::class);
        $db->shouldReceive('table')->with('statistics')->andReturn($builder);

        $collector = new Collector($db);
        $collector->event('guid', 'event', 'collection_id', 'item_id', ['some' => 'value']);
    }
}
