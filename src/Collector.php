<?php

namespace DDB\Stats;

use Carbon\Carbon;
use Illuminate\Database\DatabaseManager;

class Collector implements StatisticsCollector
{

    /**
     * Database to store stats in.
     *
     * @var \Illuminate\Database\DatabaseManager
     */
    protected $database;

    public function __construct(DatabaseManager $database)
    {
        $this->database = $database;
    }

    /**
     * {@inheritdoc}
     */
    public function event(
        ?string $guid,
        string $event,
        ?string $object_id = null,
        ?string $item_id = null,
        array $details = []
    ): void {
        $this->database->table('statistics')->insert([
            'timestamp' => Carbon::now()->timestamp,
            'guid' => $guid,
            'event' => $event,
            'object_id' => $object_id,
            'item_id' => $item_id,
            'details' => json_encode($details),
        ]);
    }
}
