<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Banner::create([
            'title' => 'Your Data Solutions',
            'subtitle' => 'At Gamma Neutral, we help businesses unlock the power of their data. From AI to cloud computing, our team delivers innovative, secure, and scalable solutions tailored to your organization\'s unique needs.',
            'cta_text' => 'Get Started',
            'cta_url' => '/contact',
            'order' => 1,
            'is_active' => true,
        ]);
    }
}

