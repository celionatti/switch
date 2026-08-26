<layout name="layouts.app" />

<section name="title">Create New Post — Switch Framework</section>

<section name="content">
    <style>
        .form-container {
            max-width: 720px;
            margin: 1rem auto 4rem;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
            transition: var(--transition-smooth);
        }

        .back-link:hover {
            color: var(--cyan-400);
        }

        .form-card {
            background: var(--bg-surface);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-lg);
            padding: 2.25rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        }

        .form-header {
            margin-bottom: 2rem;
            border-bottom: 1px solid var(--border-subtle);
            padding-bottom: 1.25rem;
        }

        .form-header h1 {
            font-size: 1.75rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            color: #ffffff;
            margin-bottom: 0.35rem;
        }

        .form-header p {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .form-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-main);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .form-input, .form-textarea {
            width: 100%;
            background: var(--bg-base);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-md);
            padding: 0.75rem 1rem;
            color: var(--text-main);
            font-family: var(--font-sans);
            font-size: 0.95rem;
            transition: var(--transition-smooth);
        }

        .form-input:focus, .form-textarea:focus {
            outline: none;
            border-color: var(--cyan-400);
            box-shadow: 0 0 0 3px rgba(6, 182, 212, 0.15);
        }

        .form-textarea {
            min-height: 160px;
            resize: vertical;
            line-height: 1.6;
        }

        .form-hint {
            font-size: 0.75rem;
            color: var(--text-dim);
            font-family: var(--font-mono);
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin: 1.5rem 0;
            padding: 0.85rem 1rem;
            background: var(--bg-base);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-md);
            cursor: pointer;
        }

        .checkbox-group input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: var(--cyan-400);
            cursor: pointer;
        }

        .checkbox-label {
            font-size: 0.9rem;
            font-weight: 500;
            color: var(--text-main);
            cursor: pointer;
        }

        .form-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 1rem;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border-subtle);
        }

        .btn-cancel {
            padding: 0.75rem 1.4rem;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: var(--transition-smooth);
        }

        .btn-cancel:hover {
            color: var(--text-main);
        }

        .btn-submit {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.75rem 1.6rem;
            border-radius: var(--radius-md);
            background: linear-gradient(135deg, #06b6d4, #2563eb);
            color: #ffffff;
            font-weight: 600;
            font-size: 0.95rem;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 18px rgba(6, 182, 212, 0.35);
            transition: var(--transition-smooth);
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 24px rgba(6, 182, 212, 0.5);
        }
    </style>

    <div class="form-container">
        <a href="/posts" class="back-link" switch-to>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
            <span>Back to All Posts</span>
        </a>

        <div class="form-card">
            <div class="form-header">
                <h1>Write a New Post</h1>
                <p>Created through CreatePostAction with automatic validation & audit log generation.</p>
            </div>

            <form action="/posts" method="POST" switch-to>
                @csrf

                <div class="form-group">
                    <label class="form-label" for="title">Post Title</label>
                    <input type="text" id="title" name="title" class="form-input" placeholder="e.g. Next-Gen Blade Directives in Switch" required autofocus />
                </div>

                <div class="form-group">
                    <label class="form-label" for="content">Content</label>
                    <textarea id="content" name="content" class="form-textarea" placeholder="Write your post content in full text or markdown..." required></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label" for="tags">Tags <span class="form-hint">Comma separated</span></label>
                    <input type="text" id="tags" name="tags" class="form-input" placeholder="php, framework, backend, tutorial" />
                </div>

                <label class="checkbox-group" for="is_featured">
                    <input type="checkbox" id="is_featured" name="is_featured" value="1" />
                    <span class="checkbox-label">Mark as Featured Article</span>
                </label>

                <div class="form-actions">
                    <a href="/posts" class="btn-cancel" switch-to>Cancel</a>
                    <button type="submit" class="btn-submit">
                        <span>Publish via Action</span>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12h14M12 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
