<?php

namespace App\Support\Eloquent\Traits;

use App\Support\Image\Filters\Fit;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Image;
use Symfony\Component\HttpFoundation\File\MimeType\ExtensionGuesser;
use Symfony\Component\HttpFoundation\File\UploadedFile;

trait ImageStorage
{
    /**
     * Store image file for the given attribute.
     *
     * @param  mixed  $file
     * @param  string  $attribute
     * @return string|null  The stored path
     */
    protected function storeImageFile($file, $attribute)
    {
        if ($file instanceof UploadedFile && ! $file->isValid()) {
            return;
        }

        try {
            $image = app('image')
                ->make($file)
                ->filter($this->getImageFilter($attribute))
                ->encode(
                    $this->getImageFormat($attribute),
                    $this->getImageQuality($attribute)
                );
        } catch (\Exception $e) {
            return;
        }

        $filename = trim($this->getImageDirectory($attribute), '/').'/'.
            md5((string) $image).$this->getFileExtensionForMIME($image->mime());

        if (Storage::disk($this->getFilesystemDisk($attribute))->put($filename, (string) $image)) {
            return $filename;
        }
    }

    /**
     * Get image filter for the given attribute.
     *
     * @see http://image.intervention.io/api/filter
     *
     * @param  string  $attribute
     */
    protected function getImageFilter($attribute)
    {
        return (new Fit)->width($this->getImageSize($attribute));
    }

    /**
     * Get the disk name of Filesystem for the given attribute.
     *
     * @param  string  $attribute
     * @return string
     */
    protected function getFilesystemDisk($attribute)
    {
        return 'public';
    }

    /**
     * Get image format for the given attribute.
     *
     * @see http://image.intervention.io/api/encode
     *
     * @param  string  $attribute
     * @return string|null
     */
    protected function getImageFormat($attribute)
    {
        return null;
    }

    /**
     * Get image quality for the given attribute.
     *
     * @see http://image.intervention.io/api/encode
     *
     * @param  string  $attribute
     * @return int
     */
    protected function getImageQuality($attribute)
    {
        return 90;
    }

    /**
     * Get image size for the given attribute.
     *
     * @param  string  $attribute
     * @return int
     */
    protected function getImageSize($attribute)
    {
        return constant('static::'.strtoupper($attribute).'_SIZE') ?: 200;
    }

    /**
     * Get image directory for the given attribute.
     *
     * @param  string  $attribute
     * @return string
     */
    protected function getImageDirectory($attribute)
    {
        return 'images/'.date('Y/m');
    }

    /**
     * Get file extension for image.
     *
     * @param  string  $mime
     * @return string|null
     */
    protected function getFileExtensionForMIME($mime)
    {
        $extension = ExtensionGuesser::getInstance()->guess($mime);

        if ($extension === 'jpeg') {
            $extension = 'jpg';
        }

        if (! empty($extension)) {
            return '.'.$extension;
        }
    }
}
