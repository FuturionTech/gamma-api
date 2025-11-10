<?php

namespace App\GraphQL\Mutations;

use App\Models\Testimonial;
use App\Traits\HasImageUploads;
use App\Helpers\FileManagement\UploadPaths;

class UpdateTestimonial
{
    use HasImageUploads;

    /**
     * Update a testimonial with optional image upload
     */
    public function __invoke($rootValue, array $args)
    {
        $id = $args['id'];
        $input = $args['input'];

        $testimonial = Testimonial::findOrFail($id);
        $oldImagePath = $testimonial->image_url;

        // Update testimonial fields
        if (isset($input['name'])) {
            $testimonial->name = $input['name'];
        }

        if (isset($input['content'])) {
            $testimonial->content = $input['content'];
        }

        if (isset($input['image_url'])) {
            $testimonial->image_url = $input['image_url'];
        }

        if (isset($input['position'])) {
            $testimonial->position = $input['position'];
        }

        if (isset($input['company'])) {
            $testimonial->company = $input['company'];
        }

        if (isset($input['rating'])) {
            $testimonial->rating = $input['rating'];
        }

        if (isset($input['order'])) {
            $testimonial->order = $input['order'];
        }

        if (isset($input['is_active'])) {
            $testimonial->is_active = $input['is_active'];
        }

        $testimonial->save();

        // Process image upload if provided
        if (isset($input['image']) && $input['image']) {
            $uploadPath = UploadPaths::TESTIMONIAL($testimonial->id);
            $imagePath = $this->processImageUpload(
                $input['image'],
                $uploadPath,
                $oldImagePath,
                'testimonial_image'
            );

            if ($imagePath) {
                $testimonial->image_url = $imagePath;
                $testimonial->save();
            }
        }

        return $testimonial->fresh();
    }
}
