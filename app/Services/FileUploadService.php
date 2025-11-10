<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use GraphQL\Error\Error;

class FileUploadService
{
    /**
     * Allowed mime types for upload
     */
    private const ALLOWED_IMAGE_MIMES = [
        'image/jpeg',
        'image/jpg',
        'image/png',
        'image/svg+xml',
        'image/webp',
    ];

    private const ALLOWED_FILE_MIMES = [
        'application/pdf',
        'image/jpeg',
        'image/jpg',
        'image/png',
        'image/svg+xml',
    ];

    /**
     * Maximum file size in kilobytes
     */
    private const MAX_FILE_SIZE = 5120; // 5MB

    /**
     * Upload an image to S3
     *
     * @param UploadedFile $file
     * @param string $directory Directory within uploads/ (e.g., 'services', 'teams')
     * @param string|null $oldPath Path to old file to delete
     * @return string The S3 path (not full URL)
     */
    public function uploadImage(UploadedFile $file, string $directory, ?string $oldPath = null): string
    {
        $this->validateImageFile($file);

        // Delete old file if exists
        if ($oldPath) {
            $this->deleteFile($oldPath);
        }

        // Generate unique filename
        $filename = $this->generateFileName($file);
        $path = "uploads/{$directory}/{$filename}";

        // Upload to S3
        Storage::disk('s3')->put($path, file_get_contents($file->getRealPath()), 'public');

        return $path;
    }

    /**
     * Upload a file to S3
     *
     * @param UploadedFile $file
     * @param string $directory
     * @param string|null $oldPath
     * @return string
     */
    public function uploadFile(UploadedFile $file, string $directory, ?string $oldPath = null): string
    {
        $this->validateFile($file);

        // Delete old file if exists
        if ($oldPath) {
            $this->deleteFile($oldPath);
        }

        // Generate unique filename
        $filename = $this->generateFileName($file);
        $path = "uploads/{$directory}/{$filename}";

        // Upload to S3
        Storage::disk('s3')->put($path, file_get_contents($file->getRealPath()), 'public');

        return $path;
    }

    /**
     * Delete a file from S3
     *
     * @param string $path
     * @return bool
     */
    public function deleteFile(string $path): bool
    {
        if (Storage::disk('s3')->exists($path)) {
            return Storage::disk('s3')->delete($path);
        }

        return false;
    }

    /**
     * Get public URL for a file
     *
     * @param string $path
     * @return string
     */
    public function getPublicUrl(string $path): string
    {
        return Storage::disk('s3')->url($path);
    }

    /**
     * Validate image file
     *
     * @param UploadedFile $file
     * @return void
     * @throws Error
     */
    private function validateImageFile(UploadedFile $file): void
    {
        // Check mime type
        if (!in_array($file->getMimeType(), self::ALLOWED_IMAGE_MIMES)) {
            throw new Error('Invalid file type. Allowed types: JPG, PNG, SVG, WebP');
        }

        // Check file size
        if ($file->getSize() > self::MAX_FILE_SIZE * 1024) {
            throw new Error('File size exceeds maximum allowed size of ' . self::MAX_FILE_SIZE . 'KB');
        }
    }

    /**
     * Validate general file
     *
     * @param UploadedFile $file
     * @return void
     * @throws Error
     */
    private function validateFile(UploadedFile $file): void
    {
        // Check mime type
        if (!in_array($file->getMimeType(), self::ALLOWED_FILE_MIMES)) {
            throw new Error('Invalid file type. Allowed types: JPG, PNG, SVG, PDF');
        }

        // Check file size
        if ($file->getSize() > self::MAX_FILE_SIZE * 1024) {
            throw new Error('File size exceeds maximum allowed size of ' . self::MAX_FILE_SIZE . 'KB');
        }
    }

    /**
     * Generate unique filename
     *
     * @param UploadedFile $file
     * @return string
     */
    private function generateFileName(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        return time() . '_' . Str::random(10) . '.' . $extension;
    }
}

