<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Book;
use App\Models\BookCategory;
use App\Models\Category;
use App\Models\Genre;
use App\Models\Publisher;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookController extends Controller
{
    public function createBook(Request $request)
    {
        $requestData = json_decode($request->get('data'), true);

        $validatedData = validator($requestData, [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'number_of_pages' => 'required|integer',
            'number_of_copies' => 'required|integer',
            'isbn' => 'required|string|max:13',
            'language' => 'required|string|max:255',
            'script' => 'required|in:' . implode(',', Book::SCRIPTS),
            'binding' => 'required|in:' . implode(',', Book::BINDINGS),
            'dimensions' => 'required|in:' . implode(',', Book::DIMENSIONS),
            'categories' => 'required|array',
            'genres' => 'required|array',
            'authors' => 'required|array',
            'publishers' => 'required|array',
        ])->validate();

        $allCategories = Category::pluck('id')->toArray();
        $allGenres = Genre::pluck('id')->toArray();
        $allAuthors = Author::pluck('id')->toArray();
        $allPublishers = Publisher::pluck('id')->toArray();

        if (array_diff($validatedData['categories'], $allCategories)) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        if (array_diff($validatedData['genres'], $allGenres)) {
            return response()->json(['message' => 'Genre not found'], 404);
        }

        if (array_diff($validatedData['authors'], $allAuthors)) {
            return response()->json(['message' => 'Author not found'], 404);
        }

        if (array_diff($validatedData['publishers'], $allPublishers)) {
            return response()->json(['message' => 'Publisher not found'], 404);
        }

        DB::beginTransaction();
        try {
            $book = Book::create([
                'name' => $validatedData['name'],
                'description' => $validatedData['description'],
                'number_of_pages' => $validatedData['number_of_pages'],
                'number_of_copies' => $validatedData['number_of_copies'],
                'isbn' => $validatedData['isbn'],
                'language' => $validatedData['language'],
                'script' => $validatedData['script'],
                'binding' => $validatedData['binding'],
                'dimensions' => $validatedData['dimensions'],
            ]);

            $book->categories()->attach($validatedData['categories']);
            $book->genres()->attach($validatedData['genres']);
            $book->authors()->attach($validatedData['authors']);
            $book->publishers()->attach($validatedData['publishers']);

            if ($request->hasFile('image')) {
                $book->addMediaFromRequest('image')
                    ->toMediaCollection('book_images');
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }

        return response()->json($book, 201);
    }

    public function deleteBook($id)
    {
        $book = Book::find($id);

        if (!$book) {
            return response()->json(['message' => 'Book not found'], 404);
        }

        $book->delete();

        return response()->json(['message' => 'Book deleted'], 200);
    }

}
