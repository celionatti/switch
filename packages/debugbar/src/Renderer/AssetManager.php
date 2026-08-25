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
/* Switch DebugBar Modern Glassmorphic Styles */
:root {
    --sdb-bg-main: #0b0f19;
    --sdb-bg-card: #111827;
    --sdb-bg-hover: #1f293d;
    --sdb-border: rgba(255, 255, 255, 0.09);
    --sdb-border-active: rgba(0, 240, 255, 0.4);
    --sdb-text-main: #f3f4f6;
    --sdb-text-muted: #9ca3af;
    --sdb-text-dim: #6b7280;
    --sdb-cyan: #00f0ff;
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
    z-index: 9999999;
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

/* Floating / Dock Bar */
.sdb-bar {
    pointer-events: auto;
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: rgba(11, 15, 25, 0.94);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border-top: 1px solid var(--sdb-border);
    box-shadow: var(--sdb-shadow);
    padding: 0 10px;
    height: 38px;
    user-select: none;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}

.sdb-bar-left, .sdb-bar-right {
    display: flex;
    align-items: center;
    gap: 4px;
    height: 100%;
    overflow-x: auto;
    scrollbar-width: none;
}
.sdb-bar-left::-webkit-scrollbar, .sdb-bar-right::-webkit-scrollbar { display: none; }

/* Brand Logo */
.sdb-brand {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 4px 10px 4px 6px;
    font-weight: 700;
    font-size: 12px;
    color: #fff;
    cursor: pointer;
    border-radius: var(--sdb-radius);
    transition: background 0.15s ease;
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
    gap: 6px;
    padding: 4px 9px;
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
}
.sdb-tab:hover {
    background: var(--sdb-bg-hover);
    color: var(--sdb-text-main);
}
.sdb-tab.sdb-active {
    background: rgba(0, 240, 255, 0.1);
    border-color: var(--sdb-border-active);
    color: var(--sdb-cyan);
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
}
.sdb-badge-success { background: rgba(16, 185, 129, 0.15); color: var(--sdb-emerald); }
.sdb-badge-warning { background: rgba(245, 158, 11, 0.15); color: var(--sdb-amber); }
.sdb-badge-danger  { background: rgba(244, 63, 94, 0.18); color: var(--sdb-rose); }
.sdb-badge-info    { background: rgba(0, 240, 255, 0.15); color: var(--sdb-cyan); }
.sdb-badge-neon    { background: rgba(168, 85, 247, 0.15); color: var(--sdb-purple); }

/* Right Quick Actions */
.sdb-btn-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    border-radius: var(--sdb-radius);
    background: transparent;
    border: none;
    color: var(--sdb-text-muted);
    cursor: pointer;
    transition: all 0.15s ease;
}
.sdb-btn-icon:hover {
    background: var(--sdb-bg-hover);
    color: #fff;
}

/* History Dropdown */
.sdb-history-select {
    background: var(--sdb-bg-card);
    border: 1px solid var(--sdb-border);
    color: var(--sdb-text-main);
    font-family: var(--sdb-mono);
    font-size: 11px;
    height: 26px;
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
    backdrop-filter: blur(24px);
    border-top: 1px solid var(--sdb-border);
    height: 400px;
    max-height: 80vh;
    display: flex;
    flex-direction: column;
    box-shadow: var(--sdb-shadow);
    transition: height 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}
.sdb-drawer.sdb-hidden { display: none; }

/* Resizer Handle */
.sdb-resizer {
    position: absolute;
    top: -4px;
    left: 0;
    right: 0;
    height: 8px;
    cursor: row-resize;
    z-index: 10;
}
.sdb-resizer:hover { background: rgba(0, 240, 255, 0.2); }

/* Panel Header */
.sdb-panel-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 16px;
    background: rgba(17, 24, 39, 0.6);
    border-bottom: 1px solid var(--sdb-border);
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
    padding: 4px 8px;
    width: 220px;
}
.sdb-search-input {
    background: transparent;
    border: none;
    color: #fff;
    font-family: inherit;
    font-size: 11px;
    width: 100%;
    outline: none;
}
.sdb-search-input::placeholder { color: var(--sdb-text-dim); }

/* Panel Body */
.sdb-panel-body {
    flex: 1;
    overflow-y: auto;
    padding: 16px;
}
.sdb-panel-content { display: none; }
.sdb-panel-content.sdb-active { display: block; }

/* Tables */
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
}
.sdb-table td {
    padding: 8px 12px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.04);
    vertical-align: top;
}
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
.sdb-query-card.sdb-query-slow { border-left: 3px solid var(--sdb-rose); }
.sdb-query-card.sdb-query-dup { border-left: 3px solid var(--sdb-amber); }
.sdb-query-meta {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 6px;
    font-size: 11px;
    color: var(--sdb-text-muted);
}
.sdb-query-sql {
    font-family: var(--sdb-mono);
    font-size: 11.5px;
    color: #e5e7eb;
    background: rgba(0, 0, 0, 0.3);
    padding: 8px;
    border-radius: var(--sdb-radius-sm);
    overflow-x: auto;
    white-space: pre-wrap;
    word-break: break-all;
}

/* Timeline Bars */
.sdb-timeline-item {
    margin-bottom: 10px;
}
.sdb-timeline-info {
    display: flex;
    justify-content: space-between;
    font-size: 11px;
    margin-bottom: 3px;
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
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 12px;
    margin-bottom: 16px;
}
.sdb-metric-card {
    background: var(--sdb-bg-card);
    border: 1px solid var(--sdb-border);
    border-radius: var(--sdb-radius);
    padding: 12px;
}
.sdb-metric-label {
    font-size: 11px;
    color: var(--sdb-text-muted);
    margin-bottom: 4px;
}
.sdb-metric-value {
    font-size: 18px;
    font-weight: 700;
    font-family: var(--sdb-mono);
    color: #fff;
}

/* Mini Collapsed Floating Pill */
.sdb-pill {
    pointer-events: auto;
    position: fixed;
    bottom: 12px;
    right: 12px;
    background: rgba(11, 15, 25, 0.92);
    backdrop-filter: blur(16px);
    border: 1px solid var(--sdb-border);
    box-shadow: var(--sdb-shadow);
    padding: 6px 12px;
    border-radius: 9999px;
    display: none;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    user-select: none;
    z-index: 9999999;
    transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}
.sdb-pill:hover { transform: translateY(-2px); border-color: var(--sdb-cyan); }
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
.sdb-tree-children { padding-left: 16px; border-left: 1px dashed rgba(255, 255, 255, 0.1); margin: 2px 0 2px 4px; }
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

/* Responsive Adjustments */
@media (max-width: 768px) {
    .sdb-bar { padding: 0 6px; height: 42px; }
    .sdb-tab { font-size: 11px; padding: 4px 6px; }
    .sdb-brand-text { display: none; }
    .sdb-drawer { height: 75vh; }
    .sdb-grid { grid-template-columns: 1fr; }
    .sdb-search-box { width: 140px; }
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
    var currentReqId = '{$requestId}';

    function init() {
        var bar = document.getElementById('sdb-main-bar');
        var pill = document.getElementById('sdb-floating-pill');
        var drawer = document.getElementById('sdb-main-drawer');
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

        // Collapse to pill button
        var minimizeBtn = document.getElementById('sdb-btn-minimize');
        if (minimizeBtn) {
            minimizeBtn.addEventListener('click', collapseToPill);
        }

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
                var query = this.value.toLowerCase();
                var activePanel = document.querySelector('.sdb-panel-content.sdb-active');
                if (!activePanel) return;

                var items = activePanel.querySelectorAll('.sdb-query-card, .sdb-table tr, .sdb-tree-item, .sdb-timeline-item');
                items.forEach(function(item) {
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
                startY = e.clientY;
                startHeight = drawer.offsetHeight;
                document.body.style.userSelect = 'none';
            });
            window.addEventListener('mousemove', function(e) {
                if (!isResizing) return;
                var newH = startHeight + (startY - e.clientY);
                if (newH > 150 && newH < window.innerHeight * 0.9) {
                    drawer.style.height = newH + 'px';
                }
            });
            window.addEventListener('mouseup', function() {
                isResizing = false;
                document.body.style.userSelect = '';
            });
        }

        // Intercept AJAX / Fetch requests automatically for history updates
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
        }

        drawer.classList.remove('sdb-hidden');
        activeTab = tabName;
        isExpanded = true;

        // Clear search box on tab switch
        var searchInput = document.getElementById('sdb-search-filter');
        if (searchInput) {
            searchInput.value = '';
            var items = drawer.querySelectorAll('.sdb-query-card, .sdb-table tr, .sdb-tree-item, .sdb-timeline-item');
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

    function interceptAjax() {
        // Intercept XMLHttpRequest
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

        // Intercept Fetch
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

        // Flash history tab badge
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

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
JS;
    }
}
