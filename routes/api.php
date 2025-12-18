<?php

use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\MemberController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('authors', AuthorController::class);
Route::apiResource('books', BookController::class);
Route::apiResource('members', MemberController::class);
Route::apiResource('borrowings', BorrowingController::class)->only(['index', 'store', 'show']);

Route::post('borrowings/{borrowing}/return', [BorrowingController::class, 'returnedBooks']);
Route::get('borrowings/overdue/list', [BorrowingController::class, 'overdue']);
