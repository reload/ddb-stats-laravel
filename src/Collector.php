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
        ?string $collectionId = null,
        ?string $itemId = null,
        ?int $totalCount = null,
        array $content = []
    ): void {
        $this->database->table('statistics')->insert([
            'timestamp' => Carbon::now(),
            'guid' => $guid,
            'event' => $event,
            'collection_id' => $collectionId,
            'item_id' => $itemId,
            'total_count' => $totalCount,
            'content' => json_encode($content),
        ]);
    }
}
