<?php

namespace App\Support\Image\Filters;

use Intervention\Image\Image;
use Intervention\Image\Filters\FilterInterface;

class Fit implements FilterInterface
{
    /**
     * The width the image will be resized to after cropping out
     * the best fitting aspect ratio.
     *
     * @var int
     */
    protected $width = 200;

    /**
     * The height the image will be resized to after cropping out
     * the best fitting aspect ratio. If no height is given, method
     * will use same value as width.
     *
     * @var int|null
     */
    protected $height = null;

    /**
     * The position where cutout will be positioned.
     *
     * @see http://image.intervention.io/api/fit
     *
     * @var string
     */
    protected $position = 'center';

    /**
     * Determines whether keeping the image from being upsized.
     *
     * @var bool
     */
    protected $upsize = true;

    /**
     * Applies filter to the given image.
     *
     * @param  \Intervention\Image\Image $image
     * @return \Intervention\Image\Image
     */
    public function applyFilter(Image $image)
    {
        return $image->orientate()->fit($this->width, $this->height, function ($constraint) {
            $this->constraint($constraint);
        }, $this->position);
    }

    /**
     * Constraints the filter.
     *
     * @param  \Intervention\Image\Constraint  $constraint
     * @return void
     */
    protected function constraint($constraint)
    {
        if ($this->upsize) {
            $constraint->upsize();
        }
    }

    /**
     * Handle dynamic method calls to set properties.
     *
     * @return $this
     */
    public function __call($method, $parameters)
    {
        if (! property_exists($this, $method)) {
            throw new \InvalidArgumentException("Property '{$method}' does not exist.");
        }

        if (count($parameters) < 1) {
            throw new \InvalidArgumentException("Method '{$method}()' requires at least one argument.");
        }

        $this->{$method} = $parameters[0];

        return $this;
    }
}
