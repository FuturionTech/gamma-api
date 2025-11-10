<?php

namespace App\Helpers\FileManagement;

/**
 * Standardized upload path generation for all entities
 */
class UploadPaths
{
    /**
     * Get base path prefix (adds 'dev/' for non-production environments)
     *
     * @return string
     */
    public static function basePath(): string
    {
        $env = config('app.env');
        return ($env === 'local' || $env === 'testing') ? 'dev/' : '';
    }

    /**
     * Generate timestamped filename for cache-busting
     *
     * @param string $type File type/context (e.g., 'logo', 'avatar')
     * @param string $extension File extension without dot
     * @return string
     */
    public static function generateLogoFilename(string $type, string $extension): string
    {
        $timestamp = now()->format('YmdHis');
        return "{$type}_{$timestamp}.{$extension}";
    }

    /**
     * Partner logo upload path
     *
     * @param int $partnerId
     * @param string|null $filename
     * @return string
     */
    public static function PARTNER(int $partnerId, ?string $filename = null): string
    {
        $base = self::basePath() . "partners/{$partnerId}";
        return $filename ? "{$base}/{$filename}" : $base;
    }

    /**
     * Client logo upload path
     *
     * @param int $clientId
     * @param string|null $filename
     * @return string
     */
    public static function CLIENT(int $clientId, ?string $filename = null): string
    {
        $base = self::basePath() . "clients/{$clientId}";
        return $filename ? "{$base}/{$filename}" : $base;
    }

    /**
     * Team profile picture upload path
     *
     * @param int $teamId
     * @param string|null $filename
     * @return string
     */
    public static function TEAM(int $teamId, ?string $filename = null): string
    {
        $base = self::basePath() . "teams/{$teamId}";
        return $filename ? "{$base}/{$filename}" : $base;
    }

    /**
     * Banner image upload path
     *
     * @param int $bannerId
     * @param string|null $filename
     * @return string
     */
    public static function BANNER(int $bannerId, ?string $filename = null): string
    {
        $base = self::basePath() . "banners/{$bannerId}";
        return $filename ? "{$base}/{$filename}" : $base;
    }

    /**
     * Blog post featured image upload path
     *
     * @param int $postId
     * @param string|null $filename
     * @return string
     */
    public static function BLOG_POST(int $postId, ?string $filename = null): string
    {
        $base = self::basePath() . "blog/posts/{$postId}";
        return $filename ? "{$base}/{$filename}" : $base;
    }

    /**
     * Project featured image upload path
     *
     * @param int $projectId
     * @param string|null $filename
     * @return string
     */
    public static function PROJECT(int $projectId, ?string $filename = null): string
    {
        $base = self::basePath() . "projects/{$projectId}";
        return $filename ? "{$base}/{$filename}" : $base;
    }

    /**
     * Testimonial image upload path
     *
     * @param int $testimonialId
     * @param string|null $filename
     * @return string
     */
    public static function TESTIMONIAL(int $testimonialId, ?string $filename = null): string
    {
        $base = self::basePath() . "testimonials/{$testimonialId}";
        return $filename ? "{$base}/{$filename}" : $base;
    }

    /**
     * Solution hero image upload path
     *
     * @param int $solutionId
     * @param string|null $filename
     * @return string
     */
    public static function SOLUTION(int $solutionId, ?string $filename = null): string
    {
        $base = self::basePath() . "solutions/{$solutionId}";
        return $filename ? "{$base}/{$filename}" : $base;
    }
}
