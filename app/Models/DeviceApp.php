<?php

namespace App\Models;

class DeviceApp extends Model
{
    public $timestamps = false;

    public static function touchDeviceApp($device_id, $appInfo)
    {
        if ($device_id &&
            $identifier = str_limit2(array_get($appInfo, 'app_identifier'), 191)
        ) {
            $version = str_limit2(array_get($appInfo, 'app_version'), 20);
            $channel = str_limit2(array_get($appInfo, 'app_channel'), 20);

            static::unguard();

            $instance = static::updateOrCreate(
                compact('device_id', 'identifier'),
                compact('version', 'channel')
            );

            static::reguard();

            return $instance;
        }
    }

    public static function findByDeviceId($device_id, $app_identifier = null)
    {
        $query = static::where('device_id', $device_id);

        if (! is_null($app_identifier)) {
            return $query->where('identifier', $app_identifier)->first();
        }

        return $query->get();
    }
}
