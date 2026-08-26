<?php

declare(strict_types=1);

use Switch\Router\Facade\Route;
use App\Controllers\HomeController;
use App\Controllers\PostWebController;

// Home & Live SPA Demos
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::post('/live/counter/increment', [HomeController::class, 'incrementCounter'])->name('live.counter.increment');
Route::post('/live/counter/decrement', [HomeController::class, 'decrementCounter'])->name('live.counter.decrement');
Route::get('/about', fn() => 'About Switch Framework')->name('about');

// Blog & Posts CRUD / Flow Web Routes
Route::get('/posts', [PostWebController::class, 'index'])->name('posts.index');
Route::get('/post', [PostWebController::class, 'index'])->name('posts.alias');
Route::get('/posts/create', [PostWebController::class, 'create'])->name('posts.create');
Route::get('/post/create', [PostWebController::class, 'create'])->name('post.create.alias');
Route::post('/posts', [PostWebController::class, 'store'])->name('posts.store');
Route::post('/post', [PostWebController::class, 'store'])->name('post.store.alias');
Route::get('/posts/{id}', [PostWebController::class, 'show'])->name('posts.show');
Route::get('/post/{id}', [PostWebController::class, 'show'])->name('post.show.alias');
Route::post('/posts/{id}/publish', [PostWebController::class, 'publish'])->name('posts.publish');
Route::post('/posts/{id}/archive', [PostWebController::class, 'archive'])->name('posts.archive');
Route::post('/posts/{id}/delete', [PostWebController::class, 'destroy'])->name('posts.destroy');

