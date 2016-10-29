<?php

namespace App\Models;

use Carbon\Carbon;
use Request;
use Holly\Support\Helper;

class Device extends Model
{
    /**
     * The current device which matches current app client.
     */
    protected static $currentDevice = false;

    protected $dates = ['last_login_at'];

    protected $appends = ['platform_string', 'push_enabled'];

    protected $fillable = ['tdid'];

    public function getPlatformStringAttribute()
    {
        return Helper::iOSPlatform($this->platform);
    }

    public function getPushEnabledAttribute()
    {
        return $this->push_token ? 1 : 0;
    }

    public static function findByTdid($tdid)
    {
        return ! empty($tdid) ? static::where('tdid', $tdid)->first() : null;
    }

    /**
     * Get the current Device instance.
     */
    public static function getCurrentDevice()
    {
        if (false === static::$currentDevice) {
            static::$currentDevice = static::findByTdid(app('AppClient')->tdid);
        }

        return static::$currentDevice;
    }

    /**
     * Get the id for the current Device instance.
     */
    public static function getCurrentDeviceId()
    {
        return ($device = static::getCurrentDevice()) ? $device->id : null;
    }

    /**
     * Fetch device_id for the given tdid.
     */
    public static function fetchDeviceIdForTdid($tdid)
    {
        return static::where('tdid', $tdid)->value('id');
    }

    /**
     * Fetch device_id for the current app client.
     */
    public static function fetchCurrentDeviceId()
    {
        return static::fetchDeviceIdForTdid(app('AppClient')->tdid);
    }

    /**
     * Update or create a Device instance.
     *
     * @param  array  $deviceInfo
     * @return static
     */
    public static function touchDevice($deviceInfo)
    {
        if (! is_array($deviceInfo)) {
            return;
        }

        $deviceInfo = array_filter($deviceInfo);

        if (empty($deviceInfo['tdid']) ||
            $deviceInfo['tdid'] !== app('AppClient')->tdid) {
            return;
        }

        $device = static::firstOrNew(array_only($deviceInfo, 'tdid'));

        $device->acid = str_limit2($deviceInfo['acid'], 40);
        $device->os = str_limit2($deviceInfo['os'], 10);
        $device->os_version = str_limit2($deviceInfo['os_version'], 20);
        $device->platform = str_limit2($deviceInfo['platform'], 20);
        $device->model = str_limit2(array_get($deviceInfo, 'model'), 20);
        $device->name = str_limit2(array_get($deviceInfo, 'name'), 150);
        $device->is_jailbroken = (bool) array_get($deviceInfo, 'jailbroken') ? 1 : 0;
        $device->carrier = str_limit2(array_get($deviceInfo, 'carrier'), 16);
        $device->locale = str_limit2(array_get($deviceInfo, 'locale'), 16);
        $device->network = str_limit2(array_get($deviceInfo, 'network'), 8);
        $device->ssid = str_limit2(array_get($deviceInfo, 'ssid'), 30);
        if (! empty($deviceInfo['push_token'])) {
            $device->push_token = str_limit2($deviceInfo['push_token'], 64);
        }
        $device->did = str_limit2(array_get($deviceInfo, 'did'), 40);
        $device->idfa = str_limit2(array_get($deviceInfo, 'idfa'), 40);
        $device->idfv = str_limit2(array_get($deviceInfo, 'idfv'), 40);
        $screen_width = 0;
        $screen_height = 0;
        if (isset($deviceInfo['screen_size']) && str_contains($deviceInfo['screen_size'], 'x')) {
            $screenSize = explode('x', $deviceInfo['screen_size']);
            if (count($screenSize) == 2) {
                $screen_width = intval($screenSize[0]);
                $screen_height = intval($screenSize[1]);
            }
        }
        $device->screen_width = $screen_width;
        $device->screen_height = $screen_height;
        $device->screen_scale = (float) array_get($deviceInfo, 'screen_scale', 0);
        $device->timezone_gmt = (int) array_get($deviceInfo, 'timezone_gmt', 0);

        if (! $device->exists) {
            $device->login_count = 1;
            $device->registered_ip = Request::ip();
        } else {
            $device->login_count++;
        }

        $device->last_login_at = Carbon::now();
        $device->last_login_ip = Request::ip();

        $device->save();

        static::$currentDevice = $device;

        return $device;
    }

    public static function updatePushTokenForTdid($tdid, $pushToken = null)
    {
        $pushToken = ! empty($pushToken) ? str_limit2($pushToken, 64) : null;

        return static::where('tdid', $tdid)->update(['push_token' => $pushToken]);
    }

    public function getDeviceApps()
    {
        return DeviceApp::findByDeviceId($this->id);
    }

    public function getUsers($withTrashed = false)
    {
        return User::whereIn('id', function ($query) use ($withTrashed) {
            $query->select('user_id')->from('user_devices')->where('device_id', $this->id);

            if (! $withTrashed) {
                $query->where('deleted_at', null);
            }
        })->get();
    }

    public function getDeviceUsers($withTrashed = false)
    {
        return UserDevice::findByUserDevice(null, $this->id, $withTrashed);
    }
}
