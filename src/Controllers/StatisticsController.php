<?php

namespace DDB\Stats\Controllers;

use Carbon\Carbon;
use DDB\Stats\Events\StatisticsClaimed;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\DatabaseManager;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;
use Symfony\Component\HttpKernel\Exception\HttpException;

class StatisticsController extends Controller
{

    /** @var \Illuminate\Database\DatabaseManager */
    private $database;

    /** @var \Illuminate\Contracts\Events\Dispatcher */
    private $dispatchcer;

    public function __construct(DatabaseManager $database, Dispatcher $dispatcher)
    {
        $this->database = $database;
        $this->dispatchcer = $dispatcher;
    }

    public function patch(Request $request)
    {
        $query = $this->database->table('statistics')
            ->orderBy('timestamp', 'ASC');
        if ($request->has('since')) {
            try {
                $since = Carbon::createFromFormat(DATE_ATOM, $request->get('since'));
                if (!$since) {
                    throw new \InvalidArgumentException('Unable to create date from format');
                }
                $query = $query->where('timestamp', '>=', $since->getTimestamp());
            } catch (\InvalidArgumentException $e) {
                throw new HttpException(400, 'Invalid since parameter. Please use ISO 8601 format.', $e);
            }
        }

        $result = $query->get(['timestamp', 'guid', 'event', 'object_id', 'item_id', 'details']);
        $response = $result->map(function (\stdClass $values) {
            return [
                'date' => (Carbon::createFromTimestamp($values->timestamp))->toIso8601String(),
                'guid' => $values->guid,
                'event' => $values->event,
                'collectionId' => $values->object_id,
                'itemId' => $values->item_id,
            ];
        });

        $since = $since ?? null;
        $this->dispatchcer->dispatch(new StatisticsClaimed($since));

        return $response;
    }
}
