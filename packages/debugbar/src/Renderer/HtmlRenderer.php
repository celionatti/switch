<?php

declare(strict_types=1);

namespace Switch\DebugBar\Renderer;

use Switch\DebugBar\Collectors\CollectorInterface;
use Switch\DebugBar\Dumper\HtmlDumper;

class HtmlRenderer
{
    /**
     * @param array<string, CollectorInterface> $collectors
     * @param array<string, mixed> $collectedData
     * @param string $requestId
     * @param string $dataUrl
     */
    public function render(
        array $collectors,
        array $collectedData,
        string $requestId,
        string $dataUrl = '/_debugbar/data'
    ): string {
        $styles = AssetManager::getStyles();
        $script = AssetManager::getScript($requestId, $dataUrl);

        $tabsHtml = $this->renderTabs($collectors, $collectedData);
        $panelsHtml = $this->renderPanels($collectors, $collectedData);
        $overviewHtml = $this->renderOverviewPanel($collectedData);

        $timeBadge = $collectedData['time']['duration_formatted'] ?? '0ms';
        $memoryBadge = $collectedData['memory']['peak_allocated_formatted'] ?? '0MB';
        $queryCount = $collectedData['queries']['count'] ?? 0;

        return <<<HTML
<!-- Switch Framework DebugBar -->
<div id="switch-debugbar">
    <style>{$styles}</style>

    <!-- Floating Minimized Pill Badge -->
    <div class="sdb-pill" id="sdb-floating-pill" title="Click or press Alt+D to expand DebugBar">
        <span class="sdb-brand-icon">⚡</span>
        <span style="font-weight: 700; color: #fff;">Switch</span>
        <span class="sdb-badge sdb-badge-info">{$timeBadge}</span>
        <span class="sdb-badge sdb-badge-neon">{$memoryBadge}</span>
        <span class="sdb-badge sdb-badge-success">{$queryCount} Q</span>
    </div>

    <!-- Main Floating / Dock Bar -->
    <div class="sdb-bar" id="sdb-main-bar">
        <div class="sdb-bar-left">
            <div class="sdb-brand" onclick="openTab('overview')" title="Switch Framework DebugBar">
                <span class="sdb-brand-icon">⚡</span>
                <span class="sdb-brand-text">Switch</span>
            </div>

            <button type="button" class="sdb-tab" data-tab="overview">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                <span>Overview</span>
            </button>

            {$tabsHtml}
        </div>

        <div class="sdb-bar-right">
            <select class="sdb-history-select" id="sdb-history-select" title="Request History / AJAX Inspector">
                <option value="{$requestId}">📍 Current ({$requestId})</option>
            </select>

            <button type="button" class="sdb-btn-icon" id="sdb-btn-minimize" title="Minimize to Pill (Alt+D)">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/></svg>
            </button>
        </div>
    </div>

    <!-- Expandable Drawer Panel -->
    <div class="sdb-drawer sdb-hidden" id="sdb-main-drawer">
        <div class="sdb-resizer" id="sdb-resizer-handle"></div>

        <div class="sdb-panel-header">
            <div class="sdb-panel-title">
                <span id="sdb-panel-current-title">Overview</span>
            </div>

            <div style="display: flex; align-items: center; gap: 8px;">
                <div class="sdb-search-box">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <input type="text" class="sdb-search-input" id="sdb-search-filter" placeholder="Search filter...">
                </div>

                <button type="button" class="sdb-btn-icon" id="sdb-btn-close-drawer" title="Close Panel (Esc)">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
        </div>

        <div class="sdb-panel-body">
            <div class="sdb-panel-content" data-panel="overview">
                {$overviewHtml}
            </div>

            {$panelsHtml}
        </div>
    </div>

    <script>{$script}</script>
</div>
<!-- /Switch Framework DebugBar -->
HTML;
    }

    private function renderTabs(array $collectors, array $collectedData): string
    {
        $html = '';
        foreach ($collectors as $name => $collector) {
            if (!$collector->isAvailable()) {
                continue;
            }

            $title = $collector->getTitle();
            $icon = $collector->getIcon();
            $badge = $collector->getBadge();
            $badgeColor = $collector->getBadgeColor();

            $badgeHtml = '';
            if ($badge !== null && $badge !== '') {
                $colorClass = match ($badgeColor) {
                    'success' => 'sdb-badge-success',
                    'warning' => 'sdb-badge-warning',
                    'danger' => 'sdb-badge-danger',
                    'info' => 'sdb-badge-info',
                    'neon' => 'sdb-badge-neon',
                    default => '',
                };
                $badgeHtml = '<span class="sdb-badge ' . $colorClass . '">' . htmlspecialchars($badge, ENT_QUOTES, 'UTF-8') . '</span>';
            }

            $html .= '<button type="button" class="sdb-tab" data-tab="' . $name . '">';
            $html .= $icon;
            $html .= '<span>' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</span>';
            $html .= $badgeHtml;
            $html .= '</button>';
        }

        return $html;
    }

    private function renderPanels(array $collectors, array $collectedData): string
    {
        $html = '';
        foreach ($collectors as $name => $collector) {
            if (!$collector->isAvailable()) {
                continue;
            }

            $data = $collectedData[$name] ?? [];
            $panelHtml = match ($name) {
                'time' => $this->renderTimePanel($data),
                'memory' => $this->renderMemoryPanel($data),
                'queries' => $this->renderQueriesPanel($data),
                'route' => $this->renderRoutePanel($data),
                'views' => $this->renderViewsPanel($data),
                'logs' => $this->renderLogsPanel($data),
                'request' => $this->renderRequestPanel($data),
                'session' => $this->renderSessionPanel($data),
                'auth' => $this->renderAuthPanel($data),
                'cache' => $this->renderCachePanel($data),
                'events' => $this->renderEventsPanel($data),
                'config' => $this->renderConfigPanel($data),
                'history' => $this->renderHistoryPanel($data),
                default => '<pre class="sdb-dumper">' . htmlspecialchars(json_encode($data, JSON_PRETTY_PRINT), ENT_QUOTES, 'UTF-8') . '</pre>',
            };

            $html .= '<div class="sdb-panel-content" data-panel="' . $name . '">' . $panelHtml . '</div>';
        }

        return $html;
    }

    private function renderOverviewPanel(array $data): string
    {
        $timeFormatted = $data['time']['duration_formatted'] ?? '0ms';
        $bootTime = isset($data['time']['boot_duration']) ? $data['time']['boot_duration'] . 'ms' : 'N/A';
        $memory = $data['memory']['peak_allocated_formatted'] ?? '0MB';
        $queries = $data['queries']['count'] ?? 0;
        $queryTime = isset($data['queries']['total_time_ms']) ? $data['queries']['total_time_ms'] . 'ms' : '0ms';
        $views = $data['views']['count'] ?? 0;
        $route = $data['route']['uri'] ?? ($data['request']['request']['path'] ?? '/');
        $method = $data['request']['request']['method'] ?? 'GET';
        $status = $data['request']['response']['status_code'] ?? 200;
        $phpVer = PHP_VERSION;

        return <<<HTML
<div class="sdb-grid">
    <div class="sdb-metric-card">
        <div class="sdb-metric-label">Execution Time</div>
        <div class="sdb-metric-value" style="color: var(--sdb-cyan);">{$timeFormatted}</div>
        <div style="font-size: 10px; color: var(--sdb-text-dim); margin-top: 4px;">Boot: {$bootTime}</div>
    </div>
    <div class="sdb-metric-card">
        <div class="sdb-metric-label">Peak Memory</div>
        <div class="sdb-metric-value" style="color: var(--sdb-purple);">{$memory}</div>
        <div style="font-size: 10px; color: var(--sdb-text-dim); margin-top: 4px;">Limit: {$data['memory']['memory_limit']}</div>
    </div>
    <div class="sdb-metric-card">
        <div class="sdb-metric-label">Database Queries</div>
        <div class="sdb-metric-value" style="color: var(--sdb-emerald);">{$queries} queries</div>
        <div style="font-size: 10px; color: var(--sdb-text-dim); margin-top: 4px;">Total Time: {$queryTime}</div>
    </div>
    <div class="sdb-metric-card">
        <div class="sdb-metric-label">HTTP Route & Status</div>
        <div class="sdb-metric-value" style="color: var(--sdb-amber); font-size: 14px; word-break: break-all;">[{$method}] {$route}</div>
        <div style="font-size: 10px; color: var(--sdb-text-dim); margin-top: 4px;">Status: {$status} | Views: {$views} | PHP: {$phpVer}</div>
    </div>
</div>
HTML;
    }

    private function renderTimePanel(array $data): string
    {
        $measures = $data['measures'] ?? [];
        if (empty($measures)) {
            return '<div style="color: var(--sdb-text-dim); padding: 12px;">No timeline measures recorded. Use <code>DebugBar::startMeasure(\'name\', \'label\')</code> to profile custom blocks.</div>';
        }

        $html = '<div style="margin-bottom: 12px; font-weight: 600; color: #fff;">Timeline Breakdown (' . ($data['duration_formatted'] ?? '0ms') . ')</div>';
        foreach ($measures as $m) {
            $label = htmlspecialchars($m['label'] ?? $m['name'], ENT_QUOTES, 'UTF-8');
            $dur = $m['duration_ms'] ?? 0;
            $pct = $m['percent'] ?? 0;
            $start = $m['relative_start_ms'] ?? 0;

            $html .= '<div class="sdb-timeline-item">';
            $html .= '<div class="sdb-timeline-info">';
            $html .= '<span><strong>' . $label . '</strong> <span style="color: var(--sdb-text-dim); font-size: 10px;">(start: +' . $start . 'ms)</span></span>';
            $html .= '<span style="font-family: var(--sdb-mono); color: var(--sdb-cyan);">' . $dur . ' ms (' . $pct . '%)</span>';
            $html .= '</div>';
            $html .= '<div class="sdb-timeline-track">';
            $html .= '<div class="sdb-timeline-bar" style="width: ' . max(1, $pct) . '%;"></div>';
            $html .= '</div>';
            $html .= '</div>';
        }

        return $html;
    }

    private function renderMemoryPanel(array $data): string
    {
        return <<<HTML
<div class="sdb-grid">
    <div class="sdb-metric-card">
        <div class="sdb-metric-label">Peak Memory (Allocated)</div>
        <div class="sdb-metric-value" style="color: var(--sdb-purple);">{$data['peak_allocated_formatted']}</div>
    </div>
    <div class="sdb-metric-card">
        <div class="sdb-metric-label">Peak Memory (Real Usage)</div>
        <div class="sdb-metric-value" style="color: var(--sdb-cyan);">{$data['peak_usage_formatted']}</div>
    </div>
    <div class="sdb-metric-card">
        <div class="sdb-metric-label">Initial Request Memory</div>
        <div class="sdb-metric-value">{$data['start_memory_formatted']}</div>
    </div>
    <div class="sdb-metric-card">
        <div class="sdb-metric-label">Memory Limit</div>
        <div class="sdb-metric-value">{$data['memory_limit']}</div>
    </div>
</div>
HTML;
    }

    private function renderQueriesPanel(array $data): string
    {
        $queries = $data['queries'] ?? [];
        if (empty($queries)) {
            return '<div style="color: var(--sdb-text-dim); padding: 12px;">No SQL queries executed during this request.</div>';
        }

        $slowCount = $data['slow_count'] ?? 0;
        $dupCount = $data['duplicate_count'] ?? 0;

        $html = '<div style="display: flex; gap: 10px; margin-bottom: 12px;">';
        $html .= '<span class="sdb-badge sdb-badge-info">' . count($queries) . ' Queries (' . ($data['total_time_ms'] ?? 0) . 'ms)</span>';
        if ($slowCount > 0) {
            $html .= '<span class="sdb-badge sdb-badge-danger">⚠️ ' . $slowCount . ' Slow (>50ms)</span>';
        }
        if ($dupCount > 0) {
            $html .= '<span class="sdb-badge sdb-badge-warning">⚠️ ' . $dupCount . ' Duplicate (Possible N+1)</span>';
        }
        $html .= '</div>';

        foreach ($queries as $q) {
            $sql = htmlspecialchars($q['interpolated'] ?? $q['sql'], ENT_QUOTES, 'UTF-8');
            $rawSql = htmlspecialchars($q['sql'], ENT_QUOTES, 'UTF-8');
            $time = $q['time_ms'] ?? 0;
            $caller = htmlspecialchars($q['caller'] ?? 'unknown', ENT_QUOTES, 'UTF-8');
            $conn = htmlspecialchars($q['connection'] ?? 'default', ENT_QUOTES, 'UTF-8');
            $isSlow = !empty($q['is_slow']);
            $isDup = !empty($q['is_duplicate']);

            $class = 'sdb-query-card' . ($isSlow ? ' sdb-query-slow' : '') . ($isDup ? ' sdb-query-dup' : '');

            $html .= '<div class="' . $class . '">';
            $html .= '<div class="sdb-query-meta">';
            $html .= '<div>';
            $html .= '<span class="sdb-badge ' . ($isSlow ? 'sdb-badge-danger' : 'sdb-badge-success') . '">' . $time . ' ms</span> ';
            $html .= '<span class="sdb-badge">' . $conn . '</span> ';
            if ($isDup) {
                $html .= '<span class="sdb-badge sdb-badge-warning">N+1 (' . ($q['duplicate_count'] ?? 2) . 'x)</span> ';
            }
            $html .= '</div>';
            $html .= '<div><span style="color: var(--sdb-text-dim); font-family: var(--sdb-mono);">' . $caller . '</span></div>';
            $html .= '</div>';
            $html .= '<pre class="sdb-query-sql">' . $sql . '</pre>';
            $html .= '</div>';
        }

        return $html;
    }

    private function renderRoutePanel(array $data): string
    {
        $uri = htmlspecialchars($data['uri'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
        $method = htmlspecialchars($data['method'] ?? 'GET', ENT_QUOTES, 'UTF-8');
        $action = htmlspecialchars($data['action'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
        $controller = htmlspecialchars($data['controller'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
        $methodName = htmlspecialchars($data['controller_method'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
        $name = htmlspecialchars($data['name'] ?? 'Unnamed', ENT_QUOTES, 'UTF-8');
        $middleware = $data['middleware'] ?? [];
        $params = $data['parameters'] ?? [];

        $mwHtml = empty($middleware)
            ? '<span style="color: var(--sdb-text-dim);">None</span>'
            : implode(' ', array_map(fn($m) => '<span class="sdb-badge sdb-badge-neon">' . htmlspecialchars((string) $m, ENT_QUOTES, 'UTF-8') . '</span>', $middleware));

        $paramHtml = empty($params)
            ? '<span style="color: var(--sdb-text-dim);">No parameters</span>'
            : HtmlDumper::dump($params);

        return <<<HTML
<table class="sdb-table">
    <tr><th style="width: 140px;">URI Pattern</th><td><code style="color: var(--sdb-cyan);">{$uri}</code></td></tr>
    <tr><th>HTTP Method</th><td><span class="sdb-badge sdb-badge-info">{$method}</span></td></tr>
    <tr><th>Route Name</th><td>{$name}</td></tr>
    <tr><th>Action / Handler</th><td><code>{$action}</code></td></tr>
    <tr><th>Controller</th><td><code>{$controller}</code></td></tr>
    <tr><th>Method</th><td><code>{$methodName}</code></td></tr>
    <tr><th>Middleware</th><td>{$mwHtml}</td></tr>
    <tr><th>Parameters</th><td>{$paramHtml}</td></tr>
</table>
HTML;
    }

    private function renderViewsPanel(array $data): string
    {
        $views = $data['views'] ?? [];
        if (empty($views)) {
            return '<div style="color: var(--sdb-text-dim); padding: 12px;">No views rendered during this request.</div>';
        }

        $html = '<div style="margin-bottom: 12px;"><span class="sdb-badge sdb-badge-info">' . count($views) . ' Views Rendered (' . ($data['total_render_time_ms'] ?? 0) . 'ms)</span></div>';
        $html .= '<table class="sdb-table">';
        $html .= '<tr><th>View Name</th><th>Path</th><th>Render Time</th><th>Data Parameters</th></tr>';

        foreach ($views as $v) {
            $name = htmlspecialchars($v['name'], ENT_QUOTES, 'UTF-8');
            $path = htmlspecialchars($v['path'], ENT_QUOTES, 'UTF-8');
            $dur = $v['render_time_ms'] ?? 0;
            $dumper = HtmlDumper::dump($v['data'] ?? []);

            $html .= '<tr>';
            $html .= '<td><strong style="color: var(--sdb-emerald);">' . $name . '</strong></td>';
            $html .= '<td><code style="font-size: 11px;">' . $path . '</code></td>';
            $html .= '<td><span class="sdb-badge sdb-badge-success">' . $dur . ' ms</span></td>';
            $html .= '<td>' . $dumper . '</td>';
            $html .= '</tr>';
        }

        $html .= '</table>';
        return $html;
    }

    private function renderLogsPanel(array $data): string
    {
        $messages = $data['messages'] ?? [];
        if (empty($messages)) {
            return '<div style="color: var(--sdb-text-dim); padding: 12px;">No messages logged. Use <code>debug(\$var)</code> or <code>DebugBar::info(\'text\')</code>.</div>';
        }

        $html = '<table class="sdb-table">';
        $html .= '<tr><th style="width: 80px;">Level</th><th style="width: 100px;">Time</th><th>Message / Dump</th><th style="width: 180px;">Caller</th></tr>';

        foreach ($messages as $m) {
            $lvl = strtoupper($m['level'] ?? 'INFO');
            $time = htmlspecialchars($m['time_formatted'] ?? '', ENT_QUOTES, 'UTF-8');
            $caller = htmlspecialchars($m['caller'] ?? '', ENT_QUOTES, 'UTF-8');
            $text = $m['message'] !== null ? htmlspecialchars($m['message'], ENT_QUOTES, 'UTF-8') : '';
            $dump = $m['dump'] ?? '';

            $badgeColor = match (strtolower($lvl)) {
                'emergency', 'alert', 'critical', 'error' => 'sdb-badge-danger',
                'warning' => 'sdb-badge-warning',
                'debug' => 'sdb-badge-neon',
                default => 'sdb-badge-info',
            };

            $html .= '<tr>';
            $html .= '<td><span class="sdb-badge ' . $badgeColor . '">' . $lvl . '</span></td>';
            $html .= '<td style="color: var(--sdb-text-dim); font-family: var(--sdb-mono);">' . $time . '</td>';
            $html .= '<td>' . ($text !== '' ? '<div>' . $text . '</div>' : '') . $dump . '</td>';
            $html .= '<td style="color: var(--sdb-text-dim); font-family: var(--sdb-mono);">' . $caller . '</td>';
            $html .= '</tr>';
        }

        $html .= '</table>';
        return $html;
    }

    private function renderRequestPanel(array $data): string
    {
        $req = $data['request'] ?? [];
        $res = $data['response'] ?? [];

        $html = '<div style="display: flex; flex-direction: column; gap: 16px;">';
        $html .= '<div>';
        $html .= '<div style="font-weight: 600; color: #fff; margin-bottom: 8px;">Request Information</div>';
        $html .= '<table class="sdb-table">';
        $html .= '<tr><th style="width: 140px;">URL</th><td><code>' . htmlspecialchars($req['uri'] ?? '', ENT_QUOTES, 'UTF-8') . '</code></td></tr>';
        $html .= '<tr><th>Method</th><td><span class="sdb-badge sdb-badge-info">' . htmlspecialchars($req['method'] ?? 'GET', ENT_QUOTES, 'UTF-8') . '</span></td></tr>';
        $html .= '<tr><th>Client IP</th><td>' . htmlspecialchars($req['ip'] ?? '', ENT_QUOTES, 'UTF-8') . '</td></tr>';
        $html .= '<tr><th>User Agent</th><td>' . htmlspecialchars($req['user_agent'] ?? 'N/A', ENT_QUOTES, 'UTF-8') . '</td></tr>';
        $html .= '<tr><th>Request Headers</th><td>' . HtmlDumper::dump($req['headers'] ?? []) . '</td></tr>';
        $html .= '<tr><th>Query Parameters</th><td>' . HtmlDumper::dump($req['query_params'] ?? []) . '</td></tr>';
        $html .= '<tr><th>Body / Payload</th><td>' . HtmlDumper::dump($req['body'] ?? []) . '</td></tr>';
        $html .= '<tr><th>Cookies</th><td>' . HtmlDumper::dump($req['cookies'] ?? []) . '</td></tr>';
        $html .= '</table>';
        $html .= '</div>';

        $html .= '<div>';
        $html .= '<div style="font-weight: 600; color: #fff; margin-bottom: 8px;">Response Information</div>';
        $html .= '<table class="sdb-table">';
        $html .= '<tr><th style="width: 140px;">Status</th><td><span class="sdb-badge sdb-badge-success">' . ($res['status_code'] ?? 200) . ' ' . htmlspecialchars($res['reason_phrase'] ?? 'OK', ENT_QUOTES, 'UTF-8') . '</span></td></tr>';
        $html .= '<tr><th>Protocol</th><td>HTTP/' . htmlspecialchars($res['protocol_version'] ?? '1.1', ENT_QUOTES, 'UTF-8') . '</td></tr>';
        $html .= '<tr><th>Response Headers</th><td>' . HtmlDumper::dump($res['headers'] ?? []) . '</td></tr>';
        $html .= '</table>';
        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }

    private function renderSessionPanel(array $data): string
    {
        $id = htmlspecialchars($data['id'] ?? 'None', ENT_QUOTES, 'UTF-8');
        $attrs = $data['attributes'] ?? [];

        return <<<HTML
<table class="sdb-table">
    <tr><th style="width: 140px;">Session ID</th><td><code>{$id}</code></td></tr>
    <tr><th>Attributes Count</th><td>{$data['count']}</td></tr>
    <tr><th>Session Data</th><td>{$this->dumpOrEmpty($attrs)}</td></tr>
</table>
HTML;
    }

    private function renderAuthPanel(array $data): string
    {
        $isAuth = !empty($data['authenticated']);
        $guard = htmlspecialchars($data['guard'] ?? 'web', ENT_QUOTES, 'UTF-8');
        $user = $data['user'] ?? [];

        if (!$isAuth) {
            return '<div style="color: var(--sdb-text-dim); padding: 12px;">Unauthenticated (Guest) on guard: <code>' . $guard . '</code></div>';
        }

        $id = htmlspecialchars((string) ($user['id'] ?? 'N/A'), ENT_QUOTES, 'UTF-8');
        $email = htmlspecialchars((string) ($user['email'] ?? 'N/A'), ENT_QUOTES, 'UTF-8');
        $name = htmlspecialchars((string) ($user['name'] ?? 'N/A'), ENT_QUOTES, 'UTF-8');
        $class = htmlspecialchars((string) ($user['class'] ?? 'User'), ENT_QUOTES, 'UTF-8');

        return <<<HTML
<table class="sdb-table">
    <tr><th style="width: 140px;">Status</th><td><span class="sdb-badge sdb-badge-success">Authenticated</span></td></tr>
    <tr><th>Guard</th><td><span class="sdb-badge sdb-badge-info">{$guard}</span></td></tr>
    <tr><th>User Model</th><td><code>{$class}</code></td></tr>
    <tr><th>ID</th><td>{$id}</td></tr>
    <tr><th>Name</th><td>{$name}</td></tr>
    <tr><th>Email</th><td>{$email}</td></tr>
    <tr><th>User Attributes</th><td>{$this->dumpOrEmpty($user['attributes'] ?? [])}</td></tr>
</table>
HTML;
    }

    private function renderCachePanel(array $data): string
    {
        return <<<HTML
<div class="sdb-grid">
    <div class="sdb-metric-card">
        <div class="sdb-metric-label">Cache Hits</div>
        <div class="sdb-metric-value" style="color: var(--sdb-emerald);">{$data['hits']}</div>
    </div>
    <div class="sdb-metric-card">
        <div class="sdb-metric-label">Cache Misses</div>
        <div class="sdb-metric-value" style="color: var(--sdb-rose);">{$data['misses']}</div>
    </div>
    <div class="sdb-metric-card">
        <div class="sdb-metric-label">Cache Writes</div>
        <div class="sdb-metric-value" style="color: var(--sdb-cyan);">{$data['writes']}</div>
    </div>
    <div class="sdb-metric-card">
        <div class="sdb-metric-label">Hit Ratio</div>
        <div class="sdb-metric-value" style="color: var(--sdb-purple);">{$data['hit_ratio']}%</div>
    </div>
</div>
HTML;
    }

    private function renderEventsPanel(array $data): string
    {
        $events = $data['events'] ?? [];
        if (empty($events)) {
            return '<div style="color: var(--sdb-text-dim); padding: 12px;">No events dispatched during this request.</div>';
        }

        $html = '<table class="sdb-table">';
        $html .= '<tr><th>Event</th><th>Listeners</th><th>Duration</th></tr>';

        foreach ($events as $ev) {
            $name = htmlspecialchars($ev['name'], ENT_QUOTES, 'UTF-8');
            $listeners = $ev['listeners'] ?? [];
            $dur = $ev['duration_ms'] ?? 0;

            $lHtml = empty($listeners)
                ? '<span style="color: var(--sdb-text-dim);">No listeners attached</span>'
                : implode('<br>', array_map(fn($l) => '<code>' . htmlspecialchars($l, ENT_QUOTES, 'UTF-8') . '</code>', $listeners));

            $html .= '<tr>';
            $html .= '<td><strong style="color: var(--sdb-amber);">' . $name . '</strong></td>';
            $html .= '<td>' . $lHtml . '</td>';
            $html .= '<td><span class="sdb-badge sdb-badge-info">' . $dur . ' ms</span></td>';
            $html .= '</tr>';
        }

        $html .= '</table>';
        return $html;
    }

    private function renderConfigPanel(array $data): string
    {
        $php = htmlspecialchars($data['php_version'] ?? PHP_VERSION, ENT_QUOTES, 'UTF-8');
        $switch = htmlspecialchars($data['switch_version'] ?? '1.0.0', ENT_QUOTES, 'UTF-8');
        $os = htmlspecialchars($data['os'] ?? PHP_OS, ENT_QUOTES, 'UTF-8');
        $sapi = htmlspecialchars($data['sapi'] ?? PHP_SAPI, ENT_QUOTES, 'UTF-8');
        $opcache = htmlspecialchars($data['opcache'] ?? 'Disabled', ENT_QUOTES, 'UTF-8');

        $envDump = HtmlDumper::dump($data['environment'] ?? []);
        $configDump = HtmlDumper::dump($data['custom_config'] ?? []);

        return <<<HTML
<table class="sdb-table">
    <tr><th style="width: 140px;">Switch Framework</th><td><strong style="color: var(--sdb-cyan);">v{$switch}</strong></td></tr>
    <tr><th>PHP Version</th><td><strong>{$php}</strong> ({$sapi}) on {$os}</td></tr>
    <tr><th>OPcache</th><td>{$opcache}</td></tr>
    <tr><th>Memory Limit</th><td>{$data['memory_limit']}</td></tr>
    <tr><th>Max Execution Time</th><td>{$data['max_execution_time']}</td></tr>
    <tr><th>Extensions ({$data['extension_count']})</th><td><details class="sdb-tree-node"><summary class="sdb-tree-summary">Show loaded extensions</summary><div style="padding: 6px 0;">{$this->renderExtensionsList($data['loaded_extensions'] ?? [])}</div></details></td></tr>
    <tr><th>Environment Variables</th><td>{$envDump}</td></tr>
    <tr><th>Configuration</th><td>{$configDump}</td></tr>
</table>
HTML;
    }

    private function renderHistoryPanel(array $data): string
    {
        $requests = $data['requests'] ?? [];
        if (empty($requests)) {
            return '<div style="color: var(--sdb-text-dim); padding: 12px;">Current request ID: <code>' . ($data['current_id'] ?? '') . '</code>. As AJAX and Live requests occur, they will be logged here.</div>';
        }

        $html = '<table class="sdb-table">';
        $html .= '<tr><th>ID</th><th>Time</th><th>Method</th><th>URI</th><th>Status</th><th>Duration</th><th>Memory</th></tr>';

        foreach ($requests as $r) {
            $id = htmlspecialchars($r['id'] ?? '', ENT_QUOTES, 'UTF-8');
            $time = htmlspecialchars($r['time_formatted'] ?? '', ENT_QUOTES, 'UTF-8');
            $method = htmlspecialchars($r['method'] ?? 'GET', ENT_QUOTES, 'UTF-8');
            $uri = htmlspecialchars($r['uri'] ?? '/', ENT_QUOTES, 'UTF-8');
            $status = $r['status'] ?? 200;
            $dur = htmlspecialchars($r['duration'] ?? '0ms', ENT_QUOTES, 'UTF-8');
            $mem = htmlspecialchars($r['memory'] ?? '0MB', ENT_QUOTES, 'UTF-8');

            $html .= '<tr>';
            $html .= '<td><code>' . $id . '</code></td>';
            $html .= '<td>' . $time . '</td>';
            $html .= '<td><span class="sdb-badge sdb-badge-info">' . $method . '</span></td>';
            $html .= '<td>' . $uri . '</td>';
            $html .= '<td><span class="sdb-badge sdb-badge-success">' . $status . '</span></td>';
            $html .= '<td>' . $dur . '</td>';
            $html .= '<td>' . $mem . '</td>';
            $html .= '</tr>';
        }

        $html .= '</table>';
        return $html;
    }

    private function renderExtensionsList(array $extensions): string
    {
        sort($extensions);
        $chips = array_map(fn($ext) => '<span class="sdb-badge" style="margin: 2px;">' . htmlspecialchars($ext, ENT_QUOTES, 'UTF-8') . '</span>', $extensions);
        return implode(' ', $chips);
    }

    private function dumpOrEmpty(mixed $value): string
    {
        if (empty($value)) {
            return '<span style="color: var(--sdb-text-dim);">Empty</span>';
        }
        return HtmlDumper::dump($value);
    }
}
