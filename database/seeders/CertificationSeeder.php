<?php

namespace Database\Seeders;

use App\Models\Certification;
use App\Models\CertificationCategory;
use Illuminate\Database\Seeder;

class CertificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = CertificationCategory::pluck('id', 'name')->toArray();

        $certifications = [
            [
                'title' => 'AWS Solutions Architect Professional',
                'category' => 'Professional Certification',
                'issued_date' => '2024-03-15',
            ],
            [
                'title' => 'Google Cloud Professional Data Engineer',
                'category' => 'Professional Certification',
                'issued_date' => '2024-05-20',
            ],
            [
                'title' => 'ISO 27001 Information Security Management',
                'category' => 'ISO Certification',
                'issued_date' => '2024-01-10',
            ],
            [
                'title' => 'ISO 9001 Quality Management System',
                'category' => 'ISO Certification',
                'issued_date' => '2023-11-22',
            ],
            [
                'title' => 'CMMI Level 3 Maturity',
                'category' => 'Quality Assurance',
                'issued_date' => '2024-06-01',
            ],
            [
                'title' => 'PMP - Project Management Professional',
                'category' => 'Professional Certification',
                'issued_date' => '2024-02-14',
            ],
            [
                'title' => 'ITIL 4 Foundation',
                'category' => 'Industry Standards',
                'issued_date' => '2023-09-30',
            ],
            [
                'title' => 'Certified Information Systems Security Professional (CISSP)',
                'category' => 'Security Certification',
                'issued_date' => '2024-04-18',
            ],
        ];

        foreach ($certifications as $certData) {
            $categoryId = $categories[$certData['category']] ?? null;

            Certification::create([
                'title' => $certData['title'],
                'certification_category_id' => $categoryId,
                'issued_date' => $certData['issued_date'],
                'is_active' => true,
            ]);
        }
    }
}
