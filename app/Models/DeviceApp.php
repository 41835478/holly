<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceApp extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'device_id', 'identifier', 'version', 'channel',
    ];

    /**
     * Create / Update a DeviceApp model.
     *
     * @param  int  $device_id
     * @param  array $appInfo
     * @return static
     */
    public static function touchDeviceApp($device_id, $appInfo)
    {
        return static::updateOrCreate([
                'device_id' => $device_id,
                'identifier' => $appInfo['app_identifier'],
            ], [
                'version' => $appInfo['app_version'],
                'channel' => $appInfo['app_channel'],
            ]);
    }

    /**
     * Fetch DeviceApp models.
     *
     * @param  int  $device_id
     * @param  string|null  $identifier
     * @return mixed
     */
    public static function findByDeviceId($device_id, $identifier = null)
    {
        $query = static::where('device_id', $device_id);

        if (! is_null($identifier)) {
            return $query->where('identifier', $identifier)->first();
        }

        return $query->get();
    }
}
