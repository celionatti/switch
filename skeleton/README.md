# Switch Framework (`celionatti/switch`)

> A fast, modular, modern, and futuristic PHP framework. Built for high performance, developer happiness, zero-compromise security, and clean software architecture.

---

## ⚡ Key Features

- 🧩 **100% Modular Architecture**: Built on decoupled PSR-compliant packages (`switch/container`, `switch/http-message`, `switch/router`, `switch/events`, `switch/config`, `switch/kernel`, `switch/view`, `switch/database`, `switch/error-handler`, `switch/console`, `switch/head`, `switch/live`, `switch/controller`, `switch/session`, `switch/foundation`).
- 🚀 **Sub-Millisecond Speed**: Pre-compiled regex routing, lightweight PSR-7/PSR-15 pipeline, and compiled view caching.
- 📂 **Auto-Discovered Developer Utilities (`app/Utils/`)**: Drop procedural helper functions or utility classes into `app/Utils/` or `app/Helpers/` and they are **automatically loaded on boot with zero configuration**.
- 🌊 **Switch Flow**: Model state machines with guarded transitions, lifecycle hooks, and automatic audit trails (`app/Flows/`).
- ⚡ **Switch Live SPA Engine**: Zero-JS SPA reactivity with 0ms optimistic UI, drag-and-drop table/Kanban reordering, DOM morphing, and prefetching.
- 🛡️ **Zero-Overhead Security by Default**: Automated security headers (`X-Content-Type-Options`, `X-Frame-Options`, `X-XSS-Protection`, `Referrer-Policy`), sliding-window session expiration, CSRF token verification, and parameterized PDO queries.
- ✉️ **Class-Based Mailables**: Clean email builder supporting HTML/text templates, attachments, and background queues (`app/Mail/`).
- 🗄️ **Fluent ORM & Database**: Active Record ORM, migration builder, database seeders (`db:seed`), eager loading (N+1 protection), soft deletes, and JSON casting.
- 🧪 **Switch Testbench**: Fluent Laravel-style testing DSL (`get`, `postJson`, `assertOk`, `assertJsonPath`, `assertJsonStructure`).
- ⚡ **Artisan-Style Switch CLI**: Generator commands (`make:action`, `make:controller`, `make:model`, `make:service`, `make:flow`, `make:mail`, `make:seeder`, `make:provider`), dev server (`serve`), and interactive `tinker` shell.

---

## 📁 Canonical Application Structure (`app/*`)

For full details, code samples, and usage guides, see the [**Application Directory Architecture Guide**](docs/APP_ARCHITECTURE_GUIDE.md).

```
my-app/
├── app/
│   ├── Actions/           # Single-responsibility Domain Actions (make:action)
│   ├── Commands/          # Custom CLI Console Commands (make:command)
│   ├── Controllers/       # Slim HTTP & API Controllers (make:controller)
│   ├── Events/            # Domain Events & Listeners (make:event)
│   ├── Flows/             # State Machines & Lifecycle Approval Graphs (make:flow)
│   ├── Mail/              # Class-based Mailables & Notifications (make:mail)
│   ├── Middleware/        # PSR-15 HTTP Middleware (make:middleware)
│   ├── Models/            # Database ORM Models & Entities (make:model)
│   ├── Providers/         # Application Service Providers (make:provider)
│   ├── Services/          # Domain Services & Business Logic (make:service)
│   └── Utils/             # Auto-discovered custom functions & utility classes
├── bootstrap/
│   └── app.php            # Framework bootstrapper & middleware stack
├── config/
│   ├── app.php            # Application settings & service providers
│   ├── auth.php           # Authentication & passwordless tokens config
│   ├── database.php       # Database connections & drivers
│   ├── security.php       # Security headers & CSRF settings
│   └── session.php        # Sliding session expiration & cookie config
├── database/
│   ├── database.sqlite    # SQLite database file
│   ├── migrations/        # Database schema migrations
│   └── seeders/           # Database seeders (db:seed)
├── public/
│   ├── index.php          # Front Controller entrypoint
│   └── .htaccess          # Web server rewrite rules
├── resources/
│   └── views/             # Switch View templates & UI components
├── routes/
│   ├── api.php            # Stateless API endpoints
│   └── web.php            # Web application routes
├── storage/
│   ├── cache/             # System cache
│   ├── logs/              # Application logs
│   ├── sessions/          # File session storage
│   └── views/             # Compiled blade views
├── .env                   # Environment configuration
├── composer.json          # Package manifest
└── switch                 # CLI executable
```

---

## 🛠️ CLI Generator Commands

```bash
# Domain & Business Logic
php switch make:action PublishArticleAction
php switch make:service BillingService
php switch make:flow OrderFlow

# HTTP & Database
php switch make:controller PostController --resource
php switch make:model Post -m -c -s -a
php switch make:migration create_orders_table
php switch make:seeder DatabaseSeeder

# Infrastructure & Communication
php switch make:mail WelcomeUserMail
php switch make:middleware EnsureUserIsAdmin
php switch make:provider PaymentServiceProvider
php switch make:command SendDigestCommand
php switch make:event UserRegisteredEvent

# Operations & Database
php switch migrate
php switch db:seed
php switch serve
php switch tinker
```

---

## 📄 Documentation

- [Application Directory Architecture Guide (`app/*`)](docs/APP_ARCHITECTURE_GUIDE.md)
- [Drag & Drop and Sorting Guide](docs/DRAG_DROP_SORTING_GUIDE.md)
- [Complete Framework Usage Guide](docs/USAGE_GUIDE.md)

---

## 📄 License

The Switch Framework is open-source software licensed under the [MIT license](LICENSE).
