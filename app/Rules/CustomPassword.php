<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CustomPassword implements Rule
{
    public function passes($attribute, $value)
    {
        $hasLetter = preg_match('/[a-zA-Z]/', $value);
        $hasNumber = preg_match('/\d/', $value);
        $hasSpecial = preg_match('/[\W]/', $value); // Matches any non-word character

        // Check if at least two of the conditions are met
        return ($hasLetter + $hasNumber + $hasSpecial) >= 2;
    }

    public function message()
    {
        return __('The password must contain at least two of the following: a letter, a number, or a special character.');
    }
}
