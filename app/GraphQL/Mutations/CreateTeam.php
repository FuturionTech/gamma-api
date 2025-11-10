<?php

namespace App\GraphQL\Mutations;

use App\Models\Team;
use App\Traits\HasImageUploads;
use App\Helpers\FileManagement\UploadPaths;

class CreateTeam
{
    use HasImageUploads;

    /**
     * Create a new team member with optional profile picture upload
     */
    public function __invoke($rootValue, array $args)
    {
        $input = $args['input'];

        // Create team member first to get ID
        $team = Team::create([
            'name' => $input['name'],
            'role' => $input['role'] ?? null,
            'email' => $input['email'] ?? null,
            'contact' => $input['contact'] ?? null,
            'biography' => $input['biography'] ?? null,
            'profile_picture_url' => $input['profile_picture_url'] ?? null,
            'order' => $input['order'] ?? 0,
            'is_active' => $input['is_active'] ?? true,
        ]);

        // Process profile picture upload if provided
        if (isset($input['profile_picture']) && $input['profile_picture']) {
            $uploadPath = UploadPaths::TEAM($team->id);
            $picturePath = $this->processImageUpload(
                $input['profile_picture'],
                $uploadPath,
                null,
                'team_profile_picture'
            );

            if ($picturePath) {
                $team->profile_picture_url = $picturePath;
                $team->save();
            }
        }

        return $team;
    }
}
