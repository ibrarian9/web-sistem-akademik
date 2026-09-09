<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\Siswa;
use App\Models\Kelas;

class MaxOneShadowTeacherPerClass implements ValidationRule
{
    protected ?int $kelasId;
    protected ?int $ignoreSiswaId;

    public function __construct(?int $kelasId = null, ?int $ignoreSiswaId = null)
    {
        $this->kelasId = $kelasId ? (int) $kelasId : null;
        $this->ignoreSiswaId = $ignoreSiswaId ? (int) $ignoreSiswaId : null;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value) || empty($this->kelasId)) {
            return;
        }

        // Check if another student in the same class already has a different shadow teacher assigned
        $existing = Siswa::where('kelas_id', $this->kelasId)
            ->when($this->ignoreSiswaId, fn($q) => $q->where('id', '!=', $this->ignoreSiswaId))
            ->whereNotNull('shadow_teacher_id')
            ->where('shadow_teacher_id', '!=', $value)
            ->with(['shadowTeacher.user', 'kelas'])
            ->first();

        if ($existing) {
            $existingTeacherName = $existing->shadowTeacher?->user?->nama ?? 'Guru Pendamping lain';
            $kelasName = $existing->kelas?->nama_kelas ?? 'kelas ini';
            $fail("Dalam satu kelas hanya diperbolehkan maksimal 1 guru pendamping. Kelas {$kelasName} sudah didampingi oleh {$existingTeacherName}.");
        }
    }
}
