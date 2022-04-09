<?php

namespace App\Models;

use App\EloquentCollections\DoctorCollection;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Doctor extends Model
{
    use HasFactory;

    public function newCollection(array $models = [])
    {
        return new DoctorCollection($models);
    }

    public function shifts()
    {
        return $this->hasMany(Shift::class, 'doctor_id');
    }

    public function getFee($date)
    {
        if (Carbon::parse($date)->isWeekend()) {
            return $this->weekend_fee;
        }
        return $this->weekday_fee;
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'doctor_id', 'id');
    }

    public function availableSlots()
    {
        return $this->hasMany(AvailableSlotsForDate::class, 'doctor_id', 'id');
    }

    public function availableSlotFor($date)
    {
        $remaining = $this->availableSlots;
        return $remaining->contains('date', $date)
            ? $remaining->where('date', $date)->first()['remain']
            : $this->shifts->where('date', $date)->pluck('slots_limit')->sum();
    }

    public function getSchedule()
    {
        return $this->shifts->pluck('date')->unique()->values();
    }

    public function workOn($date, $shifts = null)
    {
        if (is_null($shifts)) {
            $shifts = $this->shifts;
        }
        return $shifts->contains('date', $date);
    }

    public function doesNotWorkOn($date, $shifts = null)
    {
        return !$this->workOn($date, $shifts);
    }

    public function getShiftOn($date)
    {
        $type = $this->shifts->where('date',$date)->values()->pluck('type');
        return $type->count() == 1
            ? $type->first()
            : null ;
    }

    public function fullyBookedAt($time, Collection $reservations)
    {
        return $reservations->where('at',$time)->count() >= $this->slot_per_time_frame;
    }
}
