<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Actions\CreatePostAction;
use App\Models\Post;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Switch\Controller\Controller;
use Switch\Foundation\Api\ApiResponse;
use Switch\Foundation\Collection\Collection;

class PostController extends Controller
{
    public function index(): string|ResponseInterface
    {
        $posts = Post::query()->orderBy('id', 'desc')->paginate(10);
        $tags = Collection::make($posts->items())
            ->pluck('tags')
            ->filter()
            ->flatten()
            ->unique();

        return ApiResponse::success([
            'posts' => $posts->items(),
            'pagination' => $posts->toArray(),
            'tags' => $tags->values()->all(),
        ], 'Posts retrieved successfully');
    }

    public function store(ServerRequestInterface $request): ResponseInterface
    {
        $body = (array) ($request->getParsedBody() ?? []);
        $post = CreatePostAction::run($body);

        return ApiResponse::created($post->toArray(), 'Post created successfully');
    }

    public function show(int $id): ResponseInterface
    {
        $post = Post::findOrFail($id);
        return ApiResponse::success($post->toArray(), 'Post details');
    }

    public function update(int $id, ServerRequestInterface $request): ResponseInterface
    {
        $post = Post::findOrFail($id);
        $body = (array) ($request->getParsedBody() ?? []);
        $post->fill($body);
        $post->save();

        return ApiResponse::success($post->toArray(), 'Post updated successfully');
    }

    public function destroy(int $id): ResponseInterface
    {
        $post = Post::findOrFail($id);
        $post->delete();

        return ApiResponse::success(['deleted' => true, 'id' => $id], 'Post deleted successfully');
    }

    public function publish(int $id): ResponseInterface
    {
        $post = Post::findOrFail($id);
        $post->applyFlow('publish');
        $post->save();

        return ApiResponse::success($post->toArray(), 'Post published successfully');
    }
}
