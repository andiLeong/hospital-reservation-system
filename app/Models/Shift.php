<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Shift extends Model
{
    use HasFactory;
    use DoctorScope;

//    protected $appends = ['somethingFromDoctor'];

    public static function slotsCount(Doctor $doctor,$date)
    {
        return self::doctorId($doctor->id)->date($date)->pluck('slots_limit')->sum();
    }

    public function scopeType($query, $type)
    {
        return $query->where('type',$type);
    }

    public function scopeDate($query,$date)
    {
        return $query->where('date',$date);
    }

    public function scopeNextSevenDays($query,$week = null)
    {
        $week ??= Collection::times(7, fn($day) => today()->subDay()->addDays($day)->format('Y-m-d'))->toArray();
        return $query->whereIn('date',$week);
    }

}
