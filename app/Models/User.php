<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Http\UploadedFile;
use Illuminate\Notifications\Notifiable;
use GuzzleHttp\Client as HttpClient;
use App\Events\UserVIPChanged;
use Holly\Support\Helper;
use Request;
use Image;
use Exception;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract
{
    use Authenticatable, CanResetPassword, Notifiable;

    const STATUS_GUEST = 0;
    const STATUS_NORMAL = 1;
    const STATUS_FORBIDDEN = 2;

    /**
     * The directory stores all users' avatars.
     */
    const AVATAR_DIRECTORY = 'storage/avatar';

    public $timestamps = false;

    public static $filterAttributes = true;

    protected $appends = [
        'avatar', 'small_avatar', 'is_guest', 'is_forbidden',
        'is_vip', 'vip_days',
    ];

    protected $visible = [
        'id', 'email', 'phone', 'username', 'status', 'vip_expired_at',
        'avatar', 'small_avatar', 'vip_days',
    ];

    protected $dates = [
        'registered_at', 'last_login_at', 'vip_expired_at',
    ];

    protected $attributes = [
        'status' => 1, // STATUS_NORMAL
        'login_count' => 0,
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($user) {
            if ($user->vip_expired_at && $user->vip_expired_at->isPast()) {
                $user->vip_expired_at = null;
            }
        });

        static::updated(function ($user) {
            $user->checkAndFireVIPChangedEvent();
        });
    }

    public function getAvatarAttribute()
    {
        if ($this->avatar_path) {
            return asset_url($this->avatar_path);
        } elseif ($this->email) {
            return Helper::gravatar($this->email, 400);
        }
    }

    public function getSmallAvatarAttribute()
    {
        if ($this->small_avatar_path) {
            return asset_url($this->small_avatar_path);
        } elseif ($this->email) {
            return Helper::gravatar($this->email, 200);
        }
    }

    public function getIsGuestAttribute()
    {
        return $this->status === static::STATUS_GUEST;
    }

    public function getIsForbiddenAttribute()
    {
        return ($this->status & static::STATUS_FORBIDDEN) === static::STATUS_FORBIDDEN;
    }

    public function getIsVipAttribute()
    {
        return (bool) $this->vip_expired_at;
    }

    public function getVipDaysAttribute()
    {
        if ($this->vip_expired_at && $this->vip_expired_at->isFuture()) {
            return (int) ceil($this->vip_expired_at->diffInHours() / 24);
        }

        return 0;
    }

    public function setVipExpiredAtAttribute($value)
    {
        if ($value && $value->isPast()) {
            $value = null;
        }

        if ($value) {
            $value = $this->fromDateTime($value);
        }

        $this->attributes['vip_expired_at'] = $value;
    }

    public static function findExpiredVipUsers($count = 10)
    {
        return static::whereNotNull('vip_expired_at')
            ->where('vip_expired_at', '<=', (string) Carbon::now())
            ->take($count)
            ->get();
    }

    /**
     * Create a new User.
     *
     * @return \App\Models\User
     */
    public static function newUser()
    {
        $user = new static;
        $user->registered_ip = Request::ip();
        $user->registered_at = Carbon::now();

        return $user;
    }

    /**
     * Update user login information.
     *
     * @param  bool  $save
     * @return $this
     */
    public function updateLoginInfo($save = false)
    {
        $this->last_login_ip = Request::ip();
        $this->last_login_at = Carbon::now();
        $this->login_count++;

        if ($save) {
            $this->save();
        }

        return $this;
    }

    /**
     * Check whether the user's VIP has expired.
     *
     * @param  bool  $save
     * @return bool
     */
    public function checkVIP($save = false)
    {
        if ($this->vip_expired_at) {
            if ($this->vip_expired_at->isPast()) {
                $this->vip_expired_at = null;
            }

            if ($save && $this->getOriginal('vip_expired_at') !== $this->attributes['vip_expired_at']) {
                $this->save();
            }
        }

        return $this->is_vip;
    }

    public function joinVIP($days)
    {
        if ($days) {
            $this->vip_expired_at = with(
                $this->vip_expired_at ?
                $this->vip_expired_at->copy() :
                Carbon::now()
            )->addDays($days);

            return $this->save();
        }

        return false;
    }

    public function cancelVIP()
    {
        $this->vip_expired_at = null;

        return $this->save();
    }

    /**
     * Check if the user's VIP status changed, and fire the UserVIPChanged event.
     */
    protected function checkAndFireVIPChangedEvent()
    {
        if ($this->isDirty('vip_expired_at')) {
            $old = $this->getOriginal('vip_expired_at');
            if (! is_null($old)) {
                $old = $this->asDateTime($old);
            }

            event(new UserVIPChanged($this, $old));
        }
    }

    public function updateUserInfoWithSocialUser($social, $user, $save = false)
    {
        $this->updateAvatarFromUrl(SocialAuth::getAvatarFromSocialUser($social, $user));

        $this->username = str_limit(SocialAuth::getUsernameFromSocialUser($social, $user), 10, '');

        if ($save) {
            $this->save();
        }
    }

    /**
     * Upload avatar file for user.
     *
     * @param  \Illuminate\Http\UploadedFile  $file
     * @return bool
     */
    public function uploadAvatar($file)
    {
        return $this->makeAvatar($file);
    }

    /**
     * Make avatar files.
     *
     * @param  mixed  $image @see http://image.intervention.io/api/make
     * @return bool
     */
    protected function makeAvatar($image)
    {
        if ($image instanceof UploadedFile && ! $image->isValid()) {
            return false;
        }

        $avatarPath = $this->getAvatarDirectory().'/';
        $smallAvatarPath = $this->getAvatarDirectory().'/';

        if ($image instanceof UploadedFile) {
            $avatarPath .= $this->createFilenameForUploadedFile($image, 'jpg');
            $smallAvatarPath .= $this->createFilenameForUploadedFile($image, 'jpg');
        } else {
            $avatarPath .= $this->createFilename('jpg');
            $smallAvatarPath .= $this->createFilename('jpg');
        }

        try {
            Image::make($image)->save(public_path($avatarPath), 60);

            Image::make($image)->fit(200, 200, function ($constraint) {
                $constraint->upsize();
            }, 'center')
            ->save(public_path($smallAvatarPath), 60);
        } catch (Exception $e) {
            return false;
        }

        $this->avatar_path = $avatarPath;
        $this->small_avatar_path = $smallAvatarPath;

        return true;
    }

    /**
     * Update user's avatar from a remote URL.
     *
     * @param  string  $url
     * @return bool
     */
    public function updateAvatarFromUrl($url = null)
    {
        if (empty($url)) {
            return false;
        }

        $tmpfile = tmpfile();

        try {
            $response = (new HttpClient)->get($url, [
                'timeout' => 10,
                'connect_timeout' => 5,
                'sink' => $tmpfile,
            ]);
        } catch (Exception $e) {
            fclose($tmpfile);

            return false;
        }

        $makeAvatar = $this->makeAvatar($tmpfile);

        fclose($tmpfile);

        return $makeAvatar;
    }

    /**
     * Get the avatars directory.
     *
     * @return string
     */
    protected function getAvatarDirectory()
    {
        // 根目录下的每个文件夹放100个子文件夹，每个子文件夹放1000个用户的头像文件
        // e.g. 892997 => 8/5c , 129003994 => 50a/3

        $div = $this->id / 100000;
        $rootDir = dechex($div);
        $subDir = dechex(($div - intval($div)) * 100);

        $directory = static::AVATAR_DIRECTORY."/$rootDir/$subDir";

        $path = public_path($directory);
        if (! file_exists($path)) {
            mkdir($path, 0775, true);
        }

        return $directory;
    }

    /**
     * Create a filename for uploaded file instance.
     *
     * @param  \Illuminate\Http\UploadedFile  $file
     * @return string
     */
    protected function createFilenameForUploadedFile($file, $defaultExtension = '')
    {
        $extension = $file->extension();
        $extension = ! empty($extension) ? $extension : $defaultExtension;

        return $this->createFilename().(empty($extension) ? '' : '.'.$extension);
    }

    protected function createFilename($extension = '')
    {
        return $this->id.'-'.str_random().(empty($extension) ? '' : '.'.$extension);
    }

    /**
     * Get the extended image path.
     * e.g. 'path/to/file.jpg' to 'path/to/sm-file.jpg'.
     *
     * @param  string  $path
     * @param  string  $extended
     * @return string
     */
    protected function getExtendedImagePath($path, $extend = 'sm-')
    {
        if (is_null($path)) {
            return;
        }

        $appendPosition = strrpos($path, '/');
        $appendPosition = (false === $appendPosition) ? 0 : $appendPosition + 1;

        return substr_replace($path, $extend, $appendPosition, 0);
    }

    /**
     * Get user's social auth.
     *
     * @param  string|int  $social
     * @return \Illuminate\Database\Eloquent\Collection|\App\Models\SocialAuth
     */
    public function getSocials($social = null)
    {
        return SocialAuth::findByUser($this, $social);
    }

    public function getUserDevices($withTrashed = false)
    {
        return UserDevice::findByUserDevice($this->id, null, $withTrashed);
    }

    public function getDevices($withTrashed = false)
    {
        return Device::whereIn('id', function ($query) use ($withTrashed) {
            $query->select('device_id')->from('user_devices')->where('user_id', $this->id);

            if (! $withTrashed) {
                $query->where('deleted_at', null);
            }
        })->get();
    }

    public function getOrders($withTrashed = false)
    {
        return Order::findByUser($this->id, $withTrashed);
    }

    public function getPromotions($sku = null)
    {
        return Promotion::findByUser($this->id, $sku);
    }
}
