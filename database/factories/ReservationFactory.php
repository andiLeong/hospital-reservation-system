<?php

namespace Database\Factories;

use App\Models\Doctor;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reservation>
 */
class ReservationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $doctor = Doctor::factory()->create();
        $date = now()->addDay()->format('Y-m-d');

        return [
            'doctor_id' => $doctor->id,
            'shift_id' => Shift::factory()->create([
                'doctor_id' => $doctor->id,
                'date' => $date
            ])->id,
            'patient_id' => User::factory()->create()->id,
            'on' => $date,
            'at' => '10:00-11:00',
        ];
    }
}
