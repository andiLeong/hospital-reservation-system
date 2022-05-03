<?php

namespace Tests\Feature;

use App\Models\Reservation;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\DoctorAndShifts;
use Tests\TestCase;

class ReservationFeatureTest extends TestCase
{
    use LazilyRefreshDatabase;
    use DoctorAndShifts;

    private $doctor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->doctor = $this->doctor();
    }

    /** @test */
    public function it_require_on_payload()
    {
        $this->reserveAction($this->doctor->id, ['on' => ''])->assertJsonValidationErrors('on');
    }

    /** @test */
    public function it_require_at_payload()
    {
        $this->reserveAction($this->doctor->id, ['at' => ''])->assertJsonValidationErrors('at');
    }

    /** @test */
    public function it_require_a_doctor_id_payload()
    {
        $this->reserveAction(9)->assertNotFound();
    }

    /** @test */
    public function it_require_a_date_format_on()
    {
        $this->reserveAction($this->doctor->id, ['on' => '00000000000'])->assertJsonValidationErrors('on');
    }

    /** @test */
    public function it_require_a_proper_format_at()
    {
        $this->reserveAction($this->doctor->id, ['at' => 'xxxxxxxxxxxxxxxxx'])->assertJsonValidationErrors('at');
    }

    /** @test */
    public function it_can_reserve_a_doctor()
    {
        $this->withoutExceptionHandling();
        $tomorrow = $this->tomorrow();
        $this->actingAs($this->patient());
        ['doctor' => $doctor] = $this->doctorShift([], [
            'date' => $tomorrow,
            'type' => 'am',
        ]);

        $this->reserveAction($doctor->id)->assertSuccessful();
    }

    /** @test */
    public function it_gets_422_if_doctor_does_not_work_on_the_date()
    {
        $tomorrow = $this->tomorrow();
        $this->actingAs($this->patient());
        ['doctor' => $doctor] = $this->doctorShift([], [
            'date' => $tomorrow,
            'type' => 'am',
        ]);

        $this->reserveAction($doctor->id, ['on' => now()->addDays(3)->format('Y-m-d')])->assertStatus(422);
    }

    /** @test */
    public function it_gets_422_if_doctor_is_fully_reserved()
    {
        $this->actingAs($patient = $this->patient());
        $timeFrame = '15:00-16:00';

        $shift = create(Shift::class, [
            'doctor_id' => $this->doctor->id,
            'type' => 'pm',
            'date' => $this->tomorrow(),
        ]);

        create(User::class, ['is_patient' => true])->each(fn($user) => Reservation::factory(10)->create([
            'doctor_id' => $this->doctor->id,
            'patient_id' => $user->id,
            'shift_id' => $shift->id,
            'at' => $timeFrame,
            'on' => $this->tomorrow(),
        ])
        );

        $this->reserveAction($this->doctor->id, ['at' => $timeFrame])->assertStatus(422);
    }

    private function reserveAction($doctorId, $attributes = [])
    {
        return $this->postJson("/reserve/{$doctorId}", array_merge([
            'on' => $this->tomorrow(),
            'at' => '10:00-11:00',
        ], $attributes));
    }
}
