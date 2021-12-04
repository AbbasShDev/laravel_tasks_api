<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use App\Models\Category;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller {

//    public function __construct()
//    {
//        $this->middleware('throttle:2,1');
//        $this->middleware('throttle:2,1')->except('index');
//        $this->middleware('throttle:2,1')->only('index');
//    }

    public function index()
    {
        $tasks = auth()->user()->tasks()->with('category')->paginate(2);

        return TaskResource::collection($tasks);
    }

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

    public function show(Task $task)
    {
        if (auth()->id() !== $task->user_id) {
            return response()->json(['message' => "Unauthorized"], 401);
        }

        $task->load('category', 'comments', 'files');

        return new TaskResource($task);
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

        $userEmail = $task->user->email;
        $taskTitle = $task->title;

        if ($task->forceDelete()) {
            Storage::deleteDirectory('public/tasks/' . $task->id);

            Mail::send([], [], function ($msg) use ($userEmail, $taskTitle) {
                $msg->subject('Task deleted');
                $msg->to($userEmail);
                $msg->setBody('<h3>Your task is permanently deleted</h3><p><strong>Task title: </strong>' . $taskTitle . '</p>', 'text/html');
            });

            return response()->json(['message' => "Deleted successfully"]);
        }

        return response()->json(['message' => "Something went wrong"], 500);
    }

}
