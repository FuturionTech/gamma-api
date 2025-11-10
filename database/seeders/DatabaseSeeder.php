<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Core data
        $this->call([
            ApplicationSeeder::class,
            AdministratorSeeder::class,
            SocialMediaPlatformSeeder::class,
            CertificationCategorySeeder::class,
        ]);

        // Sample content
        $this->call([
            ServiceSeeder::class,
            SolutionSeeder::class,
            IndustrySeeder::class,
            ProcessStepSeeder::class,
            BannerSeeder::class,
            StatSeeder::class,
            FAQSeeder::class,
        ]);
    }
}
