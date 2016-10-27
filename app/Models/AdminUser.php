<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Notifications\Notifiable;
use App\Notifications\ResetPassword as ResetPasswordNotification;
use Holly\Support\Helper;
use Image;
use Exception;

class AdminUser extends Model implements
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword, Notifiable;

    /**
     * The avatar width or height.
     */
    const AVATAR_SIZE = 300;

    /**
     * The directory stores all admin users' avatars.
     */
    const AVATAR_DIRECTORY = 'storage/admin-avatar';

    protected $hidden = ['password', 'remember_token', 'avatar_path'];

    protected $appends = ['super_admin', 'avatar'];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($user) {
            $user->removeAvatar();
        });
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
     * Indicate the user is super admin.
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
     * @return string
     */
    public function getAvatarAttribute()
    {
        if ($this->avatar_path) {
            return asset_url($this->avatar_path);
        }

        return Helper::gravatar($this->email, static::AVATAR_SIZE);
    }

    /**
     * Set `avatar_path` for user.
     *
     * @param  string|null  $path
     * @return $this
     */
    public function setAvatarPath($path)
    {
        if (! is_null($path)) {
            $path = trim($path, '/');
            $path = empty($path) ? null : $path;
        }

        if ($path !== $this->avatar_path) {
            if ($this->avatar_path) {
                @unlink(public_path($this->avatar_path));
            }

            $this->avatar_path = $path;
        }

        return $this;
    }

    /**
     * Remove user's avatar file, and set `avatar_path` to null.
     *
     * @return $this
     */
    public function removeAvatar()
    {
        return $this->setAvatarPath(null);
    }

    /**
     * Remove user's avatar file, and use the Gravatar.
     *
     * @return void
     */
    public function useDefaultAvatar()
    {
        $this->removeAvatar();
    }

    /**
     * Upload avatar file for user.
     *
     * @param  \Illuminate\Http\UploadedFile  $file
     * @return bool
     */
    public function uploadAvatar($file)
    {
        if (! $file->isValid()) {
            return false;
        }

        try {
            $avatarPath = $this->getAvatarDirectory().'/'.$this->createFilenameForUploadedFile($file);

            Image::make($file)
                ->resize(static::AVATAR_SIZE, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->save(public_path($avatarPath));

            $this->setAvatarPath($avatarPath);
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * Get the avatars directory.
     *
     * @return string
     */
    protected function getAvatarDirectory()
    {
        $path = public_path(static::AVATAR_DIRECTORY);
        if (! file_exists($path)) {
            mkdir($path, 0775, true);
        }

        return static::AVATAR_DIRECTORY;
    }

    /**
     * Create a filename for uploaded file instance.
     *
     * @param  \Illuminate\Http\UploadedFile  $file
     * @return string
     */
    protected function createFilenameForUploadedFile($file)
    {
        return $this->id.str_random().'.'.$file->extension();
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}
