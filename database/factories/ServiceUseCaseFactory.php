<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\ServiceUseCase;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ServiceUseCase>
 */
class ServiceUseCaseFactory extends Factory
{
    protected $model = ServiceUseCase::class;

    public function definition(): array
    {
        return [
            'service_id' => Service::factory(),
            'order' => fake()->numberBetween(0, 10),
        ];
    }
}
