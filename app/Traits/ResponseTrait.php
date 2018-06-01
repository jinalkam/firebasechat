<?php

namespace App\Traits;

trait ResponseTrait {

    public function responseJson($status, $message, $statuscode, $data = '') {
       
        if (!empty($data)  ) {
            return response()->json(['status' => $status, 'message' => ucwords($message), 'data' => $data], $statuscode);
        }
        return response()->json(['status' => $status, 'message' => ucwords($message)], $statuscode);
    }

}
