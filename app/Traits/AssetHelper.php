<?php

namespace App\Traits;

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
        return Storage::disk(
            method_exists($this, 'getFilesystemDisk') ? $this->getFilesystemDisk($identifier) : null
        );
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
            return null;
        }

        if (filter_var($path, FILTER_VALIDATE_URL) !== false) {
            return $path;
        }

        return asset_url($this->getFilesystem($identifier)->url($path));
    }
}
