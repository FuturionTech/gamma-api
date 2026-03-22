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
                'title_fr' => 'IA et Apprentissage Automatique',
                'short_description' => 'Deploy intelligent systems that automate processes, predict outcomes, and unlock insights from your data.',
                'short_description_fr' => 'Déployez des systèmes intelligents qui automatisent les processus, prédisent les résultats et révèlent des insights à partir de vos données.',
                'description' => 'Custom AI models, predictive analytics, and intelligent automation. Developing intelligent systems that automate processes and provide predictive insights.',
                'description_fr' => 'Modèles d\'IA personnalisés, analytique prédictive et automatisation intelligente. Développement de systèmes intelligents qui automatisent les processus et fournissent des insights prédictifs.',
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
                'title_fr' => 'Ingénierie des Données',
                'short_description' => 'Build robust data pipelines and architectures that ensure seamless data flow across your organization.',
                'short_description_fr' => 'Construisez des pipelines de données robustes et des architectures qui assurent un flux de données fluide à travers votre organisation.',
                'description' => 'Scalable pipelines, ETL processes, and real-time data infrastructure. Designing and building robust data architectures to ensure seamless data flow and accessibility.',
                'description_fr' => 'Pipelines évolutifs, processus ETL et infrastructure de données en temps réel. Conception et construction d\'architectures de données robustes pour assurer un flux et une accessibilité sans faille.',
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
                'title_fr' => 'Infonuagique',
                'short_description' => 'Migrate and optimize your infrastructure with scalable cloud solutions from AWS, Azure, and GCP.',
                'short_description_fr' => 'Migrez et optimisez votre infrastructure avec des solutions cloud évolutives sur AWS, Azure et GCP.',
                'description' => 'Cloud migration, architecture design, and cost-optimized deployment. Providing scalable and flexible cloud solutions that enhance collaboration and efficiency.',
                'description_fr' => 'Migration vers le cloud, conception d\'architecture et déploiement optimisé en coûts. Des solutions cloud évolutives et flexibles qui améliorent la collaboration et l\'efficacité.',
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
                'title_fr' => 'Cybersécurité',
                'short_description' => 'Protect your data assets with enterprise-grade security solutions and compliance frameworks.',
                'short_description_fr' => 'Protégez vos actifs de données avec des solutions de sécurité de niveau entreprise et des cadres de conformité.',
                'description' => 'Threat detection, risk mitigation, and compliance-driven security frameworks. Implementing advanced security measures to protect data integrity and privacy.',
                'description_fr' => 'Détection des menaces, atténuation des risques et cadres de sécurité axés sur la conformité. Mise en œuvre de mesures de sécurité avancées pour protéger l\'intégrité et la confidentialité des données.',
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
                'title_fr' => 'Intelligence d\'Affaires',
                'short_description' => 'Transform raw data into actionable insights with interactive dashboards and reports.',
                'short_description_fr' => 'Transformez les données brutes en insights exploitables grâce à des tableaux de bord interactifs et des rapports.',
                'description' => 'Dashboards, reporting tools, and strategic insights for smarter decisions. Transforming data into actionable insights through advanced analytics and reporting tools.',
                'description_fr' => 'Tableaux de bord, outils de reporting et insights stratégiques pour des décisions plus éclairées. Transformation des données en insights exploitables grâce à des outils d\'analytique avancée.',
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
                'title_fr' => 'Solutions de Données Massives',
                'short_description' => 'Process and analyze massive datasets with distributed computing and advanced analytics.',
                'short_description_fr' => 'Traitez et analysez des ensembles de données massifs grâce au calcul distribué et à l\'analytique avancée.',
                'description' => 'High-volume data processing, distributed systems, and analytics at scale. Leveraging large datasets to uncover trends and patterns that inform strategic decisions.',
                'description_fr' => 'Traitement de données à haut volume, systèmes distribués et analytique à grande échelle. Exploitation de grands ensembles de données pour découvrir des tendances et des modèles qui éclairent les décisions stratégiques.',
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

            $service = Service::updateOrCreate(
                ['slug' => Str::slug($serviceData['title'])],
                [
                    'title' => $serviceData['title'],
                    'title_fr' => $serviceData['title_fr'],
                    'description' => $serviceData['description'],
                    'description_fr' => $serviceData['description_fr'],
                    'short_description' => $serviceData['short_description'],
                    'short_description_fr' => $serviceData['short_description_fr'],
                    'icon' => $serviceData['icon'],
                    'icon_color' => $serviceData['icon_color'],
                    'category' => $serviceData['category'],
                    'order' => $serviceData['order'],
                    'is_active' => true,
                ]
            );

            // Only create features if they don't exist yet
            if ($service->features()->count() === 0) {
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
}
