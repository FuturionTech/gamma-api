<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\ServiceCapabilityGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceCapabilityGroupFactory extends Factory
{
    protected $model = ServiceCapabilityGroup::class;

    public function definition(): array
    {
        return [
            'service_id' => Service::factory(),
            'icon' => fake()->randomElement(['bi-gear', 'bi-lightbulb', 'bi-shield']),
            'order' => fake()->numberBetween(0, 10),
        ];
    }
}
