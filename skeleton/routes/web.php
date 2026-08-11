<?php

declare(strict_types=1);

use Switch\Router\Facade\Route;
use App\Controllers\HomeController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', fn() => 'About Switch Framework')->name('about');
