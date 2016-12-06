<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;
use Iatstuti\Database\Support\NullableFields;
use App\Support\Traits\Eloquent\DeviceModelAttribute;

class Device extends Model
{
    use DeviceModelAttribute, NullableFields;

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'device_model', 'push_enabled',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'tdid', 'did', 'os', 'os_version', 'platform', 'model', 'name',
        'jailbroken', 'carrier', 'locale', 'network', 'ssid',
        'push_token', 'idfa', 'idfv', 'screen_width', 'screen_height',
        'screen_scale', 'timezone_gmt',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['last_login_at'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'jailbroken' => 'boolean',
        'screen_width' => 'integer',
        'screen_height' => 'integer',
        'screen_scale' => 'float',
        'timezone_gmt' => 'integer',
        'login_count' => 'integer',
    ];

    /**
     * The attributes that should be saved as null when empty.
     *
     * @var array
     */
    protected $nullable = [
        'did', 'model', 'name', 'carrier', 'locale', 'network', 'ssid',
        'push_token', 'idfa', 'idfv', 'last_login_ip', 'registered_ip',
    ];

    /**
     * The model's attributes.
     *
     * @var array
     */
    protected $attributes = [
        'jailbroken' => 0,
        'screen_width' => 0,
        'screen_height' => 0,
        'screen_scale' => 0,
        'timezone_gmt' => 0,
        'login_count' => 0,
    ];

    /**
     * The device that matches the current app client.
     *
     * @var static|bool
     */
    protected static $clientDevice = false;

    /**
     * Get the `push_enabled` attribute.
     *
     * @return bool
     */
    public function getPushEnabledAttribute()
    {
        return ! is_null($this->push_token);
    }

    /**
     * Find a Device instance.
     *
     * @param  string  $tdid
     * @return static|null
     */
    public static function findByTdid($tdid)
    {
        return ! empty($tdid) ? static::where('tdid', $tdid)->first() : null;
    }

    /**
     * Fetch device id for the given tdid.
     *
     * @param  string  $tdid
     * @return int|null
     */
    public static function fetchDeviceIdForTdid($tdid)
    {
        return static::where('tdid', $tdid)->value('id');
    }

    /**
     * Get the device for the current app client.
     *
     * @return static|null
     */
    public static function getClientDevice()
    {
        if (false === static::$clientDevice) {
            static::$clientDevice = static::findByTdid(app('client')->tdid);
        }

        return static::$clientDevice;
    }

    /**
     * Set the device for the current app client.
     *
     * @param  static  $device
     * @return static
     */
    public static function setClientDevice($device)
    {
        return static::$clientDevice = $device;
    }

    /**
     * Get the id for the client device.
     *
     * @return int|null
     */
    public static function getClientDeviceId()
    {
        if ($device = static::getClientDevice()) {
            return $device->id;
        }
    }

    /**
     * Update or create a Device instance.
     *
     * @param  array  $data
     * @return static
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public static function touchDevice(array $data)
    {
        $tdid = array_get($data, 'tdid');

        if (empty($tdid) || $tdid !== app('client')->tdid) {
            abort(403);
        }

        $device = static::firstOrNew(compact('tdid'));

        if (isset($data['screen_size']) && str_contains($data['screen_size'], 'x')) {
            $screenSize = explode('x', $data['screen_size']);
            if (count($screenSize) == 2) {
                $data['screen_width'] = (int) $screenSize[0];
                $data['screen_height'] = (int) $screenSize[1];
            }
        }

        $device->fill($data);

        $device->login_count++;
        $device->last_login_at = $device->freshTimestamp();
        $device->last_login_ip = Request::ip();

        if (! $device->exists) {
            $device->registered_ip = $device->last_login_ip;
        }

        $device->save();

        return static::setClientDevice($device);
    }

    /**
     * Update push token for the device.
     *
     * @param  string  $tdid
     * @param  string|null $push_token
     * @return bool
     */
    public static function updatePushTokenForTdid($tdid, $push_token = null)
    {
        return static::where('tdid', $tdid)->update(compact('push_token'));
    }

    /**
     * Get DeviceApp models.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getDeviceApps()
    {
        return DeviceApp::findByDeviceId($this->id);
    }

    /**
     * Get UserDevice models.
     *
     * @param  bool  $withTrashed
     * @return mixed
     */
    public function getDeviceUsers($withTrashed = false)
    {
        return UserDevice::findByUserDevice(null, $this->id, $withTrashed);
    }

    /**
     * Get users to this device.
     *
     * @param  bool  $withTrashed
     * @return \Illuminate\Support\Collection
     */
    public function getUsers($withTrashed = false)
    {
        return User::whereIn('id', function ($query) use ($withTrashed) {
            $query->select('user_id')->from('user_devices')->where('device_id', $this->id);

            if (! $withTrashed) {
                $query->where('deleted_at', null);
            }
        })->get();
    }
}
