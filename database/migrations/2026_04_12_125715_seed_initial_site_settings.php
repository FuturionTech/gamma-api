<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Skip during test suite unless explicitly opted in.
        if (app()->runningUnitTests() && ! env('FORCE_CONTENT_BACKFILL')) {
            return;
        }

        $now = now();

        $settings = [
            ['key' => 'company_name', 'value' => 'Gamma Neutral Consulting Inc.', 'type' => 'string', 'group' => 'general'],
            ['key' => 'company_tagline', 'value' => 'Transform data into opportunity', 'type' => 'string', 'group' => 'general'],
            ['key' => 'company_email', 'value' => 'info@gammaneutral.com', 'type' => 'string', 'group' => 'contact'],
            ['key' => 'company_phone', 'value' => '+1 (416) 555-0199', 'type' => 'string', 'group' => 'contact'],
            ['key' => 'address_street', 'value' => '108 Redpath Ave', 'type' => 'string', 'group' => 'address'],
            ['key' => 'address_city', 'value' => 'Toronto', 'type' => 'string', 'group' => 'address'],
            ['key' => 'address_province', 'value' => 'ON', 'type' => 'string', 'group' => 'address'],
            ['key' => 'address_postal_code', 'value' => 'M4S 2J7', 'type' => 'string', 'group' => 'address'],
            ['key' => 'address_country', 'value' => 'Canada', 'type' => 'string', 'group' => 'address'],
            ['key' => 'address_google_maps_url', 'value' => 'https://maps.google.com/?q=108+Redpath+Ave+Toronto+ON', 'type' => 'string', 'group' => 'address'],
            ['key' => 'social_linkedin', 'value' => 'https://linkedin.com/company/gamma-neutral', 'type' => 'string', 'group' => 'social'],
            ['key' => 'social_twitter', 'value' => 'https://twitter.com/gammaneutral', 'type' => 'string', 'group' => 'social'],
            ['key' => 'social_facebook', 'value' => 'https://facebook.com/gammaneutral', 'type' => 'string', 'group' => 'social'],
            ['key' => 'social_instagram', 'value' => 'https://instagram.com/gammaneutral', 'type' => 'string', 'group' => 'social'],
            ['key' => 'social_github', 'value' => 'https://github.com/FuturionTech', 'type' => 'string', 'group' => 'social'],
            ['key' => 'copyright_text', 'value' => "\u{00A9} 2026 Gamma Neutral Consulting Inc. All rights reserved.", 'type' => 'string', 'group' => 'general'],
            ['key' => 'incorporated_date', 'value' => '2024-01-15', 'type' => 'string', 'group' => 'general'],
        ];

        foreach ($settings as $setting) {
            DB::table('site_settings')->updateOrInsert(
                ['key' => $setting['key']],
                array_merge($setting, [
                    'created_at' => $now,
                    'updated_at' => $now,
                ])
            );
        }
    }

    public function down(): void
    {
        // Intentionally empty — data migration.
    }
};
