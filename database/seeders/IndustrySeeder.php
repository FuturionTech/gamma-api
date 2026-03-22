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
                'title' => ['en' => 'Banks & Financial Services', 'fr' => 'Banques et Services Financiers'],
                'short_description' => [
                    'en' => 'Enhancing data security and analytics for better financial decision-making with AI-powered risk modeling and fraud detection.',
                    'fr' => 'Renforcement de la sécurité des données et de l\'analytique pour une meilleure prise de décision financière grâce à la modélisation des risques et à la détection de fraudes propulsées par l\'IA.',
                ],
                'description' => [
                    'en' => 'Enhancing data security and analytics for better financial decision-making with AI-powered risk modeling and fraud detection. We provide comprehensive solutions for banking institutions to modernize their data infrastructure, implement advanced analytics, and ensure regulatory compliance while maintaining the highest security standards.',
                    'fr' => 'Renforcement de la sécurité des données et de l\'analytique pour une meilleure prise de décision financière grâce à la modélisation des risques et à la détection de fraudes propulsées par l\'IA. Nous offrons des solutions complètes aux institutions bancaires pour moderniser leur infrastructure de données, mettre en œuvre des analyses avancées et assurer la conformité réglementaire tout en maintenant les normes de sécurité les plus élevées.',
                ],
                'icon' => 'building-columns',
                'icon_color' => '#6B7280',
                'category' => 'FINANCIAL_SERVICES',
                'order' => 1,
            ],
            [
                'title' => ['en' => 'Healthcare Services', 'fr' => 'Services de Santé'],
                'short_description' => [
                    'en' => 'Developing AI and big data solutions to improve patient care and operational efficiency while maintaining HIPAA compliance.',
                    'fr' => 'Développement de solutions d\'IA et de mégadonnées pour améliorer les soins aux patients et l\'efficacité opérationnelle tout en maintenant la conformité HIPAA.',
                ],
                'description' => [
                    'en' => 'Developing AI and big data solutions to improve patient care and operational efficiency while maintaining HIPAA compliance. Our healthcare solutions enable medical institutions to leverage data-driven insights for better patient outcomes, streamline operations, and ensure data privacy and security in accordance with healthcare regulations.',
                    'fr' => 'Développement de solutions d\'IA et de mégadonnées pour améliorer les soins aux patients et l\'efficacité opérationnelle tout en maintenant la conformité HIPAA. Nos solutions de santé permettent aux institutions médicales de tirer parti des informations basées sur les données pour de meilleurs résultats pour les patients, de rationaliser les opérations et d\'assurer la confidentialité et la sécurité des données conformément aux réglementations en matière de santé.',
                ],
                'icon' => 'heart-pulse',
                'icon_color' => '#10B981',
                'category' => 'HEALTHCARE',
                'order' => 2,
            ],
            [
                'title' => ['en' => 'Governments & Public Services', 'fr' => 'Gouvernements et Services Publics'],
                'short_description' => [
                    'en' => 'Building secure data infrastructures for public sector organizations with smart city initiatives and citizen service optimization.',
                    'fr' => 'Construction d\'infrastructures de données sécurisées pour les organisations du secteur public avec des initiatives de villes intelligentes et l\'optimisation des services aux citoyens.',
                ],
                'description' => [
                    'en' => 'Building secure data infrastructures for public sector organizations with smart city initiatives and citizen service optimization. We help government agencies modernize their technology infrastructure, implement data-driven governance, and create citizen-centric digital services while ensuring data sovereignty and security.',
                    'fr' => 'Construction d\'infrastructures de données sécurisées pour les organisations du secteur public avec des initiatives de villes intelligentes et l\'optimisation des services aux citoyens. Nous aidons les agences gouvernementales à moderniser leur infrastructure technologique, à mettre en œuvre une gouvernance basée sur les données et à créer des services numériques centrés sur les citoyens tout en assurant la souveraineté et la sécurité des données.',
                ],
                'icon' => 'landmark',
                'icon_color' => '#3B82F6',
                'category' => 'GOVERNMENT',
                'order' => 3,
            ],
            [
                'title' => ['en' => 'Business Management', 'fr' => 'Gestion d\'Entreprise'],
                'short_description' => [
                    'en' => 'Implementing BI tools to streamline operations and improve efficiency with data-driven decision-making and process optimization.',
                    'fr' => 'Mise en œuvre d\'outils d\'intelligence d\'affaires pour rationaliser les opérations et améliorer l\'efficacité grâce à la prise de décision basée sur les données et l\'optimisation des processus.',
                ],
                'description' => [
                    'en' => 'Implementing BI tools to streamline operations and improve efficiency with data-driven decision-making and process optimization. Our business intelligence solutions empower organizations to transform raw data into actionable insights, automate workflows, and optimize business processes for maximum efficiency and profitability.',
                    'fr' => 'Mise en œuvre d\'outils d\'intelligence d\'affaires pour rationaliser les opérations et améliorer l\'efficacité grâce à la prise de décision basée sur les données et l\'optimisation des processus. Nos solutions d\'intelligence d\'affaires permettent aux organisations de transformer les données brutes en informations exploitables, d\'automatiser les flux de travail et d\'optimiser les processus commerciaux pour une efficacité et une rentabilité maximales.',
                ],
                'icon' => 'briefcase',
                'icon_color' => '#F59E0B',
                'category' => 'MANUFACTURING',
                'order' => 4,
            ],
            [
                'title' => ['en' => 'Non-Governmental Organizations', 'fr' => 'ONG et Organismes à But Non Lucratif'],
                'short_description' => [
                    'en' => 'Providing data solutions to support mission-driven initiatives with impact measurement, donor analytics, and program optimization.',
                    'fr' => 'Solutions de données pour soutenir les initiatives à vocation sociale avec la mesure d\'impact, l\'analytique des donateurs et l\'optimisation des programmes.',
                ],
                'description' => [
                    'en' => 'Providing data solutions to support mission-driven initiatives with impact measurement, donor analytics, and program optimization. We enable NGOs to maximize their social impact through data-driven program management, transparent reporting, and efficient resource allocation while maintaining donor trust and regulatory compliance.',
                    'fr' => 'Solutions de données pour soutenir les initiatives à vocation sociale avec la mesure d\'impact, l\'analytique des donateurs et l\'optimisation des programmes. Nous permettons aux ONG de maximiser leur impact social grâce à une gestion de programmes basée sur les données, des rapports transparents et une allocation efficace des ressources tout en maintenant la confiance des donateurs et la conformité réglementaire.',
                ],
                'icon' => 'circle-dot',
                'icon_color' => '#8B5CF6',
                'category' => 'NGO',
                'order' => 5,
            ],
            [
                'title' => ['en' => 'Education & Training', 'fr' => 'Éducation et Formation'],
                'short_description' => [
                    'en' => 'Developing AI-driven platforms for personalized learning experiences with student performance analytics and adaptive learning paths.',
                    'fr' => 'Développement de plateformes propulsées par l\'IA pour des expériences d\'apprentissage personnalisées avec des analyses de performance étudiante et des parcours d\'apprentissage adaptatifs.',
                ],
                'description' => [
                    'en' => 'Developing AI-driven platforms for personalized learning experiences with student performance analytics and adaptive learning paths. Our education technology solutions help institutions deliver personalized learning at scale, track student progress with predictive analytics, and create engaging educational experiences through innovative technology.',
                    'fr' => 'Développement de plateformes propulsées par l\'IA pour des expériences d\'apprentissage personnalisées avec des analyses de performance étudiante et des parcours d\'apprentissage adaptatifs. Nos solutions de technologie éducative aident les institutions à offrir un apprentissage personnalisé à grande échelle, à suivre les progrès des étudiants avec des analyses prédictives et à créer des expériences éducatives engageantes grâce à une technologie innovante.',
                ],
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
                'slug' => Str::slug($industryData['title']['en']),
                'order' => $industryData['order'],
                'is_active' => true,
            ]);
        }
    }
}
