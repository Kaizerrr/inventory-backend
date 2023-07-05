<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentRequirement extends Model
{
    use HasFactory;

    protected $fillable = [

        'requirement_id',
        'student_id',
        'submitted',
        'file_path',
        'note',
    ];

    public function students()
    {
        return $this->belongsToMany(Student::class, 'student_requirements')
            ->withPivot('submitted', 'file_name', 'filetype', 'filedata', 'filesize')
            ->using(StudentRequirement::class);
    }
}
