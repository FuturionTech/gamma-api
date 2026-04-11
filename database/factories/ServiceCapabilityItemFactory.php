<?php

namespace Database\Factories;

use App\Models\ServiceCapabilityGroup;
use App\Models\ServiceCapabilityItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceCapabilityItemFactory extends Factory
{
    protected $model = ServiceCapabilityItem::class;

    public function definition(): array
    {
        return [
            'service_capability_group_id' => ServiceCapabilityGroup::factory(),
            'order' => fake()->numberBetween(0, 10),
        ];
    }
}
