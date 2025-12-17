<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Http\Resources\BooksResource;
use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::with('author');

        if ($request->has('search')) {
            $search = $request->query('search');

            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('isbn', 'like', "%{$search}%")
                    ->orWhereHas('author', function ($authorQuery) use ($search) {
                        $authorQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->has('genre')) {
            $query->where('genre', $request->genre);
        }
        $book = $query->paginate(10);

        return BooksResource::collection($book);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookingRequest $request)
    {
        $book = Book::create($request->validated());
        $book->load('author');

        return new BooksResource($book);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $book = Book::findOrFail($id);
            $book->load('author');

            return new BooksResource($book);
        } catch (\Exception $th) {
            return response()->json(['status' => false, 'message' => 'Book not found'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreBookingRequest $request, Book $book)
    {
        try {
            $book->update($request->validated());
            $book->load('author');

            return new BooksResource($book);
        } catch (\Exception $th) {
            return response()->json(['status' => false, 'message' => 'Book not found'], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BooksResource $book)
    {
        try {
            $book->delete();

            return response()->json(['status' => true, 'message' => 'Book Deleted Successfully']);
        } catch (\Exception $th) {
            return response()->json(['status' => false, 'message' => 'Book not found'], 404);
        }
    }
}
