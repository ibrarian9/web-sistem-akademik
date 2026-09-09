<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\Guru;

class EligibleShadowTeacher implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value)) {
            return;
        }

        $guru = Guru::with('user.role')->find($value);
        if (!$guru) {
            $fail('Guru yang dipilih tidak ditemukan di sistem.');
            return;
        }

        if (!$guru->isGuruPendamping()) {
            $fail('Hanya guru dengan peran atau kategori Pendamping (Shadow Teacher) yang dapat dipilih sebagai guru pendamping.');
        }
    }
}
