<?php

namespace Database\Seeders;

use App\Models\Industry;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class IndustrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $industries = [
            [
                'title' => 'Banks & Financial Services',
                'short_description' => 'Enhancing data security and analytics for better financial decision-making with AI-powered risk modeling and fraud detection.',
                'description' => 'Enhancing data security and analytics for better financial decision-making with AI-powered risk modeling and fraud detection. We provide comprehensive solutions for banking institutions to modernize their data infrastructure, implement advanced analytics, and ensure regulatory compliance while maintaining the highest security standards.',
                'icon' => 'building-columns',
                'icon_color' => '#6B7280',
                'category' => 'FINANCIAL_SERVICES',
                'order' => 1,
            ],
            [
                'title' => 'Healthcare Services',
                'short_description' => 'Developing AI and big data solutions to improve patient care and operational efficiency while maintaining HIPAA compliance.',
                'description' => 'Developing AI and big data solutions to improve patient care and operational efficiency while maintaining HIPAA compliance. Our healthcare solutions enable medical institutions to leverage data-driven insights for better patient outcomes, streamline operations, and ensure data privacy and security in accordance with healthcare regulations.',
                'icon' => 'heart-pulse',
                'icon_color' => '#10B981',
                'category' => 'HEALTHCARE',
                'order' => 2,
            ],
            [
                'title' => 'Governments & Public Services',
                'short_description' => 'Building secure data infrastructures for public sector organizations with smart city initiatives and citizen service optimization.',
                'description' => 'Building secure data infrastructures for public sector organizations with smart city initiatives and citizen service optimization. We help government agencies modernize their technology infrastructure, implement data-driven governance, and create citizen-centric digital services while ensuring data sovereignty and security.',
                'icon' => 'landmark',
                'icon_color' => '#3B82F6',
                'category' => 'GOVERNMENT',
                'order' => 3,
            ],
            [
                'title' => 'Business Management',
                'short_description' => 'Implementing BI tools to streamline operations and improve efficiency with data-driven decision-making and process optimization.',
                'description' => 'Implementing BI tools to streamline operations and improve efficiency with data-driven decision-making and process optimization. Our business intelligence solutions empower organizations to transform raw data into actionable insights, automate workflows, and optimize business processes for maximum efficiency and profitability.',
                'icon' => 'briefcase',
                'icon_color' => '#F59E0B',
                'category' => 'MANUFACTURING',
                'order' => 4,
            ],
            [
                'title' => 'Non-Governmental Organizations',
                'short_description' => 'Providing data solutions to support mission-driven initiatives with impact measurement, donor analytics, and program optimization.',
                'description' => 'Providing data solutions to support mission-driven initiatives with impact measurement, donor analytics, and program optimization. We enable NGOs to maximize their social impact through data-driven program management, transparent reporting, and efficient resource allocation while maintaining donor trust and regulatory compliance.',
                'icon' => 'circle-dot',
                'icon_color' => '#8B5CF6',
                'category' => 'NGO',
                'order' => 5,
            ],
            [
                'title' => 'Education & Training',
                'short_description' => 'Developing AI-driven platforms for personalized learning experiences with student performance analytics and adaptive learning paths.',
                'description' => 'Developing AI-driven platforms for personalized learning experiences with student performance analytics and adaptive learning paths. Our education technology solutions help institutions deliver personalized learning at scale, track student progress with predictive analytics, and create engaging educational experiences through innovative technology.',
                'icon' => 'graduation-cap',
                'icon_color' => '#EF4444',
                'category' => 'EDUCATION',
                'order' => 6,
            ],
        ];

        foreach ($industries as $industryData) {
            Industry::create([
                'title' => $industryData['title'],
                'description' => $industryData['description'],
                'short_description' => $industryData['short_description'],
                'icon' => $industryData['icon'],
                'icon_color' => $industryData['icon_color'],
                'category' => $industryData['category'],
                'slug' => Str::slug($industryData['title']),
                'order' => $industryData['order'],
                'is_active' => true,
            ]);
        }
    }
}
