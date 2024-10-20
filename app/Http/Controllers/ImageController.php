<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function uploadIcon(CategoryRequest $request)
    {
        $categoryId = $request->input('category_id');
        $category = Category::findOrFail($categoryId);

        if ($request->hasFile('icon')) {
            $file = $request->file('icon');
            $path = $file->store('icons', 'public');

            $category->update(['icon' => $path]);

            return response()->json(new CategoryRequest($category), 200);
        }

        Log::error('No file uploaded');

        return response()->json(['error' => 'No file uploaded'], 400);
    }

    public function updateIcon(Request $request, Category $category)
    {
        $request->validate([
            'icon' => 'required|image|max:5120', // Validate the icon
        ]);

        // Delete the old icon if it exists
        if ($category->icon) {
            Storage::disk('public')->delete($category->icon);
        }

        // Store the new icon
        $path = $request->file('icon')->store('icons', 'public');

        // Update the category with the new icon path
        $category->update(['icon' => $path]);

        // Return the updated category resource
        return new CategoryResource($category);
    }
}
