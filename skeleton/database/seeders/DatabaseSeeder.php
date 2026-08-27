<?php

declare(strict_types=1);

namespace Database\Seeders;

use Switch\Database\Seeder\Seeder;
use Switch\Foundation\Data\Facade\Data;
use App\Models\Post;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed demo Administrator
        if (class_exists(User::class)) {
            User::firstOrCreate(
                ['email' => 'admin@switchframework.io'],
                [
                    'name' => 'Switch Admin',
                    'email' => 'admin@switchframework.io',
                    'password' => password_hash('password', PASSWORD_BCRYPT),
                ]
            );
        }

        // 2. Seed initial Blog Posts
        if (class_exists(Post::class)) {
            $initialPosts = [
                [
                    'title' => 'Welcome to Switch Framework',
                    'slug' => 'welcome-to-switch-framework',
                    'content' => 'Switch is a modern, modular PHP framework built with pure zero-dependency performance and Live SPA morphing.',
                    'status' => 'published',
                ],
                [
                    'title' => 'Building Live SPAs with Zero JavaScript',
                    'slug' => 'building-live-spas-with-zero-javascript',
                    'content' => 'Explore the switch-to and switch-prefetch directives to render instant seamless page transitions without full browser reloads.',
                    'status' => 'published',
                ],
            ];

            foreach ($initialPosts as $postData) {
                Post::firstOrCreate(['slug' => $postData['slug']], $postData);
            }
        }
    }
}
