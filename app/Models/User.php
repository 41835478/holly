<?php

namespace App\Models;

use App\Support\Image\Filters\Fit;
use App\Traits\ImageStorage;
use Iatstuti\Database\Support\NullableFields;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Request;

class User extends Authenticatable
{
    use NullableFields, ImageStorage, Notifiable;

    /**
     * The user status.
     */
    const STATUS_NORMAL = 1;

    /**
     * The directory stores all users' avatars.
     */
    const AVATAR_DIRECTORY = 'avatar';

    /**
     * The avatar size.
     */
    const AVATAR_SIZE = 200;
    const ORIGINAL_AVATAR_SIZE = 640;

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $visible = [
        'id', 'email', 'phone', 'username', 'avatar', 'original_avatar', 'status',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['last_login_at'];

    /**
     * The attributes that should be saved as null when empty.
     *
     * @var array
     */
    protected $nullable = [
        'email', 'phone', 'username', 'avatar', 'original_avatar',
        'last_login_ip', 'registered_ip',
    ];

    /**
     * The model's attributes.
     *
     * @var array
     */
    protected $attributes = [
        'status' => 1,
        'login_count' => 0,
    ];

    /**
     * Get the `avatar` attribute.
     *
     * @param  string|null  $value
     * @return string|null
     */
    public function getAvatarAttribute($value)
    {
        return $this->getAssetUrl($value, 'avatar');
    }

    /**
     * Set the `avatar` attribute.
     *
     * @param  string|null  $value
     */
    public function setAvatarAttribute($value)
    {
        $this->attributes['avatar'] = $value;
    }

    /**
     * Get the `original_avatar` attribute.
     *
     * @param  string|null  $value
     * @return string|null
     */
    public function getOriginalAvatarAttribute($value)
    {
        return $this->getAssetUrl($value, 'original_avatar');
    }

    /**
     * Set the `original_avatar` attribute.
     *
     * @param  string|null  $value
     */
    public function setOriginalAvatarAttribute($value)
    {
        $this->attributes['original_avatar'] = $value;
    }

    /**
     * Update login info.
     *
     * @param  bool  $save
     * @return $this
     */
    public function updateLoginInfo($save = true)
    {
        $this->login_count++;
        $this->last_login_at = $this->freshTimestamp();
        $this->last_login_ip = Request::ip();

        if ($save) {
            $this->save();
        }

        return $this;
    }

    // public function updateUserInfoWithSocialUser($social, $user, $save = false)
    // {
    //     $this->updateAvatarFromUrl(SocialAuth::getAvatarFromSocialUser($social, $user));

    //     $this->username = str_limit2(SocialAuth::getUsernameFromSocialUser($social, $user), 10);

    //     if ($save) {
    //         $this->save();
    //     }
    // }

    /**
     * Store the given file as user's avatar.
     *
     * @param  mixed  $file
     * @return bool
     */
    public function storeAvatarFile($file)
    {
        if (is_string($file) && filter_var($file, FILTER_VALIDATE_URL) !== false) {
            $file = app('image')->make($file);
        }

        if (($avatar = $this->storeImageFile(clone $file, 'avatar')) &&
            ($original_avatar = $this->storeImageFile($file, 'original_avatar'))
        ) {
            $this->avatar = $avatar;
            $this->original_avatar = $original_avatar;

            return true;
        }

        return false;
    }

    /**
     * Get image filter.
     *
     * @see http://image.intervention.io/api/filter
     *
     * @param  string|null  $identifier
     */
    protected function getImageFilter($identifier = null)
    {
        return (new Fit)->width(constant('static::'.strtoupper($identifier).'_SIZE'));
    }

    /**
     * Get image directory for the given attribute.
     *
     * @param  string  $attribute
     * @return string
     */
    protected function getImageDirectory($attribute)
    {
        return static::AVATAR_DIRECTORY.'/'.dechex((int) date('Y') - 2010).'/'.dechex(date('W'));
    }

    /**
     * Get user's devices.
     *
     * @param  bool  $withTrashed
     * @return \Illuminate\Support\Collection
     */
    public function getDevices($withTrashed = false)
    {
        return Device::whereIn('id', function ($query) use ($withTrashed) {
            $query->select('device_id')->from('user_devices')->where('user_id', $this->id);

            if (! $withTrashed) {
                $query->where('deleted_at', null);
            }
        })->get();
    }
}
