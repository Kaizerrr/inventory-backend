<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Student;

class UniqueStudentId implements Rule
{
    public function passes($attribute, $value)
    {
        // Check if the student ID is unique
        return !Student::where('studentId', $value)->exists();
    }

    public function message()
    {
        return 'The student ID has already been taken.';
    }
}
