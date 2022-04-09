<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AvailableSlotsForDate extends Model
{
    use HasFactory;
    use DoctorScope;

    public $timestamps = false;

    public static function updateRemainOrCreate(Doctor $doctor,$date)
    {
        $remainSlots = AvailableSlotsForDate::doctorId($doctor->id)->where('date',$date)->first();
        if($remainSlots){
            return $remainSlots->decrement('remain');
        }

        AvailableSlotsForDate::create([
            'date' => $date,
            'doctor_id' => $doctor->id,
            'remain' => Shift::slotsCount($doctor,$date) - 1,
        ]);

    }
}
