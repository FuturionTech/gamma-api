<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Service>
 */
class ServiceFactory extends Factory
{
    protected $model = Service::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->words(3, true);
        
        return [
            'application_id' => 1,
            'title' => $title,
            'description' => fake()->paragraph(),
            'icon' => fake()->randomElement(['chart', 'database', 'shield', 'cloud', 'brain']),
            'category' => fake()->randomElement(['Technology', 'Security', 'Analytics']),
            'slug' => Str::slug($title),
            'order' => fake()->numberBetween(1, 10),
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the service is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}

