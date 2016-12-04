<?php

namespace App\Support\Traits;

use Illuminate\Support\Facades\Storage;

trait AssetHelper
{
    /**
     * Get Filesystem instance for the given identifier.
     *
     * @param  string|null  $identifier
     * @return \Illuminate\Contracts\Filesystem\Filesystem
     */
    protected function getFilesystem($identifier = null)
    {
        return Storage::disk($this->getFilesystemDisk($identifier));
    }

    /**
     * Get asset URL.
     *
     * @param  string  $path
     * @param  string|null  $identifier
     * @return string|null
     */
    protected function getAssetUrl($path, $identifier = null)
    {
        if (empty($path)) {
            return;
        }

        if (filter_var($path, FILTER_VALIDATE_URL) !== false) {
            return $path;
        }

        return asset_url($this->getFilesystem($identifier)->url($path));
    }

    /**
     * Get disk name for filesystem.
     *
     * @param  string|null  $identifier
     * @return string|null
     */
    protected function getFilesystemDisk($identifier = null)
    {
        return 'public';
    }
}
