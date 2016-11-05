<?php

namespace App\Support\Image\Templates;

use Intervention\Image\Image;
use Intervention\Image\Filters\FilterInterface;

class Fit implements FilterInterface
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
     * @var int|null
     */
    protected $height = null;

    /**
     * The position where cutout will be positioned.
     *
     * @var string
     */
    protected $position = 'center';

    /**
     * Keep image from being upsized.
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
            $this->constraintFit($constraint);
        }, $this->position);
    }

    /**
     * Constraint the fit.
     *
     * @param  \Intervention\Image\Constraint  $constraint
     * @return void
     */
    protected function constraintFit($constraint)
    {
        $constraint->upsize();
    }
}
