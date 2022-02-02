<?php // phpcs:disable PSR1.Files.SideEffects

namespace DDB\Stats\ServiceProviders;

use DDB\Stats\Events\StatisticsClaimed;
use DDB\Stats\Listeners\RemoveObsoleteStatistics;

// Laravel and Lumen have different base classes for event service providers.
// If we detect a Lumen variant then use that class instead of the Laravel one.
// That should be safe as long as we are using basic features.
if (class_exists('\Laravel\Lumen\Providers\EventServiceProvider')) {
    class_alias(
        '\Laravel\Lumen\Providers\EventServiceProvider',
        '\Illuminate\Foundation\Support\Providers\EventServiceProvider'
    );
}

class StatisticsEventsServiceProvider extends \Illuminate\Foundation\Support\Providers\EventServiceProvider
{
    /** @var string[][] */
    protected $listen = [
        StatisticsClaimed::class => [
            RemoveObsoleteStatistics::class
        ],
    ];
}
