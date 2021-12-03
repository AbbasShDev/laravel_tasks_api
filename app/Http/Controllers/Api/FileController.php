<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FileResource;
use App\Models\File;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller {

    public function store(Request $request, $taskId)
    {
        $request->validate([
            'file' => ['required', 'file', 'max:5000', 'mimes:jpg,jpeg,png,pdf']
        ]);

        $task = Task::findOrFail($taskId);

        if ($task->user_id !== auth()->id()) {
            return response()->json(['message' => "Unauthorized"], 401);
        }

        $fileName = $request->file('file')->hashName();

        $Uploaded = $request->file('file')->storeAs('/public/tasks/' . $task->id, $fileName);

        if ($Uploaded) {

            $file = $task->files()->create([
                'user_id' => auth()->id(),
                'name'    => $fileName
            ]);

            if ($file) {
                return new FileResource($file);
            }
        }

        return response()->json(['message' => "Something went wrong"], 500);

    }

    public function destroy(File $file)
    {
        if ($file->user_id !== auth()->id()) {
            return response()->json(['message' => "Unauthorized"], 401);
        }

        if ($file->delete()) {
            $storageDeleted = Storage::delete('public/tasks/' . $file->task_id . '/' . $file->name);

            if ($storageDeleted) {
                return response()->json(['message' => "Deleted successfully"]);

            }
        }

        return response()->json(['message' => "Something went wrong"], 500);

    }
}
