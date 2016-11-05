<?php

namespace App\Support\Intervention\Image\Templates;

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
        return $image->orientate()
            ->fit($this->width, $this->height, function ($constraint) {
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
}
