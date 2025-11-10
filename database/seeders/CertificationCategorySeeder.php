<?php

namespace Database\Seeders;

use App\Models\CertificationCategory;
use Illuminate\Database\Seeder;

class CertificationCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'ISO Certification',
            'Security Certification',
            'Quality Assurance',
            'Industry Standards',
            'Professional Certification',
        ];

        foreach ($categories as $category) {
            CertificationCategory::create(['name' => $category]);
        }
    }
}

