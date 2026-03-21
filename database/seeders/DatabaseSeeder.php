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
        // Core data (order matters — these are referenced by content seeders)
        $this->call([
            ApplicationSeeder::class,
            AdministratorSeeder::class,
            SocialMediaPlatformSeeder::class,
            CertificationCategorySeeder::class,
        ]);

        // Content seeders
        $this->call([
            ServiceSeeder::class,
            SolutionSeeder::class,
            IndustrySeeder::class,
            ProcessStepSeeder::class,
            BannerSeeder::class,
            StatSeeder::class,
            FAQSeeder::class,
            TeamSeeder::class,
            JobPositionSeeder::class,
            ProjectSeeder::class,
            TestimonialSeeder::class,
            ClientSeeder::class,
            PartnerSeeder::class,
            CertificationSeeder::class,
            BlogPostSeeder::class, // Must come after TeamSeeder (needs author_id)
        ]);
    }
}
