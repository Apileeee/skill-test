<?php

use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

// Public routes - list posts and get single post
Route::get('/posts', [PostController::class, 'index'])->name('posts.index');

// These must come BEFORE the {post} parameter route
Route::middleware('auth')->group(function () {
    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
});

// Public route - get single post (must come after create and store)
Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');

// Authenticated routes with parameters
Route::middleware('auth')->group(function () {
    Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::patch('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::put('/posts/{post}', [PostController::class, 'update']);
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
});
