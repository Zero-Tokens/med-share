<?php

namespace Database\Factories;

use App\Models\Facility;
use App\Models\Municipality;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Facility>
 */
class FacilityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'municipality_id' => Municipality::factory(),
            'cnes' => str_pad((string) fake()->unique()->numberBetween(1, 9999999), 7, '0', STR_PAD_LEFT),
            'name' => 'UBS '.fake()->streetName(),
            'address' => fake()->streetAddress(),
            'postal_code' => fake()->numerify('########'),
            'latitude' => fake()->latitude(-8.3, -6.0),
            'longitude' => fake()->longitude(-38.8, -34.8),
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the facility is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
