<?php

namespace DDB\Stats;

interface StatisticsCollector
{

    /**
     * Register an event which occurred within the application.
     *
     * @param string|null $guid
     *   The globally unique identifier for the user on behalf of who the event
     *   is performed.
     * @param string $event
     *   The event name. Names use snake_case per convention.
     * @param string|null $collectionId
     *   The id for a collection of items. What constitues a collection of items
     *   is defined by the system.
     * @param string|null $itemId
     *   The id for an item related to the event. What constitutes an item is
     *   defined by the system.
     * @param int|null $totalCount
     *   The total number of items/collections after the event has been
     *   completed if relevant for the event.
     * @param string[] $content
     *   The ids within the collection after the completion of the event if
     *   relevant for the event.
     */
    public function event(
        ?string $guid,
        string $event,
        ?string $collectionId = null,
        ?string $itemId = null,
        ?int $totalCount = null,
        array $content = []
    ): void;
}
