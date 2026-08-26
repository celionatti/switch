<layout name="layouts.app" />

<section name="title">Blog Posts & Articles — Switch Framework</section>

<section name="content">
    <php>
        $posts = $posts ?? [];
        $paginator = $paginator ?? null;
        $currentTag = $currentTag ?? null;
    </php>

    <style>
        .posts-page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1.5rem;
            margin-bottom: 2.5rem;
            flex-wrap: wrap;
        }

        .posts-title-group h1 {
            font-size: clamp(1.8rem, 3.5vw, 2.5rem);
            font-weight: 800;
            letter-spacing: -0.03em;
            color: #ffffff;
            line-height: 1.2;
            margin-bottom: 0.35rem;
        }

        .posts-title-group p {
            color: var(--text-muted);
            font-size: 0.95rem;
        }

        .btn-create-post {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.4rem;
            border-radius: var(--radius-md);
            background: linear-gradient(135deg, #06b6d4, #2563eb);
            color: #ffffff;
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            box-shadow: 0 4px 18px rgba(6, 182, 212, 0.35);
            transition: var(--transition-smooth);
        }

        .btn-create-post:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 24px rgba(6, 182, 212, 0.5);
        }

        .posts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
            gap: 1.75rem;
            margin-bottom: 3.5rem;
        }

        .post-card {
            background: var(--bg-surface);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-lg);
            padding: 1.75rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: var(--transition-smooth);
            position: relative;
            text-decoration: none;
            color: inherit;
        }

        .post-card:hover {
            border-color: var(--border-hover);
            transform: translateY(-4px);
            box-shadow: 0 16px 36px rgba(0, 0, 0, 0.45);
        }

        .post-top-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .post-badge {
            font-size: 0.7rem;
            font-family: var(--font-mono);
            font-weight: 600;
            padding: 0.2rem 0.55rem;
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

        .featured-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            font-size: 0.7rem;
            color: var(--cyan-400);
            font-family: var(--font-mono);
            font-weight: 600;
        }

        .post-title {
            font-size: 1.25rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            color: #ffffff;
            margin-bottom: 0.75rem;
            line-height: 1.35;
        }

        .post-card:hover .post-title {
            color: var(--cyan-400);
        }

        .post-excerpt {
            color: var(--text-muted);
            font-size: 0.9rem;
            line-height: 1.6;
            margin-bottom: 1.25rem;
            flex-grow: 1;
        }

        .post-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 0.4rem;
            margin-bottom: 1.25rem;
        }

        .post-tag {
            font-size: 0.72rem;
            font-family: var(--font-mono);
            padding: 0.15rem 0.5rem;
            border-radius: var(--radius-sm);
            background: var(--bg-elevated);
            color: var(--text-dim);
            border: 1px solid var(--border-subtle);
        }

        .post-footer-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 1rem;
            border-top: 1px solid var(--border-subtle);
            font-size: 0.8rem;
            color: var(--text-dim);
        }

        .read-more-link {
            color: var(--cyan-400);
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            font-size: 0.85rem;
        }

        .empty-state {
            text-align: center;
            padding: 4rem 1.5rem;
            background: var(--bg-surface);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-lg);
            max-width: 600px;
            margin: 2rem auto;
        }

        .pagination-bar {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            margin-top: 2rem;
        }

        .page-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.6rem 1.2rem;
            border-radius: var(--radius-md);
            background: var(--bg-surface);
            border: 1px solid var(--border-subtle);
            color: var(--text-main);
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            transition: var(--transition-smooth);
        }

        .page-btn:hover:not(.disabled) {
            border-color: var(--cyan-400);
            color: var(--cyan-400);
        }

        .page-btn.disabled {
            opacity: 0.4;
            pointer-events: none;
        }
    </style>

    <div class="posts-page-header">
        <div class="posts-title-group">
            <h1>Blog & Knowledge Base</h1>
            <p>Demonstrating Active Record, Finite State Machines, and Domain Actions in Switch.</p>
        </div>
        <a href="/posts/create" class="btn-create-post" switch-to>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 5v14M5 12h14"/>
            </svg>
            <span>Write New Post</span>
        </a>
    </div>

    <if cond="!empty($posts)">
        <div class="posts-grid">
            <foreach items="$posts" as="$post">
                <a href="/posts/{{ $post.id }}" class="post-card" switch-to switch-prefetch>
                    <div>
                        <div class="post-top-meta">
                            <span class="post-badge status-{{ $post.status }}">{{ $post.status }}</span>
                            <if cond="!empty($post.is_featured)">
                                <span class="featured-pill">★ Featured</span>
                            </if>
                        </div>
                        <h2 class="post-title">{{ $post.title }}</h2>
                        <p class="post-excerpt">{{ $post.content }}</p>
                    </div>

                    <div>
                        <if cond="!empty($post.tags)">
                            <div class="post-tags">
                                <foreach items="$post.tags" as="$tag">
                                    <span class="post-tag">#{{ $tag }}</span>
                                </foreach>
                            </div>
                        </if>
                        <div class="post-footer-meta">
                            <span>Post #{{ $post.id }}</span>
                            <span class="read-more-link">
                                Read Post
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M5 12h14M12 5l7 7-7 7"/>
                                </svg>
                            </span>
                        </div>
                    </div>
                </a>
            </foreach>
        </div>

        <if cond="$paginator && $paginator->lastPage > 1">
            <div class="pagination-bar">
                <a href="/posts?page={{ $paginator->currentPage - 1 }}" class="page-btn {{ $paginator->currentPage <= 1 ? 'disabled' : '' }}" switch-to>
                    ← Previous
                </a>
                <span style="font-size: 0.85rem; color: var(--text-dim); font-family: var(--font-mono);">
                    Page {{ $paginator->currentPage }} of {{ $paginator->lastPage }}
                </span>
                <a href="/posts?page={{ $paginator->currentPage + 1 }}" class="page-btn {{ $paginator->currentPage >= $paginator->lastPage ? 'disabled' : '' }}" switch-to>
                    Next →
                </a>
            </div>
        </if>
    <else>
        <div class="empty-state">
            <h3 style="color: #ffffff; margin-bottom: 0.5rem;">No posts published yet</h3>
            <p style="color: var(--text-muted); margin-bottom: 1.5rem;">Be the first to publish a post using the Switch Action Engine.</p>
            <a href="/posts/create" class="btn-create-post" switch-to>Create Post</a>
        </div>
    </if>
</section>
