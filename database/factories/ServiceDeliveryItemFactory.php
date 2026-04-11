<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\ServiceDeliveryItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ServiceDeliveryItem>
 */
class ServiceDeliveryItemFactory extends Factory
{
    protected $model = ServiceDeliveryItem::class;

    public function definition(): array
    {
        return [
            'service_id' => Service::factory(),
            'icon' => fake()->randomElement(['bi-check-circle', 'bi-lightning', 'bi-gear']),
            'order' => fake()->numberBetween(0, 10),
        ];
    }
}
