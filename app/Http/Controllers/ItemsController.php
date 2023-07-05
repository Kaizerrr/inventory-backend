<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Items;

class ItemsController extends Controller
{
    public function store(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'name' => 'required|string',
            'purchased_date' => 'required|date',
            'lifespan' => 'required|numeric',
        ]);

        // Create a new item
        $item = Items::create($validatedData);

        // Return the newly created item as a response
        return response()->json([
            'message' => 'Item created successfully',
            'data' => $item,
        ], 201);
    }
}
