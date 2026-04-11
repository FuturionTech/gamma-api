<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\ServiceTechnology;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ServiceTechnology>
 */
class ServiceTechnologyFactory extends Factory
{
    protected $model = ServiceTechnology::class;

    public function definition(): array
    {
        return [
            'service_id' => Service::factory(),
            'icon' => fake()->randomElement(['bi-code', 'bi-cloud', 'bi-database']),
            'order' => fake()->numberBetween(0, 10),
        ];
    }
}
