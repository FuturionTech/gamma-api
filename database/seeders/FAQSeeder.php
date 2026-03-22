<?php

namespace Database\Seeders;

use App\Models\FAQ;
use Illuminate\Database\Seeder;

class FAQSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faqs = [
            [
                'question' => [
                    'en' => 'What services does Gamma Neutral offer?',
                    'fr' => 'Quels services offre Gamma Neutral ?',
                ],
                'answer' => [
                    'en' => 'We offer a comprehensive suite of services including Artificial Intelligence, Data Engineering, Cybersecurity, Business Intelligence, Big Data, Cloud Computing, and Project Management.',
                    'fr' => 'Nous offrons une gamme complète de services comprenant l\'intelligence artificielle, l\'ingénierie des données, la cybersécurité, l\'intelligence d\'affaires, les mégadonnées, l\'infonuagique et la gestion de projets.',
                ],
                'category' => 'Services',
                'order' => 1,
            ],
            [
                'question' => [
                    'en' => 'Which industries do you serve?',
                    'fr' => 'Quelles industries servez-vous ?',
                ],
                'answer' => [
                    'en' => 'We serve Banks & Financial Services, Education & Training, Business Management, Governments & Public Services, Non-Governmental Organizations, and Healthcare Services.',
                    'fr' => 'Nous desservons les banques et services financiers, l\'éducation et la formation, la gestion d\'entreprise, les gouvernements et services publics, les organisations non gouvernementales et les services de santé.',
                ],
                'category' => 'General',
                'order' => 2,
            ],
            [
                'question' => [
                    'en' => 'How do I get started with Gamma Neutral?',
                    'fr' => 'Comment démarrer avec Gamma Neutral ?',
                ],
                'answer' => [
                    'en' => 'Contact us through our contact form or email us at info@gammaneutral.com. We\'ll schedule a discovery call to understand your needs and propose a tailored solution.',
                    'fr' => 'Contactez-nous via notre formulaire de contact ou écrivez-nous à info@gammaneutral.com. Nous planifierons un appel de découverte pour comprendre vos besoins et vous proposer une solution sur mesure.',
                ],
                'category' => 'General',
                'order' => 3,
            ],
            [
                'question' => [
                    'en' => 'Do you offer cloud migration services?',
                    'fr' => 'Offrez-vous des services de migration cloud ?',
                ],
                'answer' => [
                    'en' => 'Yes, we provide comprehensive cloud computing services including cloud migration, architecture design, and cost-optimized deployment.',
                    'fr' => 'Oui, nous fournissons des services infonuagiques complets, y compris la migration vers le nuage, la conception d\'architecture et le déploiement optimisé en termes de coûts.',
                ],
                'category' => 'Services',
                'order' => 4,
            ],
            [
                'question' => [
                    'en' => 'What is your approach to data security?',
                    'fr' => 'Quelle est votre approche en matière de sécurité des données ?',
                ],
                'answer' => [
                    'en' => 'We implement advanced cybersecurity measures including threat detection, risk mitigation, and compliance-driven security frameworks to protect data integrity and privacy.',
                    'fr' => 'Nous mettons en œuvre des mesures de cybersécurité avancées, notamment la détection des menaces, l\'atténuation des risques et des cadres de sécurité axés sur la conformité pour protéger l\'intégrité et la confidentialité des données.',
                ],
                'category' => 'Security',
                'order' => 5,
            ],
        ];

        foreach ($faqs as $faq) {
            FAQ::create(array_merge($faq, ['is_active' => true]));
        }
    }
}
