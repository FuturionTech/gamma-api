<?php

namespace App\GraphQL\Mutations;

use App\Models\Partner;
use App\Traits\HasImageUploads;
use App\Helpers\FileManagement\UploadPaths;

class CreatePartner
{
    use HasImageUploads;

    /**
     * Create a new partner with optional logo upload
     */
    public function __invoke($rootValue, array $args)
    {
        $input = $args['input'];

        // Create partner first to get ID
        $partner = Partner::create([
            'name' => $input['name'],
            'logo_url' => $input['logo_url'] ?? null,
            'website_url' => $input['website_url'] ?? null,
            'order' => $input['order'] ?? 0,
            'is_active' => $input['is_active'] ?? true,
        ]);

        // Process logo upload if provided
        if (isset($input['logo']) && $input['logo']) {
            $uploadPath = UploadPaths::PARTNER($partner->id);
            $logoPath = $this->processImageUpload(
                $input['logo'],
                $uploadPath,
                null,
                'partner_logo'
            );

            if ($logoPath) {
                $partner->logo_url = $logoPath;
                $partner->save();
            }
        }

        return $partner;
    }
}
