<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\DoctorAndShifts;
use Tests\TestCase;

class DoctorTest extends TestCase
{
    use LazilyRefreshDatabase, DoctorAndShifts;

    /** @test */
    public function a_doctor_can_have_their_shifts()
    {
        ['doctor' => $doctor] = $this->doctorShift([], [
            'date' => $tomorrow = $this->tomorrow(),
            'type' => 'am',
        ]);

        $this->assertEquals($tomorrow, $doctor->shifts->first()->date);
        $this->assertEquals('am', $doctor->shifts->first()->type);
    }
}
