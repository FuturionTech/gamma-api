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
        $slug = 'svc-'.Str::random(8).'-'.now()->timestamp;

        return [
            'slug' => $slug,
            'icon' => fake()->randomElement(['chart', 'database', 'shield', 'cloud', 'brain']),
            'category' => fake()->randomElement(['Technology', 'Security', 'Analytics']),
            'order' => fake()->numberBetween(1, 10),
            'is_active' => true,
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Service $service) {
            if ($service->translations()->count() === 0) {
                $service->translateOrNew('en')->fill([
                    'title' => Str::headline(str_replace('-', ' ', $service->slug)),
                    'description' => fake()->paragraph(),
                    'short_description' => fake()->sentence(),
                ])->save();
            }
        });
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
