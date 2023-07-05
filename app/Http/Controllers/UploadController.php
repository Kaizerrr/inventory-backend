<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function upload(Request $request)
    {
        $file = $request->file('file');
        $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
        $file->storeAs('uploads', $fileName);
        // Save filename or file path to the database
        return response()->json([
            'message' => 'File uploaded successfully.',
            'filename' => $fileName
        ]);
    }

    public function download($filename)
    {
        $pathToFile = storage_path('app/uploads/' . $filename);
        if (!file_exists($pathToFile)) {
            return response()->json(['message' => 'File not found.'], 404);
        }
        return response()->download($pathToFile);
    }

    public function delete($filename)
    {
        $pathToFile = storage_path('app/uploads/' . $filename);
        if (!file_exists($pathToFile)) {
            return response()->json(['message' => 'File not found.'], 404);
        }
        unlink($pathToFile);
        return response()->json(['message' => 'File deleted successfully.']);
    }
}