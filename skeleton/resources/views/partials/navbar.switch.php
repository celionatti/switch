<php> $version = $version ?? '1.0.0'; </php>
<header class="switch-navbar">
    <div class="navbar-inner">
        <a href="/" class="brand-logo" switch-to switch-prefetch>
            <div class="brand-badge">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M13 2L3 14H12L11 22L21 10H12L13 2Z" fill="url(#switch-logo-grad)" stroke="#22d3ee" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <defs>
                        <linearGradient id="switch-logo-grad" x1="3" y1="2" x2="21" y2="22" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#06b6d4"/>
                            <stop offset="1" stop-color="#3b82f6"/>
                        </linearGradient>
                    </defs>
                </svg>
            </div>
            <div class="brand-text">
                <span class="brand-title">SWITCH</span>
                <span class="brand-subtitle">FRAMEWORK</span>
            </div>
        </a>

        <nav class="nav-links">
            <a href="/" class="nav-item" switch-to switch-prefetch>Home</a>
            <a href="/posts" class="nav-item" switch-to switch-prefetch>Blog Posts</a>
            <a href="https://github.com/celionatti/switch" target="_blank" rel="noopener noreferrer" class="nav-item">Docs</a>
            <a href="https://github.com/celionatti/switch-live" target="_blank" rel="noopener noreferrer" class="nav-item">Live SPA</a>
            <a href="https://github.com/celionatti/switch-view" target="_blank" rel="noopener noreferrer" class="nav-item">View Engine</a>
        </nav>

        <div class="nav-actions">
            <span class="version-pill">
                <span class="pulse-dot"></span>
                v{{ $version }}
            </span>
            <a href="https://github.com/celionatti/switch" target="_blank" rel="noopener noreferrer" class="btn-github">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.53.12-3.18 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.65.24 2.88.12 3.18.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0024 12c0-6.63-5.37-12-12-12z"/>
                </svg>
                <span>GitHub</span>
            </a>
        </div>
    </div>
</header>
