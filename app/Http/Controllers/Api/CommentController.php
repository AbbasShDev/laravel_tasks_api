<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Task;
use Illuminate\Http\Request;

class CommentController extends Controller {

    public function store(Request $request)
    {
        $task = Task::findOrFail($request->task_id);

        if ($task->user_id !== auth()->id()) {
            return response()->json(['message' => "Unauthorized"], 401);
        }

        $validatedData = $request->validate([
            'task_id' => ['required'],
            'body'    => ['required', 'string'],
        ]);

        $validatedData['user_id'] = auth()->id();

        $comment = $task->comments()->create($validatedData);

        if ($comment) {
            return $comment;
        }

        return response()->json(['message' => "Something went wrong"], 500);

    }

    public function update(Request $request, Comment $comment)
    {
        if ($comment->user_id !== auth()->id()) {
            return response()->json(['message' => "Unauthorized"], 401);
        }

        $validatedData = $request->validate([
            'body'    => ['required', 'string'],
        ]);

        $validatedData['user_id'] = auth()->id();

        $updated = $comment->update($validatedData);

        if ($updated) {
            return $comment;
        }

        return response()->json(['message' => "Something went wrong"], 500);
    }

    public function destroy(Comment $comment)
    {
        if ($comment->user_id !== auth()->id()) {
            return response()->json(['message' => "Unauthorized"], 401);
        }

        if ($comment->delete()) {
            return response()->json(['message' => "Deleted successfully"]);
        }

        return response()->json(['message' => "Something went wrong"], 500);
    }
}
