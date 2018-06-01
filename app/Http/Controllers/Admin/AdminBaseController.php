<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminBaseController extends Controller {

    /**
     * Contains today's date in 'M j, Y' (e.g., Jan 1, 2017) string format.
     * @var string
     */
    protected $today;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->today = Carbon::today()->toFormattedDateString();
    }

}
