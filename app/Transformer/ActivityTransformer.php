<?php

namespace App\Http\Transformer;

use URL;
use Carbon\Carbon;
use Auth;
use \App\Http\Services\CouponTrait;
use \App\Http\Services\UserTrait;

class ActivityTransformer {

    use CouponTrait;
    use UserTrait;

    public function transformCheckFb($user) {
        return [
            'is_fbconnected' => (!empty($user->fb_token) ? 0 : 1)
        ];
    }

    public function transformActivityList($data) {

        $var = [];
        foreach ($data as $item) {
            $user = $this->getUserDetail($item->created_by);
            $share_friend = $this->getUserDetail($item->share_friend_id);
            $name = $user->first_name . " " . $user->last_name;
            if (!empty($share_friend)) {
                $sharename = $share_friend->first_name . " " . $share_friend->last_name;
            }
            $image = (!empty($user->profile_pic)) ? URL::to('/storage/app/public/profile_pic/tmp') . '/' . $user->profile_pic : "";

            $fmessage = $this->finalMessage($item->activity_message, $item);

            if ($item->count_fb_friend == '' || $item->count_fb_friend == 0 || $item->count_fb_friend == 1) {
                $count = 0;
            } else {
                $count = $item->count_fb_friend - 1;
                $count = (string) $count . " others";
            }
            $couponshare=\App\CouponShare::where('coupon_id',$item->coupon_id)
                    ->where('user_id',Auth::id())
                     ->where('activity_id',$item->activity_id)
                    ->first();
           
            $var[] = [
                'activity_id' => $item->activity_id ?? '',
                'activity_name' => $fmessage,
                'total_like' => $item->total_like,
                'total_share' => $item->total_share ?? 0,
                'total_comment' => $item->total_comment ?? 0,
                'is_like' => $item->activitylike->is_like ?? 0,
                'creator_id' => $item->created_by,
                'creator_name' => $name,
                'share_name' => $sharename ?? '',
                'image' => $image,
                'count' => $count,
               'sharetext'=>$item->sharetext??''
             
            ];
        }

        return ['has_page' => $data->hasMorePages(), 'current_page' => $data->currentPage(), 'listing' => $var];
    }

    public function transformCommentList($data) {

        $var = [];
        $var = $data->map(function ($item) {

            return [
                'comment_id' => $item->comment_id ?? '',
                'activity_id' => $item->activity_id ?? '',
                'comment_desc' => $item->comment_desc ?? '',
                'created_by' => $item->user->first_name . " " . $item->user->last_name ?? '',
                'user_id' => ($item->created_by == Auth::id()) ? Auth::id() : 0,
                'creator_image' => (!empty($item->user->profile_pic)) ? URL::to('/storage/app/public/profile_pic') . '/' . $item->user->profile_pic : "",
            ];
        });
        return $var;
    }

    public function transformActivityDetails($item) {

        $var = [];
        $user = $this->getUserDetail($item->created_by);
        $share_friend = $this->getUserDetail($item->share_friend_id);
        $name = $user->first_name . " " . $user->last_name;
        if (!empty($share_friend)) {
            $sharename = $share_friend->first_name . " " . $share_friend->last_name;
        }
        $image = (!empty($user->profile_pic)) ? URL::to('/storage/app/public/profile_pic/tmp') . '/' . $user->profile_pic : "";

        $fmessage = $this->finalMessage($item->activity_message, $item);

        if ($item->count_fb_friend == '' || $item->count_fb_friend == 0 || $item->count_fb_friend == 1) {
            $count = 0;
        } else {
            $count = $item->count_fb_friend - 1;
            $count = (string) $count . " others";
        }
        $var = [
            'activity_id' => $item->activity_id ?? '',
            'activity_name' => $fmessage,
            'total_like' => $item->total_like,
            'total_share' => $item->total_share ?? 0,
            'total_comment' => $item->total_comment ?? 0,
            'is_like' => $item->activitylike->is_like ?? 0,
            'creator_id' => $item->created_by,
            'creator_name' => $name,
            'share_name' => $sharename ?? '',
            'image' => $image,
            'count' => $count
        ];
        return $var;
    }

}
