<?php

namespace App\Models;

use App\Http\Resources\BorrowingResource;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Borrowing extends Model
{
    /** @use HasFactory<\Database\Factories\BorrowingFactory> */
    use HasFactory;

    protected $fillable = [
        'book_id',
        'member_id',
        'borrowed_date',
        'due_date',
        'returned_date',
        'status',
    ];

    protected $casts = [
        'borrowed_date' => 'date',
        'due_date' => 'date',
        'returned_date' => 'date',
    ];

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function isOverDue(): bool
    {
        return $this->due_date < Carbon::today() && $this->status === 'borrowed';
    }

    public function borrow(): void
    {
        if ($this->available_copies > 0) {
            $this->decrement('available_copies');
        }
    }

    public function returnBook(): void
    {
        if ($this->available_copies < $this->total_copies) {
            $this->increment('available_copies');
        }
    }
}
