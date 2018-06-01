<?php

namespace App\Repositories;

use App\Models\SmsLog;

class SmsLogRepository
{
    /**
     * Saves log of the SMS sent.
     *
     * @param array $data
     */
    public function create($data) {
        $smsLog = new SmsLog;
        $smsLog->fill($data);
        $smsLog->save();
    }
}
