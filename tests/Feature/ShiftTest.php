<?php

namespace Tests\Feature;

use App\ValueObject\Patient;
use App\Models\Reservation;
use App\Models\Shift;
use App\ValueObject\TimeFrame;
use Carbon\CarbonPeriod;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Collection;
use Tests\DoctorAndShifts;
use Tests\TestCase;

class ShiftTest extends TestCase
{
    use LazilyRefreshDatabase;
    use DoctorAndShifts;

    /** @test */
    public function it_can_get_a_next_seven_day_schedule_of_doctor()
    {
        $contents = $this->collect($this->get('/shift')->getContent());

        $sevenDays = Collection::week()->map(fn($collection) => $collection->format('Y-m-d'))->toArray();
        $this->assertEquals($sevenDays, $contents->pluck('date')->toArray());
    }

    /** @test */
    public function it_can_get_a_doctor_shift()
    {
        ['doctor' => $doctor] = $this->doctorShift([
            'name' => 'sherry',
        ], [
            'date' => now(),
        ]);

        $contents = $this->collect($this->get('/shift')->getContent());
        $first = collect($contents->first());

        $this->assertTrue($first->has('doctors'));
        $this->assertEquals($doctor->name, $first->get('doctors')[0]['name']);
    }

    /** @test */
    public function it_can_get_doctor_charge_on_the_week_fee_if_shift_fee_not_defined()
    {
        $this->doctorShift([
            'weekday_fee' => 20,
            'weekend_fee' => 200,
        ], [
            'date' => now(),
        ]);

        $contents = $this->collect($this->get('/shift')->getContent());
        $doctor = collect($contents->first())->get('doctors')[0];

        if (now()->isWeekend()) {
            $this->assertEquals(200, $doctor['charge']);
        } else {
            $this->assertEquals(20, $doctor['charge']);
        }
    }

    /** @test */
    public function it_can_get_available_slot_of_a_doctor()
    {
        ['doctor' => $doctor, 'shift' => $shift] = $this->doctorShift([], [
            'date' => $tomorrow = $this->tomorrow(),
            'type' => 'am',
        ]);

        resolve(Reservation::class)->make(
            $doctor,
            Patient::init($this->patient()),
            $tomorrow,
            TimeFrame::make('09:00-10:00')
        );

        $doctor = $this->toAssoArray($this->get('/shift')->getContent())[1]['doctors'][0];
        $this->assertArrayHasKey('slots', $doctor);
        $this->assertEquals($shift->slots_limit - 1,  $this->getSlot($doctor,$tomorrow));

    }

    /** @test */
    public function it_can_get_remain_slot_of_a_doctor_who_has_a_am_and_pm_shift_of_a_same_day()
    {
        ['doctor' => $doctor, 'shift' => $shift] = $this->doctorShift([], [
            'date' => $tomorrow = $this->tomorrow(),
            'type' => 'am',
        ]);

        $shift2 = Shift::factory()->create([
            'doctor_id' => $doctor->id,
            'date' => $tomorrow,
            'type' => 'pm',
        ]);

        resolve(Reservation::class)->make(
            $doctor,
            Patient::init($this->patient()),
            $tomorrow,
            TimeFrame::make('09:00-10:00')
        );

        resolve(Reservation::class)->make(
            $doctor,
            Patient::init($this->patient()),
            $tomorrow,
            TimeFrame::make('14:00-15:00')
        );

//        dd($this->toAssoArray($this->get('/shift')->getContent()));
        $doctor = $this->toAssoArray($this->get('/shift')->getContent())[1]['doctors'][0];
        $this->assertEquals(($shift->slots_limit + $shift2->slots_limit) - 2, $this->getSlot($doctor,$tomorrow));
    }

    public function getSlot($doctor,$date)
    {
        return collect($doctor['slots'])->filter(fn($slot) => $slot['date'] == $date)->values()->first()['slot'];
    }

    public function expectedJson()
    {
        $periods = CarbonPeriod::create(today(), today()->addDays(7))->toArray();
        return collect($periods)->map(fn($period) => [
            'date' => $period->format('Y-m-d'),
            'doctors' => [
                [
                    'name' => 'sherry',
                    'bio' => 'xxx',
                    'profile_pic' => 'xxx',
                    'available_slot' => 30,
                    'registration_fee' => 300,
                ],
                [
                    'name' => 'Andrew',
                    'bio' => 'xxx',
                    'profile_pic' => 'xxx',
                    'available_slot' => 30,
                    'registration_fee' => 300,
                ],
            ],
        ]);
    }
}
