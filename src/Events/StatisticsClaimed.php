<?php

namespace DDB\Stats\Events;

use Carbon\CarbonInterface;

class StatisticsClaimed
{
    /* @var \Carbon\CarbonInterface|null */
    protected $since;

    /**
     * @param \Carbon\CarbonInterface|null $since
     *   The point in time from which statistics have been claimed.
     */
    public function __construct(CarbonInterface $since = null)
    {
        $this->since = $since;
    }

    public function getSince(): ?CarbonInterface
    {
        return  $this->since;
    }
}
