<layout name="layouts.app" />

<section name="content">
    <php>
        $version = $version ?? '1.0.0';
        $quickstart = $quickstart ?? [];
        $features = $features ?? [];
    </php>
    <style>
        /* Hero Section */
        .hero {
            text-align: center;
            padding: 3rem 1rem 4rem;
            max-width: 860px;
            margin: 0 auto;
        }

        .hero-badge-wrap {
            display: inline-flex;
            margin-bottom: 1.5rem;
        }

        .hero-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.4rem 1rem;
            border-radius: var(--radius-full);
            background: rgba(6, 182, 212, 0.08);
            border: 1px solid rgba(6, 182, 212, 0.25);
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--cyan-400);
            letter-spacing: 0.02em;
        }

        .hero-title {
            font-size: clamp(2.2rem, 5vw, 3.8rem);
            font-weight: 800;
            line-height: 1.1;
            letter-spacing: -0.035em;
            margin-bottom: 1.25rem;
            color: #ffffff;
        }

        .gradient-text {
            background: linear-gradient(135deg, #22d3ee 0%, #38bdf8 50%, #818cf8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-description {
            font-size: clamp(1rem, 2vw, 1.2rem);
            color: var(--text-muted);
            line-height: 1.6;
            margin-bottom: 2.25rem;
            max-width: 680px;
            margin-left: auto;
            margin-right: auto;
        }

        .hero-cta {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.8rem 1.6rem;
            border-radius: var(--radius-md);
            background: linear-gradient(135deg, #06b6d4, #2563eb);
            color: #ffffff;
            font-weight: 600;
            font-size: 0.95rem;
            text-decoration: none;
            box-shadow: 0 4px 20px rgba(6, 182, 212, 0.35);
            transition: var(--transition-smooth);
            border: none;
            cursor: pointer;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 28px rgba(6, 182, 212, 0.5);
            background: linear-gradient(135deg, #0891b2, #1d4ed8);
        }

        .btn-secondary {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.8rem 1.5rem;
            border-radius: var(--radius-md);
            background: var(--bg-surface);
            border: 1px solid var(--border-subtle);
            color: var(--text-main);
            font-weight: 600;
            font-size: 0.95rem;
            text-decoration: none;
            transition: var(--transition-smooth);
        }

        .btn-secondary:hover {
            border-color: var(--border-hover);
            background: var(--bg-elevated);
        }

        /* Showcase Grid */
        .showcase-grid {
            display: grid;
            grid-template-columns: 1.15fr 1fr;
            gap: 1.75rem;
            margin-bottom: 4rem;
        }

        /* Terminal Quickstart Card */
        .terminal-card {
            background: var(--bg-surface);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
            display: flex;
            flex-direction: column;
        }

        .terminal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.85rem 1.25rem;
            background: rgba(0, 0, 0, 0.3);
            border-bottom: 1px solid var(--border-subtle);
        }

        .terminal-dots {
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }

        .dot-red { background: #ef4444; }
        .dot-yellow { background: #f59e0b; }
        .dot-green { background: #10b981; }

        .terminal-title {
            font-family: var(--font-mono);
            font-size: 0.78rem;
            color: var(--text-dim);
            font-weight: 500;
        }

        .terminal-body {
            padding: 1.5rem;
            font-family: var(--font-mono);
            font-size: 0.88rem;
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
            flex: 1;
        }

        .terminal-step {
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
        }

        .step-label {
            font-size: 0.72rem;
            color: var(--text-dim);
            font-family: var(--font-sans);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .step-cmd {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--bg-base);
            padding: 0.65rem 0.9rem;
            border-radius: var(--radius-sm);
            border: 1px solid var(--border-subtle);
            color: var(--cyan-400);
            word-break: break-all;
        }

        .prompt-symbol {
            color: var(--text-dim);
            user-select: none;
            margin-right: 0.5rem;
        }

        /* Interactive Counter Card */
        .counter-card {
            background: var(--bg-surface);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-lg);
            padding: 2.25rem 2rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
            position: relative;
            overflow: hidden;
        }

        .counter-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, #06b6d4, #6366f1);
        }

        .counter-header {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            margin-bottom: 1.25rem;
        }

        .counter-tag {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--cyan-400);
        }

        .counter-badge {
            font-size: 0.7rem;
            font-family: var(--font-mono);
            padding: 0.15rem 0.5rem;
            border-radius: var(--radius-full);
            background: rgba(16, 185, 129, 0.12);
            color: var(--emerald-400);
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .counter-number {
            font-size: clamp(3.5rem, 8vw, 5rem);
            font-weight: 800;
            font-family: var(--font-mono);
            line-height: 1;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, #ffffff 40%, var(--cyan-400) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .counter-label {
            font-size: 0.85rem;
            color: var(--text-dim);
            margin-bottom: 1.75rem;
        }

        .counter-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .btn-counter {
            width: 52px;
            height: 52px;
            border-radius: var(--radius-md);
            font-size: 1.6rem;
            font-weight: 700;
            font-family: var(--font-mono);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border: 1px solid var(--border-subtle);
            background: var(--bg-elevated);
            color: var(--text-main);
            transition: var(--transition-smooth);
            user-select: none;
        }

        .btn-counter:hover {
            transform: scale(1.08);
        }

        .btn-decrement:hover {
            border-color: #ef4444;
            color: #ef4444;
            background: rgba(239, 68, 68, 0.1);
        }

        .btn-increment:hover {
            border-color: var(--cyan-400);
            color: var(--cyan-400);
            background: rgba(6, 182, 212, 0.1);
        }

        /* Features Section */
        .section-header {
            margin-bottom: 2.25rem;
            text-align: center;
        }

        .section-tag {
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--cyan-400);
            margin-bottom: 0.4rem;
        }

        .section-title {
            font-size: 1.85rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            color: #ffffff;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 1.5rem;
            margin-bottom: 4rem;
        }

        .feature-card {
            background: var(--bg-surface);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-lg);
            padding: 1.85rem 1.65rem;
            display: flex;
            flex-direction: column;
            gap: 0.9rem;
            transition: var(--transition-smooth);
            position: relative;
        }

        .feature-card:hover {
            border-color: var(--border-hover);
            transform: translateY(-3px);
            box-shadow: 0 16px 32px rgba(0, 0, 0, 0.35);
        }

        .feature-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .feature-icon {
            font-size: 1.5rem;
            width: 44px;
            height: 44px;
            border-radius: var(--radius-md);
            background: var(--bg-elevated);
            border: 1px solid var(--border-subtle);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .feature-tag {
            font-size: 0.7rem;
            font-family: var(--font-mono);
            font-weight: 600;
            color: var(--cyan-400);
            padding: 0.2rem 0.55rem;
            border-radius: var(--radius-sm);
            background: rgba(6, 182, 212, 0.08);
            border: 1px solid rgba(6, 182, 212, 0.2);
        }

        .feature-title {
            font-size: 1.15rem;
            font-weight: 600;
            color: var(--text-main);
            letter-spacing: -0.01em;
        }

        .feature-desc {
            font-size: 0.9rem;
            color: var(--text-muted);
            line-height: 1.55;
        }

        /* Responsive Breakpoints */
        @media (max-width: 900px) {
            .showcase-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {
            .hero {
                padding: 2rem 0.5rem 2.5rem;
            }

            .hero-cta {
                flex-direction: column;
                width: 100%;
            }

            .btn-primary, .btn-secondary {
                width: 100%;
                justify-content: center;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }

            .counter-card {
                padding: 1.75rem 1.25rem;
            }
        }
    </style>

    <!-- Hero Section -->
    <div class="hero">
        <if cond="$version">
            <div class="hero-badge-wrap">
                <div class="hero-pill">
                    <span class="pulse-dot"></span>
                    <span>Switch Framework v{{ $version }} Ready</span>
                </div>
            </div>
        </if>

        <h1 class="hero-title">
            The High-Velocity <br />
            <span class="gradient-text">Full-Stack PHP Framework</span>
        </h1>

        <p class="hero-description">
            Zero-overhead modular architecture, compiled blade-speed view engine, and native zero-JS SPA reactivity. Built for modern web applications that demand extreme speed.
        </p>

        <div class="hero-cta">
            <a href="https://github.com/celionatti/switch" target="_blank" rel="noopener noreferrer" class="btn-primary">
                <span>Explore Documentation</span>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 12h14M12 5l7 7-7 7"/>
                </svg>
            </a>
            <a href="https://github.com/celionatti/switch-live" target="_blank" rel="noopener noreferrer" class="btn-secondary">
                <span>View Switch Live</span>
            </a>
        </div>
    </div>

    <!-- Interactive Showcase Grid -->
    <div class="showcase-grid">
        <!-- Quickstart Commands -->
        <div class="terminal-card">
            <div class="terminal-header">
                <div class="terminal-dots">
                    <span class="dot dot-red"></span>
                    <span class="dot dot-yellow"></span>
                    <span class="dot dot-green"></span>
                </div>
                <span class="terminal-title">switch-cli ~ quickstart</span>
            </div>
            <div class="terminal-body">
                <foreach items="$quickstart" as="$step">
                    <div class="terminal-step">
                        <span class="step-label">{{ $step.step }}. {{ $step.label }}</span>
                        <div class="step-cmd">
                            <span><span class="prompt-symbol">$</span>{{ $step.command }}</span>
                        </div>
                    </div>
                </foreach>
            </div>
        </div>

        <!-- Live Reactive Counter Demo -->
        <include file="partials.counter-demo" />
    </div>

    <!-- Core Architecture Section -->
    <div class="section-header">
        <div class="section-tag">Ecosystem</div>
        <h2 class="section-title">Engineered For Pure Performance</h2>
    </div>

    <div class="features-grid">
        <foreach items="$features" as="$feature">
            <div class="feature-card">
                <div class="feature-top">
                    <div class="feature-icon">{{ $feature.icon }}</div>
                    <span class="feature-tag">{{ $feature.tag }}</span>
                </div>
                <h3 class="feature-title">{{ $feature.title }}</h3>
                <p class="feature-desc">{{ $feature.desc }}</p>
            </div>
        </foreach>
    </div>
</section>
