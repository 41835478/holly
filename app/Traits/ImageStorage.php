<?php

namespace App\Traits;

use App\Support\Image\Filters\Fit;
use Exception;
use Holly\Support\Helper;
use Intervention\Image\Image;
use Symfony\Component\HttpFoundation\File\UploadedFile;

trait ImageStorage
{
    use AssetHelper;

    /**
     * Store image file for the given identifier.
     *
     * @param  mixed  $file
     * @param  string  $identifier
     * @return string|null  The stored path
     */
    protected function storeImageFile($file, $identifier)
    {
        if ($file instanceof UploadedFile && ! $file->isValid()) {
            return;
        }

        try {
            $image = app('image')
                ->make($file)
                ->filter($this->getImageFilter($identifier))
                ->encode(
                    $this->getImageFormat($identifier),
                    $this->getImageQuality($identifier)
                );
        } catch (Exception $e) {
            return;
        }

        $path = trim($this->getImageDirectory($identifier), '/').'/'.
            md5((string) $image).Helper::fileExtensionForMimeType($image->mime());

        if ($this->getFilesystem($identifier)->put($path, (string) $image)) {
            return $path;
        }
    }

    /**
     * Get image filter for the given identifier.
     *
     * @see http://image.intervention.io/api/filter
     *
     * @param  string  $identifier
     */
    protected function getImageFilter($identifier)
    {
        return (new Fit)->width($this->getImageSize($identifier));
    }

    /**
     * Get the disk name of Filesystem for the given identifier.
     *
     * @param  string|null  $identifier
     * @return string
     */
    protected function getFilesystemDisk($identifier = null)
    {
        return 'public';
    }

    /**
     * Get image format for the given identifier.
     *
     * @see http://image.intervention.io/api/encode
     *
     * @param  string  $identifier
     * @return string|null
     */
    protected function getImageFormat($identifier)
    {
    }

    /**
     * Get image quality for the given identifier.
     *
     * @see http://image.intervention.io/api/encode
     *
     * @param  string  $identifier
     * @return int
     */
    protected function getImageQuality($identifier)
    {
        return 90;
    }

    /**
     * Get image size for the given identifier.
     *
     * @param  string  $identifier
     * @return int
     */
    protected function getImageSize($identifier)
    {
        if (defined($constant = 'static::'.strtoupper($identifier).'_SIZE')) {
            return constant($constant);
        }

        return 1024;
    }

    /**
     * Get image directory for the given identifier.
     *
     * @param  string  $identifier
     * @return string
     */
    protected function getImageDirectory($identifier)
    {
        return 'images/'.date('Y/m');
    }
}
