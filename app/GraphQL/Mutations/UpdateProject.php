<?php

namespace App\GraphQL\Mutations;

use App\Models\Project;
use App\Traits\HasImageUploads;
use App\Helpers\FileManagement\UploadPaths;

class UpdateProject
{
    use HasImageUploads;

    /**
     * Update a project with optional featured image upload
     */
    public function __invoke($rootValue, array $args)
    {
        $id = $args['id'];
        $input = $args['input'];

        $project = Project::findOrFail($id);
        $oldImagePath = $project->featured_image_url;

        // Update project fields
        if (isset($input['title'])) {
            $project->title = $input['title'];
        }

        if (isset($input['slug'])) {
            $project->slug = $input['slug'];
        }

        if (isset($input['description'])) {
            $project->description = $input['description'];
        }

        if (isset($input['challenge'])) {
            $project->challenge = $input['challenge'];
        }

        if (isset($input['solution'])) {
            $project->solution = $input['solution'];
        }

        if (isset($input['results'])) {
            $project->results = $input['results'];
        }

        if (isset($input['featured_image_url'])) {
            $project->featured_image_url = $input['featured_image_url'];
        }

        if (isset($input['gallery_images'])) {
            $project->gallery_images = $input['gallery_images'];
        }

        if (isset($input['client_name'])) {
            $project->client_name = $input['client_name'];
        }

        if (isset($input['industry'])) {
            $project->industry = $input['industry'];
        }

        if (isset($input['technologies'])) {
            $project->technologies = $input['technologies'];
        }

        if (isset($input['status'])) {
            $project->status = $input['status'];
        }

        if (isset($input['completion_date'])) {
            $project->completion_date = $input['completion_date'];
        }

        $project->save();

        // Process featured image upload if provided
        if (isset($input['featured_image']) && $input['featured_image']) {
            $uploadPath = UploadPaths::PROJECT($project->id);
            $imagePath = $this->processImageUpload(
                $input['featured_image'],
                $uploadPath,
                $oldImagePath,
                'project_featured_image'
            );

            if ($imagePath) {
                $project->featured_image_url = $imagePath;
                $project->save();
            }
        }

        return $project->fresh();
    }
}
