<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Laravel'))</title>
    @if (file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    <style>
        :root {
            color-scheme: light;
            --bg: #eef3f8;
            --panel: #ffffff;
            --panel-soft: #f6f8fc;
            --text: #162033;
            --muted: #667085;
            --accent: #1f4e79;
            --accent-dark: #153553;
            --border: #d8e0ea;
            --danger: #a12626;
            --success: #0f766e;
            --nav: #0f172a;
            --nav-muted: #94a3b8;
        }

        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background:
                radial-gradient(circle at top right, rgba(31, 78, 121, 0.14), transparent 24%),
                radial-gradient(circle at bottom left, rgba(15, 118, 110, 0.08), transparent 18%),
                linear-gradient(180deg, #f9fbfd, var(--bg));
            color: var(--text);
        }
        a { color: inherit; }
        code {
            font-family: Consolas, "Courier New", monospace;
            font-size: 0.92em;
        }
        .shell {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 16px;
        }
        .shell.admin-shell {
            align-items: stretch;
            justify-content: stretch;
            padding: 0;
        }
        .card {
            width: min(720px, 100%);
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(15, 23, 42, 0.08);
            padding: 28px;
        }
        .dashboard-shell {
            min-height: 100vh;
            width: 100%;
            display: grid;
            grid-template-columns: 280px minmax(0, 1fr);
        }
        .sidebar {
            background:
                linear-gradient(180deg, rgba(255,255,255,0.04), rgba(255,255,255,0)),
                var(--nav);
            color: #f8fafc;
            padding: 28px 20px;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }
        .sidebar-brand {
            font-size: 1.35rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }
        .sidebar-copy {
            color: var(--nav-muted);
            line-height: 1.5;
            margin: 8px 0 0;
        }
        .nav-group {
            display: grid;
            gap: 8px;
        }
        .nav-links {
            display: grid;
            gap: 8px;
        }
        .nav-label {
            color: var(--nav-muted);
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }
        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 14px;
            border-radius: 14px;
            color: #dbe6f3;
            text-decoration: none;
            border: 1px solid transparent;
            transition: background-color 120ms ease, border-color 120ms ease, color 120ms ease;
        }
        .nav-link:hover {
            background: rgba(255,255,255,0.06);
            border-color: rgba(255,255,255,0.08);
        }
        .nav-link.active {
            background: linear-gradient(135deg, rgba(31, 78, 121, 0.9), rgba(15, 118, 110, 0.85));
            color: #ffffff;
            border-color: rgba(255,255,255,0.1);
            box-shadow: 0 14px 32px rgba(0, 0, 0, 0.18);
        }
        .sidebar-footer {
            margin-top: auto;
            display: grid;
            gap: 12px;
            padding-top: 16px;
            border-top: 1px solid rgba(148, 163, 184, 0.18);
        }
        .sidebar-user {
            font-size: 0.95rem;
            color: #e2e8f0;
        }
        .sidebar-user span {
            display: block;
            color: var(--nav-muted);
            font-size: 0.82rem;
            margin-top: 4px;
        }
        .content-shell {
            padding: 28px;
        }
        .content-card {
            min-height: calc(100vh - 56px);
            background: rgba(255,255,255,0.82);
            border: 1px solid rgba(216, 224, 234, 0.9);
            border-radius: 24px;
            box-shadow: 0 28px 60px rgba(15, 23, 42, 0.08);
            padding: 28px;
            backdrop-filter: blur(10px);
        }
        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin-bottom: 24px;
        }
        .brand {
            font-size: 1.5rem;
            font-weight: 800;
            letter-spacing: 0.03em;
        }
        .muted { color: var(--muted); }
        .stack { display: grid; gap: 16px; }
        .stack-lg { display: grid; gap: 24px; }
        .metric-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
        }
        .metric-card {
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 18px;
            background: linear-gradient(180deg, #ffffff, var(--panel-soft));
        }
        a.metric-card {
            display: block;
            text-decoration: none;
            color: inherit;
            transition: transform 120ms ease, box-shadow 120ms ease, border-color 120ms ease;
        }
        a.metric-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
            border-color: #bdd1e4;
        }
        .metric-value {
            font-size: 2rem;
            font-weight: 800;
            line-height: 1;
            margin-top: 10px;
        }
        .performance-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-top: 18px;
        }
        .performance-card {
            background: linear-gradient(180deg, #ffffff, var(--panel-soft));
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 20px;
            min-height: 120px;
        }
        .status-chip {
            display: inline-flex;
            align-items: center;
            padding: 8px 12px;
            border-radius: 999px;
            background: #f8f0d8;
            color: #8a5c12;
            font-weight: 700;
            margin-top: 10px;
        }
        .progress-track {
            background: #e8f1fb;
            border-radius: 999px;
            height: 14px;
            overflow: hidden;
            margin-top: 14px;
        }
        .progress-fill {
            height: 100%;
            width: 65%;
            background: linear-gradient(90deg, #1f4e79, #0f766e);
            border-radius: 999px;
            transition: width 300ms ease;
        }
        .progress-label {
            display: flex;
            justify-content: space-between;
            margin-top: 8px;
            color: var(--muted);
            font-size: 0.95rem;
        }
        .field { display: grid; gap: 8px; }
        .field label { font-weight: 700; }
        .field input, .field select, .field textarea {
            width: 100%;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 12px 14px;
            font: inherit;
            background: #fff;
        }
        .field textarea {
            resize: vertical;
            min-height: 120px;
        }
        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: center;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 0;
            border-radius: 999px;
            padding: 12px 18px;
            font: inherit;
            text-decoration: none;
            cursor: pointer;
            background: var(--accent);
            color: #fffaf4;
        }
        .btn.secondary {
            background: transparent;
            color: var(--accent-dark);
            border: 1px solid var(--border);
        }
        .btn.ghost {
            background: rgba(255,255,255,0.08);
            color: #ffffff;
            border: 1px solid rgba(255,255,255,0.12);
        }
        .error {
            color: var(--danger);
            font-size: 0.95rem;
        }
        .panel {
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 18px;
            background: rgba(255,255,255,0.72);
        }
        .table-wrap {
            overflow-x: auto;
        }
        .mobile-only {
            display: none;
        }
        .desktop-only {
            display: block;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px 10px;
            border-bottom: 1px solid var(--border);
            text-align: left;
            vertical-align: top;
        }
        th {
            font-size: 0.95rem;
        }
        .pill {
            display: inline-flex;
            align-items: center;
            padding: 6px 10px;
            border-radius: 999px;
            background: #e8f1fb;
            color: var(--accent-dark);
            font-size: 0.82rem;
            font-weight: 700;
        }
        .question-card {
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 16px;
            background: linear-gradient(180deg, #ffffff, var(--panel-soft));
            display: grid;
            gap: 12px;
        }
        .choice-list {
            display: grid;
            gap: 10px;
        }
        .choice-item {
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 12px 14px;
            background: rgba(255,255,255,0.8);
        }
        .section-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: center;
            justify-content: space-between;
        }
        @media (max-width: 980px) {
            .dashboard-shell {
                grid-template-columns: 1fr;
            }
            .sidebar {
                gap: 16px;
                padding: 18px 16px;
            }
            .nav-links {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
            .nav-link {
                justify-content: center;
                text-align: center;
            }
            .content-shell {
                padding: 16px;
            }
            .content-card {
                min-height: auto;
                padding: 20px;
            }
            .metric-grid {
                grid-template-columns: 1fr;
            }
        }
        @media (max-width: 640px) {
            .shell {
                padding: 16px 10px;
            }
            .card {
                padding: 18px;
                border-radius: 16px;
            }
            .content-shell {
                padding: 10px;
            }
            .content-card {
                padding: 16px;
                border-radius: 18px;
            }
            .topbar {
                flex-direction: column;
                align-items: flex-start;
                margin-bottom: 18px;
            }
            .actions,
            .section-actions {
                align-items: stretch;
            }
            .actions > *,
            .section-actions > * {
                width: 100%;
            }
            .btn,
            .btn.secondary,
            .btn.ghost {
                width: 100%;
            }
            .nav-links {
                grid-template-columns: 1fr;
            }
            .mobile-only {
                display: block;
            }
            .desktop-only {
                display: none;
            }
            .brand {
                font-size: 1.3rem;
            }
            .metric-value {
                font-size: 1.75rem;
            }
        }
    </style>
</head>
<body>
    @php
        $isAdminArea = request()->routeIs('admin.*');
        $user = auth()->user();
    @endphp

    <div class="shell{{ $isAdminArea ? ' admin-shell' : '' }}">
        @if ($isAdminArea && $user?->isAdmin())
            <div class="dashboard-shell">
                <aside class="sidebar">
                    <div>
                        <div class="sidebar-brand">CSE Reviewer</div>
                        <p class="sidebar-copy">Administrative console for question intake, review workflows, and access control.</p>
                    </div>

                    <div class="nav-group">
                        <div class="nav-label">Navigation</div>
                        <div class="nav-links">
                            <a class="nav-link{{ request()->routeIs('admin.dashboard') ? ' active' : '' }}" href="{{ route('admin.dashboard') }}">Dashboard</a>
                            <a class="nav-link{{ request()->routeIs('admin.questions.*') ? ' active' : '' }}" href="{{ route('admin.questions.index') }}">Questions</a>
                            <a class="nav-link{{ request()->routeIs('admin.payments.*') ? ' active' : '' }}" href="{{ route('admin.payments.index') }}">Payments</a>
                            <a class="nav-link{{ request()->routeIs('admin.users.*') ? ' active' : '' }}" href="{{ route('admin.users.create') }}">Admin Accounts</a>
                        </div>
                    </div>

                    <div class="sidebar-footer">
                        <div class="sidebar-user">
                            {{ $user->name }}
                            <span>{{ $user->email }}</span>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="btn ghost" type="submit">Log out</button>
                        </form>
                    </div>
                </aside>

                <main class="content-shell">
                    <div class="content-card">
                        @if (session('status'))
                            <div class="session-status" data-type="success" style="display: none;">{{ session('status') }}</div>
                        @endif
                        @if ($errors->any())
                            <div class="session-status" data-type="error" style="display: none;">{{ $errors->first() }}</div>
                        @endif
                        @yield('content')
                    </div>
                </main>
            </div>
        @else
            <main class="card">
                @if (session('status'))
                    <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 8px; margin-bottom: 16px;">{{ session('status') }}</div>
                @endif
                @if ($errors->any())
                    <div style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 8px; margin-bottom: 16px;">{{ $errors->first() }}</div>
                @endif
                @yield('content')
            </main>
        @endif
    </div>
</body>
</html>
