<?php

namespace Database\Factories;

use App\Models\Medication;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Medication>
 */
class MedicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $activeIngredient = fake()->randomElement([
            'Amoxicilina', 'Dipirona', 'Losartana', 'Metformina', 'Omeprazol', 'Paracetamol',
        ]);
        $strength = fake()->randomElement(['250 mg', '500 mg', '50 mg', '850 mg', '20 mg']);
        $form = fake()->randomElement(['comprimido', 'cápsula']);

        return [
            'name' => "{$activeIngredient} {$strength} {$form}",
            'active_ingredient' => $activeIngredient,
            'strength' => $strength,
            'pharmaceutical_form' => $form,
            'presentation' => 'blister com 10',
            'supply_unit' => $form,
            'quantity_precision' => 0,
            'is_active' => true,
        ];
    }
}
