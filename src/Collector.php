<?php

namespace DDB\Stats;

use Illuminate\Database\ConnectionInterface;

class Collector implements StatisticsCollector
{

    /**
     * Database to store stats in.
     *
     * @var \Illuminate\Database\ConnectionInterface
     */
    protected $database;

    public function __construct(ConnectionInterface $database)
    {
        $this->database = $database;
    }

    /**
     * {@inheritdoc}
     */
    public function event(string $guid, string $event, string $object_id, string $item_id, array $details = []): void
    {
        $this->database->table('statistics')->insert([
            'guid' => $guid,
            'event' => $event,
            'object_id' => $object_id,
            'item_id' => $item_id,
            'details' => $details,
        ]);
    }
}
