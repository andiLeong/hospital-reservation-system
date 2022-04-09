<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Reservation;
use App\Rules\ValidateTimeFrame;
use App\ValueObject\Patient;
use App\ValueObject\TimeFrame;
use Exception;

class ReservationController extends Controller
{

    public function store(Doctor $doctor, Reservation $reservation)
    {
        $data = request()->validate([
            'on' => 'required|date_format:Y-m-d',
            'at' => ['required', new ValidateTimeFrame()],
        ]);

        try {
            $reservation->make(
                $doctor,
                Patient::init(auth()->user()),
                $data['on'],
                TimeFrame::make($data['at']),
            );
        } catch (Exception $e) {
            abort(422, $e->getMessage());
        }

        return ['status' => 'ok'];
    }
}
