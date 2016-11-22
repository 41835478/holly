<?php

namespace App\Traits;

trait MagicPropertySetter
{
    /**
     * Handle dynamic method calls to set property.
     *
     * @param  string  $method
     * @param  array $parameters
     * @return $this
     */
    public function __call($method, $parameters)
    {
        if (property_exists($this, $method) && count($parameters) > 0) {
            $this->{$method} = $parameters[0];
        }

        return $this;
    }
}
