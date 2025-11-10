<?php

namespace App\GraphQL\Mutations;

use App\Models\Banner;
use App\Traits\HasImageUploads;
use App\Helpers\FileManagement\UploadPaths;

class UpdateBanner
{
    use HasImageUploads;

    /**
     * Update a banner with optional image upload
     */
    public function __invoke($rootValue, array $args)
    {
        $id = $args['id'];
        $input = $args['input'];

        $banner = Banner::findOrFail($id);
        $oldImagePath = $banner->image_url;

        // Update banner fields
        if (isset($input['title'])) {
            $banner->title = $input['title'];
        }

        if (isset($input['subtitle'])) {
            $banner->subtitle = $input['subtitle'];
        }

        if (isset($input['image_url'])) {
            $banner->image_url = $input['image_url'];
        }

        if (isset($input['cta_text'])) {
            $banner->cta_text = $input['cta_text'];
        }

        if (isset($input['cta_url'])) {
            $banner->cta_url = $input['cta_url'];
        }

        if (isset($input['order'])) {
            $banner->order = $input['order'];
        }

        if (isset($input['is_active'])) {
            $banner->is_active = $input['is_active'];
        }

        $banner->save();

        // Process image upload if provided
        if (isset($input['image']) && $input['image']) {
            $uploadPath = UploadPaths::BANNER($banner->id);
            $imagePath = $this->processImageUpload(
                $input['image'],
                $uploadPath,
                $oldImagePath,
                'banner_image'
            );

            if ($imagePath) {
                $banner->image_url = $imagePath;
                $banner->save();
            }
        }

        return $banner->fresh();
    }
}
