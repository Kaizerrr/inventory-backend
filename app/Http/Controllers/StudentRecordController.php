<?php

namespace App\Http\Controllers;

use App\Models\StudentRequirement;
use Illuminate\Http\Request;
use App\Models\Student;


class StudentRecordController extends Controller
{
    public function index()
    {
        $requirements = StudentRequirement::all();
        return response()->json($requirements);
    }

    public function delete($id)
    {
        $student = Student::find($id);
        if (!$student) {
            return response()->json(['error' => 'Student not found.'], 404);
        }
        $student->requirements()->delete();
        $student->delete();
        return response()->json(['message' => 'Student record deleted.']);
    }
}
