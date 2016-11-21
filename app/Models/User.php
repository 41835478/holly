<?php

namespace App\Models;

use App\Support\Image\Filters\Fit;
use Exception;
use GuzzleHttp\Client as HttpClient;
use Holly\Support\Helper;
use Iatstuti\Database\Support\NullableFields;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class User extends Authenticatable
{
    use Notifiable, NullableFields;

    /**
     * The user status.
     */
    const STATUS_NORMAL = 1;

    /**
     * The directory stores all users' avatars.
     */
    const AVATAR_DIRECTORY = 'avatars';

    /**
     * The avatar size.
     */
    const AVATAR_SIZE = 200;
    const ORIGINAL_AVATAR_SIZE = 960;

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
        return $this->getAvatarValue('avatar', $value);
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
        return $this->getAvatarValue('original_avatar', $value);
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
     * Get value for avatar.
     *
     * @param  string  $attribute
     * @param  string|null  $value
     * @return string|null
     */
    protected function getAvatarValue($attribute, $value)
    {
        if (! is_null($value)) {
            return asset_url($this->getFilesystem()->url($value));
        } elseif (! is_null($this->email) &&
            ($size = constant('static::'.strtoupper($attribute).'_SIZE'))) {
            return Helper::gravatar($this->email, $size);
        }
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
        if ($this->storeAvatarFileAs($file, 'avatar') &&
            $this->storeAvatarFileAs($file, 'original_avatar')) {
            return true;
        }

        $this->avatar = $this->original_avatar = null;

        return false;
    }

    /**
     * Store user's avatar from an image URL.
     *
     * @param  string  $url
     * @return bool
     */
    public function storeAvatarFromUrl($url)
    {
        $tmpfile = tmpfile();

        try {
            with(new HttpClient)->get($url, [
                'timeout' => 15,
                'connect_timeout' => 5,
                'sink' => $tmpfile,
            ]);
        } catch (Exception $e) {
            fclose($tmpfile);

            return false;
        }

        $stored = $this->storeAvatarFile($tmpfile);

        fclose($tmpfile);

        return $stored;
    }

    /**
     * Store avatar file for the given attribute.
     *
     * @param  mixed  $file
     * @param  string  $attribute
     * @return string|null
     */
    protected function storeAvatarFileAs($file, $attribute)
    {
        $size = constant('static::'.strtoupper($attribute).'_SIZE');

        if ($image = $this->encodeAvatarImage($file, $size)) {
            $filename = $this->getFullFilename(
                md5(str_random(100)).$file->extension(),
                static::AVATAR_DIRECTORY
            );

            if ($this->getFilesystem()->put($filename, $image)) {
                return $this->{$attributes} = $filename;
            }
        }
    }

    /**
     * Encode avatar image file.
     *
     * @param  mixed  $file
     * @param  int  $size
     * @return string|false
     */
    protected function encodeAvatarImage($file, $size)
    {
        if ($file instanceof UploadedFile && ! $file->isValid()) {
            return false;
        }

        try {
            $image = Image::make($file)
                ->filter((new Fit)->width($size))
                ->encode();
        } catch (Exception $e) {
            return false;
        }

        return $image;
    }

    /**
     * Get the full filename.
     *
     * @param  string  $filename
     * @param  string  $baseDir
     * @return string
     */
    protected function getFullFilename($filename, $baseDir = 'images')
    {
        return trim($baseDir.'/'.date('Y/m/').$filename, '/');
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
