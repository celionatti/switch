<layout name="layouts.app" />

<section name="title">{{ $title }}</section>

<section name="content">
<div class="kanban-page">
    <style>
        .kanban-page {
            max-width: 1240px;
            margin: 0 auto;
        }

        .kanban-hero {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .kanban-hero .hero-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.35rem 0.9rem;
            border-radius: var(--radius-full);
            background: rgba(99, 102, 241, 0.12);
            border: 1px solid rgba(99, 102, 241, 0.3);
            color: #a5b4fc;
            font-size: 0.8rem;
            font-weight: 600;
            font-family: var(--font-mono);
            margin-bottom: 1rem;
        }

        .kanban-hero h1 {
            font-size: 2.75rem;
            font-weight: 800;
            background: linear-gradient(135deg, #ffffff 0%, #38bdf8 45%, #a855f7 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin: 0 0 0.85rem;
            letter-spacing: -0.03em;
        }

        .kanban-hero p {
            font-size: 1.15rem;
            color: var(--text-muted);
            max-width: 720px;
            margin: 0 auto;
            line-height: 1.6;
        }

        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.25rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .section-title {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.35rem;
            font-weight: 700;
            color: var(--text-main);
        }

        .badge-count {
            padding: 0.2rem 0.6rem;
            border-radius: var(--radius-full);
            font-size: 0.75rem;
            font-family: var(--font-mono);
            background: var(--bg-elevated);
            border: 1px solid var(--border-subtle);
            color: var(--cyan-400);
        }

        /* Single Table Sortable Styles */
        .table-card {
            background: var(--bg-surface);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
            margin-bottom: 3.5rem;
        }

        .table-custom {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        .table-custom th {
            padding: 1rem 1.25rem;
            font-size: 0.8rem;
            font-family: var(--font-mono);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-dim);
            background: rgba(22, 25, 34, 0.6);
            border-bottom: 1px solid var(--border-subtle);
        }

        .table-custom td {
            padding: 1rem 1.25rem;
            font-size: 0.95rem;
            color: var(--text-main);
            border-bottom: 1px solid var(--border-subtle);
            vertical-align: middle;
            background: var(--bg-surface);
            transition: background 0.15s ease;
        }

        .table-custom tr:hover td {
            background: rgba(255, 255, 255, 0.02);
        }

        .drag-handle {
            color: var(--text-dim);
            cursor: grab !important;
            font-size: 1.1rem;
            user-select: none;
            -webkit-user-select: none;
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            transition: color 0.15s ease, transform 0.15s ease, background 0.15s ease;
        }

        .drag-handle:hover {
            color: var(--cyan-400);
            background: rgba(6, 182, 212, 0.1);
            transform: scale(1.15);
        }

        .drag-handle:active {
            cursor: grabbing !important;
        }

        .tag-pill {
            display: inline-flex;
            align-items: center;
            padding: 0.2rem 0.6rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-family: var(--font-mono);
            font-weight: 500;
        }

        .tag-cyan { background: rgba(6, 182, 212, 0.12); color: #22d3ee; border: 1px solid rgba(6, 182, 212, 0.25); }
        .tag-indigo { background: rgba(99, 102, 241, 0.12); color: #a5b4fc; border: 1px solid rgba(99, 102, 241, 0.25); }
        .tag-emerald { background: rgba(52, 211, 153, 0.12); color: #34d399; border: 1px solid rgba(52, 211, 153, 0.25); }
        .tag-amber { background: rgba(251, 191, 36, 0.12); color: #fbbf24; border: 1px solid rgba(251, 191, 36, 0.25); }
        .tag-rose { background: rgba(244, 63, 94, 0.12); color: #fb7185; border: 1px solid rgba(244, 63, 94, 0.25); }

        /* Kanban Board Grid */
        .kanban-board {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
            margin-bottom: 3.5rem;
            align-items: start;
        }

        @media (max-width: 992px) {
            .kanban-board {
                grid-template-columns: 1fr;
            }
        }

        .kanban-column {
            background: var(--bg-surface);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-lg);
            padding: 1.25rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
            min-height: 480px;
        }

        .column-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid var(--border-subtle);
        }

        .column-title {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            font-size: 1rem;
            font-weight: 700;
            color: var(--text-main);
        }

        .column-cards-container {
            display: flex;
            flex-direction: column;
            gap: 0.85rem;
            flex-grow: 1;
            min-height: 380px;
            padding: 0.25rem;
            border-radius: var(--radius-md);
            transition: background 0.2s ease, outline 0.2s ease;
        }

        .task-card {
            background: var(--bg-elevated);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-md);
            padding: 1.15rem;
            cursor: grab !important;
            user-select: none;
            -webkit-user-select: none;
            -webkit-user-drag: element;
            transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .task-card:hover {
            transform: translateY(-2px);
            border-color: rgba(6, 182, 212, 0.4);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
        }

        .task-card:active {
            cursor: grabbing !important;
        }

        .task-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 0.75rem;
        }

        .task-id {
            font-size: 0.75rem;
            font-family: var(--font-mono);
            color: var(--text-dim);
            font-weight: 600;
        }

        .task-title {
            font-size: 0.925rem;
            font-weight: 600;
            color: var(--text-main);
            line-height: 1.45;
            margin-bottom: 1rem;
        }

        .task-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            padding-top: 0.75rem;
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        .assignee-badge {
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .avatar-circle {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: linear-gradient(135deg, #06b6d4, #6366f1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.65rem;
            font-weight: 700;
            color: #fff;
        }

        /* Explanation Box */
        .info-panel {
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.9) 0%, rgba(22, 25, 34, 0.9) 100%);
            border: 1px solid rgba(6, 182, 212, 0.3);
            border-radius: var(--radius-lg);
            padding: 2rem;
            margin-top: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .info-panel h3 {
            font-size: 1.25rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .feature-bullets {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.25rem;
            margin-top: 1.25rem;
        }

        .bullet-item {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            font-size: 0.9rem;
            color: var(--text-muted);
            line-height: 1.5;
        }

        .bullet-item strong {
            color: var(--text-main);
        }

        .code-pill {
            font-family: var(--font-mono);
            background: rgba(0, 0, 0, 0.4);
            padding: 0.15rem 0.45rem;
            border-radius: 4px;
            color: #38bdf8;
            font-size: 0.8rem;
        }
    </style>

    <!-- Hero Section -->
    <div class="kanban-hero">
        <div class="hero-pill">
            <span>⚡ SWITCH LIVE SPA</span> • <span>60 FPS OPTIMISTIC UI</span>
        </div>
        <h1>Interactive Drag & Drop & Multi-Table Sort</h1>
        <p>
            Experience seamless 0ms UI latency reordering. Drag table rows to reprioritize roadmap items, or transfer task cards between Kanban columns with automated background synchronization.
        </p>
    </div>

    <!-- Demo 1: Single Table Row Sorting -->
    <div class="section-header">
        <div class="section-title">
            <span>📊 Demo 1: Single Table Row Reordering</span>
            <span class="badge-count">switch-sortable</span>
        </div>
        <div class="tag-pill tag-cyan">
            Drag any row by the grab handle (☰) to reorder
        </div>
    </div>

    <div class="table-card">
        <table class="table-custom">
            <thead>
                <tr>
                    <th style="width: 50px;">Move</th>
                    <th style="width: 100px;">ID</th>
                    <th>Feature Title</th>
                    <th>Category</th>
                    <th>Priority</th>
                    <th>Estimate</th>
                    <th>Status</th>
                </tr>
            </thead>
            <!-- Switch Live Single Table Sortable Directive -->
            <tbody id="roadmap-table-body" switch-sortable="/api/kanban/reorder-table" switch-debounce="250" switch-handle=".drag-handle">
                @foreach($tableItems as $item)
                    <tr data-id="{{ $item['id'] }}" draggable="true">
                        <td style="text-align: center;">
                            <span class="drag-handle" title="Drag to reorder">⋮⋮</span>
                        </td>
                        <td>
                            <span class="code-pill">{{ $item['id'] }}</span>
                        </td>
                        <td>
                            <strong>{{ $item['title'] }}</strong>
                        </td>
                        <td>
                            <span class="tag-pill tag-indigo">{{ $item['category'] }}</span>
                        </td>
                        <td>
                            @if($item['priority'] === 'Critical')
                                <span class="tag-pill tag-rose">{{ $item['priority'] }}</span>
                            @elseif($item['priority'] === 'High')
                                <span class="tag-pill tag-amber">{{ $item['priority'] }}</span>
                            @else
                                <span class="tag-pill tag-cyan">{{ $item['priority'] }}</span>
                            @endif
                        </td>
                        <td style="font-family: var(--font-mono); color: var(--text-muted);">
                            {{ $item['estimate'] }}
                        </td>
                        <td>
                            <span class="tag-pill tag-emerald">{{ $item['status'] }}</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Demo 2: Multi-Table / Kanban Board Cross-Transfer -->
    <div class="section-header">
        <div class="section-title">
            <span>📋 Demo 2: Multi-Table / Cross-Column Kanban Transfer</span>
            <span class="badge-count">switch-sortable-group</span>
        </div>
        <div class="tag-pill tag-indigo">
            Drag cards between columns to change state
        </div>
    </div>

    <div class="kanban-board">

        <!-- Column 1: Sprint Backlog -->
        <div class="kanban-column">
            <div class="column-header">
                <div class="column-title">
                    <span>📋 Backlog</span>
                    <span class="badge-count">{{ count($backlogTasks) }}</span>
                </div>
                <span class="tag-pill tag-cyan">Queue</span>
            </div>
            <!-- Sortable Drop Container -->
            <div class="column-cards-container"
                 switch-sortable-group="kanban"
                 data-group="backlog"
                 switch-action="/api/kanban/move-card"
                 switch-debounce="300">
                @foreach($backlogTasks as $task)
                    <div class="task-card" data-id="{{ $task['id'] }}" draggable="true">
                        <div class="task-meta">
                            <span class="task-id">{{ $task['id'] }}</span>
                            <span class="tag-pill tag-{{ $task['tag_color'] }}">{{ $task['tag'] }}</span>
                        </div>
                        <div class="task-title">{{ $task['title'] }}</div>
                        <div class="task-footer">
                            <div class="assignee-badge">
                                <div class="avatar-circle">{{ substr($task['assignee'], 0, 1) }}</div>
                                <span>{{ $task['assignee'] }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Column 2: In Progress -->
        <div class="kanban-column">
            <div class="column-header">
                <div class="column-title">
                    <span>⚡ In Progress</span>
                    <span class="badge-count">{{ count($inProgressTasks) }}</span>
                </div>
                <span class="tag-pill tag-amber">WIP</span>
            </div>
            <!-- Sortable Drop Container -->
            <div class="column-cards-container"
                 switch-sortable-group="kanban"
                 data-group="in_progress"
                 switch-action="/api/kanban/move-card"
                 switch-debounce="300">
                @foreach($inProgressTasks as $task)
                    <div class="task-card" data-id="{{ $task['id'] }}" draggable="true">
                        <div class="task-meta">
                            <span class="task-id">{{ $task['id'] }}</span>
                            <span class="tag-pill tag-{{ $task['tag_color'] }}">{{ $task['tag'] }}</span>
                        </div>
                        <div class="task-title">{{ $task['title'] }}</div>
                        <div class="task-footer">
                            <div class="assignee-badge">
                                <div class="avatar-circle">{{ substr($task['assignee'], 0, 1) }}</div>
                                <span>{{ $task['assignee'] }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Column 3: Completed -->
        <div class="kanban-column">
            <div class="column-header">
                <div class="column-title">
                    <span>✅ Completed</span>
                    <span class="badge-count">{{ count($completedTasks) }}</span>
                </div>
                <span class="tag-pill tag-emerald">Done</span>
            </div>
            <!-- Sortable Drop Container -->
            <div class="column-cards-container"
                 switch-sortable-group="kanban"
                 data-group="completed"
                 switch-action="/api/kanban/move-card"
                 switch-debounce="300">
                @foreach($completedTasks as $task)
                    <div class="task-card" data-id="{{ $task['id'] }}" draggable="true">
                        <div class="task-meta">
                            <span class="task-id">{{ $task['id'] }}</span>
                            <span class="tag-pill tag-{{ $task['tag_color'] }}">{{ $task['tag'] }}</span>
                        </div>
                        <div class="task-title">{{ $task['title'] }}</div>
                        <div class="task-footer">
                            <div class="assignee-badge">
                                <div class="avatar-circle">{{ substr($task['assignee'], 0, 1) }}</div>
                                <span>{{ $task['assignee'] }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

    <!-- Architecture Explanation Box -->
    <div class="info-panel">
        <h3>⚡ How It Works Under the Hood</h3>
        <p style="color: var(--text-muted); font-size: 0.95rem; line-height: 1.6;">
            Unlike traditional AJAX or Livewire setups that wait for a full server round-trip or re-render large HTML blocks, Switch Live uses an <strong>Optimistic Client-Side Movement Pipeline</strong>:
        </p>

        <div class="feature-bullets">
            <div class="bullet-item">
                <div style="color: #22d3ee; font-size: 1.2rem;">🚀</div>
                <div>
                    <strong>0ms Instant Feedback:</strong> DOM elements reorder at 60 FPS under the user's cursor without waiting for server response.
                </div>
            </div>
            <div class="bullet-item">
                <div style="color: #34d399; font-size: 1.2rem;">📦</div>
                <div>
                    <strong>Sub-Kilobyte Payload:</strong> Only a tiny array of item IDs (<span class="code-pill">{ids: [...]}</span>) is transmitted in the background.
                </div>
            </div>
            <div class="bullet-item">
                <div style="color: #fbbf24; font-size: 1.2rem;">🛡️</div>
                <div>
                    <strong>Resilient Auto-Rollback:</strong> If the network drops or server returns an error, the item instantly snaps back to its original slot.
                </div>
            </div>
            <div class="bullet-item">
                <div style="color: #a855f7; font-size: 1.2rem;">⏱️</div>
                <div>
                    <strong>Debounced Batching:</strong> Rapid rearrangements are coalesced with <span class="code-pill">switch-debounce="250"</span> to prevent server flood.
                </div>
            </div>
        </div>
    </div>
</div>
</section>
