<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Post;
use Switch\Foundation\Action\Action;

class CreatePostAction extends Action
{
    public function rules(): array
    {
        return [
            'title' => 'required',
            'content' => 'required',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function handle(array $data): Post
    {
        if (empty($data['slug']) && !empty($data['title'])) {
            $data['slug'] = strtolower(trim((string) preg_replace('/[^A-Za-z0-9-]+/', '-', $data['title'])));
        }

        $data['status'] = $data['status'] ?? 'draft';
        $data['tags'] = $data['tags'] ?? ['general'];
        $data['is_featured'] = (bool) ($data['is_featured'] ?? false);

        /** @var Post $post */
        $post = Post::create($data);
        $post->recordAudit('created', ['title' => $post->title, 'slug' => $post->slug]);

        return $post;
    }
}
