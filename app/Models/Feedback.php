<?php

namespace App\Models;

use App\Exceptions\InvalidInputException;
use Holly\Support\Helper;
use Iatstuti\Database\Support\NullableFields;
use Illuminate\Database\Eloquent\Model;
use Request;

class Feedback extends Model
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
        'contact', 'user_id', 'device_id',
        'os_version', 'platform', 'network',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($instance) {
            $instance->created_at = $instance->freshTimestamp();
        });
    }

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
        $content = trim(array_get($data, 'feedback_content'));

        if (! is_string($content) || empty($content)) {
            throw new InvalidInputException('反馈内容不能为空！');
        }

        $contact = array_get($data, 'feedback_contact');

        $data = array_only(array_filter($data), [
            'os', 'os_version', 'platform', 'network',
        ]) + compact('user_id', 'device_id', 'content', 'contact') + [
            'os' => app('client')->os,
            'ip' => Request::ip(),
        ];

        return static::forceCreate($data);
    }
}
