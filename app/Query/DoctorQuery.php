<?php

namespace App\Query;

use App\Models\Doctor;
use Illuminate\Contracts\Database\Eloquent\Builder;

class DoctorQuery
{

    public function __invoke($weeks)
    {
        return Doctor::query()
            ->with(['shifts' => fn($query) => $query->NextSevenDays(), 'availableSlots:doctor_id,remain,date'])
            ->whereHas('shifts', fn(Builder $query) => $query->NextSevenDays())
            ->get();
    }
}
