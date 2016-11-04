<?php

namespace App\Models;

use App\Exceptions\InvalidInputException;
use Carbon\Carbon;
use Holly\Support\Helper;
use Iatstuti\Database\Support\NullableFields;
use Illuminate\Database\Eloquent\Model as BaseModel;
use Request;

class Feedback extends BaseModel
{
    use NullableFields;

    public $timestamps = false;

    protected $appends = ['platform_string'];

    protected $dates = ['created_at'];

    protected $casts = [
        'user_id' => 'integer',
        'device_id' => 'integer',
    ];

    protected $nullable = [
        'user_id', 'device_id', 'os_version',
        'platform', 'network', 'ip', 'contact',
    ];

    public function getPlatformStringAttribute()
    {
        return Helper::iOSPlatform($this->platform);
    }

    /**
     * Create a Feedback instance.
     *
     * @param  array  $data
     * @param  int  $user_id
     * @param  int  $device_id
     * @return static|null
     *
     * @throws \App\Exceptions\InvalidInputException
     */
    public static function createFeedback($data, $user_id = null, $device_id = null)
    {
        if (! is_string($content = array_get($data, 'feedback_content'))) {
            throw new InvalidInputException('反馈内容不能为空！');
        }

        $contact = array_get($data, 'feedback_contact');

        $data = array_only(array_filter($data), [
            'os', 'os_version', 'platform', 'network',
        ]) + compact('user_id', 'device_id', 'content', 'contact') + [
            'os' => app('client')->os,
            'ip' => Request::ip(),
            'created_at' => Carbon::now(),
        ];

        return static::forceCreate($data);
    }
}
