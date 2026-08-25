# Switch Framework (`celionatti/switch`)

> A fast, modular, modern, and futuristic PHP framework. Built for high performance, developer happiness, and clean software architecture.

---

## ⚡ Key Features

- 🧩 **100% Modular Architecture**: Built on decoupled PSR-compliant packages (`switch/container`, `switch/http-message`, `switch/router`, `switch/events`, `switch/config`, `switch/kernel`, `switch/view`, `switch/database`, `switch/error-handler`, `switch/console`, `switch/head`, `switch/live`, `switch/controller`, `switch/session`, `switch/foundation`).
- 🚀 **Blazing Fast**: Lightweight routing, lazy loading, and minimal memory overhead.
- 🎨 **HTML Tag View Components**: Intuitive `<x-card>`, `<x-button>`, `<x-alert>`, `<x-modal>`, `<x-spinner>`, `<x-skeleton>`, and `<x-avatar>` UI tags with built-in fluid responsive CSS.
- ⚡ **Switch Live**: Zero-JS SPA reactivity with instant transitions, DOM morphing, prefetching, and live state synchronization.
- 🌊 **Switch Flow**: Model state machines with guarded transitions and automatic audit trails.
- 🌉 **Switch Bridge**: Production-ready inbound/outbound webhook engine with HMAC signing, replay attack protection, and idempotency key deduplication.
- 🛡️ **Security by Default**: Built-in CSRF protection (`@csrf`), honeypots (`@honeypot`), CSP nonces (`@nonce`), XSS sanitization (`cleanHtml`), and script breakout prevention.
- 🗄️ **Fluent ORM & Database**: Active Record ORM, migration builder, eager loading (N+1 protection), soft deletes, JSON attribute casting, and scope builders.
- 💥 **Futuristic Error Handler**: Beautiful dark-mode stack trace UI in development, silent secure pages in production, with custom reporters (Log, Slack, Sentry).
- 🧪 **Switch Testbench**: Fluent Laravel-style testing DSL (`get`, `postJson`, `assertOk`, `assertJsonPath`, `assertJsonStructure`).
- ⚡ **Colorful Switch CLI & Tinker**: Artisan-inspired CLI (`php switch`) with generator commands, serve, route list, and interactive `tinker` REPL shell.

---

## 📦 Installation

```bash
composer create-project switch/switch my-app
cd my-app
```

---

## 🚀 Quick Start

Start the local development server:

```bash
php switch serve
```

Visit `http://127.0.0.1:8000` in your web browser!

---

## 📁 Directory Structure

```
my-app/
├── app/
│   ├── Commands/          # Custom CLI commands
│   ├── Controllers/       # HTTP Controllers
│   ├── Events/            # Event listener classes
│   ├── Middleware/        # PSR-15 Middleware
│   └── Models/            # Database ORM models
├── bootstrap/
│   └── app.php            # Framework bootstrapper
├── config/
│   ├── app.php            # Application settings
│   └── database.php       # Database configuration
├── database/
│   ├── database.sqlite    # SQLite database file
│   └── migrations/        # Database migrations
├── public/
│   ├── index.php          # HTTP Front Controller
│   └── .htaccess          # Apache rewrite rules
├── resources/
│   └── views/             # Blade-style & component views
├── routes/
│   ├── api.php            # API endpoints
│   └── web.php            # Web application routes
├── storage/
│   ├── cache/             # System cache
│   ├── logs/              # Application logs
│   └── views/             # Compiled views
├── .env                   # Environment variables
├── composer.json          # Dependencies manifest
└── switch                 # Switch CLI binary
```

---

## 🛠️ Common Commands

```bash
# Start dev server
php switch serve

# Interactive Tinker REPL
php switch tinker

# View routes table
php switch route:list

# Create controller
php switch make:controller UserController --resource

# Create model with migration
php switch make:model Post -m

# Run database migrations
php switch migrate
```

---

## 📄 License

The Switch Framework is open-source software licensed under the [MIT license](LICENSE).
