<?php

namespace App\GraphQL\Mutations;

use App\Models\Partner;
use App\Traits\HasImageUploads;
use App\Helpers\FileManagement\UploadPaths;

class UpdatePartner
{
    use HasImageUploads;

    /**
     * Update a partner with optional logo upload
     */
    public function __invoke($rootValue, array $args)
    {
        $id = $args['id'];
        $input = $args['input'];

        $partner = Partner::findOrFail($id);
        $oldLogoPath = $partner->logo_url;

        // Update partner fields
        if (isset($input['name'])) {
            $partner->name = $input['name'];
        }

        if (isset($input['logo_url'])) {
            $partner->logo_url = $input['logo_url'];
        }

        if (isset($input['website_url'])) {
            $partner->website_url = $input['website_url'];
        }

        if (isset($input['order'])) {
            $partner->order = $input['order'];
        }

        if (isset($input['is_active'])) {
            $partner->is_active = $input['is_active'];
        }

        $partner->save();

        // Process logo upload if provided
        if (isset($input['logo']) && $input['logo']) {
            $uploadPath = UploadPaths::PARTNER($partner->id);
            $logoPath = $this->processImageUpload(
                $input['logo'],
                $uploadPath,
                $oldLogoPath,
                'partner_logo'
            );

            if ($logoPath) {
                $partner->logo_url = $logoPath;
                $partner->save();
            }
        }

        return $partner->fresh();
    }
}
