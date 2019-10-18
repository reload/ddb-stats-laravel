<?php

namespace DDB\Stats\ServiceProviders;

use DDB\Stats\Events\StatisticsClaimed;
use DDB\Stats\Listeners\RemoveObsoleteStatistics;
use Illuminate\Foundation\Support\Providers\EventServiceProvider;

class StatisticsEventsServiceProvider extends EventServiceProvider
{

    protected $listen = [
      StatisticsClaimed::class => [
        RemoveObsoleteStatistics::class
      ],
    ];
}
