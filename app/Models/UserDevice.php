<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserDevice extends Model
{
    use SoftDeletes;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'device_id'];

    /**
     * Touch UserDevice model.
     *
     * @param  int  $user_id
     * @param  int  $device_id
     * @return static|null
     */
    public static function touchUserDevice($user_id, $device_id)
    {
        if ($user_id && $device_id) {
            $instance = static::withTrashed()
                ->firstOrNew(compact('user_id', 'device_id'));

            if ($instance->trashed()) {
                $instance->restore();
            }

            $instance->touch();

            return $instance;
        }
    }

    /**
     * Fetch records.
     *
     * @param  int|null  $userId
     * @param  int|null  $deviceId
     * @param  bool $withTrashed
     * @return mixed
     */
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

    /**
     * Delete model.
     *
     * @param  int  $userId
     * @param  init  $deviceId
     * @return bool|null
     */
    public static function deleteUserDevice($userId, $deviceId)
    {
        if ($userId &&
            $deviceId &&
            $instance = static::findByUserDevice($userId, $deviceId)) {
            $instance->delete();
        }
    }
}
