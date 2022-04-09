<?php

namespace Tests\Feature;

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
        $this->reserveAction($this->doctor->id,['on' => ''])->assertJsonValidationErrors('on');
    }

    /** @test */
    public function it_require_at_payload()
    {
        $this->reserveAction($this->doctor->id,['at' => ''])->assertJsonValidationErrors('at');
    }

    /** @test */
    public function it_require_a_doctor_id_payload()
    {
        $this->reserveAction(9)->assertNotFound();
    }

    /** @test */
    public function it_require_a_date_format_on()
    {
        $this->reserveAction($this->doctor->id,['on' => '00000000000'])->assertJsonValidationErrors('on');
    }

    /** @test */
    public function it_require_a_proper_format_at()
    {
        $this->reserveAction($this->doctor->id,['at' => 'xxxxxxxxxxxxxxxxx'])->assertJsonValidationErrors('at');
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

    private function reserveAction($doctorId,$attributes = [])
    {
        return $this->postJson("/reserve/{$doctorId}", array_merge([
            'on' => $this->tomorrow(),
            'at' => '10:00-11:00',
        ],$attributes));
    }
}
