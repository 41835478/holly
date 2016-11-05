<?php

namespace App\Support\Image\Templates;

use Intervention\Image\Image;
use Intervention\Image\Filters\FilterInterface;

class Resize implements FilterInterface
{
    /**
     * The new width of the image.
     *
     * @var int
     */
    protected $width = 300;

    /**
     * The new height of the image.
     *
     * @var int
     */
    protected $height = 300;

    /**
     * Applies filter to the given image.
     *
     * @param  \Intervention\Image\Image $image
     * @return \Intervention\Image\Image
     */
    public function applyFilter(Image $image)
    {
        return $image->orientate()->resize($this->width, $this->height, function ($constraint) {
            $this->constraintResize($constraint);
        });
    }

    /**
     * Constraint the resize.
     *
     * @param  \Intervention\Image\Constraint  $constraint
     * @return void
     */
    protected function constraintResize($constraint)
    {
        $constraint->aspectRatio();
        $constraint->upsize();
    }
}
