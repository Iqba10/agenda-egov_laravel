<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@hasSection('title')@yield('title') - {{ config('app.name', 'Agenda eGov') }}@else{{ $title ?? config('app.name', 'Agenda eGov') }}@endif</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="app-shell font-sans">
    <div class="fixed inset-0 z-40 bg-slate-950/50 lg:hidden invisible opacity-0 transition-all duration-200 sidebar-backdrop"></div>
    @include('partials.sidebar')

    <div class="min-h-screen lg:pl-72">
        <header class="sticky top-0 z-30 border-b border-slate-200 bg-white/95 backdrop-blur">
            <div class="flex items-center gap-3 px-4 py-4 sm:px-6 lg:px-8">
                <button type="button" data-sidebar-open
                    class="flex-shrink-0 rounded-xl p-2 text-slate-600 hover:bg-slate-100 active:bg-slate-200 lg:hidden transition-colors">
                    <i data-lucide="menu" class="h-5 w-5"></i>
                </button>
                <div class="min-w-0 flex-1">
                    <h1 class="page-title">{{ $header ?? 'Panel Aplikasi' }}</h1>
                    @isset($subheader)
                        <p class="page-subtitle mt-0.5">{{ $subheader }}</p>
                    @endisset
                </div>
                <a href="{{ route('agenda.index') }}" class="btn-secondary flex-shrink-0 hidden sm:inline-flex">Lihat Situs Publik</a>
            </div>
        </header>

        <main class="px-4 py-6 sm:px-6 lg:px-8">
            @include('partials.toast')
            @hasSection('content')
                @yield('content')
            @else
                {{ $slot ?? '' }}
            @endif
        </main>
    </div>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        function docDownload(downloadUrl, filename) {
            fetch(downloadUrl)
                .then(r => r.blob())
                .then(blob => {
                    const a = document.createElement('a');
                    a.href = URL.createObjectURL(blob);
                    a.download = filename;
                    document.body.appendChild(a);
                    a.click();
                    setTimeout(() => { URL.revokeObjectURL(a.href); a.remove(); }, 3000);
                });
        }
    </script>
    @stack('scripts')
    <style>
        body.sidebar-open .sidebar-panel { transform: translateX(0); }
        body.sidebar-open .sidebar-backdrop { opacity: 1; visibility: visible; pointer-events: auto; }
    </style>
</body>
</html>
