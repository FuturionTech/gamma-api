<?php

namespace App\Helpers\FileManagement;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

/**
 * Core file management operations for S3 uploads
 */
class FileManager
{
    /**
     * Upload a file and get its relative path
     *
     * @param UploadedFile $file
     * @param FilePath $filePathInfo
     * @return string|null The relative path to the uploaded file
     */
    public static function uploadFileAndGetPath(UploadedFile $file, FilePath $filePathInfo): ?string
    {
        try {
            $path = $filePathInfo->path;

            // Replace spaces with underscores in filename
            $path = str_replace(' ', '_', $path);

            // Get the configured default disk
            $disk = config('filesystems.default');

            // Upload to storage
            $uploaded = Storage::disk($disk)->put($path, file_get_contents($file->getRealPath()), [
                'visibility' => 'public',
            ]);

            if (!$uploaded) {
                Log::error('File upload failed', ['path' => $path]);
                return null;
            }

            return $path;
        } catch (\Exception $e) {
            Log::error('File upload exception', [
                'path' => $filePathInfo->path,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Upload a file and get its public URL
     *
     * @param UploadedFile $file
     * @param FilePath $filePathInfo
     * @return string|null The public URL to the uploaded file
     */
    public static function uploadFileAndGetUrl(UploadedFile $file, FilePath $filePathInfo): ?string
    {
        $path = self::uploadFileAndGetPath($file, $filePathInfo);

        if (!$path) {
            return null;
        }

        return self::getPublicUrl(new FilePath($path));
    }

    /**
     * Get public URL for a file path
     *
     * @param FilePath $filePathInfo
     * @return string
     */
    public static function getPublicUrl(FilePath $filePathInfo): string
    {
        $disk = config('filesystems.default');
        return Storage::disk($disk)->url($filePathInfo->path);
    }

    /**
     * Delete a file from storage
     *
     * @param FilePath $filePathInfo
     * @return bool
     */
    public static function deleteFile(FilePath $filePathInfo): bool
    {
        try {
            $disk = config('filesystems.default');
            if (self::fileExists($filePathInfo)) {
                return Storage::disk($disk)->delete($filePathInfo->path);
            }
            return true;
        } catch (\Exception $e) {
            Log::error('File deletion failed', [
                'path' => $filePathInfo->path,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Check if a file exists in storage
     *
     * @param FilePath $filePathInfo
     * @return bool
     */
    public static function fileExists(FilePath $filePathInfo): bool
    {
        try {
            $disk = config('filesystems.default');
            return Storage::disk($disk)->exists($filePathInfo->path);
        } catch (\Exception $e) {
            Log::error('File existence check failed', [
                'path' => $filePathInfo->path,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Get a pre-signed URL for temporary access (S3 only)
     *
     * @param FilePath $filePathInfo
     * @param int $expiry Expiry time in minutes (default 60)
     * @return string
     */
    public static function getPreSignedUrl(FilePath $filePathInfo, int $expiry = 60): string
    {
        $disk = config('filesystems.default');

        // Pre-signed URLs only work with S3, for local disk return regular URL
        if ($disk === 's3') {
            return Storage::disk($disk)->temporaryUrl(
                $filePathInfo->path,
                now()->addMinutes($expiry)
            );
        }

        return self::getPublicUrl($filePathInfo);
    }
}
