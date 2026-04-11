<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\ServiceStat;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ServiceStat>
 */
class ServiceStatFactory extends Factory
{
    protected $model = ServiceStat::class;

    public function definition(): array
    {
        return [
            'service_id' => Service::factory(),
            'icon' => fake()->randomElement(['bi-people', 'bi-graph-up', 'bi-star', 'bi-trophy']),
            'order' => fake()->numberBetween(0, 10),
        ];
    }
}
