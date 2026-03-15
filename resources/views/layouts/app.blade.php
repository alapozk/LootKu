<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $pageTitle ?? 'Lootku Market' }}</title>
        <meta
            name="description"
            content="Prototype marketplace item game berbasis Laravel dengan buyer storefront dan dashboard penjual."
        >
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link
            href="https://fonts.googleapis.com/css2?family=Sora:wght@600;700;800&family=Space+Grotesk:wght@400;500;700&display=swap"
            rel="stylesheet"
        >
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
        @stack('head')
    </head>
    <body class="{{ $bodyClass ?? 'bg-slate-100 text-slate-900' }}">
        @if (session('status') || session('error'))
            <div class="shell pt-4">
                <div class="rounded-[24px] border px-5 py-4 text-sm font-semibold shadow-[0_18px_40px_-30px_rgba(15,23,42,0.25)] {{ session('error') ? 'border-rose-200 bg-rose-50 text-rose-700' : 'border-emerald-200 bg-emerald-50 text-emerald-700' }}">
                    {{ session('error') ?: session('status') }}
                </div>
            </div>
        @endif
        @yield('content')
    </body>
</html>
