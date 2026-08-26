<?php

declare(strict_types=1);

use App\Controllers\PostController;
use Switch\Router\Facade\Route;

Route::get('/status', function () {
    return [
        'status' => 'ok',
        'framework' => 'Switch Framework',
        'version' => '1.0.0',
        'timestamp' => time(),
    ];
})->name('status');

// Posts API Resource Endpoints
Route::get('/posts', [PostController::class, 'index'])->name('api.posts.index');
Route::post('/posts', [PostController::class, 'store'])->name('api.posts.store');
Route::get('/posts/{id}', [PostController::class, 'show'])->name('api.posts.show');
Route::put('/posts/{id}', [PostController::class, 'update'])->name('api.posts.update');
Route::delete('/posts/{id}', [PostController::class, 'destroy'])->name('api.posts.destroy');
Route::post('/posts/{id}/publish', [PostController::class, 'publish'])->name('api.posts.publish');