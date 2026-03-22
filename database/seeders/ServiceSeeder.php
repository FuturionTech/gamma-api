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
                'title' => ['en' => 'AI & Machine Learning', 'fr' => 'IA et Apprentissage Automatique'],
                'short_description' => [
                    'en' => 'Deploy intelligent systems that automate processes, predict outcomes, and unlock insights from your data.',
                    'fr' => 'Déployez des systèmes intelligents qui automatisent les processus, prédisent les résultats et révèlent des informations à partir de vos données.',
                ],
                'description' => [
                    'en' => 'Custom AI models, predictive analytics, and intelligent automation. Developing intelligent systems that automate processes and provide predictive insights.',
                    'fr' => 'Modèles d\'IA personnalisés, analyses prédictives et automatisation intelligente. Développement de systèmes intelligents qui automatisent les processus et fournissent des informations prédictives.',
                ],
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
                'title' => ['en' => 'Data Engineering', 'fr' => 'Ingénierie des Données'],
                'short_description' => [
                    'en' => 'Build robust data pipelines and architectures that ensure seamless data flow across your organization.',
                    'fr' => 'Construisez des pipelines de données robustes et des architectures qui assurent un flux de données fluide à travers votre organisation.',
                ],
                'description' => [
                    'en' => 'Scalable pipelines, ETL processes, and real-time data infrastructure. Designing and building robust data architectures to ensure seamless data flow and accessibility.',
                    'fr' => 'Pipelines évolutifs, processus ETL et infrastructure de données en temps réel. Conception et construction d\'architectures de données robustes pour assurer un flux de données fluide et accessible.',
                ],
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
                'title' => ['en' => 'Cloud Computing', 'fr' => 'Infonuagique'],
                'short_description' => [
                    'en' => 'Migrate and optimize your infrastructure with scalable cloud solutions from AWS, Azure, and GCP.',
                    'fr' => 'Migrez et optimisez votre infrastructure avec des solutions infonuagiques évolutives d\'AWS, Azure et GCP.',
                ],
                'description' => [
                    'en' => 'Cloud migration, architecture design, and cost-optimized deployment. Providing scalable and flexible cloud solutions that enhance collaboration and efficiency.',
                    'fr' => 'Migration infonuagique, conception d\'architecture et déploiement optimisé en coûts. Des solutions infonuagiques évolutives et flexibles qui améliorent la collaboration et l\'efficacité.',
                ],
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
                'title' => ['en' => 'Cybersecurity', 'fr' => 'Cybersécurité'],
                'short_description' => [
                    'en' => 'Protect your data assets with enterprise-grade security solutions and compliance frameworks.',
                    'fr' => 'Protégez vos actifs de données avec des solutions de sécurité de niveau entreprise et des cadres de conformité.',
                ],
                'description' => [
                    'en' => 'Threat detection, risk mitigation, and compliance-driven security frameworks. Implementing advanced security measures to protect data integrity and privacy.',
                    'fr' => 'Détection des menaces, atténuation des risques et cadres de sécurité axés sur la conformité. Mise en œuvre de mesures de sécurité avancées pour protéger l\'intégrité et la confidentialité des données.',
                ],
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
                'title' => ['en' => 'Business Intelligence', 'fr' => 'Intelligence d\'Affaires'],
                'short_description' => [
                    'en' => 'Transform raw data into actionable insights with interactive dashboards and reports.',
                    'fr' => 'Transformez les données brutes en informations exploitables grâce à des tableaux de bord et des rapports interactifs.',
                ],
                'description' => [
                    'en' => 'Dashboards, reporting tools, and strategic insights for smarter decisions. Transforming data into actionable insights through advanced analytics and reporting tools.',
                    'fr' => 'Tableaux de bord, outils de rapport et informations stratégiques pour des décisions plus éclairées. Transformation des données en informations exploitables grâce à des analyses avancées et des outils de rapport.',
                ],
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
                'title' => ['en' => 'Big Data Solutions', 'fr' => 'Solutions de Mégadonnées'],
                'short_description' => [
                    'en' => 'Process and analyze massive datasets with distributed computing and advanced analytics.',
                    'fr' => 'Traitez et analysez des ensembles de données massifs avec l\'informatique distribuée et l\'analytique avancée.',
                ],
                'description' => [
                    'en' => 'High-volume data processing, distributed systems, and analytics at scale. Leveraging large datasets to uncover trends and patterns that inform strategic decisions.',
                    'fr' => 'Traitement de données à haut volume, systèmes distribués et analytique à grande échelle. Exploitation de grands ensembles de données pour découvrir les tendances et les modèles qui éclairent les décisions stratégiques.',
                ],
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
                'slug' => Str::slug($serviceData['title']['en']),
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
