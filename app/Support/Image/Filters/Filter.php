<?php

namespace App\Support\Image\Filters;

use ErrorException;
use Intervention\Image\Filters\FilterInterface;

abstract class Filter implements FilterInterface
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

            return $this;
        }

        throw new ErrorException('Call to undefined method '.get_class($this)."::{$method}()");
    }
}
