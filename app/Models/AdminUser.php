<?php

namespace App\Models;

use App\Notifications\ResetPassword;
use App\Support\Image\Filters\Fit;
use Exception;
use Holly\Support\Helper;
use Iatstuti\Database\Support\NullableFields;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AdminUser extends Authenticatable
{
    use Notifiable, NullableFields;

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
     * @return string
     */
    public function getAvatarAttribute($value)
    {
        if (! is_null($value)) {
            return asset_url($this->getFilesystem()->url($value));
        }

        return Helper::gravatar($this->email, static::AVATAR_SIZE);
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
        if ($file instanceof UploadedFile && ! $file->isValid()) {
            return false;
        }

        try {
            $image = Image::make($file)
            ->filter((new Fit)->width(static::AVATAR_SIZE))
            ->encode();
        } catch (Exception $e) {
            return false;
        }

        $filename = static::AVATAR_DIRECTORY.'/'.
            $this->id.'-'.md5($image).'.'.$file->extension();

        if ($this->getFilesystem()->put($filename, $image)) {
            $this->avatar = $filename;

            return true;
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
