<?php

namespace App\Models;

use App\Exceptions\InvalidInputException;
use App\Support\Helper;
use Iatstuti\Database\Support\NullableFields;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class Feedback extends Model
{
    use NullableFields;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['platform_string'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'user_id' => 'integer',
        'device_id' => 'integer',
    ];

    /**
     * The attributes that should be saved as null when empty.
     *
     * @var array
     */
    protected $nullable = [
        'contact', 'user_id', 'device_id',
        'os_version', 'platform', 'network',
    ];

    /**
     * The "booting" method of the model.
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function ($instance) {
            $instance->created_at = $instance->freshTimestamp();
        });
    }

    /**
     * Get the `platform_string` attribute.
     *
     * @return string|null
     */
    public function getPlatformStringAttribute()
    {
        return Helper::iDeviceModel($this->platform);
    }

    /**
     * Create a Feedback instance.
     *
     * @param  array  $data
     * @param  int  $user_id
     * @param  int  $device_id
     * @return static
     *
     * @throws \App\Exceptions\InvalidInputException
     */
    public static function createFeedback($data, $user_id = null, $device_id = null)
    {
        if (empty($content = trim(array_get($data, 'feedback_content')))) {
            throw new InvalidInputException('反馈内容不能为空！');
        }

        $contact = array_get($data, 'feedback_contact');

        $attributes = array_only($data, ['os', 'os_version', 'platform', 'network'])
            + compact('user_id', 'device_id', 'content', 'contact')
            + [
                'os' => app('client')->os,
                'ip' => Request::ip(),
            ];

        return static::forceCreate($attributes);
    }
}
