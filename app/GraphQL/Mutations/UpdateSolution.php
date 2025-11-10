<?php

namespace App\GraphQL\Mutations;

use App\Models\Solution;
use App\Traits\HasImageUploads;
use App\Helpers\FileManagement\UploadPaths;

class UpdateSolution
{
    use HasImageUploads;

    /**
     * Update a solution with optional hero image upload
     */
    public function __invoke($rootValue, array $args)
    {
        $id = $args['id'];
        $input = $args['input'];

        $solution = Solution::findOrFail($id);
        $oldImagePath = $solution->hero_image_url;

        // Update solution fields
        if (isset($input['title'])) {
            $solution->title = $input['title'];
        }

        if (isset($input['subtitle'])) {
            $solution->subtitle = $input['subtitle'];
        }

        if (isset($input['description'])) {
            $solution->description = $input['description'];
        }

        if (isset($input['slug'])) {
            $solution->slug = $input['slug'];
        }

        if (isset($input['industry_category'])) {
            $solution->industry_category = $input['industry_category'];
        }

        if (isset($input['icon'])) {
            $solution->icon = $input['icon'];
        }

        if (isset($input['icon_color'])) {
            $solution->icon_color = $input['icon_color'];
        }

        if (isset($input['hero_image_url'])) {
            $solution->hero_image_url = $input['hero_image_url'];
        }

        if (isset($input['order'])) {
            $solution->order = $input['order'];
        }

        if (isset($input['is_active'])) {
            $solution->is_active = $input['is_active'];
        }

        $solution->save();

        // Process hero image upload if provided
        if (isset($input['hero_image']) && $input['hero_image']) {
            $uploadPath = UploadPaths::SOLUTION($solution->id);
            $imagePath = $this->processImageUpload(
                $input['hero_image'],
                $uploadPath,
                $oldImagePath,
                'solution_hero_image'
            );

            if ($imagePath) {
                $solution->hero_image_url = $imagePath;
                $solution->save();
            }
        }

        return $solution->fresh();
    }
}
