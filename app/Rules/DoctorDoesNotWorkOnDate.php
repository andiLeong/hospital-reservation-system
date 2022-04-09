<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class DoctorDoesNotWorkOnDate implements Rule
{
    private $doctor;
    private $value;

    /**
     * Create a new rule instance.
     *
     * @param $doctor
     */
    public function __construct($doctor)
    {
        $this->doctor = $doctor;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $this->value = $value;
        return $this->doctor->workOn($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'doctor doesn\'t work on ' . $this->value;
    }
}
