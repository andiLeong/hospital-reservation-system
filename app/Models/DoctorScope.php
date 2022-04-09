<?php


namespace App\Models;


trait DoctorScope
{
    public function scopeDoctorId($query,$doctorId)
    {
        return $query->where('doctor_id',$doctorId);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class,'doctor_id','id');
    }
}
