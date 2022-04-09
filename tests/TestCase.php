<?php

namespace Tests;

use App\Models\Doctor;
use App\ValueObject\Patient;
use App\Models\Reservation;
use App\ValueObject\TimeFrame;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function user($attributes)
    {
        return User::factory()->create($attributes);
    }

    public function patient()
    {
        return $this->user([
            'is_patient' => true,
        ]);
    }

    public function tomorrow()
    {
        return today()->addDay()->format('Y-m-d');
    }

    public function doctor($attributes = null)
    {
        return Doctor::factory()->create($attributes);
    }

    public function collect($items)
    {
        return collect($this->toAssoArray($items));
    }

    public function toAssoArray($items)
    {
       return $this->toArray($items,true);
    }

    public function toArray($items,$associative = false)
    {
        return json_decode($items,$associative);
    }

    public function massiveReserve($doctor,$date,$time,$quantity)
    {
        User::factory($quantity)->create([
            'is_patient' => true,
        ])->each(fn($user) => $this->reserve($doctor,$user,$date,$time) );
    }

    public function reserve($doctor,$user,$date,$time)
    {
        return resolve(Reservation::class)->make(
            $doctor,
            Patient::init($user),
            $date,
            TimeFrame::make($time)
        );
    }
}
