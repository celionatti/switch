<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Post;
use Switch\Database\ORM\Collection;

class PostService
{
    /**
     * Get published posts with author relations.
     *
     * @return Collection<int, Post>
     */
    public function getPublishedPosts(int $limit = 10): Collection
    {
        return Post::where('status', 'published')
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->get();
    }

    /**
     * Create a new blog post.
     *
     * @param array<string, mixed> $data
     */
    public function createPost(array $data): Post
    {
        return Post::create([
            'title' => $data['title'],
            'slug' => $data['slug'] ?? slugify($data['title']),
            'body' => $data['body'],
            'status' => $data['status'] ?? 'draft',
            'user_id' => $data['user_id'] ?? 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
