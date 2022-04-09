<?php

namespace Database\Factories;

use App\Models\Doctor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Shift>
 */
class ShiftFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'type' => collect(['am','pm'])->random(),
            'doctor_id' => Doctor::factory()->create()->id,
            'date' => collect(range(1,7))->random(),
            'slots_limit' => 30,
        ];
    }
}
