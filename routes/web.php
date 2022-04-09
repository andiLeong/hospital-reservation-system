<?php

use App\Collections\TimesCollection;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\ShiftController;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Reservation;
use App\Models\Shift;
use App\Models\TimeFrame;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {


//    $collection = resolve(TimesCollection::class);
//    dd($collection);

//    $doctor = Doctor::find(2);
//    $user = \App\Models\User::first();
//
//    $reservation = resolve(Reservation::class);
//    $reservation->make(
//        $doctor,
//        Patient::init($user),
//        $date = '2022-04-08',
//        TimeFrame::make($time = '09:00-10:00')
//    );

//    Reservation::make( $doctor,  $patient,$date, $time);

//    \Illuminate\Support\Facades\Redis::decrby('8.available.slot.2021-04-05',1);
    return view('welcome');
});

Route::get('/shift', [ShiftController::class,'index']);
Route::get('/shift/{doctor}', [ShiftController::class,'show']);
Route::post('/reserve/{doctor}', [ReservationController::class,'store']);
