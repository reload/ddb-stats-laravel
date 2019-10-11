<?php

namespace DDB\Stats;

interface StatisticsCollector
{

    public function event(string $guid, string $event, string $object_id, string $item_id, array $details = []): void;
}
