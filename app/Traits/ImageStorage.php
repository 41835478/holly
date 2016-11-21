<?php

namespace App\Traits;

use App\Support\Image\Filters\Resize;
use Exception;
use Holly\Support\Helper;
use Intervention\Image\Image;
use Symfony\Component\HttpFoundation\File\UploadedFile;

trait ImageStorage
{
    use AssetHelper;

    /**
     * Store image file.
     *
     * @param  mixed  $file
     * @param  string|null  $identifier
     * @return string|null  The stored path
     */
    protected function storeImageFile($file, $identifier = null)
    {
        if ($file instanceof UploadedFile && ! $file->isValid()) {
            return;
        }

        try {
            $image = app('image')
                ->make($file)
                ->filter($this->getImageFilter($identifier))
                ->encode(
                    $this->getImageEncodingFormat($identifier),
                    $this->getImageEncodingQuality($identifier)
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
     * Get image filter.
     *
     * @see http://image.intervention.io/api/filter
     *
     * @param  string|null  $identifier
     */
    protected function getImageFilter($identifier = null)
    {
        return (new Resize)->width(1024);
    }

    /**
     * Get image encoding format.
     *
     * @see http://image.intervention.io/api/encode
     *
     * @param  string|null  $identifier
     * @return string|null
     */
    protected function getImageEncodingFormat($identifier = null)
    {
        return;
    }

    /**
     * Get image encoding quality.
     *
     * @see http://image.intervention.io/api/encode
     *
     * @param  string|null  $identifier
     * @return int
     */
    protected function getImageEncodingQuality($identifier = null)
    {
        return 90;
    }

    /**
     * Get image directory.
     *
     * @param  string|null  $identifier
     * @return string
     */
    protected function getImageDirectory($identifier = null)
    {
        return 'images/'.date('Y/m');
    }

    /**
     * Get the disk name of Filesystem.
     *
     * @see AssetHelper::getFilesystem()
     *
     * @param  string|null  $identifier
     * @return string
     */
    protected function getFilesystemDisk($identifier = null)
    {
        return 'public';
    }
}
