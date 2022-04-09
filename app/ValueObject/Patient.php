<?php

namespace App\ValueObject;

use App\Models\User;

class Patient
{

    private User $patient;

    private function __construct(User $user)
    {
        $this->patient = $user;
    }

    public static function init(User $user)
    {
        if(!$user->isPatient()){
            throw new \InvalidArgumentException('user ism\'t a patient');
        }
        return new static($user);
    }

    public function __get(string $name)
    {
        return $this->patient->$name;
    }

    public function __call(string $name, array $arguments)
    {
        return $this->patient->{$name}(...$arguments);
    }
}
