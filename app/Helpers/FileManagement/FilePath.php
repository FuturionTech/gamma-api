<?php

namespace App\Helpers\FileManagement;

/**
 * Value object representing a file path configuration for uploads
 */
class FilePath
{
    public string $path;
    public bool $secured;
    public bool $encrypted;

    public function __construct(
        string $path,
        bool $secured = false,
        bool $encrypted = false
    ) {
        $this->path = $path;
        $this->secured = $secured;
        $this->encrypted = $encrypted;
    }
}
