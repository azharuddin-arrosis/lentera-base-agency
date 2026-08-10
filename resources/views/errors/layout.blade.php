<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>@yield('code') · Lentera Project</title>
    <style>
        :root {
            --bg: #f8fafc; --card: #ffffff; --fg: #0f172a; --muted: #64748b;
            --border: #e2e8f0; --brand: #059669; --brand2: #16a34a;
        }
        @media (prefers-color-scheme: dark) {
            :root { --bg: #0a0f0d; --card: #111827; --fg: #f1f5f9; --muted: #94a3b8; --border: #1f2937; }
        }
        * { box-sizing: border-box; }
        body {
            margin: 0; min-height: 100vh; display: flex; align-items: center; justify-content: center;
            background: var(--bg); color: var(--fg); padding: 24px;
            font-family: ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }
        .wrap { text-align: center; max-width: 520px; }
        .badge {
            width: 76px; height: 76px; margin: 0 auto 20px; border-radius: 22px; display: flex;
            align-items: center; justify-content: center; color: #fff;
            background: linear-gradient(135deg, #34d399, var(--brand2)); box-shadow: 0 10px 30px rgba(5,150,105,.35);
        }
        .code {
            font-size: 72px; font-weight: 800; letter-spacing: -2px; line-height: 1; margin: 0;
            background: linear-gradient(135deg, var(--brand), var(--brand2));
            -webkit-background-clip: text; background-clip: text; color: transparent;
        }
        h1 { font-size: 22px; font-weight: 700; margin: 12px 0 6px; }
        .msg { color: var(--muted); margin: 0 0 26px; line-height: 1.6; }
        .actions { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }
        .btn {
            display: inline-flex; align-items: center; gap: 8px; text-decoration: none; font-weight: 600;
            font-size: 14px; padding: 11px 20px; border-radius: 12px; transition: transform .15s ease;
        }
        .btn:hover { transform: translateY(-2px); }
        .primary { background: linear-gradient(135deg, #34d399, var(--brand2)); color: #fff; }
        .ghost { border: 1px solid var(--border); color: var(--fg); background: var(--card); }
        .brand { margin-top: 34px; font-size: 13px; color: var(--muted); }
        .brand b { color: var(--fg); }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="badge">
            <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c1.5-1.26 2-5 2-5s.5 3.74 2 5a2.5 2.5 0 0 0 2.5 2.5c0 3.038-2.46 5.5-5.5 5.5S6.5 17.538 6.5 14.5a4.5 4.5 0 0 0 2 0Z"/>
            </svg>
        </div>
        <p class="code">@yield('code')</p>
        <h1>@yield('title')</h1>
        <p class="msg">@yield('message')</p>
        <div class="actions">
            <a href="/" class="btn primary">Kembali ke Beranda</a>
            <a href="/performance" class="btn ghost">Lihat Track Record</a>
        </div>
        <p class="brand"><b>Lentera Project</b> — Menerangi jalan trading Anda.</p>
    </div>
</body>
</html>
