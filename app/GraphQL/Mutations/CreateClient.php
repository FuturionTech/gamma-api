<?php

namespace App\GraphQL\Mutations;

use App\Models\Client;
use App\Traits\HasImageUploads;
use App\Helpers\FileManagement\UploadPaths;

class CreateClient
{
    use HasImageUploads;

    /**
     * Create a new client with optional logo upload
     */
    public function __invoke($rootValue, array $args)
    {
        $input = $args['input'];

        // Create client first to get ID
        $client = Client::create([
            'name' => $input['name'],
            'logo_url' => $input['logo_url'] ?? null,
            'industry' => $input['industry'] ?? null,
            'website_url' => $input['website_url'] ?? null,
            'order' => $input['order'] ?? 0,
            'is_active' => $input['is_active'] ?? true,
        ]);

        // Process logo upload if provided
        if (isset($input['logo']) && $input['logo']) {
            $uploadPath = UploadPaths::CLIENT($client->id);
            $logoPath = $this->processImageUpload(
                $input['logo'],
                $uploadPath,
                null,
                'client_logo'
            );

            if ($logoPath) {
                $client->logo_url = $logoPath;
                $client->save();
            }
        }

        return $client;
    }
}
