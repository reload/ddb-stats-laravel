<?php

namespace DDB\Stats;

use Illuminate\Foundation\Support\Providers\EventServiceProvider;

class StatisticsEventsServiceProvider extends EventServiceProvider
{

    protected $listen = [
      StatisticsClaimed::class => [
        RemoveObsoleteStatistics::class
      ],
    ];
}
