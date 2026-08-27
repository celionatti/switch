<layout name="layouts.app" />

<section name="title">{{ $title }}</section>

<section name="content">
<div class="showcase-page">
    <style>
        .showcase-page {
            max-width: 1200px;
            margin: 0 auto;
        }

        .showcase-hero {
            text-align: center;
            margin-bottom: 3rem;
            position: relative;
        }

        .showcase-hero h1 {
            font-size: 2.75rem;
            font-weight: 800;
            background: linear-gradient(135deg, #ffffff 0%, #38bdf8 50%, #818cf8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin: 0 0 1rem;
            letter-spacing: -0.03em;
        }

        .showcase-hero p {
            font-size: 1.15rem;
            color: var(--text-muted);
            max-width: 680px;
            margin: 0 auto;
            line-height: 1.6;
        }

        .showcase-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 2rem;
            margin-bottom: 3rem;
        }

        @media (max-width: 900px) {
            .showcase-grid {
                grid-template-columns: 1fr;
            }
        }

        .feature-card {
            background: rgba(30, 41, 59, 0.5);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: var(--radius-lg);
            padding: 2rem;
            position: relative;
            overflow: hidden;
            transition: var(--transition-smooth);
        }

        .feature-card:hover {
            border-color: rgba(56, 189, 248, 0.35);
            box-shadow: 0 12px 36px rgba(0, 0, 0, 0.45);
            transform: translateY(-2px);
        }

        .feature-card-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        }

        .feature-icon-badge {
            width: 46px;
            height: 46px;
            border-radius: var(--radius-md);
            background: rgba(56, 189, 248, 0.12);
            border: 1px solid rgba(56, 189, 248, 0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
        }

        .feature-title-group h3 {
            margin: 0 0 0.25rem;
            font-size: 1.25rem;
            font-weight: 700;
            color: #ffffff;
        }

        .feature-title-group .feature-badge {
            font-size: 0.72rem;
            font-family: var(--font-mono);
            color: var(--cyan-400);
            background: rgba(6, 182, 212, 0.1);
            padding: 0.15rem 0.5rem;
            border-radius: 4px;
            border: 1px solid rgba(6, 182, 212, 0.2);
        }

        .code-snippet-box {
            background: #090d16;
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: var(--radius-md);
            padding: 1rem;
            font-family: var(--font-mono);
            font-size: 0.8rem;
            color: #a5f3fc;
            overflow-x: auto;
            margin: 1rem 0;
            line-height: 1.5;
        }

        .stat-pills-row {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin: 1rem 0;
        }

        .stat-pill {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 6px;
            padding: 0.4rem 0.8rem;
            font-size: 0.82rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .stat-pill strong {
            color: #ffffff;
            font-family: var(--font-mono);
        }

        .stat-pill span {
            color: var(--text-dim);
        }

        .mock-items-list {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            margin: 1rem 0;
        }

        .mock-item-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.75rem 1rem;
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: var(--radius-md);
            font-size: 0.88rem;
        }

        .mock-user-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .mock-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, #38bdf8, #818cf8);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-weight: 700;
            font-size: 0.8rem;
        }

        .tree-node {
            padding: 0.4rem 0.8rem;
            margin: 0.3rem 0;
            border-left: 2px solid var(--cyan-400);
            background: rgba(6, 182, 212, 0.04);
            border-radius: 0 6px 6px 0;
            font-size: 0.82rem;
            font-family: var(--font-mono);
        }

        .tree-node.child {
            margin-left: 1.5rem;
            border-left-color: #818cf8;
            background: rgba(129, 140, 248, 0.04);
        }

        .btn-showcase-action {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.6rem 1.2rem;
            background: linear-gradient(135deg, #06b6d4, #2563eb);
            color: #ffffff;
            font-weight: 600;
            font-size: 0.85rem;
            border-radius: var(--radius-md);
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: var(--transition-smooth);
            margin-top: 0.5rem;
        }

        .btn-showcase-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 18px rgba(6, 182, 212, 0.4);
        }
    </style>

    <div class="showcase-hero">
        <span class="feature-badge" style="font-size: 0.85rem; padding: 0.3rem 0.8rem; margin-bottom: 1rem; display: inline-block;">
            ⚡ Switch Foundation Toolkit
        </span>
        <h1>Package & Subsystem Showcase</h1>
        <p>
            Experience the unified powerhouse features built directly into Switch Framework — Context API, Mock Data, Collections, Bridge Webhooks, Mailers, and Passwordless Authentication.
        </p>
    </div>

    <div class="showcase-grid">
        <!-- 1. Context API Showcase -->
        <div class="feature-card">
            <div class="feature-card-header">
                <div class="feature-icon-badge">🧠</div>
                <div class="feature-title-group">
                    <h3>Context API Subsystem</h3>
                    <span class="feature-badge">context() • Scoped Stacks</span>
                </div>
            </div>

            <p style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.5;">
                Hierarchical scoped context management with dot-notation querying and automated client-side state hydration.
            </p>

            <div class="stat-pills-row">
                <div class="stat-pill">
                    <span>Theme:</span>
                    <strong>{{ $currentTheme }}</strong>
                </div>
                <div class="stat-pill">
                    <span>Tenant:</span>
                    <strong>{{ $tenantName }}</strong>
                </div>
            </div>

            <div class="code-snippet-box">
// Provide Server Context:
Context::provide('app.tenant', ['name' => 'Acme Cloud']);
$tenant = Context::use('app.tenant.name'); // "Acme Cloud"

// Share with Frontend Client:
Context::share('client.user', ['name' => 'Sarah Connor']);
            </div>

            <details style="margin-top: 1rem;">
                <summary style="font-size: 0.85rem; color: var(--cyan-400); cursor: pointer; font-weight: 600;">
                    View Client Context JSON Payload
                </summary>
                <pre class="code-snippet-box" style="margin-top: 0.5rem; max-height: 180px;">{{ $clientContextJson }}</pre>
            </details>
        </div>

        <!-- 2. Data & Mocking Subsystem -->
        <div class="feature-card">
            <div class="feature-card-header">
                <div class="feature-icon-badge">🎲</div>
                <div class="feature-title-group">
                    <h3>Data & Mock Blueprints</h3>
                    <span class="feature-badge">data() • mock() • fake()</span>
                </div>
            </div>

            <p style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.5;">
                Generate instant realistic fixtures, load static localized dataset files, and test without external dependencies.
            </p>

            <div class="mock-items-list">
                <foreach items="$mockUsers" as="$u">
                    <div class="mock-item-row">
                        <div class="mock-user-info">
                            <div class="mock-avatar">{{ strtoupper(substr($u.name, 0, 1)) }}</div>
                            <div>
                                <div style="font-weight: 600; color: #ffffff;">{{ $u.name }}</div>
                                <div style="color: var(--text-dim); font-size: 0.78rem;">{{ $u.email }}</div>
                            </div>
                        </div>
                        <span class="stat-pill" style="font-size: 0.75rem;">{{ $u.role }}</span>
                    </div>
                </foreach>
            </div>

            <div class="stat-pills-row">
                <div class="stat-pill">
                    <span>Fake UUID:</span>
                    <strong style="font-size: 0.75rem;">{{ substr($sampleFakes.uuid, 0, 13) }}...</strong>
                </div>
                <div class="stat-pill">
                    <span>Fake Price:</span>
                    <strong>${{ $sampleFakes.price }}</strong>
                </div>
            </div>
        </div>

        <!-- 3. Collection Engine Showcase -->
        <div class="feature-card">
            <div class="feature-card-header">
                <div class="feature-icon-badge">⚡</div>
                <div class="feature-title-group">
                    <h3>Fluent Collection Engine</h3>
                    <span class="feature-badge">collect() • Tree Transformation</span>
                </div>
            </div>

            <p style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.5;">
                High-performance fluent pipelines with higher-order proxies, multi-column sorting, and hierarchical tree generation.
            </p>

            <div class="stat-pills-row">
                <div class="stat-pill">
                    <span>Total Employees:</span>
                    <strong>{{ $employeeCount }}</strong>
                </div>
                <div class="stat-pill">
                    <span>Active:</span>
                    <strong>{{ $activeCount }}</strong>
                </div>
                <div class="stat-pill">
                    <span>Avg Active Salary:</span>
                    <strong>${{ number_format($salarySummary.average) }}</strong>
                </div>
            </div>

            <div style="margin: 1rem 0;">
                <div style="font-size: 0.8rem; color: var(--text-dim); margin-bottom: 0.5rem;">Org Chart Hierarchy via <code>toTree('id', 'parent_id')</code>:</div>
                <foreach items="$orgTree" as="$lead">
                    <div class="tree-node">
                        👔 {{ $lead.name }} ({{ $lead.department }})
                    </div>
                    <if cond="!empty($lead.children)">
                        <foreach items="$lead.children" as="$sub">
                            <div class="tree-node child">
                                └─ 👤 {{ $sub.name }} - ${{ number_format($sub.salary) }}
                            </div>
                        </foreach>
                    </if>
                </foreach>
            </div>
        </div>

        <!-- 4. Bridge Webhooks Subsystem -->
        <div class="feature-card">
            <div class="feature-card-header">
                <div class="feature-icon-badge">🌉</div>
                <div class="feature-title-group">
                    <h3>Bridge Webhook Dispatcher</h3>
                    <span class="feature-badge">HMAC-SHA256 • Idempotency</span>
                </div>
            </div>

            <p style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.5;">
                Signed outbound webhook delivery with cryptographic SHA-256 HMAC signatures, replay attack prevention, and retry backoff.
            </p>

            <div class="code-snippet-box" style="font-size: 0.75rem;">
// Outbound Webhook Verification Header:
X-Switch-Signature: {{ substr($webhookSignature, 0, 32) }}...
X-Switch-Timestamp: {{ time() }}
X-Switch-Idempotency: idem_4f9a88c2
            </div>

            <div class="stat-pills-row">
                <div class="stat-pill">
                    <span>Status:</span>
                    <strong style="color: var(--emerald-400);">Verified ✓</strong>
                </div>
                <div class="stat-pill">
                    <span>Algorithm:</span>
                    <strong>HMAC-SHA256</strong>
                </div>
            </div>
        </div>

        <!-- 5. Mailer Subsystem Preview -->
        <div class="feature-card">
            <div class="feature-card-header">
                <div class="feature-icon-badge">📬</div>
                <div class="feature-title-group">
                    <h3>Mailer & Mailable Engine</h3>
                    <span class="feature-badge">mail_manager() • Queueable</span>
                </div>
            </div>

            <p style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.5;">
                Zero-dependency MIME email compiler supporting automatic background queueing, attachments, and dual HTML/text rendering.
            </p>

            <div class="mock-item-row" style="margin: 1rem 0;">
                <div>
                    <div style="font-weight: 600; color: #ffffff; font-size: 0.85rem;">Subject: {{ $mailableSubject }}</div>
                    <div style="color: var(--text-dim); font-size: 0.78rem;">To: client@example.com • Transports: SMTP, Sendmail, Log, Array</div>
                </div>
                <span class="stat-pill" style="color: var(--cyan-400);">RFC 2822</span>
            </div>

            <div class="code-snippet-box">
(new Mailable())
    ->to('client@example.com')
    ->subject('Welcome!')
    ->view('emails.welcome', ['name' => 'Sarah'])
    ->queue();
            </div>
        </div>

        <!-- 6. Passwordless Authentication Subsystem -->
        <div class="feature-card">
            <div class="feature-card-header">
                <div class="feature-icon-badge">🔐</div>
                <div class="feature-title-group">
                    <h3>Passwordless Magic Links</h3>
                    <span class="feature-badge">passwordless() • Rate Limited</span>
                </div>
            </div>

            <p style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.5;">
                Single-use cryptographic magic links with automatic expiration, rate limiting per email, and zero-password authentication.
            </p>

            <div class="stat-pills-row">
                <div class="stat-pill">
                    <span>Token Entropy:</span>
                    <strong>64-Hex (256-bit)</strong>
                </div>
                <div class="stat-pill">
                    <span>Expiry:</span>
                    <strong>15 Minutes</strong>
                </div>
                <div class="stat-pill">
                    <span>Rate Limit:</span>
                    <strong>5 Req / Hour</strong>
                </div>
            </div>

            <div class="code-snippet-box" style="font-size: 0.75rem; word-break: break-all;">
// Generated Single-Use Magic Link:
{{ $sampleVerifyUrl }}
            </div>

            <a href="/posts" class="btn-showcase-action" switch-to>
                <span>Explore Blog & Flow Demo →</span>
            </a>
        </div>
    </div>
</div>
</section>
</layout>
