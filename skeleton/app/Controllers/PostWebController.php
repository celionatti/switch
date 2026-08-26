<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Actions\CreatePostAction;
use App\Models\Post;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Switch\Controller\Controller;

class PostWebController extends Controller
{
    /**
     * Display a listing of posts with pagination and automatic demo seeding.
     */
    public function index(ServerRequestInterface $request): string|ResponseInterface
    {
        $this->ensureSeedData();

        $page = isset($request->getQueryParams()['page']) ? (int) $request->getQueryParams()['page'] : 1;
        $tag = $request->getQueryParams()['tag'] ?? null;

        $query = Post::query()->orderBy('id', 'desc');

        if ($tag) {
            $query->where('tags', 'like', "%{$tag}%");
        }

        $paginator = $query->paginate(perPage: 6, page: $page);

        return $this->view('posts.index', [
            'title' => 'Blog Posts & Articles — Switch Framework',
            'posts' => $paginator->items(),
            'paginator' => $paginator,
            'currentTag' => $tag,
        ]);
    }

    /**
     * Show form to create a new post.
     */
    public function create(): string
    {
        return $this->view('posts.create', [
            'title' => 'Create New Post — Switch Framework',
        ]);
    }

    /**
     * Store newly created post via CreatePostAction domain action.
     */
    public function store(ServerRequestInterface $request): ResponseInterface
    {
        $data = (array) ($request->getParsedBody() ?? []);
        
        // Parse tags if provided as comma-separated string
        if (isset($data['tags']) && is_string($data['tags'])) {
            $data['tags'] = array_values(array_filter(array_map('trim', explode(',', $data['tags']))));
        }

        $data['is_featured'] = isset($data['is_featured']) && ($data['is_featured'] === '1' || $data['is_featured'] === 'on' || $data['is_featured'] === true);

        try {
            /** @var Post $post */
            $post = CreatePostAction::run($data);
            $this->toast('Post created successfully!', 'success');
            return $this->redirect("/posts/{$post->id}");
        } catch (\Throwable $e) {
            $this->toast('Error creating post: ' . $e->getMessage(), 'error');
            return $this->redirect('/posts/create');
        }
    }

    /**
     * Display single post view (e.g. /posts/3) with state machine flow and audit trail.
     */
    public function show(mixed $id = null, ?ServerRequestInterface $request = null): string|ResponseInterface
    {
        $this->ensureSeedData();

        if ($id instanceof ServerRequestInterface) {
            $request = $id;
            $id = $request->getAttribute('id') ?? $request->getAttribute('post');
        } elseif ($id === null && $request !== null) {
            $id = $request->getAttribute('id') ?? $request->getAttribute('post');
        }

        /** @var Post|null $post */
        $post = is_numeric($id)
            ? Post::find((int) $id)
            : Post::where('slug', '=', (string) $id)->first();

        if (!$post) {
            $this->toast("Post '{$id}' not found.", 'error');
            return $this->redirect('/posts');
        }

        // Record a view event in the audit trail
        $post->recordAudit('viewed', ['referrer' => $_SERVER['HTTP_REFERER'] ?? 'direct']);

        return $this->view('posts.show', [
            'title' => $post->title . ' — Switch Framework',
            'post' => $post,
            'audits' => $post->history(),
            'canPublish' => $post->canApply('publish'),
            'canArchive' => $post->canApply('archive'),
            'canDraft' => $post->canApply('draft'),
        ]);
    }

    /**
     * Transition post to published state using Finite State Machine (Flow).
     */
    public function publish(mixed $id = null, ?ServerRequestInterface $request = null): ResponseInterface
    {
        if ($id instanceof ServerRequestInterface) {
            $request = $id;
            $id = $request->getAttribute('id');
        } elseif ($id === null && $request !== null) {
            $id = $request->getAttribute('id');
        }

        /** @var Post|null $post */
        $post = is_numeric($id) ? Post::find((int) $id) : Post::where('slug', '=', (string) $id)->first();

        if (!$post) {
            $this->toast('Post not found', 'error');
            return $this->redirect('/posts');
        }

        try {
            $post->applyFlow('publish');
            $post->save();
            $this->toast("Post #{$post->id} is now published!", 'success');
        } catch (\Throwable $e) {
            $this->toast("Could not publish post: " . $e->getMessage(), 'error');
        }

        return $this->redirect("/posts/{$post->id}");
    }

    /**
     * Transition post to archived state using Finite State Machine (Flow).
     */
    public function archive(mixed $id = null, ?ServerRequestInterface $request = null): ResponseInterface
    {
        if ($id instanceof ServerRequestInterface) {
            $request = $id;
            $id = $request->getAttribute('id');
        } elseif ($id === null && $request !== null) {
            $id = $request->getAttribute('id');
        }

        /** @var Post|null $post */
        $post = is_numeric($id) ? Post::find((int) $id) : Post::where('slug', '=', (string) $id)->first();

        if (!$post) {
            $this->toast('Post not found', 'error');
            return $this->redirect('/posts');
        }

        try {
            $post->applyFlow('archive');
            $post->save();
            $this->toast("Post #{$post->id} has been archived.", 'info');
        } catch (\Throwable $e) {
            $this->toast("Could not archive post: " . $e->getMessage(), 'error');
        }

        return $this->redirect("/posts/{$post->id}");
    }

    /**
     * Soft delete a post.
     */
    public function destroy(mixed $id = null, ?ServerRequestInterface $request = null): ResponseInterface
    {
        if ($id instanceof ServerRequestInterface) {
            $request = $id;
            $id = $request->getAttribute('id');
        } elseif ($id === null && $request !== null) {
            $id = $request->getAttribute('id');
        }

        /** @var Post|null $post */
        $post = is_numeric($id) ? Post::find((int) $id) : Post::where('slug', '=', (string) $id)->first();

        if ($post) {
            $post->recordAudit('deleted', ['id' => $post->id]);
            $post->delete();
            $this->toast("Post #{$post->id} deleted successfully.", 'info');
        }

        return $this->redirect('/posts');
    }

    private static bool $seeded = false;

    /**
     * Seed initial demo posts if the database is currently empty.
     * Runs only once per request to avoid duplicate migration-check queries.
     */
    private function ensureSeedData(): void
    {
        if (self::$seeded) {
            return;
        }
        self::$seeded = true;

        try {
            $db = Post::getConnection();
            if ($db) {
                // Ensure migrations are run if tables do not exist
                $repo = new \Switch\Database\Migration\MigrationRepository($db);
                $runner = new \Switch\Database\Migration\MigrationRunner($db, $repo);

                $migrationsDir = __DIR__ . '/../../database/migrations';
                if (is_dir($migrationsDir)) {
                    $files = glob($migrationsDir . '/*.php') ?: [];
                    $migrations = [];
                    foreach ($files as $file) {
                        $name = basename($file, '.php');
                        $migrations[$name] = require $file;
                    }
                    if (!empty($migrations)) {
                        $runner->run($migrations);
                    }
                }
            }

            if (Post::query()->count() === 0) {
                $p1 = Post::create([
                    'title' => 'Getting Started with Switch Framework',
                    'slug' => 'getting-started-with-switch-framework',
                    'content' => 'Switch is a high-velocity full-stack PHP framework built with decoupled PSR packages, high-performance view compilation, and built-in developer tooling.',
                    'status' => 'published',
                    'tags' => ['quickstart', 'php', 'switch'],
                    'is_featured' => true,
                ]);
                $p1->recordAudit('created', ['by' => 'system']);
                $p1->recordAudit('published', ['by' => 'system']);

                $p2 = Post::create([
                    'title' => 'Quad-Engine Architecture: Domain Actions',
                    'slug' => 'quad-engine-architecture-domain-actions',
                    'content' => 'Switch Actions act as HTTP Controllers, CLI Commands, Queue Jobs, and Domain Services all in a single reusable PHP class.',
                    'status' => 'published',
                    'tags' => ['architecture', 'actions', 'design-patterns'],
                    'is_featured' => false,
                ]);
                $p2->recordAudit('created', ['by' => 'system']);
                $p2->recordAudit('published', ['by' => 'system']);

                $p3 = Post::create([
                    'title' => 'Finite State Machines & Audit Trails in ORM',
                    'slug' => 'finite-state-machines-and-audit-trails-in-orm',
                    'content' => 'Easily manage state workflows and automatically track lifecycle events with HasFlow and HasAuditTrail traits in Switch models.',
                    'status' => 'draft',
                    'tags' => ['flow', 'audit', 'database'],
                    'is_featured' => true,
                ]);
                $p3->recordAudit('created', ['by' => 'system']);
            }
        } catch (\Throwable) {
            // Silently ignore if table not created yet in current test context
        }
    }
}
