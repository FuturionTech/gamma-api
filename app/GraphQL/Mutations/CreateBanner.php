<?php

namespace App\GraphQL\Mutations;

use App\Models\Banner;
use App\Traits\HasImageUploads;
use App\Helpers\FileManagement\UploadPaths;

class CreateBanner
{
    use HasImageUploads;

    /**
     * Create a new banner with optional image upload
     */
    public function __invoke($rootValue, array $args)
    {
        $input = $args['input'];

        // Create banner first to get ID
        $banner = Banner::create([
            'title' => $input['title'],
            'subtitle' => $input['subtitle'] ?? null,
            'image_url' => $input['image_url'] ?? null,
            'cta_text' => $input['cta_text'] ?? null,
            'cta_url' => $input['cta_url'] ?? null,
            'order' => $input['order'] ?? 0,
            'is_active' => $input['is_active'] ?? true,
        ]);

        // Process image upload if provided
        if (isset($input['image']) && $input['image']) {
            $uploadPath = UploadPaths::BANNER($banner->id);
            $imagePath = $this->processImageUpload(
                $input['image'],
                $uploadPath,
                null,
                'banner_image'
            );

            if ($imagePath) {
                $banner->image_url = $imagePath;
                $banner->save();
            }
        }

        return $banner;
    }
}
