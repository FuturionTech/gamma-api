<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\ServiceBusinessImpact;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ServiceBusinessImpact>
 */
class ServiceBusinessImpactFactory extends Factory
{
    protected $model = ServiceBusinessImpact::class;

    public function definition(): array
    {
        return [
            'service_id' => Service::factory(),
            'icon' => fake()->randomElement(['bi-graph-up', 'bi-currency-dollar', 'bi-speedometer']),
            'order' => fake()->numberBetween(0, 10),
        ];
    }
}
