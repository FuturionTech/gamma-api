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
            ['label' => 'Projects Completed', 'value' => '150', 'unit' => '+', 'icon' => 'folder', 'order' => 1],
            ['label' => 'Happy Clients', 'value' => '50', 'unit' => '+', 'icon' => 'users', 'order' => 2],
            ['label' => 'Years Experience', 'value' => '10', 'unit' => '+', 'icon' => 'calendar', 'order' => 3],
            ['label' => 'Client Satisfaction', 'value' => '98', 'unit' => '%', 'icon' => 'star', 'order' => 4],
        ];

        foreach ($stats as $stat) {
            Stat::create(array_merge($stat, ['is_active' => true]));
        }
    }
}

