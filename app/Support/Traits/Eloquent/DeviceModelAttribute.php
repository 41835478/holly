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
        return Helper::iDeviceModel($this->platform);
    }
}
