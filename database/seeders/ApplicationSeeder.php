<?php

namespace Database\Seeders;

use App\Models\Application;
use Illuminate\Database\Seeder;

class ApplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Application::create([
            'name' => 'Gamma Neutral Consulting Inc.',
            'logo_url' => null,
            'settings' => [
                'company' => [
                    'name' => 'Gamma Neutral Consulting Inc.',
                    'slogan' => 'Your Data Solutions',
                    'address' => '108 Redpath Ave, Suite 19, Toronto, ON M4S 2J7, Canada',
                    'phone' => null,
                    'email' => 'info@gammaneutral.com',
                    'website' => 'https://www.gammaneutral.com',
                    'incorporated' => 'June 16, 2025',
                ],
                'social_media' => [
                    'linkedin' => null,
                    'twitter' => null,
                    'facebook' => null,
                ],
                'mission' => 'At Gamma Neutral Consulting, we empower businesses to harness the full potential of their data. Our mission is to deliver innovative, secure, and scalable solutions that drive informed decision-making and operational excellence.',
            ],
        ]);
    }
}

