<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'Agenda eGov') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#f8fafc] font-sans text-slate-900 selection:bg-blue-100 selection:text-blue-700">
    @include('partials.toast')
    
    <div class="fixed inset-0 -z-10 overflow-hidden">
        <div class="absolute -top-[10%] -left-[10%] h-[40%] w-[40%] rounded-full bg-blue-50/50 blur-[120px]"></div>
        <div class="absolute top-[60%] -right-[5%] h-[50%] w-[50%] rounded-full bg-indigo-50/40 blur-[120px]"></div>
    </div>

    <div class="mx-auto flex min-h-screen w-full max-w-7xl items-center justify-center p-0 sm:p-4 md:p-6 lg:p-8">
        <div class="grid min-h-screen w-full items-stretch gap-0 overflow-hidden sm:min-h-[auto] sm:rounded-[2.5rem] border-slate-200 bg-white shadow-2xl lg:grid-cols-[1fr_1fr] xl:grid-cols-[1.2fr_.8fr]">
            <div class="relative hidden flex-col justify-between bg-slate-900 p-8 lg:p-12 text-white lg:flex">
                <div class="absolute inset-0 opacity-20" style="background-image: url('data:image/svg+xml,%3Csvg width=\'20\' height=\'20\' viewBox=\'0 0 20 20\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.2\' fill-rule=\'evenodd\'%3E%3Ccircle cx=\'3\' cy=\'3\' r=\'1\'/%3E%3C/g%3E%3C/svg%3E');"></div>
                
                <div class="relative z-10">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-600 shadow-lg shadow-blue-500/20">
                            <i data-lucide="calendar" class="h-6 w-6 text-white"></i>
                        </div>
                        <span class="text-xl font-bold tracking-tight text-white">Agenda <span class="text-blue-400">eGov</span></span>
                    </div>
                    
                    <div class="mt-8 lg:mt-12">
                        <h1 class="text-4xl lg:text-5xl font-extrabold leading-[1.2] tracking-tight text-white">
                            Kelola Agenda <br/>
                            <span class="text-blue-400">Pemerintahan</span> <br/>
                            Lebih Modern.
                        </h1>
                        <p class="mt-4 lg:mt-5 max-w-md text-base lg:text-lg leading-relaxed text-slate-400">
                            Transformasi tata kelola agenda publik menjadi lebih terstructured, transparan, dan profesional dalam satu platform terintegrasi.
                        </p>
                    </div>
                </div>

                <div class="relative z-10 flex flex-col gap-10">
                    <div class="flex items-center gap-4 rounded-2xl bg-white/5 p-5 backdrop-blur-sm border border-white/10">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-blue-500/20 text-blue-400">
                            <i data-lucide="shield-check" class="h-6 w-6"></i>
                        </div>
                        <div class="space-y-1">
                            <p class="font-bold text-white tracking-wide">Akses Terproteksi</p>
                            <p class="text-sm text-slate-400 leading-relaxed">Sistem RBAC untuk menjamin keamanan data.</p>
                        </div>
                    </div>
                    
                    <a href="{{ route('agenda.index') }}" class="group inline-flex items-center gap-2 text-sm font-semibold text-blue-400 transition hover:text-blue-300">
                        <i data-lucide="arrow-left" class="h-4 w-4 transition-transform group-hover:-translate-x-1"></i>
                        Kembali ke Situs Publik
                    </a>
                </div>
            </div>

            <div class="flex flex-col justify-center px-8 py-12 sm:px-12 lg:px-16 bg-white">
                <div class="mx-auto w-full max-w-sm">
                    <div class="mb-10 lg:hidden">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-600">
                                <i data-lucide="calendar" class="h-6 w-6 text-white"></i>
                            </div>
                            <span class="text-xl font-bold tracking-tight">Agenda <span class="text-blue-600">eGov</span></span>
                        </div>
                    </div>

                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();
    </script>
</body>
</html>
