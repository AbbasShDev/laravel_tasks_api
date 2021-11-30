<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller {

    public function index()
    {
        return auth()->user()->categories;
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title'       => ['string', 'required'],
            'description' => ['string', 'nullable'],
        ]);

        return auth()->user()->categories()->create($validatedData);
    }

    public function show(Category $category)
    {
        //
    }

    public function update(Request $request, Category $category)
    {
        if (auth()->id() !== $category->user_id) {
            return response()->json(['message' => "Unauthorized"], 401);
        }

        $validatedData = $request->validate([
            'title'       => ['string', 'nullable'],
            'description' => ['string', 'nullable'],
        ]);

        if ($category->update($validatedData)) {
            return response()->json(['message' => "Updated successfully"]);
        }

        return response()->json(['message' => "Something went wrong"], 500);
    }

    public function destroy(Category $category)
    {
        if (auth()->id() !== $category->user_id) {
            return response()->json(['message' => "Unauthorized"], 401);
        }

        if ($category->delete()) {
            return response()->json(['message' => "Deleted successfully"]);
        }

        return response()->json(['message' => "Something went wrong"], 500);
    }

    public function restore($categoryId)
    {
        $category = Category::withTrashed()->findOrFail($categoryId);

        if (auth()->id() !== $category->user_id) {
            return response()->json(['message' => "Unauthorized"], 401);
        }


        if ($category->restore()) {
            return response()->json(['message' => "Restored successfully"]);
        }

        return response()->json(['message' => "Something went wrong"], 500);
    }

    public function forceDelete($categoryId)
    {
        $category = Category::withTrashed()->findOrFail($categoryId);

        if (auth()->id() !== $category->user_id) {
            return response()->json(['message' => "Unauthorized"], 401);
        }

        if ($category->forceDelete()) {
            return response()->json(['message' => "Deleted successfully"]);
        }

        return response()->json(['message' => "Something went wrong"], 500);
    }
}
