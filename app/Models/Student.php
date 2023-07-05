<?php

namespace App\Models;
use App\Models\StudentRequirement;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\HasMany;

use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    public function requirements()
    {
        return $this->belongsToMany(Requirement::class, 'student_requirements')
            ->withPivot('submitted', 'file_path', 'note');
    }

    /**
     * Get all the logs for the user.
     */
    public function logs(): HasMany
    {
        return $this->hasMany(Log::class);
    }

    public function latestLogs() {
        return $this->logs()->with('user')->latest()->take(5);
    }

    // protected static function booted()
    // {
    //     static::deleting(function ($student) {
    //         $student->logs()->delete();
    //     });

    //     static::restoring(function ($student) {
    //         $student->logs()->withTrashed()->whereNull('student_id')->update(['student_id' => $student->id]);
    //     });
    // }

    /**
     * Get the last action done by the user.
     */
    public function lastLog()
    {
        return $this->logs()->latest()->first();
    }

    public function lastUser()
    {
        $log = $this->logs()->with('user')->latest()->first();
        if ($log && $log->user) {
            return $log->user->username;
        }
        return null;
    }
}