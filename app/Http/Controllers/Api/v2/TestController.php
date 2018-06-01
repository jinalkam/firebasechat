<?php

namespace App\Http\Controllers\APIs\V2;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TestController extends Controller
{
    public function index() {
        return response()->json(
            [
                'data' => [
                    1 => 'Monday',
                    2 => 'Tuesday',
                    3 => 'Wednesday',
                    4 => 'Thursday',
                    5 => 'Friday',
                    6 => 'Saturday',
                    7 => 'Sunday',
                ],
                'namespace' => 'App\Http\Controllers\APIs\V2'
            ],
            200
        );
    }
}
