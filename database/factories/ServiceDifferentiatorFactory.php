<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\ServiceDifferentiator;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ServiceDifferentiator>
 */
class ServiceDifferentiatorFactory extends Factory
{
    protected $model = ServiceDifferentiator::class;

    public function definition(): array
    {
        return [
            'service_id' => Service::factory(),
            'icon' => fake()->randomElement(['bi-star', 'bi-award', 'bi-trophy']),
            'order' => fake()->numberBetween(0, 10),
        ];
    }
}
