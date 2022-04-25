<?php

namespace App\EloquentCollections;


use Illuminate\Database\Eloquent\Collection;

class DoctorCollection extends Collection
{

    public function worksOn($date)
    {
        return $this->filter(fn($doctor) => $doctor->schedules->contains($date));
    }

    public function applySchedule()
    {
        $this->map(function ($doctor) {
            $doctor->schedules = $doctor->getSchedule();
            $doctor->slots = $doctor->schedules->map(
                fn($date) => [
                    'date' => $date,
                    'slot' => $doctor->availableSlotFor($date),
                ]);
    //                unset($doctor->shifts);
    //                unset($doctor->availableSlots);
            return $doctor;
        });
        return $this;
    }

    public function applyCharge($date)
    {
        $this->map(function ($doctor) use ($date) {
            $doctor->charge = $doctor->getFee($date);
            return $doctor;
        });
        return $this;
    }
}
