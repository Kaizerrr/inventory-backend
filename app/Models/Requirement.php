<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Requirement extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'submitted', // add this line
    ];

    public function students()
    {
        return $this->belongsToMany(Student::class, 'student_requirements')
            ->withPivot('submitted', 'file_name', 'filetype', 'filedata', 'filesize')
            ->using(StudentRequirement::class);
    }

    public function studentRequirements()
    {
        return $this->hasMany(StudentRequirement::class);
    }
}
