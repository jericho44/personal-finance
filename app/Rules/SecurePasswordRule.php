<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SecurePasswordRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $regex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*_+\-]).{6,}$/';

        if (!preg_match($regex, (string) $value)) {
            $fail(__("Password harus minimal 6 karakter, mengandung huruf besar, huruf kecil, angka, dan karakter spesial (!@#$%^&*_+-)."));
        }
    }
}
