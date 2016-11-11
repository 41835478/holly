<?php

namespace App\Models;

use App\Notifications\ResetPassword;
use App\Support\Image\Filters\Fit;
use Exception;
use Holly\Support\Helper;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\UploadedFile;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class AdminUser extends Authenticatable
{
    use Notifiable;

    /**
     * The avatar size.
     */
    const AVATAR_SIZE = 200;

    /**
     * The directory stores all admin users' avatars.
     */
    const AVATAR_DIRECTORY = 'admin-avatar';

    protected $appends = [
        'super_admin', 'avatar',
    ];

    protected $hidden = [
        'password', 'remember_token', 'avatar_path',
    ];

    /**
     * The "booting" method of the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($user) {
            $user->removeAvatar();
        });
    }

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
     * Get the `avatar` attribute.
     *
     * @return string
     */
    public function getAvatarAttribute()
    {
        if (! is_null($this->avatar_path)) {
            return asset_url($this->getFilesystem()->url($this->avatar_path));
        }

        return Helper::gravatar($this->email, static::AVATAR_SIZE);
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
     * Set the `avatar_path`.
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
                $this->getFilesystem()->delete($this->avatar_path);
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
     * Store the uploaded file as user's avatar.
     *
     * @param  \Illuminate\Http\UploadedFile  $file
     * @return bool
     */
    public function storeAvatarFile(UploadedFile $file)
    {
        if ($file->isValid()) {
            try {
                $image = Image::make($file)
                    ->filter((new Fit)->width(static::AVATAR_SIZE))
                    ->encode();
            } catch (Exception $e) {
                return false;
            }

            $filename = static::AVATAR_DIRECTORY.'/'.md5($image).'.'.$file->extension();

            if ($this->getFilesystem()->put($filename, $image)) {
                $this->setAvatarPath($filename);

                return true;
            }
        }

        return false;
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

    /**
     * Get the Filesystem instance.
     *
     * @return \Illuminate\Contracts\Filesystem\Filesystem
     */
    protected function getFilesystem()
    {
        return Storage::disk('public');
    }
}
