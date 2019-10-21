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
        ?string $collection_id = null,
        ?string $item_id = null,
        ?int $total_count = null,
        array $content = []
    ): void {
        $this->database->table('statistics')->insert([
            'timestamp' => Carbon::now()->timestamp,
            'guid' => $guid,
            'event' => $event,
            'collection_id' => $collection_id,
            'item_id' => $item_id,
            'total_count' => $total_count,
            'content' => json_encode($content),
        ]);
    }
}
