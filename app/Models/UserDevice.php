<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class UserDevice extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = ['user_id', 'device_id'];

    public static function touchUserDevice($user_id, $device_id)
    {
        if ($user_id && $device_id) {
            $instance = static::withTrashed()->firstOrNew(compact('user_id', 'device_id'));

            if ($instance->trashed()) {
                $instance->restore();
            } else {
                $instance->touch();
            }

            return $instance;
        }
    }

    public static function findByUserDevice($userId = null, $deviceId = null, $withTrashed = false)
    {
        $query = static::query();

        if (! is_null($userId)) {
            if (is_object($userId)) {
                $userId = $userId->id;
            }

            $query->where('user_id', $userId);
        }

        if (! is_null($deviceId)) {
            if (is_object($deviceId)) {
                $deviceId = $deviceId->id;
            }

            $query->where('device_id', $deviceId);
        }

        if ($withTrashed) {
            $query->withTrashed();
        }

        if (! is_null($userId) && ! is_null($deviceId)) {
            return $query->first();
        }

        return $query->orderBy('updated_at', 'desc')->get();
    }

    public static function deleteUserDevice($userId, $deviceId)
    {
        if ($userId &&
            $deviceId &&
            $instance = static::findByUserDevice($userId, $deviceId)) {
            $instance->delete();
        }
    }
}
