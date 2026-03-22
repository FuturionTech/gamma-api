<?php

namespace Database\Seeders;

use App\Models\Stat;
use Illuminate\Database\Seeder;

class StatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stats = [
            [
                'label' => ['en' => 'Projects Completed', 'fr' => 'Projets réalisés'],
                'value' => '150',
                'unit' => '+',
                'icon' => 'folder',
                'order' => 1,
            ],
            [
                'label' => ['en' => 'Happy Clients', 'fr' => 'Clients satisfaits'],
                'value' => '50',
                'unit' => '+',
                'icon' => 'users',
                'order' => 2,
            ],
            [
                'label' => ['en' => 'Years Experience', 'fr' => 'Années d\'expérience'],
                'value' => '10',
                'unit' => '+',
                'icon' => 'calendar',
                'order' => 3,
            ],
            [
                'label' => ['en' => 'Client Satisfaction', 'fr' => 'Satisfaction des clients'],
                'value' => '98',
                'unit' => '%',
                'icon' => 'star',
                'order' => 4,
            ],
        ];

        foreach ($stats as $stat) {
            Stat::create(array_merge($stat, ['is_active' => true]));
        }
    }
}
