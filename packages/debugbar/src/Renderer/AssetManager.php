<?php

declare(strict_types=1);

namespace Switch\DebugBar\Renderer;

/**
 * Manages ultra-compact, zero-dependency inline CSS and JavaScript for the Switch DebugBar.
 */
class AssetManager
{
    /**
     * Get complete CSS stylesheet for the DebugBar.
     */
    public static function getStyles(): string
    {
        return <<<'CSS'
/* Switch DebugBar Ultra-Modern Glassmorphic Styles */
:root {
    --sdb-bg-main: #0b0f19;
    --sdb-bg-surface: rgba(15, 23, 42, 0.95);
    --sdb-bg-card: #111827;
    --sdb-bg-card-alt: #161f30;
    --sdb-bg-hover: #1e293b;
    --sdb-border: rgba(255, 255, 255, 0.08);
    --sdb-border-subtle: rgba(255, 255, 255, 0.04);
    --sdb-border-active: rgba(0, 240, 255, 0.45);
    --sdb-text-main: #f3f4f6;
    --sdb-text-muted: #9ca3af;
    --sdb-text-dim: #64748b;
    --sdb-cyan: #00f0ff;
    --sdb-cyan-glow: rgba(0, 240, 255, 0.2);
    --sdb-emerald: #10b981;
    --sdb-amber: #f59e0b;
    --sdb-rose: #f43f5e;
    --sdb-purple: #a855f7;
    --sdb-blue: #3b82f6;
    --sdb-radius-sm: 4px;
    --sdb-radius: 8px;
    --sdb-radius-lg: 12px;
    --sdb-font: 'Instrument Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    --sdb-mono: 'JetBrains Mono', 'Fira Code', Consolas, monospace;
    --sdb-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.6), 0 0 1px 1px rgba(255, 255, 255, 0.08);
}

#switch-debugbar {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 99999999;
    font-family: var(--sdb-font);
    font-size: 12px;
    line-height: 1.4;
    color: var(--sdb-text-main);
    box-sizing: border-box;
    pointer-events: none;
    -webkit-font-smoothing: antialiased;
}

#switch-debugbar * {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

/* Custom Scrollbars */
#switch-debugbar *::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}
#switch-debugbar *::-webkit-scrollbar-track {
    background: rgba(0, 0, 0, 0.2);
}
#switch-debugbar *::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.15);
    border-radius: 4px;
}
#switch-debugbar *::-webkit-scrollbar-thumb:hover {
    background: var(--sdb-cyan);
}

/* Floating / Dock Bar */
.sdb-bar {
    pointer-events: auto;
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: rgba(11, 15, 25, 0.96);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border-top: 1px solid var(--sdb-border);
    box-shadow: var(--sdb-shadow);
    padding: 0 8px;
    height: 40px;
    user-select: none;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    position: relative;
}

.sdb-bar-left {
    display: flex;
    align-items: center;
    gap: 4px;
    height: 100%;
    flex: 1;
    min-width: 0;
    overflow: hidden;
    position: relative;
}

/* Scrollable tabs container with smooth mousewheel navigation */
.sdb-tabs-wrapper {
    display: flex;
    align-items: center;
    gap: 3px;
    height: 100%;
    overflow-x: auto;
    scroll-behavior: smooth;
    scrollbar-width: none;
    padding: 0 4px;
    mask-image: linear-gradient(to right, transparent, black 12px, black calc(100% - 12px), transparent);
    -webkit-mask-image: linear-gradient(to right, transparent, black 12px, black calc(100% - 12px), transparent);
}
.sdb-tabs-wrapper::-webkit-scrollbar { display: none; }

.sdb-nav-arrow {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 22px;
    height: 28px;
    border-radius: var(--sdb-radius-sm);
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid var(--sdb-border);
    color: var(--sdb-text-muted);
    cursor: pointer;
    flex-shrink: 0;
    transition: all 0.15s ease;
}
.sdb-nav-arrow:hover {
    background: var(--sdb-bg-hover);
    color: #fff;
    border-color: var(--sdb-cyan);
}

.sdb-bar-right {
    display: flex;
    align-items: center;
    gap: 6px;
    height: 100%;
    flex-shrink: 0;
    padding-left: 8px;
    border-left: 1px solid var(--sdb-border);
    background: rgba(11, 15, 25, 0.98);
    box-shadow: -8px 0 16px rgba(11, 15, 25, 0.95);
    z-index: 5;
}

/* Brand Logo */
.sdb-brand {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 4px 8px;
    font-weight: 700;
    font-size: 12px;
    color: #fff;
    cursor: pointer;
    border-radius: var(--sdb-radius);
    transition: background 0.15s ease;
    flex-shrink: 0;
}
.sdb-brand:hover { background: var(--sdb-bg-hover); }
.sdb-brand-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 22px;
    height: 22px;
    background: linear-gradient(135deg, #00f0ff 0%, #7000ff 100%);
    border-radius: 6px;
    box-shadow: 0 0 10px rgba(0, 240, 255, 0.4);
    color: #fff;
    font-size: 11px;
}
.sdb-brand-text {
    letter-spacing: -0.02em;
    background: linear-gradient(90deg, #fff 0%, #9ca3af 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

/* Tabs */
.sdb-tab {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 8px;
    height: 28px;
    border-radius: var(--sdb-radius);
    background: transparent;
    border: 1px solid transparent;
    color: var(--sdb-text-muted);
    font-family: inherit;
    font-size: 11.5px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.15s ease;
    white-space: nowrap;
    flex-shrink: 0;
}
.sdb-tab:hover {
    background: var(--sdb-bg-hover);
    color: var(--sdb-text-main);
}
.sdb-tab.sdb-active {
    background: rgba(0, 240, 255, 0.12);
    border-color: var(--sdb-border-active);
    color: var(--sdb-cyan);
    box-shadow: 0 0 12px var(--sdb-cyan-glow);
}
.sdb-tab svg { stroke: currentColor; flex-shrink: 0; }

/* Status Badges */
.sdb-badge {
    display: inline-flex;
    align-items: center;
    padding: 1px 6px;
    font-size: 10px;
    font-weight: 600;
    font-family: var(--sdb-mono);
    border-radius: 9999px;
    background: rgba(255, 255, 255, 0.08);
    color: var(--sdb-text-muted);
    border: 1px solid transparent;
}
.sdb-badge-success { background: rgba(16, 185, 129, 0.15); color: var(--sdb-emerald); border-color: rgba(16, 185, 129, 0.3); }
.sdb-badge-warning { background: rgba(245, 158, 11, 0.15); color: var(--sdb-amber); border-color: rgba(245, 158, 11, 0.3); }
.sdb-badge-danger  { background: rgba(244, 63, 94, 0.18); color: var(--sdb-rose); border-color: rgba(244, 63, 94, 0.35); }
.sdb-badge-info    { background: rgba(0, 240, 255, 0.15); color: var(--sdb-cyan); border-color: rgba(0, 240, 255, 0.3); }
.sdb-badge-neon    { background: rgba(168, 85, 247, 0.15); color: var(--sdb-purple); border-color: rgba(168, 85, 247, 0.3); }

/* Buttons & Controls */
.sdb-btn-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    border-radius: var(--sdb-radius);
    background: transparent;
    border: 1px solid transparent;
    color: var(--sdb-text-muted);
    cursor: pointer;
    transition: all 0.15s ease;
}
.sdb-btn-icon:hover {
    background: var(--sdb-bg-hover);
    color: #fff;
    border-color: var(--sdb-border);
}

.sdb-history-select {
    background: var(--sdb-bg-card);
    border: 1px solid var(--sdb-border);
    color: var(--sdb-text-main);
    font-family: var(--sdb-mono);
    font-size: 11px;
    height: 28px;
    max-width: 170px;
    padding: 0 6px;
    border-radius: var(--sdb-radius);
    outline: none;
    cursor: pointer;
}

/* Expandable Drawer Panel */
.sdb-drawer {
    pointer-events: auto;
    position: relative;
    background: rgba(11, 15, 25, 0.98);
    backdrop-filter: blur(28px);
    -webkit-backdrop-filter: blur(28px);
    border-top: 1px solid var(--sdb-border);
    height: 440px;
    max-height: 90vh;
    min-height: 200px;
    display: flex;
    flex-direction: column;
    box-shadow: var(--sdb-shadow);
    transition: height 0.18s cubic-bezier(0.16, 1, 0.3, 1);
}
.sdb-drawer.sdb-hidden { display: none; }
.sdb-drawer.sdb-maximized { height: 88vh !important; }

/* Resizer Handle */
.sdb-resizer {
    position: absolute;
    top: -5px;
    left: 0;
    right: 0;
    height: 10px;
    cursor: ns-resize;
    z-index: 20;
    transition: background 0.15s;
}
.sdb-resizer:hover, .sdb-resizer.sdb-resizing {
    background: linear-gradient(180deg, transparent, rgba(0, 240, 255, 0.4), transparent);
}

/* Panel Header */
.sdb-panel-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 16px;
    background: rgba(17, 24, 39, 0.7);
    border-bottom: 1px solid var(--sdb-border);
    flex-shrink: 0;
    gap: 12px;
}
.sdb-panel-title {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    font-size: 13px;
    color: #fff;
}
.sdb-search-box {
    display: flex;
    align-items: center;
    gap: 6px;
    background: var(--sdb-bg-card);
    border: 1px solid var(--sdb-border);
    border-radius: var(--sdb-radius);
    padding: 4px 10px;
    width: 240px;
    transition: border-color 0.15s ease;
}
.sdb-search-box:focus-within {
    border-color: var(--sdb-cyan);
    box-shadow: 0 0 8px var(--sdb-cyan-glow);
}
.sdb-search-input {
    background: transparent;
    border: none;
    color: #fff;
    font-family: inherit;
    font-size: 11.5px;
    width: 100%;
    outline: none;
}
.sdb-search-input::placeholder { color: var(--sdb-text-dim); }

/* Panel Body */
.sdb-panel-body {
    flex: 1;
    overflow-y: auto;
    overflow-x: hidden;
    padding: 16px;
    background: radial-gradient(circle at 50% 0%, rgba(0, 240, 255, 0.03) 0%, transparent 60%);
}
.sdb-panel-content { display: none; }
.sdb-panel-content.sdb-active { display: block; animation: sdb-fade-in 0.15s ease-out; }

@keyframes sdb-fade-in {
    from { opacity: 0; transform: translateY(4px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Master-Detail / Collapsible Cards */
.sdb-card {
    background: var(--sdb-bg-card);
    border: 1px solid var(--sdb-border);
    border-radius: var(--sdb-radius);
    margin-bottom: 10px;
    transition: all 0.15s ease;
    overflow: hidden;
}
.sdb-card:hover {
    border-color: rgba(255, 255, 255, 0.18);
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.3);
}
.sdb-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 14px;
    cursor: pointer;
    background: var(--sdb-bg-card-alt);
    border-bottom: 1px solid var(--sdb-border-subtle);
    gap: 12px;
}
.sdb-card-header:hover {
    background: var(--sdb-bg-hover);
}
.sdb-card-title {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    font-size: 12px;
    color: #fff;
    min-width: 0;
    flex: 1;
}
.sdb-card-title code {
    font-family: var(--sdb-mono);
    color: var(--sdb-cyan);
    font-size: 11px;
    word-break: break-all;
}
.sdb-card-meta {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-shrink: 0;
}
.sdb-card-body {
    padding: 12px 14px;
    background: var(--sdb-bg-card);
}

/* Copy Action Button */
.sdb-btn-copy {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 2px 6px;
    font-size: 10px;
    border-radius: var(--sdb-radius-sm);
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid var(--sdb-border);
    color: var(--sdb-text-muted);
    cursor: pointer;
    transition: all 0.15s ease;
}
.sdb-btn-copy:hover {
    background: var(--sdb-bg-hover);
    color: #fff;
    border-color: var(--sdb-cyan);
}

/* Tables */
.sdb-table-wrap {
    width: 100%;
    overflow-x: auto;
    border: 1px solid var(--sdb-border);
    border-radius: var(--sdb-radius);
    background: var(--sdb-bg-card);
}
.sdb-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 11.5px;
}
.sdb-table th {
    text-align: left;
    padding: 8px 12px;
    background: rgba(255, 255, 255, 0.03);
    color: var(--sdb-text-muted);
    font-weight: 600;
    border-bottom: 1px solid var(--sdb-border);
    white-space: nowrap;
}
.sdb-table td {
    padding: 8px 12px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.04);
    vertical-align: top;
}
.sdb-table tr:last-child td { border-bottom: none; }
.sdb-table tr:hover td { background: rgba(255, 255, 255, 0.02); }

/* SQL Query Block */
.sdb-query-card {
    background: var(--sdb-bg-card);
    border: 1px solid var(--sdb-border);
    border-radius: var(--sdb-radius);
    padding: 12px;
    margin-bottom: 8px;
    transition: border-color 0.15s ease;
}
.sdb-query-card:hover { border-color: rgba(255, 255, 255, 0.2); }
.sdb-query-card.sdb-query-slow { border-left: 4px solid var(--sdb-rose); }
.sdb-query-card.sdb-query-dup { border-left: 4px solid var(--sdb-amber); }
.sdb-query-meta {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 8px;
    font-size: 11px;
    color: var(--sdb-text-muted);
    gap: 8px;
    flex-wrap: wrap;
}
.sdb-query-sql {
    font-family: var(--sdb-mono);
    font-size: 11.5px;
    line-height: 1.5;
    color: #e5e7eb;
    background: rgba(0, 0, 0, 0.4);
    padding: 10px 12px;
    border-radius: var(--sdb-radius-sm);
    overflow-x: auto;
    white-space: pre-wrap;
    word-break: break-all;
    border: 1px solid rgba(255, 255, 255, 0.05);
}

/* Timeline Bars */
.sdb-timeline-item {
    margin-bottom: 12px;
}
.sdb-timeline-info {
    display: flex;
    justify-content: space-between;
    font-size: 11.5px;
    margin-bottom: 4px;
}
.sdb-timeline-track {
    width: 100%;
    height: 10px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 9999px;
    overflow: hidden;
    position: relative;
}
.sdb-timeline-bar {
    height: 100%;
    background: linear-gradient(90deg, var(--sdb-cyan), var(--sdb-purple));
    border-radius: 9999px;
}

/* Card Grid for Metrics */
.sdb-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 12px;
    margin-bottom: 16px;
}
.sdb-metric-card {
    background: var(--sdb-bg-card);
    border: 1px solid var(--sdb-border);
    border-radius: var(--sdb-radius);
    padding: 14px;
    position: relative;
    overflow: hidden;
}
.sdb-metric-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, var(--sdb-cyan), transparent);
}
.sdb-metric-label {
    font-size: 11px;
    color: var(--sdb-text-muted);
    margin-bottom: 6px;
    font-weight: 500;
}
.sdb-metric-value {
    font-size: 20px;
    font-weight: 700;
    font-family: var(--sdb-mono);
    color: #fff;
    letter-spacing: -0.02em;
}

/* Mini Collapsed Floating Pill */
.sdb-pill {
    pointer-events: auto;
    position: fixed;
    bottom: 14px;
    right: 14px;
    background: rgba(11, 15, 25, 0.94);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid var(--sdb-border);
    box-shadow: var(--sdb-shadow);
    padding: 6px 14px;
    border-radius: 9999px;
    display: none;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    user-select: none;
    z-index: 99999999;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}
.sdb-pill:hover {
    transform: translateY(-2px);
    border-color: var(--sdb-cyan);
    box-shadow: 0 0 16px var(--sdb-cyan-glow);
}
.sdb-pill.sdb-visible { display: inline-flex; }

/* Interactive Dumper Tree Nodes */
.sdb-dumper {
    font-family: var(--sdb-mono);
    font-size: 11.5px;
    line-height: 1.5;
}
.sdb-tree-node { margin: 2px 0; }
.sdb-tree-summary {
    cursor: pointer;
    outline: none;
    list-style: none;
    user-select: none;
}
.sdb-tree-summary::-webkit-details-marker { display: none; }
.sdb-tree-summary::before {
    content: '▶';
    display: inline-block;
    font-size: 9px;
    margin-right: 4px;
    color: var(--sdb-text-dim);
    transition: transform 0.15s ease;
}
.sdb-tree-node[open] > .sdb-tree-summary::before { transform: rotate(90deg); }
.sdb-tree-children {
    padding-left: 16px;
    border-left: 1px dashed rgba(255, 255, 255, 0.1);
    margin: 2px 0 2px 4px;
}
.sdb-tree-item { margin: 2px 0; }
.sdb-val-str { color: #34d399; }
.sdb-val-num { color: #38bdf8; }
.sdb-val-bool { color: #c084fc; font-weight: 600; }
.sdb-val-null { color: #f472b6; font-style: italic; }
.sdb-val-type { color: var(--sdb-text-dim); font-size: 10px; }
.sdb-key { color: #fbbf24; }
.sdb-assign { color: var(--sdb-text-dim); }
.sdb-val-class { color: #818cf8; font-weight: 600; }
.sdb-modifier { color: var(--sdb-text-dim); margin-right: 2px; }

/* Toast Notification */
.sdb-toast {
    position: fixed;
    bottom: 50px;
    right: 20px;
    background: rgba(16, 185, 129, 0.95);
    color: #fff;
    padding: 6px 14px;
    border-radius: var(--sdb-radius);
    font-size: 11px;
    font-weight: 600;
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.4);
    z-index: 999999999;
    opacity: 0;
    transform: translateY(10px);
    transition: all 0.2s ease;
    pointer-events: none;
}
.sdb-toast.sdb-toast-show {
    opacity: 1;
    transform: translateY(0);
}

/* Responsive Adjustments */
@media (max-width: 900px) {
    .sdb-history-select { max-width: 110px; }
    .sdb-search-box { width: 160px; }
}

@media (max-width: 640px) {
    .sdb-bar { padding: 0 4px; height: 38px; }
    .sdb-tab span { display: none; }
    .sdb-brand-text { display: none; }
    .sdb-drawer { height: 80vh; }
    .sdb-grid { grid-template-columns: 1fr; }
    .sdb-search-box { width: 120px; }
}
CSS;
    }

    /**
     * Get complete client-side JavaScript engine for the DebugBar.
     */
    public static function getScript(string $requestId, string $dataUrl): string
    {
        return <<<JS
(function() {
    if (window.__switchDebugBarInit) return;
    window.__switchDebugBarInit = true;

    var activeTab = null;
    var isExpanded = false;
    var isMaximized = false;
    var currentReqId = '{$requestId}';

    function init() {
        var bar = document.getElementById('sdb-main-bar');
        var pill = document.getElementById('sdb-floating-pill');
        var drawer = document.getElementById('sdb-main-drawer');
        var tabsWrapper = document.getElementById('sdb-tabs-wrapper');
        if (!bar || !drawer) return;

        // Load persisted state
        var savedState = localStorage.getItem('sdb_collapsed');
        if (savedState === '1') {
            collapseToPill();
        }

        // Tab click handler
        var tabs = document.querySelectorAll('.sdb-tab');
        tabs.forEach(function(tab) {
            tab.addEventListener('click', function() {
                var tabName = this.getAttribute('data-tab');
                if (activeTab === tabName && isExpanded) {
                    closeDrawer();
                } else {
                    openTab(tabName);
                }
            });
        });

        // Mousewheel horizontal scrolling on tabs wrapper
        if (tabsWrapper) {
            tabsWrapper.addEventListener('wheel', function(e) {
                if (e.deltaY !== 0) {
                    e.preventDefault();
                    tabsWrapper.scrollLeft += e.deltaY;
                }
            }, { passive: false });
        }

        // Nav arrow buttons for tab scrolling
        var arrowLeft = document.getElementById('sdb-nav-prev');
        var arrowRight = document.getElementById('sdb-nav-next');
        if (arrowLeft && tabsWrapper) {
            arrowLeft.addEventListener('click', function() {
                tabsWrapper.scrollLeft -= 150;
            });
        }
        if (arrowRight && tabsWrapper) {
            arrowRight.addEventListener('click', function() {
                tabsWrapper.scrollLeft += 150;
            });
        }

        // Pill restore
        if (pill) {
            pill.addEventListener('click', function() {
                restoreFromPill();
            });
        }

        // Close drawer button
        var closeBtn = document.getElementById('sdb-btn-close-drawer');
        if (closeBtn) {
            closeBtn.addEventListener('click', closeDrawer);
        }

        // Maximize drawer button
        var maxBtn = document.getElementById('sdb-btn-maximize-drawer');
        if (maxBtn) {
            maxBtn.addEventListener('click', toggleMaximizeDrawer);
        }

        // Collapse to pill button
        var minimizeBtn = document.getElementById('sdb-btn-minimize');
        if (minimizeBtn) {
            minimizeBtn.addEventListener('click', collapseToPill);
        }

        // Copy button handler (delegated)
        document.addEventListener('click', function(e) {
            var btn = e.target.closest('.sdb-btn-copy');
            if (!btn) return;
            var text = btn.getAttribute('data-copy') || '';
            if (!text && btn.previousElementSibling) {
                text = btn.previousElementSibling.textContent;
            }
            if (text) {
                copyToClipboard(text);
            }
        });

        // Card accordion header toggle
        document.addEventListener('click', function(e) {
            var header = e.target.closest('.sdb-card-header');
            if (!header) return;
            var card = header.closest('.sdb-card');
            if (card) {
                var body = card.querySelector('.sdb-card-body');
                if (body) {
                    var isHidden = body.style.display === 'none';
                    body.style.display = isHidden ? 'block' : 'none';
                    var arrow = header.querySelector('.sdb-card-arrow');
                    if (arrow) {
                        arrow.style.transform = isHidden ? 'rotate(90deg)' : 'rotate(0deg)';
                    }
                }
            }
        });

        // Keyboard shortcut: Alt+D or Ctrl+Shift+D
        window.addEventListener('keydown', function(e) {
            if ((e.altKey && e.key.toLowerCase() === 'd') || (e.ctrlKey && e.shiftKey && e.key.toLowerCase() === 'd')) {
                e.preventDefault();
                toggleDebugBar();
            } else if (e.key === 'Escape' && isExpanded) {
                closeDrawer();
            }
        });

        // Search filtering in drawer
        var searchInput = document.getElementById('sdb-search-filter');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                var query = this.value.toLowerCase().trim();
                var activePanel = document.querySelector('.sdb-panel-content.sdb-active');
                if (!activePanel) return;

                var items = activePanel.querySelectorAll('.sdb-card, .sdb-query-card, .sdb-table tr, .sdb-tree-item, .sdb-timeline-item');
                items.forEach(function(item) {
                    if (query === '') {
                        item.style.display = '';
                        return;
                    }
                    var text = item.textContent.toLowerCase();
                    item.style.display = text.indexOf(query) !== -1 ? '' : 'none';
                });
            });
        }

        // Resizer handling
        var resizer = document.getElementById('sdb-resizer-handle');
        if (resizer) {
            var isResizing = false;
            var startY, startHeight;
            resizer.addEventListener('mousedown', function(e) {
                isResizing = true;
                resizer.classList.add('sdb-resizing');
                startY = e.clientY;
                startHeight = drawer.offsetHeight;
                document.body.style.userSelect = 'none';
            });
            window.addEventListener('mousemove', function(e) {
                if (!isResizing) return;
                var newH = startHeight + (startY - e.clientY);
                if (newH > 160 && newH < window.innerHeight * 0.92) {
                    drawer.style.height = newH + 'px';
                }
            });
            window.addEventListener('mouseup', function() {
                if (isResizing) {
                    isResizing = false;
                    resizer.classList.remove('sdb-resizing');
                    document.body.style.userSelect = '';
                }
            });
        }

        // Intercept AJAX / Fetch requests
        interceptAjax();
    }

    function openTab(tabName) {
        var drawer = document.getElementById('sdb-main-drawer');
        var tabs = document.querySelectorAll('.sdb-tab');
        var contents = document.querySelectorAll('.sdb-panel-content');
        var titleEl = document.getElementById('sdb-panel-current-title');

        tabs.forEach(function(t) {
            t.classList.toggle('sdb-active', t.getAttribute('data-tab') === tabName);
        });

        contents.forEach(function(c) {
            c.classList.toggle('sdb-active', c.getAttribute('data-panel') === tabName);
        });

        var selectedTab = document.querySelector('.sdb-tab[data-tab="' + tabName + '"]');
        if (selectedTab && titleEl) {
            titleEl.textContent = selectedTab.textContent.trim();
            // Scroll selected tab into view smoothly
            selectedTab.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
        }

        drawer.classList.remove('sdb-hidden');
        activeTab = tabName;
        isExpanded = true;

        // Clear search box on tab switch
        var searchInput = document.getElementById('sdb-search-filter');
        if (searchInput) {
            searchInput.value = '';
            var items = drawer.querySelectorAll('.sdb-card, .sdb-query-card, .sdb-table tr, .sdb-tree-item, .sdb-timeline-item');
            items.forEach(function(el) { el.style.display = ''; });
        }
    }

    function closeDrawer() {
        var drawer = document.getElementById('sdb-main-drawer');
        var tabs = document.querySelectorAll('.sdb-tab');
        if (drawer) drawer.classList.add('sdb-hidden');
        tabs.forEach(function(t) { t.classList.remove('sdb-active'); });
        isExpanded = false;
        activeTab = null;
    }

    function toggleMaximizeDrawer() {
        var drawer = document.getElementById('sdb-main-drawer');
        if (!drawer) return;
        isMaximized = !isMaximized;
        drawer.classList.toggle('sdb-maximized', isMaximized);
    }

    function collapseToPill() {
        closeDrawer();
        var bar = document.getElementById('sdb-main-bar');
        var pill = document.getElementById('sdb-floating-pill');
        if (bar) bar.style.display = 'none';
        if (pill) pill.classList.add('sdb-visible');
        localStorage.setItem('sdb_collapsed', '1');
    }

    function restoreFromPill() {
        var bar = document.getElementById('sdb-main-bar');
        var pill = document.getElementById('sdb-floating-pill');
        if (pill) pill.classList.remove('sdb-visible');
        if (bar) bar.style.display = 'flex';
        localStorage.setItem('sdb_collapsed', '0');
    }

    function toggleDebugBar() {
        var bar = document.getElementById('sdb-main-bar');
        if (bar && bar.style.display === 'none') {
            restoreFromPill();
        } else {
            collapseToPill();
        }
    }

    function copyToClipboard(text) {
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(showToast).catch(fallbackCopy);
        } else {
            fallbackCopy();
        }

        function fallbackCopy() {
            var textarea = document.createElement('textarea');
            textarea.value = text;
            textarea.style.position = 'fixed';
            textarea.style.opacity = '0';
            document.body.appendChild(textarea);
            textarea.select();
            try {
                document.execCommand('copy');
                showToast();
            } catch (err) {}
            document.body.removeChild(textarea);
        }
    }

    function showToast() {
        var toast = document.getElementById('sdb-toast-msg');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'sdb-toast-msg';
            toast.className = 'sdb-toast';
            toast.textContent = '✓ Copied to clipboard!';
            document.getElementById('switch-debugbar').appendChild(toast);
        }
        toast.classList.add('sdb-toast-show');
        setTimeout(function() {
            toast.classList.remove('sdb-toast-show');
        }, 1800);
    }

    function interceptAjax() {
        var origOpen = XMLHttpRequest.prototype.open;
        var origSend = XMLHttpRequest.prototype.send;
        XMLHttpRequest.prototype.open = function(method, url) {
            this._sdbMethod = method;
            this._sdbUrl = url;
            return origOpen.apply(this, arguments);
        };
        XMLHttpRequest.prototype.send = function() {
            this.addEventListener('load', function() {
                var debugId = this.getResponseHeader('X-Switch-Debug-Bar');
                if (debugId) {
                    addAjaxRequest(debugId, this._sdbMethod || 'GET', this._sdbUrl || '/');
                }
            });
            return origSend.apply(this, arguments);
        };

        if (window.fetch) {
            var origFetch = window.fetch;
            window.fetch = function() {
                return origFetch.apply(this, arguments).then(function(res) {
                    var debugId = res.headers.get('X-Switch-Debug-Bar');
                    if (debugId) {
                        addAjaxRequest(debugId, 'FETCH', res.url);
                    }
                    return res;
                });
            };
        }
    }

    function addAjaxRequest(id, method, url) {
        var select = document.getElementById('sdb-history-select');
        if (!select) return;

        var opt = document.createElement('option');
        opt.value = id;
        opt.textContent = '⚡ [' + method + '] ' + url.substring(0, 30);
        select.appendChild(opt);

        var historyTab = document.querySelector('.sdb-tab[data-tab="history"]');
        if (historyTab) {
            var badge = historyTab.querySelector('.sdb-badge');
            if (badge) {
                var count = parseInt(badge.textContent || '0', 10) + 1;
                badge.textContent = count;
                badge.className = 'sdb-badge sdb-badge-neon';
            }
        }
    }

    window.openTab = openTab;

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
JS;
    }
}
