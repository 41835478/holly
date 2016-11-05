<?php

namespace App\Support\Intervention\Image\Templates;

use Intervention\Image\Image;
use Intervention\Image\Filters\FilterInterface;

class Resize implements FilterInterface
{
    /**
     * The new width of the image.
     *
     * @var int
     */
    protected $width = 200;

    /**
     * The new height of the image.
     *
     * @var int
     */
    protected $height = 200;

    /**
     * Determines whether constrainting the current aspect-ratio of the image.
     *
     * @var bool
     */
    protected $aspectRatio = true;

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
            ->resize($this->width, $this->height, function ($constraint) {
                    $this->constraint($constraint);
                });
    }

    /**
     * Constraints the filter.
     *
     * @param  \Intervention\Image\Constraint  $constraint
     * @return void
     */
    protected function constraint($constraint)
    {
        if ($this->aspectRatio) {
            $constraint->aspectRatio();
        }

        if ($this->upsize) {
            $constraint->upsize();
        }
    }
}
