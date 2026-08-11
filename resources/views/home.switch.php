<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Switch Framework — Let's Get Started</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400..700;1,400..700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg-black: #0a0a0a;
            --card-bg: #121214;
            --card-border: #1e1e24;
            --text-primary: #ededed;
            --text-muted: #888890;
            --text-dim: #555560;
            
            /* Unique Switch Theme: Electric Cyan to Neon Violet */
            --switch-cyan: #06b6d4;
            --switch-cyan-glow: #22d3ee;
            --switch-purple: #8b5cf6;
            --switch-purple-dark: #581c87;
            --switch-gradient: linear-gradient(135deg, #06b6d4, #8b5cf6);
        }

        body {
            font-family: 'Instrument Sans', system-ui, -apple-system, sans-serif;
            background-color: var(--bg-black);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            line-height: 1.5;
        }

        /* Main Container Card */
        .container {
            max-width: 960px;
            width: 100%;
            background-color: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 16px;
            display: grid;
            grid-template-columns: 1.15fr 1fr;
            overflow: hidden;
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.6);
            min-height: 480px;
        }

        /* Left Panel */
        .left-panel {
            padding: 3.5rem 3rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .heading-group h2 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.75rem;
            letter-spacing: -0.02em;
        }

        .heading-group p {
            font-size: 0.95rem;
            color: var(--text-muted);
            line-height: 1.6;
            margin-bottom: 2.5rem;
        }

        /* Timeline Checklist */
        .checklist {
            position: relative;
            display: flex;
            flex-direction: column;
            gap: 1.75rem;
            margin-bottom: 2.5rem;
        }

        .checklist::before {
            content: '';
            position: absolute;
            left: 10px;
            top: 24px;
            bottom: 24px;
            width: 1px;
            background-color: var(--card-border);
        }

        .check-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            position: relative;
            z-index: 1;
            text-decoration: none;
            color: var(--text-primary);
            font-size: 0.95rem;
            font-weight: 500;
        }

        .check-circle {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border: 2px solid var(--card-border);
            background-color: var(--card-bg);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            flex-shrink: 0;
        }

        .check-circle::after {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background-color: transparent;
            transition: background-color 0.2s ease;
        }

        .check-item:hover .check-circle {
            border-color: var(--switch-cyan-glow);
            box-shadow: 0 0 10px rgba(34, 211, 238, 0.3);
        }

        .check-item:hover .check-circle::after {
            background-color: var(--switch-cyan-glow);
        }

        .link-text {
            color: var(--switch-cyan-glow);
            border-bottom: 1px dotted var(--switch-cyan);
            transition: color 0.2s ease;
        }

        .check-item:hover .link-text {
            color: #67e8f9;
        }

        .arrow-icon {
            font-size: 0.8rem;
            margin-left: 2px;
            color: var(--switch-cyan);
        }

        /* Action Controls */
        .action-area {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .btn-deploy {
            background-color: #ffffff;
            color: #0a0a0a;
            font-weight: 600;
            font-size: 0.9rem;
            padding: 0.65rem 1.4rem;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .btn-deploy:hover {
            background-color: #e5e5e5;
            transform: translateY(-1px);
        }

        .changelog-footer {
            font-size: 0.85rem;
            color: var(--text-dim);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 2rem;
        }

        .changelog-footer a {
            color: var(--switch-cyan-glow);
            text-decoration: underline;
            text-underline-offset: 3px;
        }

        /* Right Graphic Panel */
        .right-panel {
            background-color: #06080d;
            border-left: 1px solid var(--card-border);
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 2rem;
            user-select: none;
        }

        .graphic-brand {
            font-size: 4rem;
            font-weight: 800;
            letter-spacing: -0.04em;
            color: var(--switch-cyan);
            line-height: 1;
            z-index: 2;
            text-transform: none;
        }

        /* Abstract Layered 3D Typography Graphic */
        .graphic-stack {
            position: absolute;
            right: -20px;
            bottom: -30px;
            width: 320px;
            height: 300px;
            pointer-events: none;
            z-index: 1;
        }

        .version-text {
            font-size: 11rem;
            font-weight: 900;
            line-height: 0.85;
            font-family: 'Instrument Sans', sans-serif;
            position: absolute;
            bottom: 0;
            right: 0;
            letter-spacing: -0.06em;
        }

        .v-layer-1 {
            color: transparent;
            -webkit-text-stroke: 1.5px rgba(6, 182, 212, 0.45);
            transform: translate(-36px, -36px);
        }

        .v-layer-2 {
            color: transparent;
            -webkit-text-stroke: 1.5px rgba(139, 92, 246, 0.35);
            transform: translate(-24px, -24px);
        }

        .v-layer-3 {
            color: transparent;
            -webkit-text-stroke: 1.5px rgba(6, 182, 212, 0.2);
            transform: translate(-12px, -12px);
        }

        .v-layer-main {
            color: #0e1726;
            text-shadow: 0 0 40px rgba(6, 182, 212, 0.15);
        }

        /* Responsive Layout */
        @media (max-width: 768px) {
            .container {
                grid-template-columns: 1fr;
            }
            .right-panel {
                min-height: 240px;
                border-left: none;
                border-top: 1px solid var(--card-border);
            }
            .left-panel {
                padding: 2.5rem 1.75rem;
            }
            .graphic-brand {
                font-size: 3rem;
            }
            .version-text {
                font-size: 8rem;
            }
        }
    </style>
</head>
<body>

    <!-- Main Card Container -->
    <div class="container">
        
        <!-- Left Column: Navigation & Starter Steps -->
        <div class="left-panel">
            <div>
                <div class="heading-group">
                    <h2>Let's get started</h2>
                    <p>With so many options available to you, we suggest you start with the following:</p>
                </div>

                <!-- Step Checklist -->
                <div class="checklist">
                    <a href="https://github.com/celionatti/switch" target="_blank" class="check-item">
                        <div class="check-circle"></div>
                        <span>Read the <span class="link-text">Documentation</span> <span class="arrow-icon">↗</span></span>
                    </a>

                    <a href="/api/status" class="check-item">
                        <div class="check-circle"></div>
                        <span>Explore the <span class="link-text">API Endpoints</span> <span class="arrow-icon">↗</span></span>
                    </a>

                    <a href="https://github.com/celionatti/switch-console" target="_blank" class="check-item">
                        <div class="check-circle"></div>
                        <span>Run CLI &amp; REPL via <span class="link-text">Switch Tinker</span> <span class="arrow-icon">↗</span></span>
                    </a>
                </div>

                <!-- Deploy Button -->
                <div class="action-area">
                    <a href="https://github.com/celionatti/switch" target="_blank" class="btn-deploy">Deploy now</a>
                </div>
            </div>

            <!-- Footer Changelog -->
            <div class="changelog-footer">
                <span>v1.0.0</span>
                <a href="https://github.com/celionatti/switch" target="_blank">View changelog ↗</a>
            </div>
        </div>

        <!-- Right Column: Abstract Typography Graphic -->
        <div class="right-panel">
            <div class="graphic-brand">Switch</div>

            <!-- Multi-layered 3D Offset Version Graphic -->
            <div class="graphic-stack">
                <div class="version-text v-layer-1">1.0</div>
                <div class="version-text v-layer-2">1.0</div>
                <div class="version-text v-layer-3">1.0</div>
                <div class="version-text v-layer-main">1.0</div>
            </div>
        </div>

    </div>

</body>
</html>
