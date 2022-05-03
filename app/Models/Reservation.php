<?php

namespace App\Models;

use App\Exceptions\DoctorNotWorkOnThisDateException;
use App\Exceptions\SlotIsFullyReservedException;
use App\ValueObject\Patient;
use App\ValueObject\TimeFrame;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;
    use DoctorScope;

    public function make(Doctor $doctor, Patient $patient,$date,TimeFrame $time): bool
    {
        $shift = $doctor->getShiftOn($date, $time->getShift(),fn() =>
            throw new DoctorNotWorkOnThisDateException('doctor does not work on ' . $date)
        );

        if($this->fullyReservedFor($doctor,$date,$time)){
            throw new SlotIsFullyReservedException("Doctor is fully reserved on {$date} at {$time}");
        }

        //todo a same user can not reserve the same doctor same date time

        self::create([
            'on' => $date,
            'at' => $time,
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'shift_id' => $shift->id,
        ]);

        AvailableSlotsForDate::updateRemainOrCreate($doctor,$date);

        return true;
    }

    public function scopeAt(Builder $query,$time)
    {
        return $query->where('at',$time);
    }

    public function scopeOn(Builder $query,$date)
    {
        return $query->where('on',$date);
    }

    private function fullyReservedFor(Doctor $doctor, $date, TimeFrame $time): bool
    {
        $count = self::where('doctor_id',$doctor->id)->on($date)->at($time)->count();
        return $count >= $doctor->slot_per_time_frame;
    }

    public function reserveOnSameDateTime()
    {
       //todo check user reserve the same date time already with the same doctor
    }
}
