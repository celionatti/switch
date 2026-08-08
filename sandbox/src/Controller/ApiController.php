<?php

declare(strict_types=1);

namespace App\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Switch\Http\Response;
use Switch\Http\Stream;

class ApiController
{
    public function getUser(ServerRequestInterface $request): Response
    {
        $id = $request->getAttribute('id');

        $data = [
            'status' => 'success',
            'user' => [
                'id' => (int) $id,
                'name' => 'Jane Doe',
                'email' => 'jane@example.com',
                'role' => 'administrator'
            ]
        ];

        return new Response(
            200,
            ['Content-Type' => 'application/json'],
            Stream::create(json_encode($data, JSON_PRETTY_PRINT))
        );
    }
}
