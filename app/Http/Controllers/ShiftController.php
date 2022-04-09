<?php

namespace App\Http\Controllers;

use App\Collections\TimesCollection;
use App\Models\Doctor;
use App\Models\Reservation;
use App\Query\DoctorQuery;
use App\Rules\DoctorDoesNotWorkOnDate;
use Illuminate\Support\Collection;

class ShiftController extends Controller
{
    public function index(DoctorQuery $query)
    {
        $weeks = Collection::week()->map(fn($date) => $date->format('Y-m-d'));
        $doctors = $query($weeks)->applySchedule();

        return $weeks->map(fn($date) => [
            'date' => $date,
            'doctors' => $doctors->filter(fn($doctor) => $doctor->schedules->contains($date))
                ->values()
                ->applyCharge($date)
        ]);
    }

    public function show(Doctor $doctor, TimesCollection $timesCollection)
    {
        $data = request()->validate([
            'date' => ['required', 'date_format:Y-m-d', new DoctorDoesNotWorkOnDate($doctor)],
        ]);

        $doctor->load('shifts');
        $date = $data['date'];

        $doctor->charge = $doctor->getFee($date);
        $timeFrames = $timesCollection->get($doctor->getShiftOn($date));

        $reservations = Reservation::doctorId($doctor->id)->on($date)->get();

        return [
            'doctor' => $doctor,
            'time_frames' => $timeFrames->map(fn($times) => $times->map(function ($time) use ($reservations, $doctor) {
                $time['is_fully_booked'] = $doctor->fullyBookedAt($time['time'], $reservations);
                return $time;
            })
            ),
        ];

    }
}
