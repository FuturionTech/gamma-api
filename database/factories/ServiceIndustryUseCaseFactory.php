<?php

namespace Database\Factories;

use App\Models\ServiceIndustryApplication;
use App\Models\ServiceIndustryUseCase;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ServiceIndustryUseCase>
 */
class ServiceIndustryUseCaseFactory extends Factory
{
    protected $model = ServiceIndustryUseCase::class;

    public function definition(): array
    {
        return [
            'service_industry_application_id' => ServiceIndustryApplication::factory(),
            'order' => fake()->numberBetween(0, 10),
        ];
    }
}
