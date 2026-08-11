<?php

declare(strict_types=1);

namespace App\Controllers;

use Switch\View\View;

class HomeController
{
    public function index(): string
    {
        return View::render('home', [
            'title' => 'Welcome to Switch Framework',
            'framework' => 'Switch',
            'version' => '1.0.0',
        ]);
    }
}
