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
                'question' => 'What services does Gamma Neutral offer?',
                'answer' => 'We offer a comprehensive suite of services including Artificial Intelligence, Data Engineering, Cybersecurity, Business Intelligence, Big Data, Cloud Computing, and Project Management.',
                'category' => 'Services',
                'order' => 1,
            ],
            [
                'question' => 'Which industries do you serve?',
                'answer' => 'We serve Banks & Financial Services, Education & Training, Business Management, Governments & Public Services, Non-Governmental Organizations, and Healthcare Services.',
                'category' => 'General',
                'order' => 2,
            ],
            [
                'question' => 'How do I get started with Gamma Neutral?',
                'answer' => 'Contact us through our contact form or email us at info@gammaneutral.com. We\'ll schedule a discovery call to understand your needs and propose a tailored solution.',
                'category' => 'General',
                'order' => 3,
            ],
            [
                'question' => 'Do you offer cloud migration services?',
                'answer' => 'Yes, we provide comprehensive cloud computing services including cloud migration, architecture design, and cost-optimized deployment.',
                'category' => 'Services',
                'order' => 4,
            ],
            [
                'question' => 'What is your approach to data security?',
                'answer' => 'We implement advanced cybersecurity measures including threat detection, risk mitigation, and compliance-driven security frameworks to protect data integrity and privacy.',
                'category' => 'Security',
                'order' => 5,
            ],
        ];

        foreach ($faqs as $faq) {
            FAQ::create(array_merge($faq, ['is_active' => true]));
        }
    }
}

