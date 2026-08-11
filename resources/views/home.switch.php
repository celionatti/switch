<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Switch Framework' }}</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: linear-gradient(135deg, #0f0c29 0%, #302b63 50%, #24243e 100%);
            color: #e0e0e0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        header {
            padding: 1.5rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo { font-size: 1.4rem; font-weight: 700; color: #fff; letter-spacing: 0.05em; }
        .logo span { color: #7c3aed; }
        nav a {
            color: #a0a0b8; text-decoration: none; margin-left: 1.5rem;
            font-size: 0.95rem; transition: color 0.2s;
        }
        nav a:hover { color: #c084fc; }
        main {
            flex: 1; display: flex; flex-direction: column;
            align-items: center; justify-content: center; padding: 2rem; text-align: center;
        }
        .hero-badge {
            background: rgba(124,58,237,0.15); border: 1px solid rgba(124,58,237,0.3);
            border-radius: 999px; padding: 0.4rem 1.2rem; font-size: 0.8rem;
            color: #c084fc; margin-bottom: 1.5rem; letter-spacing: 0.04em;
        }
        h1 {
            font-size: clamp(2.2rem, 5vw, 3.8rem); font-weight: 800;
            background: linear-gradient(90deg, #c084fc, #7c3aed, #06b6d4);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            line-height: 1.15; margin-bottom: 1rem;
        }
        .subtitle { font-size: 1.15rem; color: #9090a8; max-width: 550px; line-height: 1.6; margin-bottom: 2.5rem; }
        .actions { display: flex; gap: 1rem; flex-wrap: wrap; justify-content: center; }
        .btn {
            padding: 0.75rem 2rem; border-radius: 10px; font-size: 0.95rem;
            font-weight: 600; text-decoration: none; transition: all 0.25s;
        }
        .btn-primary {
            background: linear-gradient(135deg, #7c3aed, #6d28d9);
            color: #fff; border: none;
        }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(124,58,237,0.35); }
        .btn-outline {
            background: transparent; color: #c084fc;
            border: 1.5px solid rgba(124,58,237,0.4);
        }
        .btn-outline:hover { background: rgba(124,58,237,0.1); }
        .features {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.5rem; max-width: 900px; width: 100%; margin-top: 4rem;
        }
        .feature-card {
            background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.06);
            border-radius: 14px; padding: 1.5rem; text-align: left;
            transition: transform 0.2s, border-color 0.2s;
        }
        .feature-card:hover { transform: translateY(-4px); border-color: rgba(124,58,237,0.3); }
        .feature-icon { font-size: 1.6rem; margin-bottom: 0.8rem; }
        .feature-card h3 { font-size: 1rem; color: #fff; margin-bottom: 0.4rem; }
        .feature-card p { font-size: 0.85rem; color: #8080a0; line-height: 1.5; }
        footer { text-align: center; padding: 2rem; font-size: 0.8rem; color: #5a5a78; }
    </style>
</head>
<body>
    <header>
        <div class="logo">⚡ <span>Switch</span></div>
        <nav>
            <a href="/">Home</a>
            <a href="/about">About</a>
            <a href="https://github.com/celionatti" target="_blank">GitHub</a>
        </nav>
    </header>

    <main>
        <div class="hero-badge">✨ v{{ $version ?? '1.0.0' }} — Fast, Modular &amp; Futuristic</div>
        <h1>{{ $title ?? 'Welcome to Switch' }}</h1>
        <p class="subtitle">
            A fast, modular, and futuristic PHP framework built for modern web development.
            Elegant syntax, powerful ORM, and blazing performance.
        </p>
        <div class="actions">
            <a href="https://github.com/celionatti" class="btn btn-primary" target="_blank">Get Started</a>
            <a href="/about" class="btn btn-outline">Learn More</a>
        </div>

        <div class="features">
            <div class="feature-card">
                <div class="feature-icon">🚀</div>
                <h3>Blazing Fast</h3>
                <p>Optimized routing, lazy loading, and minimal overhead for maximum performance.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🧩</div>
                <h3>Modular Design</h3>
                <p>Every package is standalone — use only what you need, replace what you don't.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🛡️</div>
                <h3>Secure by Default</h3>
                <p>CSRF protection, XSS sanitization, CSP nonces, and honeypot fields built-in.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">⚡</div>
                <h3>Modern CLI</h3>
                <p>Colorful Switch CLI with generators, tinker REPL, and auto-discovery.</p>
            </div>
        </div>
    </main>

    <footer>
        &copy; {{ date('Y') }} Switch Framework — Built with ❤️ by celionatti
    </footer>
</body>
</html>
