<?php

namespace App\Models;

/**
 * Class Student
 *
 * Model alias for Siswa to support standard naming and test queries
 * (e.g. Student::where('shadow_teacher_id', ...)->get()).
 */
class Student extends Siswa
{
    // Inherits all table mappings, attributes, scopes and relations from Siswa
}
