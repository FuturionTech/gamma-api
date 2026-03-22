<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceFeature;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'title' => 'AI & Machine Learning',
                'short_description' => 'Deploy intelligent systems that automate processes, predict outcomes, and unlock insights from your data.',
                'description' => 'Custom AI models, predictive analytics, and intelligent automation. Developing intelligent systems that automate processes and provide predictive insights.',
                'icon' => 'brain',
                'icon_color' => '#8B5CF6',
                'category' => 'Technology',
                'order' => 1,
                'features' => [
                    ['title' => 'Predictive Analytics', 'icon' => 'chart-line', 'order' => 1],
                    ['title' => 'Natural Language Processing', 'icon' => 'message', 'order' => 2],
                    ['title' => 'Computer Vision', 'icon' => 'eye', 'order' => 3],
                ],
            ],
            [
                'title' => 'Data Engineering',
                'short_description' => 'Build robust data pipelines and architectures that ensure seamless data flow across your organization.',
                'description' => 'Scalable pipelines, ETL processes, and real-time data infrastructure. Designing and building robust data architectures to ensure seamless data flow and accessibility.',
                'icon' => 'database',
                'icon_color' => '#3B82F6',
                'category' => 'Technology',
                'order' => 2,
                'features' => [
                    ['title' => 'ETL/ELT Pipelines', 'icon' => 'workflow', 'order' => 1],
                    ['title' => 'Real-time Processing', 'icon' => 'clock', 'order' => 2],
                    ['title' => 'Data Lake Design', 'icon' => 'database', 'order' => 3],
                ],
            ],
            [
                'title' => 'Cloud Computing',
                'short_description' => 'Migrate and optimize your infrastructure with scalable cloud solutions from AWS, Azure, and GCP.',
                'description' => 'Cloud migration, architecture design, and cost-optimized deployment. Providing scalable and flexible cloud solutions that enhance collaboration and efficiency.',
                'icon' => 'cloud',
                'icon_color' => '#10B981',
                'category' => 'Infrastructure',
                'order' => 3,
                'features' => [
                    ['title' => 'Cloud Migration', 'icon' => 'upload', 'order' => 1],
                    ['title' => 'Serverless Architecture', 'icon' => 'server', 'order' => 2],
                    ['title' => 'Cost Optimization', 'icon' => 'dollar', 'order' => 3],
                ],
            ],
            [
                'title' => 'Cybersecurity',
                'short_description' => 'Protect your data assets with enterprise-grade security solutions and compliance frameworks.',
                'description' => 'Threat detection, risk mitigation, and compliance-driven security frameworks. Implementing advanced security measures to protect data integrity and privacy.',
                'icon' => 'shield',
                'icon_color' => '#EF4444',
                'category' => 'Security',
                'order' => 4,
                'features' => [
                    ['title' => 'Threat Detection', 'icon' => 'alert', 'order' => 1],
                    ['title' => 'Compliance Management', 'icon' => 'check-circle', 'order' => 2],
                    ['title' => '24/7 Monitoring', 'icon' => 'eye', 'order' => 3],
                ],
            ],
            [
                'title' => 'Business Intelligence',
                'short_description' => 'Transform raw data into actionable insights with interactive dashboards and reports.',
                'description' => 'Dashboards, reporting tools, and strategic insights for smarter decisions. Transforming data into actionable insights through advanced analytics and reporting tools.',
                'icon' => 'chart',
                'icon_color' => '#F59E0B',
                'category' => 'Analytics',
                'order' => 5,
                'features' => [
                    ['title' => 'Interactive Dashboards', 'icon' => 'chart-bar', 'order' => 1],
                    ['title' => 'KPI Monitoring', 'icon' => 'target', 'order' => 2],
                    ['title' => 'Strategic Reporting', 'icon' => 'document', 'order' => 3],
                ],
            ],
            [
                'title' => 'Big Data Solutions',
                'short_description' => 'Process and analyze massive datasets with distributed computing and advanced analytics.',
                'description' => 'High-volume data processing, distributed systems, and analytics at scale. Leveraging large datasets to uncover trends and patterns that inform strategic decisions.',
                'icon' => 'server',
                'icon_color' => '#8B5CF6',
                'category' => 'Technology',
                'order' => 6,
                'features' => [
                    ['title' => 'Distributed Processing', 'icon' => 'grid', 'order' => 1],
                    ['title' => 'Spark & Hadoop', 'icon' => 'code', 'order' => 2],
                    ['title' => 'Data Lake Solutions', 'icon' => 'database', 'order' => 3],
                ],
            ],
        ];

        foreach ($services as $serviceData) {
            $features = $serviceData['features'] ?? [];
            unset($serviceData['features']);

            $service = Service::create([
                'title' => $serviceData['title'],
                'description' => $serviceData['description'],
                'short_description' => $serviceData['short_description'],
                'icon' => $serviceData['icon'],
                'icon_color' => $serviceData['icon_color'],
                'category' => $serviceData['category'],
                'slug' => Str::slug($serviceData['title']),
                'order' => $serviceData['order'],
                'is_active' => true,
            ]);

            foreach ($features as $feature) {
                ServiceFeature::create([
                    'service_id' => $service->id,
                    'title' => $feature['title'],
                    'icon' => $feature['icon'],
                    'order' => $feature['order'],
                ]);
            }
        }
    }
}

