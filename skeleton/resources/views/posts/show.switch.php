<layout name="layouts.app" />

<section name="title">{{ $post.title }} — Switch Framework</section>

<section name="content">
    <php>
        $post = $post ?? null;
        $audits = $audits ?? [];
        $canPublish = $canPublish ?? false;
        $canArchive = $canArchive ?? false;
        $canDraft = $canDraft ?? false;
    </php>

    <style>
        .post-detail-container {
            max-width: 860px;
            margin: 1rem auto 4rem;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.9rem;
            margin-bottom: 1.75rem;
            transition: var(--transition-smooth);
        }

        .back-link:hover {
            color: var(--cyan-400);
        }

        .post-article-card {
            background: var(--bg-surface);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-lg);
            padding: 2.5rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
            margin-bottom: 2rem;
        }

        .article-header {
            margin-bottom: 2rem;
            border-bottom: 1px solid var(--border-subtle);
            padding-bottom: 1.5rem;
        }

        .article-meta-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1rem;
            flex-wrap: wrap;
        }

        .meta-badges {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .post-badge {
            font-size: 0.72rem;
            font-family: var(--font-mono);
            font-weight: 600;
            padding: 0.25rem 0.65rem;
            border-radius: var(--radius-full);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .status-published {
            background: rgba(16, 185, 129, 0.12);
            color: var(--emerald-400);
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .status-draft {
            background: rgba(245, 158, 11, 0.12);
            color: var(--amber-400);
            border: 1px solid rgba(245, 158, 11, 0.3);
        }

        .status-archived {
            background: rgba(99, 102, 241, 0.12);
            color: var(--indigo-500);
            border: 1px solid rgba(99, 102, 241, 0.3);
        }

        .article-title {
            font-size: clamp(1.8rem, 4vw, 2.5rem);
            font-weight: 800;
            letter-spacing: -0.03em;
            color: #ffffff;
            line-height: 1.2;
            margin-bottom: 0.75rem;
        }

        .article-subtitle-meta {
            display: flex;
            align-items: center;
            gap: 1rem;
            font-size: 0.85rem;
            color: var(--text-dim);
            font-family: var(--font-mono);
        }

        .article-body {
            color: var(--text-main);
            font-size: 1.05rem;
            line-height: 1.8;
            margin-bottom: 2rem;
            white-space: pre-line;
        }

        .article-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border-subtle);
        }

        .article-tag {
            font-size: 0.8rem;
            font-family: var(--font-mono);
            padding: 0.25rem 0.75rem;
            border-radius: var(--radius-sm);
            background: var(--bg-elevated);
            color: var(--cyan-400);
            border: 1px solid var(--border-subtle);
        }

        /* Flow & Controls Card */
        .controls-card {
            background: var(--bg-surface);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-lg);
            padding: 1.75rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1.5rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }

        .flow-state-info {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .flow-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--text-dim);
            font-weight: 700;
        }

        .flow-current {
            font-size: 1.1rem;
            font-weight: 700;
            color: #ffffff;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .flow-actions-group {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .btn-flow {
            padding: 0.55rem 1.1rem;
            border-radius: var(--radius-md);
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            border: 1px solid var(--border-subtle);
            background: var(--bg-elevated);
            color: var(--text-main);
            transition: var(--transition-smooth);
        }

        .btn-flow:hover {
            transform: translateY(-2px);
        }

        .btn-publish {
            background: rgba(16, 185, 129, 0.15);
            border-color: rgba(16, 185, 129, 0.4);
            color: var(--emerald-400);
        }

        .btn-publish:hover {
            background: rgba(16, 185, 129, 0.25);
            box-shadow: 0 4px 16px rgba(16, 185, 129, 0.3);
        }

        .btn-archive {
            background: rgba(99, 102, 241, 0.15);
            border-color: rgba(99, 102, 241, 0.4);
            color: var(--indigo-500);
        }

        .btn-archive:hover {
            background: rgba(99, 102, 241, 0.25);
        }

        .btn-delete {
            background: rgba(239, 68, 68, 0.12);
            border-color: rgba(239, 68, 68, 0.3);
            color: #ef4444;
        }

        .btn-delete:hover {
            background: rgba(239, 68, 68, 0.25);
        }

        /* Audit Trail Card */
        .audit-card {
            background: var(--bg-surface);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-lg);
            padding: 2rem;
        }

        .audit-card h3 {
            font-size: 1.15rem;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .audit-timeline {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            position: relative;
            padding-left: 1.5rem;
        }

        .audit-timeline::before {
            content: '';
            position: absolute;
            top: 6px;
            bottom: 6px;
            left: 5px;
            width: 2px;
            background: var(--border-subtle);
        }

        .audit-item {
            position: relative;
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .audit-item::before {
            content: '';
            position: absolute;
            left: -1.5rem;
            top: 5px;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: var(--cyan-400);
            box-shadow: 0 0 8px var(--cyan-glow);
        }

        .audit-event-name {
            font-weight: 600;
            font-size: 0.88rem;
            color: #ffffff;
            font-family: var(--font-mono);
        }

        .audit-time {
            font-size: 0.75rem;
            color: var(--text-dim);
            font-family: var(--font-mono);
        }
    </style>

    <div class="post-detail-container">
        <a href="/posts" class="back-link" switch-to>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
            <span>Back to All Posts</span>
        </a>

        <!-- Main Article -->
        <article class="post-article-card">
            <header class="article-header">
                <div class="article-meta-row">
                    <div class="meta-badges">
                        <span class="post-badge status-{{ $post.status }}">{{ $post.status }}</span>
                        <if cond="!empty($post.is_featured)">
                            <span style="color: var(--cyan-400); font-size: 0.8rem; font-weight: 600; font-family: var(--font-mono);">★ Featured Article</span>
                        </if>
                    </div>
                    <span style="font-size: 0.8rem; color: var(--text-dim); font-family: var(--font-mono);">Post #{{ $post.id }}</span>
                </div>

                <h1 class="article-title">{{ $post.title }}</h1>

                <div class="article-subtitle-meta">
                    <span>Slug: /{{ $post.slug }}</span>
                    <span>•</span>
                    <span>Created: {{ $post.created_at ?? 'Just now' }}</span>
                </div>
            </header>

            <div class="article-body">
                {{ $post.content }}
            </div>

            <if cond="!empty($post.tags)">
                <div class="article-tags">
                    <foreach items="$post.tags" as="$tag">
                        <span class="article-tag">#{{ $tag }}</span>
                    </foreach>
                </div>
            </if>
        </article>

        <!-- Finite State Machine (Flow) & Action Controls -->
        <div class="controls-card">
            <div class="flow-state-info">
                <span class="flow-label">Finite State Machine (Flow)</span>
                <span class="flow-current">
                    Status: <span class="post-badge status-{{ $post.status }}">{{ $post.status }}</span>
                </span>
            </div>

            <div class="flow-actions-group">
                <if cond="$canPublish">
                    <form action="/posts/{{ $post.id }}/publish" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn-flow btn-publish">✓ Publish Post</button>
                    </form>
                </if>

                <if cond="$canArchive">
                    <form action="/posts/{{ $post.id }}/archive" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn-flow btn-archive">📦 Archive Post</button>
                    </form>
                </if>

                <form action="/posts/{{ $post.id }}/delete" method="POST" style="display: inline;" onsubmit="return confirm('Soft-delete this post?');">
                    @csrf
                    <button type="submit" class="btn-flow btn-delete">🗑 Delete</button>
                </form>
            </div>
        </div>

        <!-- Audit Trail Timeline -->
        <div class="audit-card">
            <h3>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                </svg>
                <span>Audit Trail History (HasAuditTrail)</span>
            </h3>

            <if cond="!empty($audits)">
                <div class="audit-timeline">
                    <foreach items="$audits" as="$audit">
                        <div class="audit-item">
                            <span class="audit-event-name">Event: {{ $audit.event }}</span>
                            <span class="audit-time">{{ $audit.created_at ?? 'Recorded' }} • IP: {{ $audit.ip_address ?? '127.0.0.1' }}</span>
                        </div>
                    </foreach>
                </div>
            <else>
                <p style="color: var(--text-muted); font-size: 0.9rem;">No audit logs recorded for this post yet.</p>
            </if>
        </div>
    </div>
</section>
