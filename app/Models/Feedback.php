<?php

namespace App\Models;

use Carbon\Carbon;
use Request;
use Holly\Support\Helper;
use App\Jobs\SendBearyChat;

class Feedback extends Model
{
    public $timestamps = false;

    protected $dates = ['created_at'];

    protected $appends = ['platform_string'];

    public function getPlatformStringAttribute()
    {
        return Helper::iOSPlatform($this->platform);
    }

    public static function createFeedback($data, $user_id = null, $device_id = null)
    {
        $data = array_filter($data);

        if (! isset($data['feedback_content'])) {
            return;
        }

        $instance = new static;
        $instance->user_id = $user_id;
        $instance->device_id = $device_id;
        $instance->os = str_limit2(array_get($data, 'os', app('AppClient')->os), 10);
        $instance->os_version = str_limit2(array_get($data, 'os_version'), 20);
        $instance->platform = str_limit2(array_get($data, 'platform'), 20);
        $instance->network = str_limit2(array_get($data, 'network'), 8);
        $instance->ip = Request::ip();
        $instance->content = $data['feedback_content'];
        $instance->contact = str_limit2(array_get($data, 'feedback_contact'), 100);
        $instance->created_at = Carbon::now();

        $instance->save();

        static::notifyNewFeedback($instance);

        return $instance;
    }

    public static function notifyNewFeedback($feedback)
    {
        dispatch(
            (new SendBearyChat('收到新的意见反馈！', $feedback->content, $feedback->contact))
            ->notification('收到新的意见反馈：'.str_limit2($feedback->content, 50))
        );
    }
}
