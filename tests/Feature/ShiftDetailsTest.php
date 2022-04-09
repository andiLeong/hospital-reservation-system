<?php

namespace Tests\Feature;

use App\Models\Shift;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\DoctorAndShifts;
use Tests\TestCase;

class ShiftDetailsTest extends TestCase
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
    public function it_require_date_payload()
    {
        $this->fire($this->doctor->id, ['date' => ''])->assertJsonValidationErrors('date');
    }

    /** @test */
    public function it_require_a_proper_format_date()
    {
        $this->fire($this->doctor->id, ['date' => 'xxxxxxxxxxxxxxxxx'])->assertJsonValidationErrors('date');
    }

    /** @test */
    public function it_require_a_doctor_id_payload()
    {
        $this->fire(9)->assertNotFound();
    }

    /** @test */
    public function it_can_am_shift_detail_from_a_doctor()
    {
        ['doctor' => $doctor] = $this->doctorShift([], [
            'date' => $tomorrow = $this->tomorrow(),
            'type' => 'am',
        ]);
        $response = $this->toAssoArray($this->fire($doctor->id)->getContent());

        $this->assertArrayHasKey('time_frames', $response);
        $this->assertArrayHasKey('am', $response['time_frames']);
        $this->assertArrayNotHasKey('pm', $response['time_frames']);
    }

    /** @test */
    public function it_can_pm_shift_detail_from_a_doctor()
    {
        ['doctor' => $doctor] = $this->doctorShift([], [
            'date' => $tomorrow = $this->tomorrow(),
            'type' => 'pm',
        ]);
        $response = $this->toAssoArray($this->fire($doctor->id)->getContent());

        $this->assertArrayHasKey('pm', $response['time_frames']);
        $this->assertArrayNotHasKey('am', $response['time_frames']);
    }

    /** @test */
    public function it_can_full_shift_detail_from_a_doctor()
    {
        ['doctor' => $doctor] = $this->doctorShift([], [
            'date' => $tomorrow = $this->tomorrow(),
            'type' => 'pm',
        ]);

        Shift::factory()->create([
            'date' => $tomorrow = $this->tomorrow(),
            'type' => 'am',
            'doctor_id' => $doctor->id,
        ]);
        $response = $this->toAssoArray($this->fire($doctor->id)->getContent());

        $this->assertArrayHasKey('pm', $response['time_frames']);
        $this->assertArrayHasKey('am', $response['time_frames']);
    }

    /** @test */
    public function if_a_doctor_is_fully_booked_attribute_is_true()
    {
        $this->withoutExceptionHandling();
        ['doctor' => $doctor] = $this->doctorShift([], [
            'date' => $tomorrow = $this->tomorrow(),
            'type' => 'am',
        ]);
        $time = '10:00-11:00';

        $this->massiveReserve($doctor, $tomorrow, $time, 10);

        $response = $this->toAssoArray($this->fire($doctor->id)->getContent());
//dd($response);
        $shift = collect($response['time_frames']['am'])->where('time', $time)->values()->first();
//        dd($shift);
        $this->assertArrayHasKey('is_fully_booked', $shift);
        $this->assertTrue($shift['is_fully_booked']);
    }


    /** @test */
    public function if_a_doctor_is_fully_booked_attribute_is_false()
    {
        $this->withoutExceptionHandling();
        ['doctor' => $doctor] = $this->doctorShift([], [
            'date' => $tomorrow = $this->tomorrow(),
            'type' => 'am',
        ]);
        $time = '10:00-11:00';

        $this->reserve($doctor, $this->patient(), $tomorrow, $time);

        $response = $this->toAssoArray($this->fire($doctor->id)->getContent());
        $shift = collect($response['time_frames']['am'])->where('time', $time)->values()->first();
        $this->assertFalse($shift['is_fully_booked']);
    }

    /** @test */
    public function it_gets_422_if_doctor_does_not_work_on_the_date()
    {
        $this->fire($this->doctor->id, ['date' => '2022-04-12'])->assertJsonValidationErrors('date');
    }

    private function fire(mixed $id, array $attributes = [])
    {
        $query = http_build_query(array_merge([
            'date' => $this->tomorrow(),
        ], $attributes));
        return $this->getJson("/shift/{$id}?{$query}",);
    }
}
