<?php

namespace DDB\Stats;

use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;

class StatisticsController extends Controller
{
    public function get(Request $request)
    {
        return [
            'here' => 'will be statistics'
        ];
    }
}
