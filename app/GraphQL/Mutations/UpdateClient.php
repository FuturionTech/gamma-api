<?php

namespace App\GraphQL\Mutations;

use App\Models\Client;
use App\Traits\HasImageUploads;
use App\Helpers\FileManagement\UploadPaths;

class UpdateClient
{
    use HasImageUploads;

    /**
     * Update a client with optional logo upload
     */
    public function __invoke($rootValue, array $args)
    {
        $id = $args['id'];
        $input = $args['input'];

        $client = Client::findOrFail($id);
        $oldLogoPath = $client->logo_url;

        // Update client fields
        if (isset($input['name'])) {
            $client->name = $input['name'];
        }

        if (isset($input['logo_url'])) {
            $client->logo_url = $input['logo_url'];
        }

        if (isset($input['industry'])) {
            $client->industry = $input['industry'];
        }

        if (isset($input['website_url'])) {
            $client->website_url = $input['website_url'];
        }

        if (isset($input['order'])) {
            $client->order = $input['order'];
        }

        if (isset($input['is_active'])) {
            $client->is_active = $input['is_active'];
        }

        $client->save();

        // Process logo upload if provided
        if (isset($input['logo']) && $input['logo']) {
            $uploadPath = UploadPaths::CLIENT($client->id);
            $logoPath = $this->processImageUpload(
                $input['logo'],
                $uploadPath,
                $oldLogoPath,
                'client_logo'
            );

            if ($logoPath) {
                $client->logo_url = $logoPath;
                $client->save();
            }
        }

        return $client->fresh();
    }
}
