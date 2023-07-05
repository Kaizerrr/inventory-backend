<?php

namespace App\Http\Controllers;

use App\Models\Requirement;

class RequirementController extends Controller
{
    public function index()
    {
        $requirements = Requirement::all();
        return response()->json($requirements);
    }
}