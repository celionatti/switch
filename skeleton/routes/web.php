<?php

declare(strict_types=1);

use Switch\Router\Facade\Route;
use App\Controllers\HomeController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::post('/live/counter/increment', [HomeController::class, 'incrementCounter'])->name('live.counter.increment');
Route::post('/live/counter/decrement', [HomeController::class, 'decrementCounter'])->name('live.counter.decrement');
Route::get('/about', fn() => 'About Switch Framework')->name('about');
