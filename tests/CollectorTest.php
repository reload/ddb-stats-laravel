<?php

namespace DDB\Stats;

use PHPUnit\Framework\TestCase;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Query\Builder;

class CollectorTest extends TestCase
{
    /**
     * Test that adding events sends it to the DB.
     */
    public function testEvent()
    {
        $builder = $this->prophesize(Builder::class);
        $builder->insert([
            'guid' => 'guid',
            'event' => 'event',
            'object_id' => 'object_id',
            'item_id' => 'item_id',
            'details' => ['some' => 'value'],
        ])->shouldBeCalled();
        $db = $this->prophesize(ConnectionInterface::class);
        $db->table('statistics')->willReturn($builder);

        $collector = new Collector($db->reveal());
        $collector->event('guid', 'event', 'object_id', 'item_id', ['some' => 'value']);
    }
}
