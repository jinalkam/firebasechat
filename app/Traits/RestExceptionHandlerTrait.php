<?php

namespace App\Traits;

use Exception;
use Illuminate\Http\Request;

trait RestExceptionHandlerTrait {
    /**
     * Creates a new JSON response based on exception type.
     *
     * @param Request $request
     * @param Exception $e
     * @return \Illuminate\Http\JsonResponse
     */
    protected function getJsonResponseForException(Request $request, Exception $e) {
        return response()->json(
            [
                'message' => $e->getMessage()
            ],
            500 // Internal Server Error
        );
    }
}
