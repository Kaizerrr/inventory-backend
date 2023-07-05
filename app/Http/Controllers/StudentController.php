<?php

namespace App\Http\Controllers;

use App\Models\Requirement;
use App\Models\User;
use App\Models\Student;
use App\Models\StudentRequirement;
use App\Models\Log as UserLog;
use App\Http\Resources\StudentResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Rules\UniqueStudentId;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Gate;

use Illuminate\Support\Collection;



class StudentController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'page' => 'integer|min:0',
            'size' => 'integer|min:1',
            'sortBy' => 'in:name,studentId,program,studentClass,storageLocation',
            'sortDir' => 'in:asc,desc',
            'search' => 'string'
        ]);

        $page = (int) $request->get('page', 0);
        $size = (int) $request->get('size', 10);
        $sortBy = $request->get('sortBy', 'name');
        $sortDir = $request->get('sortDir', 'asc');
        $searchTerm = $request->get('search');
        $selectedPrograms = $request->get('programs', []);
        $selectedClasses = $request->get('classes', []);
        $selectedStatus = $request->get('status', []);

        $sortByColumn = 'name';
        switch ($sortBy) {
            case 'studentId':
                $sortByColumn = 'studentId';
                break;
            case 'program':
                $sortByColumn = 'program';
                break;
            case 'studentClass':
                $sortByColumn = 'studentClass';
                break;
            case 'location':
                $sortByColumn = 'location';
                break;
        }

        $query = Student::orderBy($sortByColumn, $sortDir);

        // Search
        if (!empty($searchTerm)) {
            $columns = ['name', 'studentId', 'program', 'studentClass', 'storageLocation'];
            $query->where(function ($q) use ($searchTerm, $columns) {
                foreach ($columns as $column) {
                    $q->orWhere($column, 'like', '%' . $searchTerm . '%');
                }
            });
        }

        // Filter by programs
        if (!empty($selectedPrograms)) {
            $query->whereIn('program', $selectedPrograms);
        }

        // Filter by student classes
        if (!empty($selectedClasses)) {
            $query->whereIn('studentClass', $selectedClasses);
        }

        // Filter by active status
        if (!empty($selectedStatus)) {
            $query->whereIn('activeStatus', $selectedStatus);
        }

        $totalElements = $query->count();

        $students = $query->skip($page * $size)
            ->take($size)
            ->get();

        $result = [
            'page' => $page,
            'size' => count($students),
            'totalElements' => $totalElements,
            'content' => $students->map(function ($student) {
                return collect($student)->except(['created_at', 'updated_at']);
            }),
        ];

        return response()->json($result);
    }


    public function show($id)
    {
        $this->authorize('view-student');

        $student = Student::with('requirements')->findOrFail($id);

        $lastLog = $student->lastLog();
        $lastUser = $student->lastUser();

        $data = [
            'lastLog' => $lastLog,
            'lastUser' => $lastUser,
            'id' => $student->id,
            'name' => $student->name,
            'program' => $student->program,
            'department' => $student->department,
            'studentId' => $student->studentId,
            'remarks' => $student->remarks,
            'activeStatus' => $student->activeStatus,
            'studentClass' => $student->studentClass,
            'storageLocation' => $student->storageLocation,
            'requirements' => [],
        ];

        foreach ($student->requirements as $requirement) {
            $requirementData = [
                'id' => $requirement->id,
                'name' => $requirement->name,
                'codeName' => $requirement->codeName,
                'submitted' => $requirement->pivot->submitted,
                'file_path' => $requirement->pivot->file_path,
                'note' => $requirement->pivot->note,
            ];
            $data['requirements'][] = $requirementData;
        }

        return response()->json($data);
    }

    public function store(Request $request)
    {
        $this->authorize('add-student');

        // Log::debug('Request payload:', $request->all());

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'program' => 'required|string',
            'department' => 'string',
            'studentId' => 'required|string|unique:students',
            'remarks' => 'nullable|string',
            'activeStatus' => 'required|in:1,0',
            'studentClass' => 'required|string',
            'storageLocation' => 'required|string',
            'requirements' => 'required|array',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422); // 422 is Unprocessable Entity status code
        }

        // Convert activeStatus to boolean
        $activeStatus = $request->get('activeStatus');
        if (is_string($activeStatus)) {
            $activeStatus = $activeStatus === '1';
        } else if (is_int($activeStatus)) {
            $activeStatus = $activeStatus === 1;
        }

        // Save student
        $student = new Student;
        $student->name = $request->get('name');
        $student->program = $request->get('program');
        $student->department = $request->get('department') ?? null;
        $student->studentId = $request->get('studentId');
        $student->remarks = $request->get('remarks') ?? '';
        $student->activeStatus = $activeStatus;
        $student->studentClass = $request->get('studentClass');
        $student->storageLocation = $request->get('storageLocation');
        $student->save();

        // Save student requirements
        foreach ($request->get('requirements') as $requirementData) {
            $requirement = new StudentRequirement;
            $requirement->requirement_id = $requirementData['id'];
            $requirement->student_id = $student->id;
            $requirement->submitted = $requirementData['submitted'];
            $requirement->file_path = $requirementData['file_path'] ?? null;
            $requirement->note = $requirementData['note'] ?? null;
            $requirement->save();
        }

        $this->logAction('created', $student, "Created {$student->name}");

        return response()->json(['message' => 'Student created successfully']);
    }


    public function delete($id)
    {
        $this->authorize('delete-student');

        // Find the student
        $student = Student::findOrFail($id);

        // Delete the student requirements
        $student->requirements()->detach();

        $this->logAction('deleted', $student, "Deleted {$student->name}");

        // Delete the student
        $student->delete();

        // Return a success response
        return response()->json(['message' => 'Student and associated requirements deleted.']);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('edit-student');

        // Validate request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'program' => 'required|string',
            'department' => 'nullable|string',
            'studentId' => [
                'required',
                'string',
                Rule::unique('students')->ignore($id),
            ],
            'remarks' => 'nullable|string',
            'activeStatus' => 'required|in:1,0',
            'studentClass' => 'required|string',
            'storageLocation' => 'required|string',
            'requirements' => 'required|array',
            'requirements.*.id' => 'required|exists:requirements,id',
            'requirements.*.submitted' => 'required|in:1,0',
            'requirements.*.file_path' => 'nullable|string',
            'requirements.*.note' => 'nullable|string',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422); // 422 is Unprocessable Entity status code
        }

        // Convert activeStatus to boolean
        $activeStatus = $request->get('activeStatus');
        if (is_string($activeStatus)) {
            $activeStatus = $activeStatus === '1';
        } else if (is_int($activeStatus)) {
            $activeStatus = $activeStatus === 1;
        }

        // Get the student to update
        $student = Student::findOrFail($id);
        $student->name = $request->get('name');
        $student->program = $request->get('program');
        $student->department = $request->get('department');
        $student->studentId = $request->get('studentId');
        $student->remarks = $request->get('remarks') ?? '';
        $student->activeStatus = $activeStatus;
        $student->studentClass = $request->get('studentClass');
        $student->storageLocation = $request->get('storageLocation');
        $student->save();

        // Get the IDs of the requirements that were checked in the form
        $checkedRequirements = collect($request->get('requirements'))
            ->filter(function ($requirementData) {
                return $requirementData['submitted'] == 1;
            })
            ->pluck('id')
            ->all();

        // Update unchecked requirements to not submitted
        $student->requirements()
            ->whereNotIn('requirements.id', $checkedRequirements)
            ->update(['submitted' => 0]);

        // Update or create student requirements for checked requirements
        foreach ($request->get('requirements') as $requirementData) {
            if ($requirementData['submitted'] === 1) {
                StudentRequirement::updateOrCreate(
                    [
                        'requirement_id' => $requirementData['id'],
                        'student_id' => $id
                    ],
                    [
                        'submitted' => $requirementData['submitted'],
                        'file_path' => $requirementData['file_path'] ?? null,
                        'note' => $requirementData['note'] ?? null,
                    ]
                );
            }
        }

        $this->logAction('updated', $student, "Edited {$student->name}");

        return response()->json(['message' => 'Student updated successfully']);
    }

    public function exportAll(Request $request)
    {
        $request->validate([
            'sortBy' => 'in:name,studentId, program, studentclass, location',
            'sortDir' => 'in:asc,desc',
        ]);

        $sortBy = $request->get('sortBy', 'name');
        $sortDir = $request->get('sortDir', 'asc');

        $sortByColumn = 'name';
        switch ($sortBy) {
            case 'studentId':
                $sortByColumn = 'studentId';
                break;
            case 'program':
                $sortByColumn = 'program';
                break;
            case 'studentClass':
                $sortByColumn = 'studentClass';
                break;
            case 'location':
                $sortByColumn = 'location';
                break;
        }

        $students = Student::orderBy($sortByColumn, $sortDir)
            ->with('requirements')
            ->get();

        $result = $students->map(function ($student) {
            $studentData = collect($student)->except(['created_at', 'updated_at']);
            $requirementsData = $student->requirements->map(function ($requirement) {
                return collect($requirement)->only(['name', 'codeName']);
            });
            $studentData['requirements'] = $requirementsData;
            return $studentData;
        });

        return $result;
    }

    public function getAll()
    {
        $students = Student::with('requirements')->get();
        $data = [];

        foreach ($students as $student) {
            $studentData = [
                'id' => $student->id,
                'name' => $student->name,
                'program' => $student->program,
                'department' => $student->department,
                'studentId' => $student->studentId,
                'remarks' => $student->remarks,
                'activeStatus' => $student->activeStatus,
                'studentClass' => $student->studentClass,
                'storageLocation' => $student->storageLocation,
                'requirements' => [],
            ];

            foreach ($student->requirements as $requirement) {
                $requirementData = [
                    'id' => $requirement->id,
                    'name' => $requirement->name,
                    'codeName' => $requirement->codeName,
                    'submitted' => $requirement->pivot->submitted,
                    'file_path' => $requirement->pivot->file_path,
                    'note' => $requirement->pivot->note,
                ];
                $studentData['requirements'][] = $requirementData;
            }

            $data[] = $studentData;
        }

        $this->logAction('exported', null, "Exported data");

        return response()->json($data);
    }

    public function storeMultiple(Request $request)
    {
        // Log::debug('Request payload:', $request->all());

        $validator = Validator::make($request->all(), [
            '*.name' => 'required|string',
            '*.department' => 'string',
            // '*.studentId' => 'required|string',
            '*.remarks' => 'nullable|string',
            // '*.activeStatus' => 'required|in:1,0',
            // '*.studentClass' => 'required|string',
            '*.requirements' => 'required|array',
            '*.requirements.*.id' => 'required|exists:requirements,id',
            '*.requirements.*.submitted' => 'required|in:1,0',
            '*.requirements.*.file_path' => 'nullable|string',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422); // 422 is Unprocessable Entity status code
        }

        // Convert activeStatus to boolean
        foreach ($request->all() as $studentData) {
            $activeStatus = $studentData['activeStatus'];
            if (is_string($activeStatus)) {
                $activeStatus = $activeStatus === '1';
            } else if (is_int($activeStatus)) {
                $activeStatus = $activeStatus === 1;
            } else {
                $activeStatus = true;
            }

            // Save student
            $student = new Student;
            $student->name = $studentData['name'];
            $student->program = $studentData['program'] ?? 'N/A';
            $student->department = $studentData['department'] ?? null;
            $student->studentId = $studentData['studentId'] ?? Str::random(10);
            $student->remarks = $studentData['remarks'] ?? '';
            $student->activeStatus = $activeStatus;
            $student->studentClass = $studentData['studentClass'] ?? 'N/A';
            $student->storageLocation = $studentData['storageLocation'] ?? 'N/A';
            $student->save();

            // Save student requirements
            foreach ($studentData['requirements'] as $requirementData) {
                $requirement = new StudentRequirement;
                $requirement->requirement_id = $requirementData['id'];
                $requirement->student_id = $student->id;
                $requirement->submitted = $requirementData['submitted'];
                $requirement->file_path = $requirementData['file_path'] ?? null;
                $requirement->note = $requirementData['note'] ?? null;
                $requirement->save();
            }

            $this->logAction('imported', $student, "Imported {$student->name}");
        }

        // $this->logAction('imported', null);

        return response()->json(['message' => 'Students created successfully', 'status' => 'OK']);
    }

    /**
     * Log user actions
     *
     * @param string $action
     * @param \App\Models\Student|null $student
     * @return void
     */
    private function logAction(string $action, ?Student $student, string $description): void
    {
        $log = new UserLog();
        if (auth()->user()) {
            $log->user_id = auth()->user()->id;
        } else {
            $log->user_id = 1;
        }
        $log->action = $action;
        $log->endpoint = '/students';
        if ($student) {
            $log->student_id = $student->id;
        }
        $log->description = $description;
        $log->save();
    }

    private function getUser()
    {
        return auth()->user();
    }
}