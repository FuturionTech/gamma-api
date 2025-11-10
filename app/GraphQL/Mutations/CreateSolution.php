<?php

namespace App\GraphQL\Mutations;

use App\Models\Solution;
use App\Traits\HasImageUploads;
use App\Helpers\FileManagement\UploadPaths;
use Illuminate\Support\Str;

class CreateSolution
{
    use HasImageUploads;

    /**
     * Create a new solution with optional hero image upload
     */
    public function __invoke($rootValue, array $args)
    {
        $input = $args['input'];

        // Create solution first to get ID
        $solution = Solution::create([
            'title' => $input['title'],
            'subtitle' => $input['subtitle'] ?? null,
            'description' => $input['description'] ?? null,
            'slug' => $input['slug'] ?? Str::slug($input['title']),
            'industry_category' => $input['industry_category'] ?? null,
            'icon' => $input['icon'] ?? null,
            'icon_color' => $input['icon_color'] ?? null,
            'hero_image_url' => $input['hero_image_url'] ?? null,
            'order' => $input['order'] ?? 0,
            'is_active' => $input['is_active'] ?? true,
        ]);

        // Process hero image upload if provided
        if (isset($input['hero_image']) && $input['hero_image']) {
            $uploadPath = UploadPaths::SOLUTION($solution->id);
            $imagePath = $this->processImageUpload(
                $input['hero_image'],
                $uploadPath,
                null,
                'solution_hero_image'
            );

            if ($imagePath) {
                $solution->hero_image_url = $imagePath;
                $solution->save();
            }
        }

        return $solution;
    }
}
