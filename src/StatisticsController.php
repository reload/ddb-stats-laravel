<?php

namespace DDB\Stats;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Lumen\Routing\Controller;
use Symfony\Component\HttpKernel\Exception\HttpException;

class StatisticsController extends Controller
{

    public function get(Request $request)
    {
        $query = DB::table('statistics')
            ->orderBy('timestamp', 'ASC');
        if ($request->has('since')) {
            try {
                $since = Carbon::createFromFormat(DATE_ATOM, $request->get('since'));
                if (!$since) {
                    throw new \InvalidArgumentException('Unable to create date from format');
                }
                $query = $query->where('timestamp', '>=', $since->timestamp);
            } catch (\InvalidArgumentException $e) {
                throw new HttpException(400, 'Invalid since parameter. Please use ISO 8601 format.', $e);
            }
        }

        $result = $query->get(['timestamp', 'guid', 'event', 'object_id', 'item_id', 'details']);
        return $result->map(function (\stdClass $values) {
            return [
                'date' => (Carbon::createFromTimestamp($values->timestamp))->toIso8601String(),
                'guid' => $values->guid,
                'event' => $values->event,
                'collectionId' => $values->object_id,
                'itemId' => $values->item_id,
            ];
        });
    }
}
