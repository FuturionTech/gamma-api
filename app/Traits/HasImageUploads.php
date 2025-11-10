<?php

namespace App\Traits;

use App\Helpers\FileManagement\FilePath;
use App\Helpers\FileManagement\FileManager;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

trait HasImageUploads
{
    /**
     * Process an image upload (supports both UploadedFile and base64)
     *
     * @param mixed $input The upload input (UploadedFile or base64 string)
     * @param string $uploadPath The S3 path where the file should be stored
     * @param string|null $oldPath The old file path to delete (if updating)
     * @param string $context Context for logging (e.g., 'partner_logo', 'team_avatar')
     * @return string|null The uploaded file path or null on failure
     */
    protected function processImageUpload(
        mixed $input,
        string $uploadPath,
        ?string $oldPath = null,
        string $context = 'image'
    ): ?string {
        try {
            // Handle null input
            if ($input === null) {
                return null;
            }

            $file = null;

            // Handle UploadedFile
            if ($input instanceof UploadedFile) {
                $file = $input;
            }
            // Handle base64 encoded image
            elseif (is_string($input) && Str::startsWith($input, 'data:image')) {
                $file = $this->base64ToUploadedFile($input);
            }

            if (!$file) {
                Log::warning("Invalid upload input for {$context}", [
                    'type' => gettype($input),
                ]);
                return null;
            }

            // Validate file
            if (!$this->validateImageFile($file, $context)) {
                return null;
            }

            // Generate filename
            $extension = $file->getClientOriginalExtension() ?: 'jpg';
            $filename = Str::random(20) . '.' . $extension;
            $fullPath = $uploadPath . '/' . $filename;

            // Upload the file
            $filePath = new FilePath($fullPath);
            $uploadedPath = FileManager::uploadFileAndGetPath($file, $filePath);

            if (!$uploadedPath) {
                Log::error("File upload failed for {$context}", [
                    'path' => $fullPath,
                ]);
                return null;
            }

            // Delete old file if exists
            if ($oldPath) {
                FileManager::deleteFile(new FilePath($oldPath));
                Log::info("Deleted old {$context}", ['path' => $oldPath]);
            }

            Log::info("Successfully uploaded {$context}", [
                'path' => $uploadedPath,
            ]);

            return $uploadedPath;
        } catch (\Exception $e) {
            Log::error("Image upload exception for {$context}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return null;
        }
    }

    /**
     * Validate image file
     *
     * @param UploadedFile $file
     * @param string $context
     * @return bool
     */
    protected function validateImageFile(UploadedFile $file, string $context): bool
    {
        // Check file validity
        if (!$file->isValid()) {
            Log::warning("Invalid file upload for {$context}");
            return false;
        }

        // Check file size (max 10MB)
        $maxSize = 10 * 1024 * 1024; // 10MB in bytes
        if ($file->getSize() > $maxSize) {
            Log::warning("File too large for {$context}", [
                'size' => $file->getSize(),
                'max' => $maxSize,
            ]);
            return false;
        }

        // Check MIME type
        $allowedMimes = [
            'image/jpeg',
            'image/jpg',
            'image/png',
            'image/gif',
            'image/svg+xml',
            'image/webp',
            'image/bmp',
            'image/tiff',
        ];

        if (!in_array($file->getMimeType(), $allowedMimes)) {
            Log::warning("Invalid MIME type for {$context}", [
                'mime' => $file->getMimeType(),
                'allowed' => $allowedMimes,
            ]);
            return false;
        }

        return true;
    }

    /**
     * Convert base64 string to UploadedFile
     *
     * @param string $base64String
     * @return UploadedFile|null
     */
    protected function base64ToUploadedFile(string $base64String): ?UploadedFile
    {
        try {
            // Extract MIME type and data
            if (!preg_match('/^data:image\/(\w+);base64,/', $base64String, $matches)) {
                return null;
            }

            $extension = $matches[1];
            $data = substr($base64String, strpos($base64String, ',') + 1);
            $data = base64_decode($data);

            if ($data === false) {
                return null;
            }

            // Create temporary file with proper path
            $tmpDir = sys_get_temp_dir();
            $tmpPath = $tmpDir . '/' . uniqid('upload_', true) . '.' . $extension;
            file_put_contents($tmpPath, $data);

            // Create UploadedFile
            return new UploadedFile(
                $tmpPath,
                'upload.' . $extension,
                'image/' . $extension,
                null,
                true
            );
        } catch (\Exception $e) {
            Log::error('Base64 conversion failed', [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }
}
