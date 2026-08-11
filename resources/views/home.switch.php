<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Switch — The Modern PHP Framework</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        
        :root {
            --bg-main: #0b0f17;
            --bg-card: rgba(255, 255, 255, 0.025);
            --bg-card-hover: rgba(255, 255, 255, 0.045);
            --border-color: rgba(255, 255, 255, 0.08);
            --border-hover: rgba(239, 68, 68, 0.4);
            --accent-red: #ef4444;
            --accent-gradient: linear-gradient(135deg, #f87171, #ef4444, #dc2626);
            --text-primary: #f8fafc;
            --text-secondary: #94a3b8;
            --text-muted: #64748b;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: var(--bg-main);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            line-height: 1.6;
            overflow-x: hidden;
            background-image: 
                radial-gradient(circle at 50% 0%, rgba(239, 68, 68, 0.12) 0%, transparent 60%),
                radial-gradient(circle at 85% 30%, rgba(124, 58, 237, 0.06) 0%, transparent 40%);
        }

        /* Top Navigation */
        header {
            max-width: 1200px;
            width: 100%;
            margin: 0 auto;
            padding: 2rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: var(--text-primary);
            font-weight: 700;
            font-size: 1.25rem;
            letter-spacing: -0.02em;
        }

        .brand-icon {
            width: 34px;
            height: 34px;
            background: var(--accent-gradient);
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            box-shadow: 0 4px 14px rgba(239, 68, 68, 0.35);
        }

        nav {
            display: flex;
            align-items: center;
            gap: 1.75rem;
        }

        nav a {
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: color 0.2s ease;
        }

        nav a:hover {
            color: var(--text-primary);
        }

        .nav-btn {
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid var(--border-color);
            padding: 0.5rem 1rem;
            border-radius: 8px;
            color: var(--text-primary) !important;
            transition: all 0.2s ease;
        }

        .nav-btn:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.2);
        }

        /* Hero Container */
        .hero-container {
            max-width: 1000px;
            width: 100%;
            margin: 3.5rem auto 0;
            padding: 0 1.5rem;
            text-align: center;
        }

        .version-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(239, 68, 68, 0.08);
            border: 1px solid rgba(239, 68, 68, 0.25);
            padding: 0.35rem 1rem;
            border-radius: 9999px;
            font-size: 0.825rem;
            font-weight: 500;
            color: #fca5a5;
            margin-bottom: 2rem;
        }

        .version-badge span {
            width: 6px;
            height: 6px;
            background: var(--accent-red);
            border-radius: 50%;
            display: inline-block;
        }

        h1 {
            font-size: clamp(2.5rem, 5.5vw, 4.2rem);
            font-weight: 800;
            letter-spacing: -0.03em;
            line-height: 1.1;
            margin-bottom: 1.5rem;
            color: #ffffff;
        }

        h1 span {
            background: var(--accent-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        p.hero-desc {
            font-size: clamp(1.05rem, 2vw, 1.25rem);
            color: var(--text-secondary);
            max-width: 680px;
            margin: 0 auto 2.5rem;
            font-weight: 400;
        }

        .cta-group {
            display: flex;
            gap: 1rem;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: 4rem;
        }

        .btn-primary {
            background: var(--accent-gradient);
            color: #ffffff;
            font-weight: 600;
            padding: 0.85rem 2rem;
            border-radius: 10px;
            text-decoration: none;
            font-size: 0.95rem;
            box-shadow: 0 8px 25px rgba(239, 68, 68, 0.3);
            transition: all 0.25s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(239, 68, 68, 0.45);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            font-weight: 500;
            padding: 0.85rem 2rem;
            border-radius: 10px;
            text-decoration: none;
            font-size: 0.95rem;
            transition: all 0.25s ease;
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(255, 255, 255, 0.2);
        }

        /* Code Preview Terminal */
        .terminal-box {
            max-width: 750px;
            margin: 0 auto 5rem;
            background: #06090e;
            border: 1px solid var(--border-color);
            border-radius: 14px;
            text-align: left;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
        }

        .terminal-header {
            background: rgba(255, 255, 255, 0.02);
            padding: 0.75rem 1.25rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .dot { width: 10px; height: 10px; border-radius: 50%; }
        .dot-red { background: #ef4444; }
        .dot-yellow { background: #f59e0b; }
        .dot-green { background: #10b981; }

        .terminal-title {
            margin-left: auto;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        .terminal-body {
            padding: 1.5rem;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.875rem;
            line-height: 1.7;
            color: #e2e8f0;
            overflow-x: auto;
        }

        .keyword { color: #f472b6; }
        .function { color: #60a5fa; }
        .string { color: #34d399; }
        .comment { color: var(--text-muted); }

        /* Feature Cards Grid */
        .grid-container {
            max-width: 1100px;
            margin: 0 auto 5rem;
            padding: 0 1.5rem;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
        }

        .card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 1.75rem;
            transition: all 0.25s ease;
        }

        .card:hover {
            background: var(--bg-card-hover);
            border-color: var(--border-hover);
            transform: translateY(-4px);
        }

        .card-icon {
            width: 42px;
            height: 42px;
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            margin-bottom: 1.25rem;
        }

        .card h3 {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #ffffff;
        }

        .card p {
            font-size: 0.875rem;
            color: var(--text-secondary);
            line-height: 1.6;
        }

        /* Footer */
        footer {
            margin-top: auto;
            border-top: 1px solid var(--border-color);
            padding: 2.5rem 1.5rem;
            text-align: center;
            font-size: 0.875rem;
            color: var(--text-muted);
        }

        footer a {
            color: var(--text-secondary);
            text-decoration: none;
            margin: 0 0.5rem;
        }

        footer a:hover {
            color: var(--text-primary);
        }

        @media (max-width: 640px) {
            header { padding: 1.25rem 1rem; }
            .hero-container { margin-top: 2rem; }
            .cta-group { flex-direction: column; width: 100%; }
            .btn-primary, .btn-secondary { width: 100%; text-align: center; }
        }
    </style>
</head>
<body>

    <!-- Header -->
    <header>
        <a href="/" class="brand">
            <div class="brand-icon">⚡</div>
            <span>Switch</span>
        </a>
        <nav>
            <a href="/about">About</a>
            <a href="/api/status">API</a>
            <a href="https://github.com/celionatti/switch" class="nav-btn" target="_blank">GitHub</a>
        </nav>
    </header>

    <!-- Hero -->
    <section class="hero-container">
        <div class="version-badge">
            <span></span> Switch v{{ $version ?? '1.0.0' }} Released
        </div>
        
        <h1>The PHP Framework for<br><span>Modern Web Artisans</span></h1>
        
        <p class="hero-desc">
            Switch is an ultra-fast, modular PHP framework built with clean architecture, HTML tag components, active record ORM, and a colorful CLI.
        </p>

        <div class="cta-group">
            <a href="https://github.com/celionatti/switch" class="btn-primary" target="_blank">Get Started</a>
            <a href="/about" class="btn-secondary">Explore Features</a>
        </div>

        <!-- Terminal Preview -->
        <div class="terminal-box">
            <div class="terminal-header">
                <div class="dot dot-red"></div>
                <div class="dot dot-yellow"></div>
                <div class="dot dot-green"></div>
                <div class="terminal-title">routes/web.php</div>
            </div>
            <div class="terminal-body">
                <span class="comment">// Define fluent HTTP routes in Switch</span><br>
                <span class="keyword">use</span> Switch\Router\Facade\<span class="function">Route</span>;<br><br>
                <span class="function">Route</span>::<span class="function">get</span>(<span class="string">'/'</span>, <span class="keyword">function</span> () {<br>
                &nbsp;&nbsp;&nbsp;&nbsp;<span class="keyword">return</span> <span class="function">view</span>(<span class="string">'home'</span>, [<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="string">'version'</span> =&gt; <span class="string">'1.0.0'</span><br>
                &nbsp;&nbsp;&nbsp;&nbsp;]);<br>
                });
            </div>
        </div>
    </section>

    <!-- Features Cards -->
    <section class="grid-container">
        <div class="card">
            <div class="card-icon">⚡</div>
            <h3>Blazing Performance</h3>
            <p>Minimalist footprint with zero unnecessary bloat. Optimized route matching and fast rendering engine.</p>
        </div>

        <div class="card">
            <div class="card-icon">🧩</div>
            <h3>10 Modular Packages</h3>
            <p>Built on standalone PSR-compliant components (`switch/router`, `switch/database`, `switch/view`, `switch/console`).</p>
        </div>

        <div class="card">
            <div class="card-icon">🛡️</div>
            <h3>Security by Default</h3>
            <p>Built-in CSRF tokens, honeypot protection, CSP nonces, and XSS HTML sanitization out of the box.</p>
        </div>

        <div class="card">
            <div class="card-icon">💻</div>
            <h3>Switch CLI &amp; Tinker</h3>
            <p>Artisan-style console with code generators, colorful ANSI output, and an interactive `tinker` REPL shell.</p>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <p>Switch Framework is open-source software licensed under the <a href="https://opensource.org/licenses/MIT" target="_blank">MIT License</a>.</p>
        <p style="margin-top: 0.5rem; font-size: 0.8rem;">Created by <a href="https://github.com/celionatti" target="_blank">celionatti</a> &bull; PHP 8.2+</p>
    </footer>

</body>
</html>
