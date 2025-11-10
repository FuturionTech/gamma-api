<?php

namespace App\GraphQL\Mutations;

use App\Models\Team;
use App\Traits\HasImageUploads;
use App\Helpers\FileManagement\UploadPaths;

class UpdateTeam
{
    use HasImageUploads;

    /**
     * Update a team member with optional profile picture upload
     */
    public function __invoke($rootValue, array $args)
    {
        $id = $args['id'];
        $input = $args['input'];

        $team = Team::findOrFail($id);
        $oldPicturePath = $team->profile_picture_url;

        // Update team fields
        if (isset($input['name'])) {
            $team->name = $input['name'];
        }

        if (isset($input['role'])) {
            $team->role = $input['role'];
        }

        if (isset($input['email'])) {
            $team->email = $input['email'];
        }

        if (isset($input['contact'])) {
            $team->contact = $input['contact'];
        }

        if (isset($input['biography'])) {
            $team->biography = $input['biography'];
        }

        if (isset($input['profile_picture_url'])) {
            $team->profile_picture_url = $input['profile_picture_url'];
        }

        if (isset($input['order'])) {
            $team->order = $input['order'];
        }

        if (isset($input['is_active'])) {
            $team->is_active = $input['is_active'];
        }

        $team->save();

        // Process profile picture upload if provided
        if (isset($input['profile_picture']) && $input['profile_picture']) {
            $uploadPath = UploadPaths::TEAM($team->id);
            $picturePath = $this->processImageUpload(
                $input['profile_picture'],
                $uploadPath,
                $oldPicturePath,
                'team_profile_picture'
            );

            if ($picturePath) {
                $team->profile_picture_url = $picturePath;
                $team->save();
            }
        }

        return $team->fresh();
    }
}
