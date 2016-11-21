<?php

namespace App\Models;

use App\Notifications\ResetPassword;
use App\Support\Image\Filters\Fit;
use App\Traits\ImageStorage;
use Holly\Support\Helper;
use Iatstuti\Database\Support\NullableFields;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class AdminUser extends Authenticatable
{
    use NullableFields, Notifiable, ImageStorage;

    /**
     * The avatar size.
     */
    const AVATAR_SIZE = 200;

    /**
     * The directory stores all admin users' avatars.
     */
    const AVATAR_DIRECTORY = 'admin-avatar';

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['super_admin'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * The attributes that should be saved as null when empty.
     *
     * @var array
     */
    protected $nullable = ['avatar'];

    /**
     * Create an admin user.
     *
     * @param  array  $data
     * @return static
     */
    public static function createUser($data)
    {
        return static::forceCreate([
            'email' => mb_strtolower(trim($data['email'])),
            'password' => bcrypt($data['password']),
            'username' => trim($data['username']),
        ]);
    }

    /**
     * Get the `super_admin` attribute.
     *
     * @return bool
     */
    public function getSuperAdminAttribute()
    {
        return $this->id === 1;
    }

    /**
     * Determines whether the user is a super admin.
     *
     * @return bool
     */
    public function isSuperAdmin()
    {
        return $this->super_admin;
    }

    /**
     * Get the `avatar` attribute.
     *
     * @param  string|null  $value
     * @return string
     */
    public function getAvatarAttribute($value)
    {
        if (! is_null($value)) {
            return $this->getAssetUrl($value, 'avatar');
        }

        return Helper::gravatar($this->email, static::AVATAR_SIZE);
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
     * Use the default avatar.
     */
    public function useDefaultAvatar()
    {
        $this->avatar = null;
    }

    /**
     * Store the given file as user's avatar.
     *
     * @param  mixed  $file
     * @return bool
     */
    public function storeAvatarFile($file)
    {
        if ($path = $this->storeImageFile($file, 'avatar')) {
            $this->avatar = $path;

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
        return (new Fit)->width($this->getImageSize($identifier));
    }

    /**
     * Get image directory for the given attribute.
     *
     * @param  string  $attribute
     * @return string
     */
    protected function getImageDirectory($attribute)
    {
        return static::AVATAR_DIRECTORY;
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }
}
