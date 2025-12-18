<?php

namespace App\Http\Controllers;

use App\Http\Requests\BorrowingRequest;
use App\Http\Resources\BorrowingResource;
use App\Models\book;
use App\Models\Borrowing;
use Illuminate\Http\Request;

class BorrowingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $queary = Borrowing::with(['book', 'member']);
        if ($request->has('status')) {
            $queary->where('status', $request->status);
        }

        if ($request->has('member_id')) {
            $queary->where('member_id', $request->member_id);
        }

        $borrowings = $queary->latest()->paginate(10);

        return BorrowingResource::collection($borrowings);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BorrowingRequest $request)
    {
        $book = book::findOrFail($request->book_id);

        if (! $book->is_available()) {
            return response()->json(['message' => 'Book is not available for borrowing.'], 400);
        }

        $borrowing = Borrowing::create($request->validated());
        $book->borrow();
        $borrowing->load(['book', 'member']);

        return new BorrowingResource($borrowing);
    }

    /**
     * Display the specified resource.
     */
    public function show(Borrowing $borrowing)
    {
        $borrowing->load(['book', 'member']);

        return new BorrowingResource($borrowing);
    }

    public function returnedBooks(Borrowing $borrowing)
    {
        if ($borrowing->status !== 'borrowed') {
            return response()->json(['message' => 'Book has already been returned.'], 400);
        }

        $borrowing->update([
            'returned_date' => now(),
            'status' => 'returned']);

        $borrowing->book->returnedBooks();
        $borrowing->load(['book', 'member']);

        return new BorrowingResource($borrowing);
    }

    public function overdue()
    {
        $overdueBorrowings = Borrowing::with(['book', 'member'])
            ->where('due_date', '<', now())
            ->update(['status' => 'overdue']);

        Borrowing::where('status', 'borrowed')
            ->where('due_date', '<', now())
            ->update(['status' => 'overdue']);

        return BorrowingResource::collection($overdueBorrowings);
    }
}
