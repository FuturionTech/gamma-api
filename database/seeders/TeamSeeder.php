<?php

namespace Database\Seeders;

use App\Models\SocialMediaPlatform;
use App\Models\Team;
use App\Models\TeamSocialMediaLink;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $linkedinPlatform = SocialMediaPlatform::where('name', 'LinkedIn')->first();
        $twitterPlatform = SocialMediaPlatform::where('name', 'Twitter')->first();

        $members = [
            [
                'name' => 'Dr. Sarah Chen',
                'role' => 'Chief Executive Officer',
                'email' => 'sarah.chen@gammaneutral.com',
                'biography' => 'Dr. Chen brings over 20 years of experience in data science and AI, having led transformative projects at Fortune 500 companies. She holds a Ph.D. in Computer Science from MIT and is passionate about democratizing data analytics for organizations of all sizes.',
                'profile_picture_url' => 'https://ui-avatars.com/api/?name=Sarah+Chen&size=400&background=8b5cf6&color=fff',
                'order' => 1,
                'socials' => [
                    ['platform' => 'LinkedIn', 'url' => 'https://linkedin.com/in/sarahchen'],
                    ['platform' => 'Twitter', 'url' => 'https://twitter.com/sarahchen'],
                ],
            ],
            [
                'name' => 'Michael Rodriguez',
                'role' => 'Chief Technology Officer',
                'email' => 'michael.rodriguez@gammaneutral.com',
                'biography' => 'Michael is a visionary technologist with 18 years of expertise in cloud architecture and scalable AI systems. He has architected data platforms processing billions of transactions daily for leading financial institutions and tech companies.',
                'profile_picture_url' => 'https://ui-avatars.com/api/?name=Michael+Rodriguez&size=400&background=8b5cf6&color=fff',
                'order' => 2,
                'socials' => [
                    ['platform' => 'LinkedIn', 'url' => 'https://linkedin.com/in/michaelrodriguez'],
                ],
            ],
            [
                'name' => 'Dr. Amara Okonkwo',
                'role' => 'Chief Data Scientist',
                'email' => 'amara.okonkwo@gammaneutral.com',
                'biography' => 'Dr. Okonkwo leads our data science initiatives, specializing in predictive analytics and deep learning. With a Ph.D. from Oxford and 15 years of experience, she has published over 30 papers on machine learning applications in enterprise settings.',
                'profile_picture_url' => 'https://ui-avatars.com/api/?name=Amara+Okonkwo&size=400&background=8b5cf6&color=fff',
                'order' => 3,
                'socials' => [
                    ['platform' => 'LinkedIn', 'url' => 'https://linkedin.com/in/amaraokonkwo'],
                    ['platform' => 'Twitter', 'url' => 'https://twitter.com/amaraokonkwo'],
                ],
            ],
            [
                'name' => 'Jessica Park',
                'role' => 'VP of Engineering',
                'email' => 'jessica.park@gammaneutral.com',
                'biography' => 'Jessica leads our engineering teams with 12 years of experience ensuring delivery of robust and scalable solutions. She is a Stanford alumna and a strong advocate for agile methodologies, continuous improvement, and engineering excellence.',
                'profile_picture_url' => 'https://ui-avatars.com/api/?name=Jessica+Park&size=400&background=8b5cf6&color=fff',
                'order' => 4,
                'socials' => [
                    ['platform' => 'LinkedIn', 'url' => 'https://linkedin.com/in/jessicapark'],
                ],
            ],
            [
                'name' => 'David Thompson',
                'role' => 'Head of AI Research',
                'email' => 'david.thompson@gammaneutral.com',
                'biography' => 'David leads our AI research division with 14 years of experience, focusing on developing next-generation machine learning algorithms. He holds a Ph.D. in Artificial Intelligence from Stanford and bridges the gap between cutting-edge research and practical business applications.',
                'profile_picture_url' => 'https://ui-avatars.com/api/?name=David+Thompson&size=400&background=8b5cf6&color=fff',
                'order' => 5,
                'socials' => [
                    ['platform' => 'LinkedIn', 'url' => 'https://linkedin.com/in/davidthompson'],
                ],
            ],
            [
                'name' => 'Rachel Kumar',
                'role' => 'Director of Client Success',
                'email' => 'rachel.kumar@gammaneutral.com',
                'biography' => 'Rachel ensures our clients achieve maximum value from our solutions. With an MBA from Wharton and 10 years of experience, she has a proven track record of building strong client relationships and driving successful enterprise implementations.',
                'profile_picture_url' => 'https://ui-avatars.com/api/?name=Rachel+Kumar&size=400&background=8b5cf6&color=fff',
                'order' => 6,
                'socials' => [
                    ['platform' => 'LinkedIn', 'url' => 'https://linkedin.com/in/rachelkumar'],
                ],
            ],
            [
                'name' => 'Marcus Wei',
                'role' => 'Director of Cybersecurity',
                'email' => 'marcus.wei@gammaneutral.com',
                'biography' => 'Marcus brings 16 years of cybersecurity expertise, having served as CISO at two major financial institutions. He holds CISSP and CISM certifications and leads our security practice, ensuring enterprise-grade protection for all client engagements.',
                'profile_picture_url' => 'https://ui-avatars.com/api/?name=Marcus+Wei&size=400&background=8b5cf6&color=fff',
                'order' => 7,
                'socials' => [
                    ['platform' => 'LinkedIn', 'url' => 'https://linkedin.com/in/marcuswei'],
                ],
            ],
            [
                'name' => 'Elena Vasquez',
                'role' => 'Cloud Architecture Lead',
                'email' => 'elena.vasquez@gammaneutral.com',
                'biography' => 'Elena is a certified AWS Solutions Architect and Google Cloud Professional with 11 years of experience designing scalable cloud infrastructure. She has led over 40 cloud migration projects across financial services, healthcare, and government sectors.',
                'profile_picture_url' => 'https://ui-avatars.com/api/?name=Elena+Vasquez&size=400&background=8b5cf6&color=fff',
                'order' => 8,
                'socials' => [
                    ['platform' => 'LinkedIn', 'url' => 'https://linkedin.com/in/elenavasquez'],
                    ['platform' => 'Twitter', 'url' => 'https://twitter.com/elenavasquez'],
                ],
            ],
            [
                'name' => 'James Osei',
                'role' => 'Operations Manager',
                'email' => 'james.osei@gammaneutral.com',
                'biography' => 'James oversees day-to-day operations and project delivery with 9 years of experience in consulting operations. PMP-certified and detail-oriented, he ensures every engagement runs smoothly from kickoff to delivery and beyond.',
                'profile_picture_url' => 'https://ui-avatars.com/api/?name=James+Osei&size=400&background=8b5cf6&color=fff',
                'order' => 9,
                'socials' => [
                    ['platform' => 'LinkedIn', 'url' => 'https://linkedin.com/in/jamesosei'],
                ],
            ],
        ];

        foreach ($members as $memberData) {
            $socials = $memberData['socials'];
            unset($memberData['socials']);

            $member = Team::create(array_merge($memberData, ['is_active' => true]));

            foreach ($socials as $social) {
                $platform = match ($social['platform']) {
                    'LinkedIn' => $linkedinPlatform,
                    'Twitter' => $twitterPlatform,
                    default => null,
                };

                if ($platform) {
                    TeamSocialMediaLink::create([
                        'team_id' => $member->id,
                        'platform_id' => $platform->id,
                        'url' => $social['url'],
                    ]);
                }
            }
        }
    }
}
