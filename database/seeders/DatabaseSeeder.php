<?php

namespace Database\Seeders;

use App\Models\Shift;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
         \App\Models\User::factory(2)->create(['is_patient' => true]);
         $doctors = \App\Models\Doctor::factory(10)->create();
         $doctors->each(function($doctor){
             Shift::create([
                 'doctor_id' => $doctor->id,
                 'date' => now()->addDay()->format('Y-m-d'),
                 'type' => 'am',
             ]);
         });
    }
}
