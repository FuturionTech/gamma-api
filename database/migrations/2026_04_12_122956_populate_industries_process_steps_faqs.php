<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Skip during test suite unless explicitly opted in.
        if (app()->runningUnitTests() && ! env('FORCE_CONTENT_BACKFILL')) {
            return;
        }

        DB::transaction(function () {
            $this->seedIndustries();
            $this->seedProcessSteps();
            $this->seedFaqs();
        });
    }

    public function down(): void
    {
        // Intentionally empty — data migration.
    }

    // ─── Industries (6 rows, bilingual) ─────────────────────────────

    private function seedIndustries(): void
    {
        $now = now();

        $industries = [
            [
                'title' => json_encode(['en' => 'Banks & Financial Services', 'fr' => 'Banques et Services Financiers']),
                'short_description' => json_encode([
                    'en' => 'Enhancing data security and analytics for better financial decision-making with AI-powered risk modeling and fraud detection.',
                    'fr' => 'Renforcement de la sécurité des données et de l\'analytique pour une meilleure prise de décision financière grâce à la modélisation des risques et à la détection de fraudes propulsées par l\'IA.',
                ]),
                'description' => json_encode([
                    'en' => 'Enhancing data security and analytics for better financial decision-making with AI-powered risk modeling and fraud detection. We provide comprehensive solutions for banking institutions to modernize their data infrastructure, implement advanced analytics, and ensure regulatory compliance while maintaining the highest security standards.',
                    'fr' => 'Renforcement de la sécurité des données et de l\'analytique pour une meilleure prise de décision financière grâce à la modélisation des risques et à la détection de fraudes propulsées par l\'IA. Nous offrons des solutions complètes aux institutions bancaires pour moderniser leur infrastructure de données, mettre en œuvre des analyses avancées et assurer la conformité réglementaire tout en maintenant les normes de sécurité les plus élevées.',
                ]),
                'icon' => 'building-columns',
                'icon_color' => '#6B7280',
                'category' => 'FINANCIAL_SERVICES',
                'slug' => 'banks-financial-services',
                'order' => 1,
            ],
            [
                'title' => json_encode(['en' => 'Healthcare Services', 'fr' => 'Services de Santé']),
                'short_description' => json_encode([
                    'en' => 'Developing AI and big data solutions to improve patient care and operational efficiency while maintaining HIPAA compliance.',
                    'fr' => 'Développement de solutions d\'IA et de mégadonnées pour améliorer les soins aux patients et l\'efficacité opérationnelle tout en maintenant la conformité HIPAA.',
                ]),
                'description' => json_encode([
                    'en' => 'Developing AI and big data solutions to improve patient care and operational efficiency while maintaining HIPAA compliance. Our healthcare solutions enable medical institutions to leverage data-driven insights for better patient outcomes, streamline operations, and ensure data privacy and security in accordance with healthcare regulations.',
                    'fr' => 'Développement de solutions d\'IA et de mégadonnées pour améliorer les soins aux patients et l\'efficacité opérationnelle tout en maintenant la conformité HIPAA. Nos solutions de santé permettent aux institutions médicales de tirer parti des informations basées sur les données pour de meilleurs résultats pour les patients, de rationaliser les opérations et d\'assurer la confidentialité et la sécurité des données conformément aux réglementations en matière de santé.',
                ]),
                'icon' => 'heart-pulse',
                'icon_color' => '#10B981',
                'category' => 'HEALTHCARE',
                'slug' => 'healthcare-services',
                'order' => 2,
            ],
            [
                'title' => json_encode(['en' => 'Governments & Public Services', 'fr' => 'Gouvernements et Services Publics']),
                'short_description' => json_encode([
                    'en' => 'Building secure data infrastructures for public sector organizations with smart city initiatives and citizen service optimization.',
                    'fr' => 'Construction d\'infrastructures de données sécurisées pour les organisations du secteur public avec des initiatives de villes intelligentes et l\'optimisation des services aux citoyens.',
                ]),
                'description' => json_encode([
                    'en' => 'Building secure data infrastructures for public sector organizations with smart city initiatives and citizen service optimization. We help government agencies modernize their technology infrastructure, implement data-driven governance, and create citizen-centric digital services while ensuring data sovereignty and security.',
                    'fr' => 'Construction d\'infrastructures de données sécurisées pour les organisations du secteur public avec des initiatives de villes intelligentes et l\'optimisation des services aux citoyens. Nous aidons les agences gouvernementales à moderniser leur infrastructure technologique, à mettre en œuvre une gouvernance basée sur les données et à créer des services numériques centrés sur les citoyens tout en assurant la souveraineté et la sécurité des données.',
                ]),
                'icon' => 'landmark',
                'icon_color' => '#3B82F6',
                'category' => 'GOVERNMENT',
                'slug' => 'governments-public-services',
                'order' => 3,
            ],
            [
                'title' => json_encode(['en' => 'Business Management', 'fr' => 'Gestion d\'Entreprise']),
                'short_description' => json_encode([
                    'en' => 'Implementing BI tools to streamline operations and improve efficiency with data-driven decision-making and process optimization.',
                    'fr' => 'Mise en œuvre d\'outils d\'intelligence d\'affaires pour rationaliser les opérations et améliorer l\'efficacité grâce à la prise de décision basée sur les données et l\'optimisation des processus.',
                ]),
                'description' => json_encode([
                    'en' => 'Implementing BI tools to streamline operations and improve efficiency with data-driven decision-making and process optimization. Our business intelligence solutions empower organizations to transform raw data into actionable insights, automate workflows, and optimize business processes for maximum efficiency and profitability.',
                    'fr' => 'Mise en œuvre d\'outils d\'intelligence d\'affaires pour rationaliser les opérations et améliorer l\'efficacité grâce à la prise de décision basée sur les données et l\'optimisation des processus. Nos solutions d\'intelligence d\'affaires permettent aux organisations de transformer les données brutes en informations exploitables, d\'automatiser les flux de travail et d\'optimiser les processus commerciaux pour une efficacité et une rentabilité maximales.',
                ]),
                'icon' => 'briefcase',
                'icon_color' => '#F59E0B',
                'category' => 'MANUFACTURING',
                'slug' => 'business-management',
                'order' => 4,
            ],
            [
                'title' => json_encode(['en' => 'Non-Governmental Organizations', 'fr' => 'ONG et Organismes à But Non Lucratif']),
                'short_description' => json_encode([
                    'en' => 'Providing data solutions to support mission-driven initiatives with impact measurement, donor analytics, and program optimization.',
                    'fr' => 'Solutions de données pour soutenir les initiatives à vocation sociale avec la mesure d\'impact, l\'analytique des donateurs et l\'optimisation des programmes.',
                ]),
                'description' => json_encode([
                    'en' => 'Providing data solutions to support mission-driven initiatives with impact measurement, donor analytics, and program optimization. We enable NGOs to maximize their social impact through data-driven program management, transparent reporting, and efficient resource allocation while maintaining donor trust and regulatory compliance.',
                    'fr' => 'Solutions de données pour soutenir les initiatives à vocation sociale avec la mesure d\'impact, l\'analytique des donateurs et l\'optimisation des programmes. Nous permettons aux ONG de maximiser leur impact social grâce à une gestion de programmes basée sur les données, des rapports transparents et une allocation efficace des ressources tout en maintenant la confiance des donateurs et la conformité réglementaire.',
                ]),
                'icon' => 'circle-dot',
                'icon_color' => '#8B5CF6',
                'category' => 'NGO',
                'slug' => 'non-governmental-organizations',
                'order' => 5,
            ],
            [
                'title' => json_encode(['en' => 'Education & Training', 'fr' => 'Éducation et Formation']),
                'short_description' => json_encode([
                    'en' => 'Developing AI-driven platforms for personalized learning experiences with student performance analytics and adaptive learning paths.',
                    'fr' => 'Développement de plateformes propulsées par l\'IA pour des expériences d\'apprentissage personnalisées avec des analyses de performance étudiante et des parcours d\'apprentissage adaptatifs.',
                ]),
                'description' => json_encode([
                    'en' => 'Developing AI-driven platforms for personalized learning experiences with student performance analytics and adaptive learning paths. Our education technology solutions help institutions deliver personalized learning at scale, track student progress with predictive analytics, and create engaging educational experiences through innovative technology.',
                    'fr' => 'Développement de plateformes propulsées par l\'IA pour des expériences d\'apprentissage personnalisées avec des analyses de performance étudiante et des parcours d\'apprentissage adaptatifs. Nos solutions de technologie éducative aident les institutions à offrir un apprentissage personnalisé à grande échelle, à suivre les progrès des étudiants avec des analyses prédictives et à créer des expériences éducatives engageantes grâce à une technologie innovante.',
                ]),
                'icon' => 'graduation-cap',
                'icon_color' => '#EF4444',
                'category' => 'EDUCATION',
                'slug' => 'education-training',
                'order' => 6,
            ],
        ];

        foreach ($industries as $industry) {
            $slug = $industry['slug'];
            unset($industry['slug']);

            DB::table('industries')->updateOrInsert(
                ['slug' => $slug],
                array_merge($industry, [
                    'slug' => $slug,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])
            );
        }
    }

    // ─── Process Steps (6 steps + 19 items, bilingual) ──────────────

    private function seedProcessSteps(): void
    {
        $now = now();

        $steps = [
            [
                'step_number' => 1,
                'title' => json_encode(['en' => 'Discovery', 'fr' => 'Découverte']),
                'short_description' => json_encode([
                    'en' => 'We begin by understanding your unique challenges, data landscape, and business objectives.',
                    'fr' => 'Nous commençons par comprendre vos défis uniques, votre paysage de données et vos objectifs d\'affaires.',
                ]),
                'description' => json_encode([
                    'en' => 'We begin by understanding your unique challenges, data landscape, and business objectives. Through comprehensive analysis and stakeholder engagement, we identify opportunities and define clear goals.',
                    'fr' => 'Nous commençons par comprendre vos défis uniques, votre paysage de données et vos objectifs d\'affaires. Grâce à une analyse approfondie et à l\'engagement des parties prenantes, nous identifions les opportunités et définissons des objectifs clairs.',
                ]),
                'icon' => 'magnifying-glass',
                'icon_color' => '#1E1E1E',
                'slug' => 'discovery',
                'order' => 1,
                'items' => [
                    ['title' => json_encode(['en' => 'Requirements Analysis', 'fr' => 'Analyse des exigences']), 'icon' => 'check', 'order' => 1],
                    ['title' => json_encode(['en' => 'Current State Assessment', 'fr' => 'Évaluation de l\'état actuel']), 'icon' => 'check', 'order' => 2],
                    ['title' => json_encode(['en' => 'Stakeholder Interviews', 'fr' => 'Entrevues avec les parties prenantes']), 'icon' => 'check', 'order' => 3],
                ],
            ],
            [
                'step_number' => 2,
                'title' => json_encode(['en' => 'Solution Design', 'fr' => 'Conception de Solutions']),
                'short_description' => json_encode([
                    'en' => 'Our experts architect comprehensive solutions aligned with your goals and industry best practices.',
                    'fr' => 'Nos experts conçoivent des solutions complètes alignées sur vos objectifs et les meilleures pratiques de l\'industrie.',
                ]),
                'description' => json_encode([
                    'en' => 'Our experts architect comprehensive solutions aligned with your goals and industry best practices. We create detailed roadmaps and select the right technologies to meet your specific needs.',
                    'fr' => 'Nos experts conçoivent des solutions complètes alignées sur vos objectifs et les meilleures pratiques de l\'industrie. Nous créons des feuilles de route détaillées et sélectionnons les bonnes technologies pour répondre à vos besoins spécifiques.',
                ]),
                'icon' => 'lightbulb',
                'icon_color' => '#8B5CF6',
                'slug' => 'solution-design',
                'order' => 2,
                'items' => [
                    ['title' => json_encode(['en' => 'Architecture Planning', 'fr' => 'Planification de l\'architecture']), 'icon' => 'check', 'order' => 1],
                    ['title' => json_encode(['en' => 'Technology Selection', 'fr' => 'Sélection technologique']), 'icon' => 'check', 'order' => 2],
                    ['title' => json_encode(['en' => 'Roadmap Creation', 'fr' => 'Création de la feuille de route']), 'icon' => 'check', 'order' => 3],
                ],
            ],
            [
                'step_number' => 3,
                'title' => json_encode(['en' => 'Development', 'fr' => 'Développement']),
                'short_description' => json_encode([
                    'en' => 'Using agile methodologies, we build robust solutions with continuous testing and refinement.',
                    'fr' => 'En utilisant des méthodologies agiles, nous construisons des solutions robustes avec des tests et des raffinements continus.',
                ]),
                'description' => json_encode([
                    'en' => 'Using agile methodologies, we build robust solutions with continuous testing and refinement. Our development process ensures high quality, scalability, and alignment with your business objectives.',
                    'fr' => 'En utilisant des méthodologies agiles, nous construisons des solutions robustes avec des tests et des raffinements continus. Notre processus de développement assure une haute qualité, l\'évolutivité et l\'alignement avec vos objectifs d\'affaires.',
                ]),
                'icon' => 'code-bracket',
                'icon_color' => '#3B82F6',
                'slug' => 'development',
                'order' => 3,
                'items' => [
                    ['title' => json_encode(['en' => 'Agile Development', 'fr' => 'Développement agile']), 'icon' => 'check', 'order' => 1],
                    ['title' => json_encode(['en' => 'Quality Assurance', 'fr' => 'Assurance qualité']), 'icon' => 'check', 'order' => 2],
                    ['title' => json_encode(['en' => 'CI/CD Pipeline', 'fr' => 'Pipeline CI/CD']), 'icon' => 'check', 'order' => 3],
                ],
            ],
            [
                'step_number' => 4,
                'title' => json_encode(['en' => 'Deployment', 'fr' => 'Déploiement']),
                'short_description' => json_encode([
                    'en' => 'Seamless deployment with comprehensive training ensures your team maximizes value.',
                    'fr' => 'Un déploiement transparent avec une formation complète garantit que votre équipe maximise la valeur.',
                ]),
                'description' => json_encode([
                    'en' => 'Seamless deployment with comprehensive training ensures your team maximizes value. We handle production releases, provide thorough documentation, and train your team for success.',
                    'fr' => 'Un déploiement transparent avec une formation complète garantit que votre équipe maximise la valeur. Nous gérons les mises en production, fournissons une documentation complète et formons votre équipe pour le succès.',
                ]),
                'icon' => 'rocket-launch',
                'icon_color' => '#F59E0B',
                'slug' => 'deployment',
                'order' => 4,
                'items' => [
                    ['title' => json_encode(['en' => 'Production Release', 'fr' => 'Mise en production']), 'icon' => 'check', 'order' => 1],
                    ['title' => json_encode(['en' => 'User Training', 'fr' => 'Formation des utilisateurs']), 'icon' => 'check', 'order' => 2],
                    ['title' => json_encode(['en' => 'Documentation', 'fr' => 'Documentation']), 'icon' => 'check', 'order' => 3],
                ],
            ],
            [
                'step_number' => 5,
                'title' => json_encode(['en' => 'Support', 'fr' => 'Soutien']),
                'short_description' => json_encode([
                    'en' => 'Our partnership continues with dedicated support and optimization for sustained success.',
                    'fr' => 'Notre partenariat se poursuit avec un soutien dédié et une optimisation pour un succès durable.',
                ]),
                'description' => json_encode([
                    'en' => 'Our partnership continues with dedicated support and optimization for sustained success. We provide ongoing monitoring, performance tuning, and regular updates to ensure your solution evolves with your needs.',
                    'fr' => 'Notre partenariat se poursuit avec un soutien dédié et une optimisation pour un succès durable. Nous fournissons une surveillance continue, l\'optimisation des performances et des mises à jour régulières pour garantir que votre solution évolue avec vos besoins.',
                ]),
                'icon' => 'lifebuoy',
                'icon_color' => '#10B981',
                'slug' => 'support',
                'order' => 5,
                'items' => [
                    ['title' => json_encode(['en' => '24/7 Monitoring', 'fr' => 'Surveillance continue']), 'icon' => 'check', 'order' => 1],
                    ['title' => json_encode(['en' => 'Performance Tuning', 'fr' => 'Optimisation des performances']), 'icon' => 'check', 'order' => 2],
                    ['title' => json_encode(['en' => 'Regular Updates', 'fr' => 'Mises à jour régulières']), 'icon' => 'check', 'order' => 3],
                ],
            ],
            [
                'step_number' => 6,
                'title' => json_encode(['en' => 'Results', 'fr' => 'Résultats']),
                'short_description' => json_encode([
                    'en' => 'Our proven process delivers measurable outcomes for your business.',
                    'fr' => 'Notre processus éprouvé produit des résultats mesurables pour votre entreprise.',
                ]),
                'description' => json_encode([
                    'en' => 'Our proven process delivers tangible results: faster time to market, reduced operational costs, improved data quality, and competitive advantage.',
                    'fr' => 'Notre processus éprouvé produit des résultats tangibles : un délai de mise en marché réduit, des coûts opérationnels réduits, une qualité des données améliorée et un avantage concurrentiel.',
                ]),
                'icon' => 'star',
                'icon_color' => '#000000',
                'slug' => 'results',
                'order' => 6,
                'items' => [
                    ['title' => json_encode(['en' => 'Faster Time to Market', 'fr' => 'Délai de mise en marché réduit']), 'icon' => 'arrow-right', 'order' => 1],
                    ['title' => json_encode(['en' => 'Reduced Operational Costs', 'fr' => 'Réduction des coûts opérationnels']), 'icon' => 'arrow-right', 'order' => 2],
                    ['title' => json_encode(['en' => 'Improved Data Quality', 'fr' => 'Qualité des données améliorée']), 'icon' => 'arrow-right', 'order' => 3],
                    ['title' => json_encode(['en' => 'Competitive Advantage', 'fr' => 'Avantage concurrentiel']), 'icon' => 'arrow-right', 'order' => 4],
                ],
            ],
        ];

        foreach ($steps as $stepData) {
            $items = $stepData['items'];
            $slug = $stepData['slug'];
            unset($stepData['items'], $stepData['slug']);

            DB::table('process_steps')->updateOrInsert(
                ['slug' => $slug],
                array_merge($stepData, [
                    'slug' => $slug,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])
            );

            $stepId = DB::table('process_steps')->where('slug', $slug)->value('id');

            // Delete existing items then re-insert for idempotency
            DB::table('process_step_items')->where('process_step_id', $stepId)->delete();

            foreach ($items as $item) {
                DB::table('process_step_items')->insert(array_merge($item, [
                    'process_step_id' => $stepId,
                    'description' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]));
            }
        }
    }

    // ─── FAQs (10 rows, bilingual) ──────────────────────────────────

    private function seedFaqs(): void
    {
        $now = now();

        $faqs = [
            [
                'question' => json_encode(['en' => 'What services does Gamma Neutral offer?', 'fr' => 'Quels services offre Gamma Neutral ?']),
                'answer' => json_encode([
                    'en' => 'We offer a comprehensive suite of services including Artificial Intelligence, Data Engineering, Cybersecurity, Business Intelligence, Big Data, Cloud Computing, and Project Management.',
                    'fr' => 'Nous offrons une gamme complète de services comprenant l\'intelligence artificielle, l\'ingénierie des données, la cybersécurité, l\'intelligence d\'affaires, les mégadonnées, l\'infonuagique et la gestion de projets.',
                ]),
                'category' => 'Services',
                'order' => 1,
            ],
            [
                'question' => json_encode(['en' => 'Which industries do you serve?', 'fr' => 'Quelles industries servez-vous ?']),
                'answer' => json_encode([
                    'en' => 'We serve Banks & Financial Services, Education & Training, Business Management, Governments & Public Services, Non-Governmental Organizations, and Healthcare Services.',
                    'fr' => 'Nous desservons les banques et services financiers, l\'éducation et la formation, la gestion d\'entreprise, les gouvernements et services publics, les organisations non gouvernementales et les services de santé.',
                ]),
                'category' => 'General',
                'order' => 2,
            ],
            [
                'question' => json_encode(['en' => 'How do I get started with Gamma Neutral?', 'fr' => 'Comment démarrer avec Gamma Neutral ?']),
                'answer' => json_encode([
                    'en' => 'Contact us through our contact form or email us at info@gammaneutral.com. We\'ll schedule a discovery call to understand your needs and propose a tailored solution.',
                    'fr' => 'Contactez-nous via notre formulaire de contact ou écrivez-nous à info@gammaneutral.com. Nous planifierons un appel de découverte pour comprendre vos besoins et vous proposer une solution sur mesure.',
                ]),
                'category' => 'General',
                'order' => 3,
            ],
            [
                'question' => json_encode(['en' => 'Do you offer cloud migration services?', 'fr' => 'Offrez-vous des services de migration cloud ?']),
                'answer' => json_encode([
                    'en' => 'Yes, we provide comprehensive cloud computing services including cloud migration, architecture design, and cost-optimized deployment.',
                    'fr' => 'Oui, nous fournissons des services infonuagiques complets, y compris la migration vers le nuage, la conception d\'architecture et le déploiement optimisé en termes de coûts.',
                ]),
                'category' => 'Services',
                'order' => 4,
            ],
            [
                'question' => json_encode(['en' => 'What is your approach to data security?', 'fr' => 'Quelle est votre approche en matière de sécurité des données ?']),
                'answer' => json_encode([
                    'en' => 'We implement advanced cybersecurity measures including threat detection, risk mitigation, and compliance-driven security frameworks to protect data integrity and privacy.',
                    'fr' => 'Nous mettons en œuvre des mesures de cybersécurité avancées, notamment la détection des menaces, l\'atténuation des risques et des cadres de sécurité axés sur la conformité pour protéger l\'intégrité et la confidentialité des données.',
                ]),
                'category' => 'Security',
                'order' => 5,
            ],
            [
                'question' => json_encode(['en' => 'How long does a typical project take?', 'fr' => 'Combien de temps dure un projet typique ?']),
                'answer' => json_encode([
                    'en' => 'Project timelines depend on scope and complexity. A focused pilot typically takes 4-8 weeks, while enterprise-wide implementations may span 3-6 months. We provide detailed timelines during the discovery phase.',
                    'fr' => 'Les délais de projet dépendent de la portée et de la complexité. Un projet pilote ciblé prend généralement de 4 à 8 semaines, tandis que les implémentations à l\'échelle de l\'entreprise peuvent s\'étendre sur 3 à 6 mois. Nous fournissons des échéanciers détaillés lors de la phase de découverte.',
                ]),
                'category' => 'General',
                'order' => 6,
            ],
            [
                'question' => json_encode(['en' => 'Do you work with startups or only large enterprises?', 'fr' => 'Travaillez-vous avec des startups ou uniquement des grandes entreprises ?']),
                'answer' => json_encode([
                    'en' => 'We work with organizations of all sizes - from fast-growing startups needing their first data platform to Fortune 500 companies modernizing legacy systems. Our engagement models adapt to your scale and budget.',
                    'fr' => 'Nous travaillons avec des organisations de toutes tailles - des startups en croissance rapide ayant besoin de leur première plateforme de données aux entreprises du Fortune 500 modernisant leurs systèmes existants. Nos modèles d\'engagement s\'adaptent à votre échelle et à votre budget.',
                ]),
                'category' => 'General',
                'order' => 7,
            ],
            [
                'question' => json_encode(['en' => 'What technologies and platforms do you specialize in?', 'fr' => 'Dans quelles technologies et plateformes êtes-vous spécialisés ?']),
                'answer' => json_encode([
                    'en' => 'Our team has deep expertise in AWS, Azure, GCP, Snowflake, Databricks, Apache Spark, Power BI, Tableau, Python, and leading AI/ML frameworks. We are vendor-agnostic and recommend the best tools for your specific needs.',
                    'fr' => 'Notre équipe possède une expertise approfondie en AWS, Azure, GCP, Snowflake, Databricks, Apache Spark, Power BI, Tableau, Python et les principaux cadres IA/ML. Nous sommes agnostiques en matière de fournisseurs et recommandons les meilleurs outils pour vos besoins spécifiques.',
                ]),
                'category' => 'Services',
                'order' => 8,
            ],
            [
                'question' => json_encode(['en' => 'How do you handle data privacy and regulatory compliance?', 'fr' => 'Comment gérez-vous la confidentialité des données et la conformité réglementaire ?']),
                'answer' => json_encode([
                    'en' => 'We build compliance into every engagement from day one. Our team is experienced with PIPEDA, SOC 2, HIPAA, GDPR, and industry-specific regulations. We implement data governance frameworks, encryption, access controls, and audit trails as standard practice.',
                    'fr' => 'Nous intégrons la conformité dans chaque mandat dès le premier jour. Notre équipe est expérimentée avec la LPRPDE, SOC 2, HIPAA, RGPD et les réglementations sectorielles. Nous mettons en œuvre des cadres de gouvernance des données, le chiffrement, les contrôles d\'accès et les pistes d\'audit comme pratique standard.',
                ]),
                'category' => 'Security',
                'order' => 9,
            ],
            [
                'question' => json_encode(['en' => 'Can you augment our existing team or do you only deliver full projects?', 'fr' => 'Pouvez-vous renforcer notre équipe existante ou ne livrez-vous que des projets complets ?']),
                'answer' => json_encode([
                    'en' => 'Both. We offer dedicated team augmentation where our consultants embed with your team, as well as end-to-end project delivery. Many clients start with augmentation and transition to managed projects as needs evolve.',
                    'fr' => 'Les deux. Nous offrons l\'augmentation d\'équipe dédiée où nos consultants s\'intègrent à votre équipe, ainsi que la livraison de projets de bout en bout. Beaucoup de clients commencent par l\'augmentation et passent à des projets gérés au fur et à mesure que les besoins évoluent.',
                ]),
                'category' => 'General',
                'order' => 10,
            ],
        ];

        foreach ($faqs as $faq) {
            DB::table('faqs')->updateOrInsert(
                ['order' => $faq['order']],
                array_merge($faq, [
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])
            );
        }
    }
};
