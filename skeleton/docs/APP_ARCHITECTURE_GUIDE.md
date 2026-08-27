# Switch Framework — Application Directory Architecture Guide (`app/*`)

The `app/` directory is the heart of your Switch Framework application. It is organized into dedicated, single-responsibility layers designed for extreme velocity, clean code separation, security by default, and high maintainability.

---

## 📁 Directory Overview

| Directory | Purpose | CLI Scaffolding Command |
| :--- | :--- | :--- |
| [`app/Actions/`](#1-appactions--domain-actions) | Single-responsibility Domain Business Logic & Workflows | `php switch make:action <Name>` |
| [`app/Controllers/`](#2-appcontrollers--http--api-controllers) | Slim HTTP & API Request Handlers | `php switch make:controller <Name>` |
| [`app/Models/`](#3-appmodels--database-orm-models) | Database Entities, Relationships, and Query Scopes | `php switch make:model <Name> -m -c` |
| [`app/Services/`](#4-appservices--domain-services) | Reusable Business Services, Third-Party APIs, Complex Logic | `php switch make:service <Name>` |
| [`app/Flows/`](#5-appflows--state-machines--workflows) | Finite State Machines, Lifecycle Approval Graphs, Audit Trails | `php switch make:flow <Name>` |
| [`app/Utils/`](#6-apputils--auto-loaded-helpers--utilities) | Auto-discovered Custom Procedural Functions & Utility Classes | *Auto-discovered on Boot* |
| [`app/Mail/`](#7-appmail--mailables--notifications) | Email Mailables, HTML/Text Templates, Attachments | `php switch make:mail <Name>` |
| [`app/Middleware/`](#8-appmiddleware--psr-15-middleware) | PSR-15 HTTP Request/Response Filters, Auth, Rate Limiting | `php switch make:middleware <Name>` |
| [`app/Providers/`](#9-appproviders--service-providers) | Dependency Injection Container Bindings & Bootstrap Hooks | `php switch make:provider <Name>` |
| [`app/Events/`](#10-appevents--event-driven-architecture) | Domain Events & Event Listeners | `php switch make:event <Name>` |
| [`app/Commands/`](#11-appcommands--custom-cli-commands) | Custom CLI Commands & Scheduled Tasks | `php switch make:command <Name>` |

---

## 1. `app/Actions/` — Domain Actions

Actions are **single-responsibility, self-validating invokable business tasks**. They can be called directly from code, executed as an HTTP Controller endpoint, or dispatched to background queues asynchronously.

### Generating an Action:
```bash
php switch make:action PublishArticleAction
```

### Complete Implementation Example:
```php
<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Post;
use Switch\Foundation\Action\Action;

class PublishArticleAction extends Action
{
    /**
     * Define input validation rules.
     */
    public function rules(): array
    {
        return [
            'post_id' => 'required|numeric',
            'publish_at' => 'nullable|date',
        ];
    }

    /**
     * Check authorization before execution.
     */
    public function authorize(): bool
    {
        return true; // e.g. auth()->check() && auth()->user()->can('publish')
    }

    /**
     * Execute the domain logic.
     */
    public function handle(array $data): Post
    {
        $post = Post::findOrFail((int) $data['post_id']);
        $post->applyFlow('publish');
        return $post;
    }
}
```

### 3 Ways to Execute Actions:
```php
// 1. Direct Service Call:
$post = PublishArticleAction::run(['post_id' => 42]);

// 2. Direct HTTP Route (as a Controller):
Route::post('/articles/publish', PublishArticleAction::class);

// 3. Asynchronous Queue Dispatch (Background Worker):
PublishArticleAction::dispatch(['post_id' => 42]);
```

---

## 2. `app/Controllers/` — HTTP & API Controllers

Controllers in Switch Framework are slim coordinators that accept PSR-7 requests, delegate logic to Models, Actions, or Services, and return PSR-7 responses or view templates.

### Generating a Controller:
```bash
php switch make:controller UserController
# Or an API controller:
php switch make:controller Api/V1/PostController --api
```

### Complete Implementation Example:
```php
<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\PostService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Switch\Controller\Controller;

class PostController extends Controller
{
    public function __construct(private PostService $postService)
    {
    }

    public function index(): string
    {
        $posts = $this->postService->getPublishedPosts(10);

        return $this->view('posts.index', [
            'title' => 'Latest Articles',
            'posts' => $posts,
        ]);
    }

    public function store(ServerRequestInterface $request): ResponseInterface
    {
        $body = (array) ($request->getParsedBody() ?? []);
        
        $post = $this->postService->createPost($body);
        $this->toast('Article published successfully!', 'success');

        return $this->json([
            'success' => true,
            'post' => $post->toArray(),
        ], 201);
    }
}
```

---

## 3. `app/Models/` — Database ORM Models

Switch ORM models provide Active Record syntax, zero-overhead parameterized SQL, relationship management (`belongsTo`, `hasMany`, `hasOne`), soft deletes, JSON casting, query scopes, and state machine flow integration.

### Generating a Model with Migration, Controller, and Seeder:
```bash
php switch make:model Article -m -c -s
```

### Complete Implementation Example:
```php
<?php

declare(strict_types=1);

namespace App\Models;

use App\Flows\PostFlow;
use Switch\Database\ORM\Model;
use Switch\Database\ORM\SoftDeletes;
use Switch\Foundation\Flow\HasAuditTrail;
use Switch\Foundation\Flow\HasFlow;
use Switch\Foundation\Flow\StateMachine;

class Post extends Model
{
    use HasFlow, HasAuditTrail, SoftDeletes;

    protected string $table = 'posts';
    protected string $primaryKey = 'id';
    protected bool $softDeletes = true;

    protected array $fillable = [
        'user_id',
        'title',
        'slug',
        'body',
        'status',
        'tags',
        'is_featured',
    ];

    protected array $casts = [
        'tags' => 'json',
        'is_featured' => 'bool',
    ];

    /**
     * Attach Finite State Machine Flow.
     */
    public static function flow(): StateMachine
    {
        return PostFlow::create('status');
    }

    /**
     * Relationship: Author
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Query Scope: Published Only
     */
    public function scopePublished($query)
    {
        return $query->where('status', '=', 'published');
    }
}
```

---

## 4. `app/Services/` — Domain Services

Services encapsulate multi-step business logic, external API integrations (Stripe, AWS, Twilio), data imports/exports, and reusable domain calculations without cluttering controllers.

### Generating a Service:
```bash
php switch make:service BillingService
```

### Complete Implementation Example:
```php
<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;

class BillingService
{
    /**
     * Charge customer and update subscription tier.
     *
     * @param array<string, mixed> $paymentData
     */
    public function processSubscription(User $user, string $planId, array $paymentData): bool
    {
        // 1. Process payment via Stripe/Payment Gateway
        // 2. Update user subscription tier
        $user->subscription_tier = $planId;
        $user->subscription_expires_at = date('Y-m-d H:i:s', strtotime('+1 month'));
        $user->save();

        // 3. Dispatch confirmation email
        mailer()->to($user->email)->send(new \App\Mail\SubscriptionConfirmedMail($planId));

        return true;
    }
}
```

---

## 5. `app/Flows/` — State Machines & Approval Workflows

State Machines govern model state lifecycles (e.g. `Draft -> Review -> Published -> Archived`). They enforce valid transitions, execute guard conditions, run before/after transition hooks, and automatically record audit trails.

### Generating a Flow:
```bash
php switch make:flow OrderFlow
```

### Complete Implementation Example:
```php
<?php

declare(strict_types=1);

namespace App\Flows;

use Switch\Foundation\Flow\StateMachine;

class OrderFlow
{
    public static function create(string $field = 'status'): StateMachine
    {
        return StateMachine::define($field)
            ->states(['pending', 'processing', 'shipped', 'delivered', 'cancelled'])
            ->initial('pending')
            ->allow('pay', 'pending', 'processing', function ($order, $context) {
                return $order->total_amount > 0;
            })
            ->allow('ship', 'processing', 'shipped', function ($order, $context) {
                return !empty($order->tracking_number);
            })
            ->allow('deliver', 'shipped', 'delivered')
            ->allow('cancel', ['pending', 'processing'], 'cancelled')
            ->afterTransition('ship', function ($order, $context) {
                mailer()->to($order->customer_email)->send(new \App\Mail\OrderShippedMail($order));
            });
    }
}
```

### Using Flows on Models:
```php
$order = Order::find(101);

// Check if transition is allowed under current state & guard:
if ($order->canApply('ship')) {
    $order->applyFlow('ship'); // Transitions state, auto-saves, and records audit trail!
}

// Get all currently available transitions:
$transitions = $order->availableTransitions(); // ['ship', 'cancel']

// Inspect state transition history:
$history = $order->history();
```

---

## 6. `app/Utils/` — Auto-Loaded Helpers & Utilities

Any file placed inside `app/Utils/` or `app/Helpers/` is **automatically discovered and loaded on framework startup with zero configuration**.

### Features:
- **Procedural Functions**: Place functions in `app/Utils/helpers.php` or `app/Utils/formatting.php` and use them anywhere in views, controllers, actions, and models.
- **Utility Classes**: Place classes in `app/Utils/FormatUtil.php` under the `App\Utils` namespace.

### Procedural Functions Example (`app/Utils/helpers.php`):
```php
<?php

if (!function_exists('currency')) {
    function currency(float|int $amount, string $symbol = '$'): string
    {
        return $symbol . number_format((float) $amount, 2, '.', ',');
    }
}

if (!function_exists('slugify')) {
    function slugify(string $text): string
    {
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $text), '-'));
    }
}
```

### Utility Class Example (`app/Utils/FormatUtil.php`):
```php
<?php

namespace App\Utils;

class FormatUtil
{
    public static function truncate(string $text, int $limit = 100): string
    {
        return mb_strlen($text) <= $limit ? $text : mb_substr($text, 0, $limit) . '...';
    }
}
```

### Usage Anywhere:
```php
// In a View:
<span>{{ currency($product->price) }}</span>
<a href="/posts/{{ slugify($post->title) }}">{{ FormatUtil::truncate($post->title, 40) }}</a>

// In a Controller/Action:
$slug = slugify($request->get('title'));
```

---

## 7. `app/Mail/` — Mailables & Notifications

Mailables are clean class-based email builders that support subject lines, recipient addressing, HTML/text body rendering, attachments, and asynchronous queueing.

### Generating a Mailable:
```bash
php switch make:mail WelcomeUserMail
```

### Complete Implementation Example:
```php
<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\User;
use Switch\Foundation\Mailer\Mailable;

class WelcomeUserMail extends Mailable
{
    public function __construct(public User $user)
    {
    }

    public function build(): self
    {
        return $this->subject("Welcome to Switch Framework, {$this->user->name}!")
            ->from('support@switch-framework.dev', 'Switch Team')
            ->view('emails.welcome', [
                'user' => $this->user,
                'loginUrl' => 'https://example.com/login',
            ]);
    }
}
```

### Sending Mail:
```php
// Synchronous Send:
mailer()->to($user->email)->send(new WelcomeUserMail($user));

// Send with Global Helper:
mail_to('sarah@example.com', new WelcomeUserMail($user));

// Queue for Background Dispatch:
mailer()->to($user->email)->queue(new WelcomeUserMail($user));
```

---

## 8. `app/Middleware/` — PSR-15 Middleware

Middleware filters incoming HTTP requests before reaching your controllers, and modifies outgoing HTTP responses (e.g. Authentication, CORS, Rate Limiting, Logging).

### Generating a Middleware:
```bash
php switch make:middleware EnsureUserIsAdmin
```

### Complete Implementation Example:
```php
<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Switch\Http\Response;

class EnsureUserIsAdmin implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            return new Response(403, ['Content-Type' => 'text/html'], 'Access Denied: Admins only.');
        }

        return $handler->handle($request);
    }
}
```

### Registering Middleware in `bootstrap/app.php`:
```php
->withMiddleware(function (MiddlewareCollector $middleware) {
    // 1. Global Middleware (Runs on every request):
    $middleware->append(\App\Middleware\LogHttpTraffic::class);

    // 2. Web Group Middleware:
    $middleware->web(\Switch\Session\Middleware\StartSession::class);

    // 3. Route Aliases:
    $middleware->alias([
        'admin' => \App\Middleware\EnsureUserIsAdmin::class,
    ]);
})
```

---

## 9. `app/Providers/` — Service Providers

Service Providers are the central place for configuring and binding services into the dependency injection container, registering event listeners, and running bootstrap routines.

### Generating a Service Provider:
```bash
php switch make:provider PaymentServiceProvider
```

### Complete Implementation Example:
```php
<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\PaymentGateway;
use App\Services\StripePaymentGateway;
use Switch\Container\Container;
use Switch\Container\ServiceProviderInterface;

class PaymentServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        $container->singleton(PaymentGateway::class, function () {
            return new StripePaymentGateway(env('STRIPE_SECRET_KEY'));
        });
    }

    public function boot(Container $container): void
    {
        // Bootstrap hooks (e.g. registering event listeners, custom view directives)
    }
}
```

---

## 10. `app/Events/` — Event-Driven Architecture

Events decouple actions in your application (e.g. `UserRegisteredEvent` triggers `SendWelcomeEmailListener` and `CreateInitialProfileListener`).

### Generating an Event:
```bash
php switch make:event UserRegisteredEvent
```

### Complete Implementation Example:
```php
<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\User;

class UserRegisteredEvent
{
    public function __construct(public User $user)
    {
    }
}
```

### Dispatching & Listening to Events:
```php
// Dispatch:
event(new UserRegisteredEvent($user));

// Register Listener (e.g. in a Provider):
events()->listen(UserRegisteredEvent::class, function (UserRegisteredEvent $event) {
    mailer()->to($event->user->email)->send(new WelcomeUserMail($event->user));
});
```

---

## 11. `app/Commands/` — Custom CLI Commands

Custom CLI commands allow you to automate cron tasks, database syncs, queue workers, and maintenance routines.

### Generating a Command:
```bash
php switch make:command SendWeeklyDigestCommand
```

### Complete Implementation Example:
```php
<?php

declare(strict_types=1);

namespace App\Commands;

use Switch\Console\Command\Command;

class SendWeeklyDigestCommand extends Command
{
    protected string $signature = 'digest:send {--dry-run : Simulate sending without dispatching emails}';
    protected string $description = 'Send the weekly summary digest to all active subscribers';

    public function handle(): int
    {
        $dryRun = $this->hasOption('dry-run');
        $this->info("Starting weekly digest dispatch (Dry run: " . ($dryRun ? 'YES' : 'NO') . ")...");

        // Logic here...

        $this->success("Digest successfully sent to 1,420 subscribers.");
        return 0;
    }
}
```

### Running the Command:
```bash
php switch digest:send --dry-run
```

---

## 🔒 Security & Performance Guarantees

1. **Sliding Session Expiration**: Active user requests continuously slide the session expiration forward. Configured via `config/session.php` (`lifetime` = 120 mins, `http_only` = true, `same_site` = 'lax').
2. **Zero-Overhead Security Headers**: `SecurityHeadersMiddleware` protects all responses with `X-Content-Type-Options: nosniff`, `X-Frame-Options: SAMEORIGIN`, `X-XSS-Protection: 1; mode=block`, and `Referrer-Policy: strict-origin-when-cross-origin`.
3. **Prepared Statements**: 100% of database queries use PDO prepared parameter binding.
4. **Auto-Escaped Views**: `{{ $var }}` is automatically sanitized against XSS attacks.
5. **Instant Live SPA**: `switch-live.js` morphs the DOM with 0ms perceived lag and prefetching.
