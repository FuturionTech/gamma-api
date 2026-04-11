<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\ServiceIndustryApplication;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ServiceIndustryApplication>
 */
class ServiceIndustryApplicationFactory extends Factory
{
    protected $model = ServiceIndustryApplication::class;

    public function definition(): array
    {
        return [
            'service_id' => Service::factory(),
            'icon' => fake()->randomElement(['bi-building', 'bi-hospital', 'bi-bank', 'bi-cart']),
            'order' => fake()->numberBetween(0, 10),
        ];
    }
}
