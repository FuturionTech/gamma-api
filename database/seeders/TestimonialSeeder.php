<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $testimonials = [
            [
                'name' => 'Catherine Moreau',
                'content' => 'Gamma Neutral transformed our risk management capabilities. Their team implemented a real-time analytics platform that reduced our risk reporting time from 48 hours to 15 minutes. The ROI was evident within the first quarter.',
                'position' => 'Chief Risk Officer',
                'company' => 'National Bank of Canada',
                'rating' => 5,
                'order' => 1,
            ],
            [
                'name' => 'Dr. Rajesh Patel',
                'content' => 'The predictive analytics platform Gamma Neutral built for our hospital network has been a game changer. We reduced patient readmission rates by 25% and improved overall care quality scores significantly. Their understanding of healthcare data regulations was impeccable.',
                'position' => 'Chief Medical Officer',
                'company' => 'Ontario Provincial Health Network',
                'rating' => 5,
                'order' => 2,
            ],
            [
                'name' => 'Jennifer Martinez',
                'content' => 'Our conversion rates increased by 35% after implementing Gamma Neutral\'s personalized recommendation system. Their data scientists worked closely with our team to understand our unique retail challenges and delivered a solution that exceeded our expectations.',
                'position' => 'VP of Digital Commerce',
                'company' => 'MapleLeaf Retail Group',
                'rating' => 5,
                'order' => 3,
            ],
            [
                'name' => 'Robert Thompson',
                'content' => 'Gamma Neutral\'s smart city analytics platform helped us reduce traffic congestion by 30% and improve emergency response times by 22%. Their ability to navigate government procurement and security requirements while maintaining innovation was remarkable.',
                'position' => 'Director of Innovation',
                'company' => 'City of Toronto',
                'rating' => 4,
                'order' => 4,
            ],
            [
                'name' => 'David Lee',
                'content' => 'Predictive maintenance from Gamma Neutral reduced our unplanned downtime by 45% and saved us over $1.6 million in annual maintenance costs. The implementation was smooth, and their ongoing support has been excellent.',
                'position' => 'VP of Operations',
                'company' => 'Canadian Auto Manufacturing Corp.',
                'rating' => 5,
                'order' => 5,
            ],
            [
                'name' => 'Dr. Emily Johnson',
                'content' => 'The learning analytics platform Gamma Neutral developed for our university consortium has been transformative. We can now identify at-risk students 60% earlier and have seen a 12% increase in graduation rates across five institutions.',
                'position' => 'Dean of Academic Affairs',
                'company' => 'Ontario University Consortium',
                'rating' => 5,
                'order' => 6,
            ],
            [
                'name' => 'Andrew Kim',
                'content' => 'After two security incidents, we needed a partner we could trust. Gamma Neutral implemented a zero-trust architecture that has kept us incident-free for over a year. Their cybersecurity team is world-class and their ongoing monitoring gives us peace of mind.',
                'position' => 'CTO',
                'company' => 'Northern Shield Insurance',
                'rating' => 5,
                'order' => 7,
            ],
        ];

        foreach ($testimonials as $testimonial) {
            Testimonial::create(array_merge($testimonial, ['is_active' => true]));
        }
    }
}
