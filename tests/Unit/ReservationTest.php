<?php

namespace Tests\Unit;

use App\Exceptions\DoctorNotWorkOnThisDateException;
use App\Exceptions\SlotIsFullyReservedException;
use App\Models\AvailableSlotsForDate;
use App\Models\Doctor;
use App\Models\Reservation;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class ReservationTest extends TestCase
{
    use LazilyRefreshDatabase;

    private User $patient;
    private $doctor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->patient = $this->patient();
        $this->doctor = Doctor::factory()->create();
    }

    /** @test */
    public function it_can_make_a_reservation_with_a_doctor()
    {
        $shift = Shift::factory()->create([
            'date' => $tomorrow = $this->tomorrow(),
            'doctor_id' => $this->doctor->id,
            'type' => 'am',
        ]);

        $this->reserve($this->doctor, $this->patient, $tomorrow, '09:00-10:00');

        $result = Reservation::where('shift_id', $shift->id)->where('patient_id', $this->patient->id)->where('doctor_id', $this->doctor->id)->exists();
        $this->assertTrue($result);
    }

    /** @test */
    public function it_throw_exception_if_doctor_does_not_work_that_date()
    {
        $this->expectException(DoctorNotWorkOnThisDateException::class);
        Shift::factory()->create([
            'date' => now()->addDay(),
            'doctor_id' => $this->doctor->id,
            'type' => 'am',
        ]);

        $this->reserve($this->doctor, $this->patient, now()->addDays(7)->format('Y-m-d'), '09:00-10:00');
    }

    /** @test */
    public function it_throw_exception_if_doctor_shift_time_frame_is_fully_reserved()
    {
        $this->expectException(SlotIsFullyReservedException::class);

        $timeFrame = '15:00-16:00';
        $tomorrow = now()->addDay()->format('Y-m-d');
        Shift::factory()->create([
            'date' => $tomorrow,
            'doctor_id' => $this->doctor->id,
            'type' => 'pm',
        ]);
        $this->assertDatabaseCount('Reservations', 0);

        $this->massiveReserve($this->doctor, $tomorrow, $timeFrame, 10);
        $this->assertDatabaseCount('Reservations', 10);
        $this->reserve($this->doctor, $this->patient, $tomorrow, $timeFrame);
    }

    /** @test */
    public function it_can_record_available_slot_for_a_date()
    {
        $shift = Shift::factory()->create([
            'date' => $tomorrow = $this->tomorrow(),
            'doctor_id' => $this->doctor->id,
            'type' => 'am',
        ]);

        $this->assertDatabaseCount('available_slots_for_dates', 0);
        $this->reserve($this->doctor, $this->patient, $tomorrow, '09:00-10:00');

        $first = AvailableSlotsForDate::first();
        $this->assertEquals($shift->slots_limit - 1, $first->remain);
        $this->assertNotNull($first);
    }
}
