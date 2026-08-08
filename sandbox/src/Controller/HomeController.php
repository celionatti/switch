<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Product;
use Psr\Http\Message\ServerRequestInterface;
use Switch\Config\Config;
use Switch\Http\Response;
use Switch\Http\Stream;
use Switch\View\Engine\ViewEngine;

class HomeController
{
    public function __construct(
        private readonly Config $config,
        private readonly ViewEngine $viewEngine
    ) {
    }

    public function index(ServerRequestInterface $request): Response
    {
        $products = Product::all();

        $html = $this->viewEngine->render('home', [
            'title' => 'Home - Switch Framework',
            'appName' => $this->config->get('app.name', 'Switch App'),
            'user' => [
                'name' => 'Alex Developer',
                'role' => 'Administrator'
            ],
            'subsystems' => [
                'switch/container (PSR-11)',
                'switch/http-message (PSR-7 & PSR-17)',
                'switch/router',
                'switch/events (PSR-14)',
                'switch/config',
                'switch/kernel (PSR-15)',
                'switch/view (HTML Tag Compiler)',
                'switch/database (Query Builder, Schema, Migrations, ORM)'
            ],
            'products' => $products
        ]);

        return new Response(200, ['Content-Type' => 'text/html'], Stream::create($html));
    }
}
