<?php

namespace DDB\Stats\Listeners;

use DDB\Stats\Events\StatisticsClaimed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\DatabaseManager;

class RemoveObsoleteStatistics implements ShouldQueue
{
    private $database;

    public function __construct(DatabaseManager $database)
    {
        $this->database = $database;
    }

    public function handle(StatisticsClaimed $event): void
    {
        $since = $event->getSince();
        if ($since !== null) {
            $this->database->table('statistics')
                ->where('timestamp', '<', $since->getTimestamp())
                ->delete();
        }
    }

    public function shouldQueue(StatisticsClaimed $event): bool
    {
        // Only remove statistics if a point in time has been specified.
        return $event->getSince() !== null;
    }
}
