<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\ServiceApproachStep;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ServiceApproachStep>
 */
class ServiceApproachStepFactory extends Factory
{
    protected $model = ServiceApproachStep::class;

    public function definition(): array
    {
        return [
            'service_id' => Service::factory(),
            'icon' => fake()->randomElement(['bi-1-circle', 'bi-2-circle', 'bi-3-circle', 'bi-4-circle']),
            'order' => fake()->numberBetween(0, 10),
        ];
    }
}
