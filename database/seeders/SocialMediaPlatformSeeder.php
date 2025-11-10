<?php

namespace Database\Seeders;

use App\Models\SocialMediaPlatform;
use Illuminate\Database\Seeder;

class SocialMediaPlatformSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $platforms = [
            ['name' => 'LinkedIn', 'icon' => 'linkedin', 'base_url' => 'https://linkedin.com/in/'],
            ['name' => 'Twitter', 'icon' => 'twitter', 'base_url' => 'https://twitter.com/'],
            ['name' => 'Facebook', 'icon' => 'facebook', 'base_url' => 'https://facebook.com/'],
            ['name' => 'GitHub', 'icon' => 'github', 'base_url' => 'https://github.com/'],
            ['name' => 'Instagram', 'icon' => 'instagram', 'base_url' => 'https://instagram.com/'],
        ];

        foreach ($platforms as $platform) {
            SocialMediaPlatform::create($platform);
        }
    }
}

