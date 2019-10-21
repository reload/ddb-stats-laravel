<?php

namespace DDB\Stats;

interface StatisticsCollector
{

    public function event(
        ?string $guid,
        string $event,
        ?string $collection_id = null,
        ?string $item_id = null,
        ?int $total_count = null,
        array $content = []
    ): void;
}
