<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NotRussian implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Match Cyrillic characters (used in Russian)
        if (preg_match('/[\p{Cyrillic}]/u', $value)) {
            $fail("The $attribute must not contain Russian language.");
        }
    }
}
