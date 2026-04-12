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

        $now = now();

        $jobs = [
            [
                'title' => json_encode(['en' => 'Senior Data Engineer', 'fr' => 'Ingénieur de données sénior']),
                'department' => 'Engineering',
                'location' => 'Toronto, ON (Hybrid)',
                'job_type' => 'full_time',
                'is_remote' => true,
                'experience_required' => '5+ years',
                'summary' => json_encode([
                    'en' => 'Design and build scalable data pipelines and infrastructure to power analytics and AI initiatives across the organization.',
                    'fr' => 'Concevoir et construire des pipelines de données évolutifs et une infrastructure pour alimenter les initiatives d\'analytique et d\'IA dans toute l\'organisation.',
                ]),
                'description' => json_encode([
                    'en' => 'We are looking for a Senior Data Engineer to design, build, and maintain robust data pipelines and infrastructure. You will work closely with data scientists and analysts to ensure reliable, scalable data flows that power our clients\' analytics and AI initiatives.',
                    'fr' => 'Nous recherchons un ingénieur de données sénior pour concevoir, construire et maintenir des pipelines de données robustes et une infrastructure fiable. Vous travaillerez en étroite collaboration avec les scientifiques de données et les analystes pour assurer des flux de données fiables et évolutifs qui alimentent les initiatives d\'analytique et d\'IA de nos clients.',
                ]),
                'responsibilities' => json_encode([
                    'en' => [
                        'Design and implement scalable ETL/ELT pipelines using modern data stack tools',
                        'Build and maintain data warehouse and data lake architectures',
                        'Optimize query performance and data processing workflows',
                        'Collaborate with data scientists to operationalize ML models',
                        'Implement data quality monitoring and alerting systems',
                    ],
                    'fr' => [
                        'Concevoir et mettre en œuvre des pipelines ETL/ELT évolutifs à l\'aide d\'outils modernes',
                        'Construire et maintenir les architectures d\'entrepôt de données et de lac de données',
                        'Optimiser les performances des requêtes et les flux de traitement des données',
                        'Collaborer avec les scientifiques de données pour opérationnaliser les modèles ML',
                        'Mettre en place des systèmes de surveillance et d\'alerte de la qualité des données',
                    ],
                ]),
                'requirements' => json_encode([
                    'en' => [
                        '5+ years of experience in data engineering or related field',
                        'Strong proficiency in Python and SQL',
                        'Experience with cloud platforms (AWS, Azure, or GCP)',
                        'Hands-on experience with Apache Spark, Airflow, or similar tools',
                        'Knowledge of data modeling and warehouse design patterns',
                    ],
                    'fr' => [
                        '5+ ans d\'expérience en ingénierie des données ou domaine connexe',
                        'Maîtrise solide de Python et SQL',
                        'Expérience avec les plateformes infonuagiques (AWS, Azure ou GCP)',
                        'Expérience pratique avec Apache Spark, Airflow ou outils similaires',
                        'Connaissance des modèles de données et des patrons de conception d\'entrepôts de données',
                    ],
                ]),
                'nice_to_have' => json_encode([
                    'en' => [
                        'Experience with Snowflake or Databricks',
                        'Familiarity with dbt or similar transformation tools',
                        'Knowledge of streaming data systems (Kafka, Kinesis)',
                    ],
                    'fr' => [
                        'Expérience avec Snowflake ou Databricks',
                        'Familiarité avec dbt ou outils de transformation similaires',
                        'Connaissance des systèmes de données en continu (Kafka, Kinesis)',
                    ],
                ]),
                'benefits' => json_encode([
                    'en' => [
                        'Competitive salary and performance bonuses',
                        'Flexible hybrid work arrangement',
                        'Comprehensive health and dental benefits',
                        'Professional development budget',
                        'Annual conference attendance',
                    ],
                    'fr' => [
                        'Salaire compétitif et primes de performance',
                        'Modalités de travail hybride flexibles',
                        'Avantages sociaux complets (santé et dentaire)',
                        'Budget de développement professionnel',
                        'Participation annuelle à des conférences',
                    ],
                ]),
                'skills' => json_encode(['Python', 'SQL', 'Apache Spark', 'AWS', 'Airflow', 'Snowflake', 'dbt']),
                'posted_date' => '2026-03-15',
                'status' => 'active',
            ],
            [
                'title' => json_encode(['en' => 'AI/ML Solutions Architect', 'fr' => 'Architecte de solutions IA/ML']),
                'department' => 'AI & Innovation',
                'location' => 'Toronto, ON (Remote)',
                'job_type' => 'full_time',
                'is_remote' => true,
                'experience_required' => '7+ years',
                'summary' => json_encode([
                    'en' => 'Lead the design and delivery of AI/ML solutions for enterprise clients, bridging the gap between business needs and cutting-edge technology.',
                    'fr' => 'Diriger la conception et la livraison de solutions IA/ML pour les clients entreprises, comblant le fossé entre les besoins d\'affaires et la technologie de pointe.',
                ]),
                'description' => json_encode([
                    'en' => 'As an AI/ML Solutions Architect, you will lead the design and delivery of AI and machine learning solutions for our enterprise clients. You will bridge the gap between business requirements and cutting-edge ML technology, ensuring solutions are scalable, reliable, and impactful.',
                    'fr' => 'En tant qu\'architecte de solutions IA/ML, vous dirigerez la conception et la livraison de solutions d\'intelligence artificielle et d\'apprentissage automatique pour nos clients entreprises. Vous comblerez le fossé entre les exigences d\'affaires et la technologie ML de pointe, en veillant à ce que les solutions soient évolutives, fiables et percutantes.',
                ]),
                'responsibilities' => json_encode([
                    'en' => [
                        'Design end-to-end ML system architectures for client engagements',
                        'Lead technical discovery sessions and present solutions to stakeholders',
                        'Guide data science teams on model development and deployment best practices',
                        'Evaluate and recommend AI/ML tools, platforms, and frameworks',
                        'Develop proof-of-concepts and technical prototypes',
                    ],
                    'fr' => [
                        'Concevoir des architectures de systèmes ML de bout en bout pour les mandats clients',
                        'Diriger des sessions de découverte technique et présenter des solutions aux parties prenantes',
                        'Guider les équipes de science des données sur les meilleures pratiques de développement et déploiement de modèles',
                        'Évaluer et recommander des outils, plateformes et cadres IA/ML',
                        'Développer des preuves de concept et des prototypes techniques',
                    ],
                ]),
                'requirements' => json_encode([
                    'en' => [
                        '7+ years in software engineering with 4+ years focused on ML/AI',
                        'Deep understanding of ML frameworks (TensorFlow, PyTorch, scikit-learn)',
                        'Experience deploying ML models to production at scale',
                        'Strong cloud architecture skills (AWS SageMaker, Azure ML, or Vertex AI)',
                        'Excellent client-facing communication and presentation skills',
                    ],
                    'fr' => [
                        '7+ ans en génie logiciel dont 4+ ans en IA/ML',
                        'Compréhension approfondie des cadres ML (TensorFlow, PyTorch, scikit-learn)',
                        'Expérience du déploiement de modèles ML en production à grande échelle',
                        'Solides compétences en architecture infonuagique (AWS SageMaker, Azure ML ou Vertex AI)',
                        'Excellentes compétences en communication et en présentation devant les clients',
                    ],
                ]),
                'nice_to_have' => json_encode([
                    'en' => [
                        'Experience with LLMs and generative AI applications',
                        'MLOps experience (MLflow, Kubeflow, or similar)',
                        'Published research or patents in AI/ML',
                    ],
                    'fr' => [
                        'Expérience avec les LLM et les applications d\'IA générative',
                        'Expérience MLOps (MLflow, Kubeflow ou similaire)',
                        'Recherches publiées ou brevets en IA/ML',
                    ],
                ]),
                'benefits' => json_encode([
                    'en' => [
                        'Competitive salary with equity options',
                        'Fully remote work flexibility',
                        'Comprehensive health and dental benefits',
                        'Generous professional development budget',
                        'Access to cutting-edge AI tools and research resources',
                    ],
                    'fr' => [
                        'Salaire compétitif avec options d\'équité',
                        'Flexibilité de travail entièrement à distance',
                        'Avantages sociaux complets (santé et dentaire)',
                        'Généreux budget de développement professionnel',
                        'Accès aux outils d\'IA de pointe et aux ressources de recherche',
                    ],
                ]),
                'skills' => json_encode(['Python', 'TensorFlow', 'PyTorch', 'AWS SageMaker', 'MLOps', 'LLMs']),
                'posted_date' => '2026-03-20',
                'status' => 'active',
            ],
            [
                'title' => json_encode(['en' => 'Cybersecurity Consultant', 'fr' => 'Consultant en cybersécurité']),
                'department' => 'Security',
                'location' => 'Toronto, ON (Hybrid)',
                'job_type' => 'full_time',
                'is_remote' => false,
                'experience_required' => '3+ years',
                'summary' => json_encode([
                    'en' => 'Help our clients protect their digital assets through comprehensive security assessments, threat modeling, and compliance-driven security strategies.',
                    'fr' => 'Aider nos clients à protéger leurs actifs numériques grâce à des évaluations de sécurité complètes, la modélisation des menaces et des stratégies de sécurité axées sur la conformité.',
                ]),
                'description' => json_encode([
                    'en' => 'Join our security practice as a Cybersecurity Consultant to help clients protect their digital assets and meet regulatory requirements. You will conduct security assessments, design security architectures, and implement threat detection and response strategies across diverse industries.',
                    'fr' => 'Rejoignez notre pratique de sécurité en tant que consultant en cybersécurité pour aider nos clients à protéger leurs actifs numériques et à répondre aux exigences réglementaires. Vous effectuerez des évaluations de sécurité, concevrez des architectures de sécurité et mettrez en œuvre des stratégies de détection et de réponse aux menaces dans diverses industries.',
                ]),
                'responsibilities' => json_encode([
                    'en' => [
                        'Conduct vulnerability assessments and penetration testing',
                        'Design and implement security frameworks aligned with industry standards',
                        'Perform risk assessments and develop mitigation strategies',
                        'Advise clients on compliance requirements (PIPEDA, SOC 2, HIPAA)',
                        'Develop incident response plans and security awareness training',
                    ],
                    'fr' => [
                        'Effectuer des évaluations de vulnérabilité et des tests d\'intrusion',
                        'Concevoir et mettre en œuvre des cadres de sécurité conformes aux normes de l\'industrie',
                        'Réaliser des évaluations des risques et développer des stratégies d\'atténuation',
                        'Conseiller les clients sur les exigences de conformité (LPRPDE, SOC 2, HIPAA)',
                        'Développer des plans de réponse aux incidents et des formations de sensibilisation à la sécurité',
                    ],
                ]),
                'requirements' => json_encode([
                    'en' => [
                        '3+ years of experience in cybersecurity consulting or operations',
                        'Knowledge of security frameworks (NIST, ISO 27001, CIS Controls)',
                        'Experience with SIEM tools and threat detection platforms',
                        'Understanding of cloud security principles and best practices',
                        'Strong analytical and problem-solving skills',
                    ],
                    'fr' => [
                        '3+ ans d\'expérience en consultation ou opérations de cybersécurité',
                        'Connaissance des cadres de sécurité (NIST, ISO 27001, CIS Controls)',
                        'Expérience avec les outils SIEM et les plateformes de détection des menaces',
                        'Compréhension des principes et meilleures pratiques de sécurité infonuagique',
                        'Solides compétences analytiques et en résolution de problèmes',
                    ],
                ]),
                'nice_to_have' => json_encode([
                    'en' => [
                        'CISSP, CEH, or OSCP certification',
                        'Experience with security automation and SOAR platforms',
                        'Bilingual (English/French) is an asset',
                    ],
                    'fr' => [
                        'Certification CISSP, CEH ou OSCP',
                        'Expérience avec l\'automatisation de la sécurité et les plateformes SOAR',
                        'Le bilinguisme (anglais/français) est un atout',
                    ],
                ]),
                'benefits' => json_encode([
                    'en' => [
                        'Competitive salary and annual bonuses',
                        'Hybrid work model with downtown Toronto office',
                        'Comprehensive health and dental benefits',
                        'Certification and training sponsorship',
                        'Collaborative team environment',
                    ],
                    'fr' => [
                        'Salaire compétitif et primes annuelles',
                        'Modèle de travail hybride avec bureau au centre-ville de Toronto',
                        'Avantages sociaux complets (santé et dentaire)',
                        'Parrainage de certifications et de formations',
                        'Environnement d\'équipe collaboratif',
                    ],
                ]),
                'skills' => json_encode(['SIEM', 'Penetration Testing', 'NIST', 'ISO 27001', 'Cloud Security', 'Risk Assessment']),
                'posted_date' => '2026-03-25',
                'status' => 'active',
            ],
            [
                'title' => json_encode(['en' => 'Business Intelligence Analyst', 'fr' => 'Analyste en intelligence d\'affaires']),
                'department' => 'Analytics',
                'location' => 'Toronto, ON (Remote)',
                'job_type' => 'full_time',
                'is_remote' => true,
                'experience_required' => '2+ years',
                'summary' => json_encode([
                    'en' => 'Transform raw data into actionable business insights through interactive dashboards, reports, and data-driven recommendations for our consulting clients.',
                    'fr' => 'Transformer les données brutes en informations commerciales exploitables grâce à des tableaux de bord interactifs, des rapports et des recommandations basées sur les données pour nos clients en consultation.',
                ]),
                'description' => json_encode([
                    'en' => 'We are seeking a Business Intelligence Analyst to transform raw data into actionable insights for our consulting clients. You will design interactive dashboards, build analytical reports, and deliver data-driven recommendations that help organizations make better decisions.',
                    'fr' => 'Nous recherchons un analyste en intelligence d\'affaires pour transformer les données brutes en informations exploitables pour nos clients. Vous concevrez des tableaux de bord interactifs, créerez des rapports analytiques et fournirez des recommandations basées sur les données qui aident les organisations à prendre de meilleures décisions.',
                ]),
                'responsibilities' => json_encode([
                    'en' => [
                        'Design and develop interactive dashboards and reports using Power BI or Tableau',
                        'Gather and analyze business requirements from client stakeholders',
                        'Build and maintain data models optimized for reporting',
                        'Perform ad-hoc analysis to support strategic decision-making',
                        'Document data definitions, business rules, and reporting standards',
                    ],
                    'fr' => [
                        'Concevoir et développer des tableaux de bord et rapports interactifs avec Power BI ou Tableau',
                        'Recueillir et analyser les exigences d\'affaires des parties prenantes clients',
                        'Construire et maintenir des modèles de données optimisés pour le reporting',
                        'Effectuer des analyses ad hoc pour soutenir la prise de décision stratégique',
                        'Documenter les définitions de données, les règles d\'affaires et les normes de reporting',
                    ],
                ]),
                'requirements' => json_encode([
                    'en' => [
                        '2+ years of experience in business intelligence or data analytics',
                        'Proficiency in Power BI, Tableau, or similar BI tools',
                        'Strong SQL skills for data extraction and analysis',
                        'Experience with data modeling and star/snowflake schemas',
                        'Excellent communication skills for presenting insights to non-technical audiences',
                    ],
                    'fr' => [
                        '2+ ans d\'expérience en intelligence d\'affaires ou analytique de données',
                        'Maîtrise de Power BI, Tableau ou outils BI similaires',
                        'Solides compétences SQL pour l\'extraction et l\'analyse de données',
                        'Expérience avec la modélisation de données et les schémas en étoile/flocon',
                        'Excellentes compétences en communication pour présenter des informations à un public non technique',
                    ],
                ]),
                'nice_to_have' => json_encode([
                    'en' => [
                        'Experience with Python or R for statistical analysis',
                        'Knowledge of cloud data warehouses (Snowflake, BigQuery)',
                        'Familiarity with agile project methodologies',
                    ],
                    'fr' => [
                        'Expérience avec Python ou R pour l\'analyse statistique',
                        'Connaissance des entrepôts de données infonuagiques (Snowflake, BigQuery)',
                        'Familiarité avec les méthodologies de projet agiles',
                    ],
                ]),
                'benefits' => json_encode([
                    'en' => [
                        'Competitive salary',
                        'Fully remote work flexibility',
                        'Comprehensive health and dental benefits',
                        'Professional development and certification support',
                        'Mentorship from senior consultants',
                    ],
                    'fr' => [
                        'Salaire compétitif',
                        'Flexibilité de travail entièrement à distance',
                        'Avantages sociaux complets (santé et dentaire)',
                        'Soutien au développement professionnel et aux certifications',
                        'Mentorat par des consultants séniors',
                    ],
                ]),
                'skills' => json_encode(['Power BI', 'Tableau', 'SQL', 'Data Modeling', 'Python', 'Excel']),
                'posted_date' => '2026-04-01',
                'status' => 'active',
            ],
        ];

        foreach ($jobs as $job) {
            $titleJson = $job['title'];

            DB::table('job_positions')->updateOrInsert(
                ['title' => $titleJson],
                array_merge($job, [
                    'created_at' => $now,
                    'updated_at' => $now,
                ])
            );
        }
    }

    public function down(): void
    {
        // Intentionally empty — data migration.
    }
};
