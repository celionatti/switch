<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title><yield name="title" default="Switch Framework — High-Velocity PHP Framework" /></title>
    
    <!-- Modern Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400..700;1,400..700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --bg-base: #08090c;
            --bg-surface: #0f1117;
            --bg-elevated: #161922;
            --bg-card: rgba(22, 25, 34, 0.75);
            
            --border-subtle: rgba(255, 255, 255, 0.07);
            --border-glow: rgba(6, 182, 212, 0.35);
            --border-hover: rgba(255, 255, 255, 0.16);

            --text-main: #f3f4f6;
            --text-muted: #9ca3af;
            --text-dim: #6b7280;

            --cyan-500: #06b6d4;
            --cyan-400: #22d3ee;
            --cyan-glow: rgba(6, 182, 212, 0.15);

            --emerald-400: #34d399;
            --amber-400: #fbbf24;
            --indigo-500: #6366f1;

            --font-sans: 'Instrument Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            --font-mono: 'JetBrains Mono', monospace;

            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 18px;
            --radius-full: 9999px;

            --transition-smooth: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }

        body {
            font-family: var(--font-sans);
            background-color: var(--bg-base);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            line-height: 1.55;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            overflow-x: hidden;
        }

        /* Ambient Glow Backgrounds */
        .ambient-glow {
            position: fixed;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 1000px;
            height: 450px;
            background: radial-gradient(circle, rgba(6, 182, 212, 0.12) 0%, rgba(99, 102, 241, 0.05) 50%, transparent 70%);
            filter: blur(80px);
            pointer-events: none;
            z-index: 0;
        }

        /* Responsive Layout Container */
        .page-wrapper {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            width: 100%;
        }

        .main-content {
            flex: 1;
            max-width: 1200px;
            width: 100%;
            margin: 0 auto;
            padding: 2.5rem 1.5rem 4rem;
        }

        /* Navbar Styles */
        .switch-navbar {
            width: 100%;
            border-bottom: 1px solid var(--border-subtle);
            background: rgba(8, 9, 12, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .navbar-inner {
            max-width: 1200px;
            margin: 0 auto;
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: inherit;
        }

        .brand-badge {
            width: 36px;
            height: 36px;
            border-radius: var(--radius-sm);
            background: rgba(6, 182, 212, 0.1);
            border: 1px solid rgba(6, 182, 212, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition-smooth);
        }

        .brand-logo:hover .brand-badge {
            border-color: var(--cyan-400);
            box-shadow: 0 0 16px var(--cyan-glow);
            transform: scale(1.05);
        }

        .brand-text {
            display: flex;
            flex-direction: column;
            line-height: 1.1;
        }

        .brand-title {
            font-size: 1.15rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            background: linear-gradient(135deg, #ffffff 40%, var(--cyan-400) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .brand-subtitle {
            font-size: 0.65rem;
            font-weight: 600;
            letter-spacing: 0.12em;
            color: var(--text-dim);
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 1.75rem;
        }

        .nav-item {
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: var(--transition-smooth);
        }

        .nav-item:hover {
            color: var(--cyan-400);
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .version-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.3rem 0.75rem;
            font-size: 0.75rem;
            font-family: var(--font-mono);
            font-weight: 500;
            color: var(--cyan-400);
            background: rgba(6, 182, 212, 0.08);
            border: 1px solid rgba(6, 182, 212, 0.25);
            border-radius: var(--radius-full);
        }

        .pulse-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--emerald-400);
            box-shadow: 0 0 8px var(--emerald-400);
            animation: pulse-ring 2s infinite ease-in-out;
        }

        @keyframes pulse-ring {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(0.85); }
        }

        .btn-github {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.45rem 0.9rem;
            border-radius: var(--radius-sm);
            background: var(--bg-elevated);
            border: 1px solid var(--border-subtle);
            color: var(--text-main);
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            transition: var(--transition-smooth);
        }

        .btn-github:hover {
            border-color: var(--border-hover);
            background: rgba(255,255,255,0.08);
        }

        /* Footer Styles */
        .switch-footer {
            width: 100%;
            border-top: 1px solid var(--border-subtle);
            background: var(--bg-surface);
            padding: 1.5rem;
            margin-top: auto;
        }

        .footer-inner {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .footer-left {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.85rem;
            color: var(--text-dim);
        }

        .footer-logo {
            font-weight: 700;
            font-size: 0.8rem;
            letter-spacing: 0.08em;
            color: var(--text-muted);
        }

        .footer-stats {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .stat-badge {
            font-size: 0.75rem;
            font-family: var(--font-mono);
            padding: 0.25rem 0.6rem;
            border-radius: var(--radius-sm);
            background: var(--bg-elevated);
            border: 1px solid var(--border-subtle);
            color: var(--text-muted);
        }

        .status-ready {
            color: var(--emerald-400);
            border-color: rgba(52, 211, 153, 0.25);
            background: rgba(52, 211, 153, 0.06);
        }

        /* Mobile & Tablet Responsiveness */
        @media (max-width: 768px) {
            .navbar-inner {
                padding: 0.85rem 1rem;
            }

            .nav-links {
                display: none;
            }

            .main-content {
                padding: 1.75rem 1rem 3rem;
            }

            .footer-inner {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.75rem;
            }
        }
    </style>
</head>
<body>
    <div class="ambient-glow"></div>

    <div class="page-wrapper">
        <include file="partials.navbar" />

        <main id="app" class="main-content" switch-live-root>
            <yield name="content" />
        </main>

        <include file="partials.footer" />
    </div>

    @liveScripts
</body>
</html>
