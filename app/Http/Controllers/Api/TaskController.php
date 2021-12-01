<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller {

//    public function index()
//    {
//        return auth()->user()->categories;
//    }

    public function store(Request $request)
    {

        $category = Category::findOrFail($request->category_id);

        if ($category->user_id !== auth()->id()) {
            return response()->json(['message' => "Unauthorized"], 401);
        }

        $validatedData = $request->validate([
            'category_id' => ['integer', 'required'],
            'title'       => ['string', 'required'],
            'description' => ['string', 'nullable'],
            'due_date'    => ['date', 'required', 'date_format:Y-m-d', 'after_or_equal:' . date('Y-m-d')],
        ]);

        $validatedData['user_id'] = auth()->id();

        $task = $category->tasks()->create($validatedData);

        if ($task) {
            return $task;
        }

        return response()->json(['message' => "Something went wrong"], 500);
    }

    public function update(Request $request, Task $task)
    {
        $category = Category::findOrFail($request->category_id);

        if ($category->user_id !== auth()->id() || auth()->id() !== $task->user_id) {
            return response()->json(['message' => "Unauthorized"], 401);
        }

        $validatedData = $request->validate([
            'category_id' => ['integer', 'nullable'],
            'title'       => ['string', 'nullable'],
            'description' => ['string', 'nullable'],
            'due_date'    => ['date', 'nullable', 'date_format:Y-m-d', 'after_or_equal:' . date('Y-m-d')],
        ]);

        if ($task->update($validatedData)) {
            return response()->json(['message' => "Updated successfully"]);
        }

        return response()->json(['message' => "Something went wrong"], 500);
    }

    public function destroy(Task $task)
    {
        if (auth()->id() !== $task->user_id) {
            return response()->json(['message' => "Unauthorized"], 401);
        }

        if ($task->delete()) {
            return response()->json(['message' => "Deleted successfully"]);
        }

        return response()->json(['message' => "Something went wrong"], 500);
    }

    public function restore($taskId)
    {
        $task = Task::withTrashed()->findOrFail($taskId);

        if (auth()->id() !== $task->user_id) {
            return response()->json(['message' => "Unauthorized"], 401);
        }

        if ($task->restore()) {
            return response()->json(['message' => "Restored successfully"]);
        }

        return response()->json(['message' => "Something went wrong"], 500);
    }

    public function forceDelete($taskId)
    {
        $task = Task::withTrashed()->findOrFail($taskId);

        if (auth()->id() !== $task->user_id) {
            return response()->json(['message' => "Unauthorized"], 401);
        }

        if ($task->forceDelete()) {
            return response()->json(['message' => "Deleted successfully"]);
        }

        return response()->json(['message' => "Something went wrong"], 500);
    }

}
