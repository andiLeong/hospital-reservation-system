<?php

namespace App\Rules;

use App\ValueObject\TimeFrame;
use Exception;
use Illuminate\Contracts\Validation\Rule;

class ValidateTimeFrame implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        try {
            TimeFrame::make($value);
            return true;
        } catch(Exception $e) {
            return false;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The TimeFrame chosen is invalid';
    }
}
