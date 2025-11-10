<?php

namespace App\GraphQL\Mutations;

use App\Models\Project;
use App\Traits\HasImageUploads;
use App\Helpers\FileManagement\UploadPaths;
use Illuminate\Support\Str;

class CreateProject
{
    use HasImageUploads;

    /**
     * Create a new project with optional featured image upload
     */
    public function __invoke($rootValue, array $args)
    {
        $input = $args['input'];

        // Create project first to get ID
        $project = Project::create([
            'title' => $input['title'],
            'slug' => $input['slug'] ?? Str::slug($input['title']),
            'description' => $input['description'] ?? null,
            'challenge' => $input['challenge'] ?? null,
            'solution' => $input['solution'] ?? null,
            'results' => $input['results'] ?? null,
            'featured_image_url' => $input['featured_image_url'] ?? null,
            'gallery_images' => $input['gallery_images'] ?? null,
            'client_name' => $input['client_name'] ?? null,
            'industry' => $input['industry'] ?? null,
            'technologies' => $input['technologies'] ?? null,
            'status' => $input['status'] ?? 'draft',
            'completion_date' => $input['completion_date'] ?? null,
        ]);

        // Process featured image upload if provided
        if (isset($input['featured_image']) && $input['featured_image']) {
            $uploadPath = UploadPaths::PROJECT($project->id);
            $imagePath = $this->processImageUpload(
                $input['featured_image'],
                $uploadPath,
                null,
                'project_featured_image'
            );

            if ($imagePath) {
                $project->featured_image_url = $imagePath;
                $project->save();
            }
        }

        return $project;
    }
}
