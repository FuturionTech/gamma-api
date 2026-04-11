<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\ServicePainPoint;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ServicePainPoint>
 */
class ServicePainPointFactory extends Factory
{
    protected $model = ServicePainPoint::class;

    public function definition(): array
    {
        return [
            'service_id' => Service::factory(),
            'order' => fake()->numberBetween(0, 10),
        ];
    }
}
