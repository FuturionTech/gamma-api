<?php

namespace App\GraphQL\Mutations;

use App\Models\Testimonial;
use App\Traits\HasImageUploads;
use App\Helpers\FileManagement\UploadPaths;

class CreateTestimonial
{
    use HasImageUploads;

    /**
     * Create a new testimonial with optional image upload
     */
    public function __invoke($rootValue, array $args)
    {
        $input = $args['input'];

        // Create testimonial first to get ID
        $testimonial = Testimonial::create([
            'name' => $input['name'],
            'content' => $input['content'],
            'image_url' => $input['image_url'] ?? null,
            'position' => $input['position'] ?? null,
            'company' => $input['company'] ?? null,
            'rating' => $input['rating'] ?? 5,
            'order' => $input['order'] ?? 0,
            'is_active' => $input['is_active'] ?? true,
        ]);

        // Process image upload if provided
        if (isset($input['image']) && $input['image']) {
            $uploadPath = UploadPaths::TESTIMONIAL($testimonial->id);
            $imagePath = $this->processImageUpload(
                $input['image'],
                $uploadPath,
                null,
                'testimonial_image'
            );

            if ($imagePath) {
                $testimonial->image_url = $imagePath;
                $testimonial->save();
            }
        }

        return $testimonial;
    }
}
