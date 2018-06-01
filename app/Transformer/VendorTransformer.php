<?php

namespace App\Http\Transformer;

use URL;
use Carbon\Carbon;

class VendorTransformer {

    use \App\Http\Services\VendorTrait;

    public function transform(User $user) {
        return [
            'name' => $user->name,
        ];
    }

    public function transformLogin($user) {
        return [
            'user_id' => $user->vendor->user_id ?? '',
            'vendor_name' => $user->vendor->vendor_name ?? '',
            'vendor_address' => $user->vendor->vendor_street_address ?? '',
            'email' => $user->email ?? '',
            'vendor_phone' => $user->vendor->vendor_phone ?? '',
            'vendor_logo' => (!empty($user->vendor->vendor_logo)) ? URL::to($user->vendor->vendor_logo) : "",
            'profile_pic_thumbnail' => (!empty($user->vendor->vendor_logo)) ? URL::to($user->vendor->vendor_logo) : "",
            'api_token' => $user->api_token ?? ''
        ];
    }

    public function transformNotification($data) {

        $var = [];
        foreach ($data as $item) {
            $fromuser = \App\User::find($item->from_id);
            if (!empty($fromuser)) {
                $profile = URL::to('/storage/app/public/profile_pic') . '/' . $fromuser->userDetail->profile_pic;
            } else {
                $profile = '';
            }
            $touser = \App\User::find($item->notifiable_id);

            $fmessage = $this->finalNotificationMessage($item);
            $var[] = [
                'notification_id' => $item->id ?? '',
                'notification_message' => $fmessage ?? '',
                'notification_image' => $profile,
                'is_read' => $item->is_read ?? '',
                'to_name' => (!empty($touser) ? $touser->userDetail->first_name . ' ' . $touser->userDetail->last_name : '')
            ];
        }
        return ['has_page' => $data->hasMorePages(), 'current_page' => $data->currentPage(), 'listing' => $var];
    }

    public function settingsData($data) {
        return [
            'user_id' => $data['vendor_id'] ?? '',
            'businessname1' => $data['vendor_name'] ?? '',
            'businessname2' => $data['billing_businessname'] ?? '',
            'vendor_address' => $data['vendor_street_address'] ?? '',
            'vendor_city' => $data['vendor_city'] ?? '',
            'vendor_state' => $data['vendor_state'] ?? '',
            'vendor_zip' => $data['vendor_zip'] ?? '',
            'vendor_phone' => $data['vendor_phone'] ?? '',
            'vendor_country' => $data['country_name'] ?? '',
            'billing_home' => $data['billing_home'] ?? '',
            'billing_city' => $data['billing_city'] ?? '',
            'billing_state' => $data['billing_state'] ?? '',
            'billing_zip' => $data['billing_zip'] ?? '',
            'billing_country' => $data['billing_country_name'] ?? '',
            'card_last_four' => $data['card_last_four'] ?? '',
            'subscription_plan' => $data['stripe_plan'] ?? '',
            'card_brand' => $data['card_brand'] ?? '',
            'vendor_logo' => (!empty($data['vendor_logo'])) ? URL::to('storage/app/public/vendor_logo/' . $data['vendor_logo']) : URL::to('storage/app/public/vendor_logo/'),
        ];
    }

    public function transformvendorData($data) {
        return [
            'vendor_id' => $data['vendor_id'] ?? '',
            'businessname1' => $data['vendor_name'] ?? '',
            'businessname2' => $data['billing_businessname'] ?? '',
            'vendor_address' => $data['vendor_street_address'] ?? '',
            'vendor_city' => $data['vendor_city'] ?? '',
            'vendor_state' => $data['vendor_state'] ?? '',
            'vendor_zip' => $data['vendor_zip'] ?? '',
            'vendor_phone' => $data['vendor_phone'] ?? '',
            'vendor_country' => $data['country_name'] ?? '',
            'vendor_logo' => (!empty($data['vendor_logo'])) ? URL::to('storage/app/public/vendor_logo/' . $data['vendor_logo']) : URL::to('storage/app/public/vendor_logo/'),
        ];
    }

    public function transformList($vendor) {
        $var = [];
        $var = $vendor->map(function ($item) {
            return [
                'vendor_id' => $item->vendor_id ?? '',
                'businessname1' => $item->vendor_name ?? '',
                'businessname2' => $item->billing_businessname ?? '',
                'vendor_lat' => $item->vendor_lat ?? '',
                'vendor_long' => $item->vendor_long ?? '',
                'distance' => number_format($item->distance, 2) ?? '',
                'vendor_logo' => $item->vendor_logo ?? URL::to('storage/app/public/vendor_logo/'),
            ];
        });
        return ['has_page' => $vendor->hasMorePages(), 'current_page' => $vendor->currentPage(), 'listing' => $var];
    }

}
