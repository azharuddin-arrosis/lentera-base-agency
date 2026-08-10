<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"  @class(['dark' => ($appearance ?? 'system') == 'dark'])>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        {{-- ===== SEO (per-halaman, diisi controller via Seo) ===== --}}
        @php($seo = app(\App\Support\Seo::class))
        <meta name="description" content="{{ $seo->description }}">
        <meta name="keywords" content="konsultan trading, mentoring trading, sinyal crypto, sinyal futures, analisis pasar, Lentera Project, belajar trading, manajemen risiko">
        <meta name="author" content="Lentera Project">
        <meta name="robots" content="index, follow">
        <link rel="canonical" href="{{ $seo->canonical() }}">

        {{-- Open Graph --}}
        <meta property="og:type" content="{{ $seo->type }}">
        <meta property="og:site_name" content="{{ config('app.name') }}">
        <meta property="og:title" content="{{ $seo->title() }}">
        <meta property="og:description" content="{{ $seo->description }}">
        <meta property="og:url" content="{{ $seo->canonical() }}">
        <meta property="og:image" content="{{ $seo->image() }}">
        <meta property="og:locale" content="id_ID">

        {{-- Twitter --}}
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $seo->title() }}">
        <meta name="twitter:description" content="{{ $seo->description }}">
        <meta name="twitter:image" content="{{ $seo->image() }}">

        {{-- Google Search Console verification (opsional, isi di .env) --}}
        @if(config('services.google.site_verification'))
            <meta name="google-site-verification" content="{{ config('services.google.site_verification') }}">
        @endif

        {{-- Structured data: Organization + WebSite + schema per-halaman --}}
        <script type="application/ld+json">{!! $seo->jsonLd() !!}</script>

        {{-- Inline script to detect system dark mode preference and apply it immediately --}}
        <script>
            (function() {
                const appearance = '{{ $appearance ?? "system" }}';

                if (appearance === 'system') {
                    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

                    if (prefersDark) {
                        document.documentElement.classList.add('dark');
                    }
                }
            })();
        </script>

        {{-- Inline style to set the HTML background color based on our theme in app.css --}}
        <style>
            html {
                background-color: oklch(1 0 0);
            }

            html.dark {
                background-color: oklch(0.145 0 0);
            }
        </style>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        @fonts

        @vite(['resources/css/app.css', 'resources/js/app.ts', "resources/js/pages/{$page['component']}.vue"])
        <x-inertia::head>
            <title>{{ $seo->title() }}</title>
        </x-inertia::head>

        {{-- Google Analytics (GA4) — aktif otomatis bila GOOGLE_ANALYTICS_ID diisi di .env --}}
        @if(config('services.google.analytics_id'))
            <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('services.google.analytics_id') }}"></script>
            <script>
                window.dataLayer = window.dataLayer || [];
                function gtag(){dataLayer.push(arguments);}
                gtag('js', new Date());
                gtag('config', '{{ config('services.google.analytics_id') }}');
            </script>
        @endif
    </head>
    <body class="font-sans antialiased">
        <x-inertia::app />
    </body>
</html>
