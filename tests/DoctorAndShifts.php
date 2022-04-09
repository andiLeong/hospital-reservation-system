<?php


namespace Tests;


use App\Models\Shift;

trait DoctorAndShifts
{

    public function doctorShift($doctor = [] , $shift = [])
    {
        $doctor = $this->doctor($doctor);
        $shiftModel = Shift::factory()->create(array_merge([
            'doctor_id' => $doctor->id,
        ],$shift));

        return [
            'doctor' => $doctor,
            'shift' => $shiftModel,
        ];
    }
}
