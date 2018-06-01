<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait RestTrait {

    /**
     * Determine if request is an API call or not.
     * It checks if the request URI contains '/api/v'.
     *
     * @param Request $request
     * @return bool
     */
    protected function isApiCall(Request $request) {
        return strpos($request->getUri(), '/api/v') !== false;
    }
}
