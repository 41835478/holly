<?php

namespace App\Support\Traits\Eloquent;

use App\Support\Helper;

trait DeviceModelAttribute
{
    /**
     * Get the `device_model` attribute.
     *
     * @return string|null
     */
    public function getDeviceModelAttribute()
    {
        $platform = property_exists($this, 'deviceModelKey') ? $this->deviceModelKey : 'platform';

        return Helper::iDeviceModel($this->{$platform});
    }
}
