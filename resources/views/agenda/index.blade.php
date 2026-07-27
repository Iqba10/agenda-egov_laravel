<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    {{-- Firebase Config --}}
    <meta name="firebase-api-key" content="{{ config('services.firebase.client.api_key') }}">
    <meta name="firebase-auth-domain" content="{{ config('services.firebase.client.auth_domain') }}">
    <meta name="firebase-project-id" content="{{ config('services.firebase.project_id') }}">
    <meta name="firebase-storage-bucket" content="{{ config('services.firebase.client.storage_bucket') }}">
    <meta name="firebase-messaging-sender-id" content="{{ config('services.firebase.client.messaging_sender_id') }}">
    <meta name="firebase-app-id" content="{{ config('services.firebase.client.app_id') }}">
    <meta name="firebase-vapid-key" content="{{ config('services.firebase.client.vapid_key') }}">
    
    <title>Agenda eGov - Diskominfo Sambas</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 font-sans text-slate-900 min-h-screen flex flex-col">
    @include('partials.toast')

    {{-- Header --}}
    <header class="bg-slate-900 bg-[radial-gradient(circle,rgba(255,255,255,0.05)_1px,transparent_1px)] bg-[length:24px_24px] text-white py-6 shadow-sm border-b border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
                <div>
                    <h1 class="text-2xl md:text-3xl font-extrabold flex items-center gap-3 tracking-tight">
                        <span class="p-2 bg-blue-600 rounded-xl shadow-lg shadow-blue-900/20">
                            <i data-lucide="calendar" class="h-5 w-5 text-white"></i>
                        </span>
                        Jadwal Agenda Kegiatan
                    </h1>
                    <p class="text-slate-400 text-sm font-medium mt-2 flex items-center gap-2">
                        <span class="w-5 h-px bg-slate-700"></span>
                        Dinas Komunikasi dan Informatika Kabupaten Sambas
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    {{-- Weather Widget --}}
                    <div class="bg-white/5 border border-white/10 rounded-xl px-4 py-3 flex items-center gap-3 backdrop-blur-sm shadow-sm transition-all hover:bg-white/10">
                        <i data-lucide="cloud-sun" class="h-5 w-5 text-sky-400"></i>
                        <div id="weatherWidget">
                            <div class="text-[9px] font-bold text-slate-500 uppercase tracking-widest" id="weatherLocation">Cuaca</div>
                            <div class="text-slate-200 font-bold" id="weatherContent">
                                <span class="text-xs opacity-50 font-medium">Memuat lokasi...</span>
                            </div>
                        </div>
                    </div>
                    {{-- Clock --}}
                    <div class="bg-white/5 border border-white/10 rounded-xl px-4 py-3 flex items-center gap-3 backdrop-blur-sm shadow-sm transition-all hover:bg-white/10">
                        <i data-lucide="clock" class="h-5 w-5 text-amber-400"></i>
                        <div>
                            <div id="currentDate" class="text-[9px] font-bold text-slate-500 uppercase tracking-widest">Memuat...</div>
                            <div id="currentTime" class="text-base font-bold text-white tracking-tight">--:--</div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </header>

    {{-- Main Content --}}
    <main class="max-w-7xl mx-auto px-4 sm:px-6 py-6 w-full flex-1">

        {{-- Stats Cards --}}
        <div class="dashboard-grid">
            <div class="dashboard-stat-card dashboard-stat-card-blue animate-dashboard" style="animation-delay: .05s">
                <div class="relative z-10">
                    <p class="dashboard-stat-label text-blue-600">Total Agenda</p>
                    <p class="dashboard-stat-value text-blue-900">{{ $stats['total'] }}</p>
                    <p class="dashboard-stat-helper text-blue-500">Semua kegiatan</p>
                </div>
                <i data-lucide="calendar-check" class="dashboard-stat-icon text-blue-200"></i>
            </div>
            <div class="dashboard-stat-card dashboard-stat-card-amber animate-dashboard" style="animation-delay: .1s">
                <div class="relative z-10">
                    <p class="dashboard-stat-label text-amber-600">Terjadwal</p>
                    <p class="dashboard-stat-value text-amber-900">{{ $stats['terjadwal'] }}</p>
                    <p class="dashboard-stat-helper text-amber-500">Akan datang</p>
                </div>
                <i data-lucide="clock" class="dashboard-stat-icon text-amber-200"></i>
            </div>
            <div class="dashboard-stat-card dashboard-stat-card-emerald animate-dashboard" style="animation-delay: .15s">
                <div class="relative z-10">
                    <p class="dashboard-stat-label text-emerald-600">Selesai</p>
                    <p class="dashboard-stat-value text-emerald-900">{{ $stats['selesai'] }}</p>
                    <p class="dashboard-stat-helper text-emerald-500">Terlaksana</p>
                </div>
                <i data-lucide="circle-check-big" class="dashboard-stat-icon text-emerald-200"></i>
            </div>
            <div class="dashboard-stat-card dashboard-stat-card-red animate-dashboard" style="animation-delay: .2s">
                <div class="relative z-10">
                    <p class="dashboard-stat-label text-rose-600">Dibatalkan</p>
                    <p class="dashboard-stat-value text-rose-900">{{ $stats['dibatalkan'] }}</p>
                    <p class="dashboard-stat-helper text-rose-500">Tidak jadi</p>
                </div>
                <i data-lucide="circle-x" class="dashboard-stat-icon text-rose-200"></i>
            </div>
        </div>

        {{-- Agenda Panel --}}
        <section class="dashboard-panel mt-6 animate-dashboard" style="animation-delay: .25s">
            <div class="dashboard-panel-header">
                <h2 class="dashboard-panel-title">
                    <span class="dashboard-icon-tile"><i data-lucide="list" class="h-5 w-5"></i></span>
                    Daftar Agenda Kegiatan
                </h2>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="openNotifModal()" class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 text-sm font-semibold rounded-lg transition-colors">
                        <i data-lucide="bell" class="h-4 w-4"></i>
                        Dapatkan Notifikasi
                    </button>
                    <a href="{{ route('notify.bulk') }}" class="inline-flex items-center gap-2 px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 text-sm font-semibold rounded-lg transition-colors">
                        <i data-lucide="users-plus" class="h-4 w-4"></i>
                        <span class="hidden sm:inline">Registrasi Massal</span>
                    </a>

                    @auth
                        {{-- User Card with Dropdown --}}
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" @keydown.escape.window="open = false"
                                class="inline-flex items-center gap-2 px-3 py-1.5 bg-white hover:bg-slate-50 border border-slate-200 text-sm font-semibold rounded-lg transition-colors shadow-sm">
                                <span class="flex h-7 w-7 items-center justify-center rounded-full {{ Auth::user()->role === 'admin' ? 'bg-amber-100 text-amber-700' : 'bg-blue-100 text-blue-700' }} font-bold text-[11px] leading-none">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                                </span>
                                <span class="hidden sm:inline text-slate-700">{{ Auth::user()->name }}</span>
                                <i data-lucide="chevron-down" class="h-3.5 w-3.5 text-slate-400 transition-transform" :class="open && 'rotate-180'"></i>
                            </button>

                            {{-- Dropdown --}}
                            <div x-show="open" x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                x-transition:leave-end="opacity-0 scale-95 translate-y-1"
                                @click.away="open = false"
                                class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-2xl border border-slate-200 overflow-hidden z-50">
                                <div class="px-4 py-3 border-b border-slate-100 bg-slate-50/50">
                                    <p class="text-sm font-bold text-slate-800 truncate">{{ Auth::user()->name }}</p>
                                    <p class="text-xs text-slate-500 truncate">{{ Auth::user()->email }}</p>
                                </div>
                                <div class="py-1.5">
                                    @if(Auth::user()->role === 'admin')
                                        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                                            <i data-lucide="layout-dashboard" class="h-4 w-4 text-slate-400"></i>
                                            Panel Admin
                                        </a>
                                    @endif
                                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                                        <i data-lucide="user" class="h-4 w-4 text-slate-400"></i>
                                        Profil Saya
                                    </a>
                                    <div class="my-1 border-t border-slate-100"></div>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="flex items-center gap-2.5 w-full px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50 transition-colors">
                                            <i data-lucide="log-out" class="h-4 w-4"></i>
                                            Keluar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-3 py-1.5 bg-slate-800 hover:bg-slate-900 text-white text-sm font-semibold rounded-lg transition-colors">
                            <i data-lucide="log-in" class="h-4 w-4"></i>
                            Masuk Dashboard
                        </a>
                    @endauth
                </div>
            </div>

            {{-- Search & Filters --}}
            <form action="{{ route('agenda.index') }}" method="GET" class="border-b border-slate-100 bg-slate-50/50 p-4 flex flex-col md:flex-row gap-3">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <div class="flex-1 relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i data-lucide="search" class="h-4 w-4 text-slate-400"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari agenda, tempat, asal surat..." class="w-full pl-10 pr-4 py-2 text-sm border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-slate-800">
                </div>
                <div class="flex gap-3">
                    <select name="month" class="py-2 pl-3 pr-8 text-sm border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-slate-800">
                        <option value="">Semua Bulan</option>
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                            </option>
                        @endforeach
                    </select>
                    <select name="year" class="py-2 pl-3 pr-8 text-sm border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-slate-800">
                        <option value="">Semua Tahun</option>
                        @foreach(range(date('Y') + 1, date('Y') - 5) as $y)
                            <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white text-sm font-medium rounded-lg transition-colors flex items-center gap-2">
                        <i data-lucide="filter" class="h-4 w-4"></i> <span class="hidden sm:inline">Filter</span>
                    </button>
                    @if(request('search') || request('month') || request('year'))
                        <a href="{{ route('agenda.index', array_filter(['status' => request('status')])) }}" class="px-4 py-2 bg-red-100 hover:bg-red-200 text-red-600 text-sm font-medium rounded-lg transition-colors flex items-center gap-2" title="Reset Filter">
                            <i data-lucide="x" class="h-4 w-4"></i>
                        </a>
                    @endif
                </div>
            </form>

            {{-- Status Filter Bar --}}
            <div class="dashboard-filter-bar">
                @php($active = $status ?: 'semua')
                @foreach ([
                    'semua'       => ['label' => 'Semua',       'icon' => 'list',            'count' => $stats['total'],       'active' => 'dashboard-filter-active-slate'],
                    'terjadwal'   => ['label' => 'Terjadwal',   'icon' => 'clock',           'count' => $stats['terjadwal'],   'active' => 'dashboard-filter-active-amber'],
                    'berlangsung' => ['label' => 'Berlangsung', 'icon' => 'radio',           'count' => $stats['berlangsung'], 'active' => 'dashboard-filter-active-blue'],
                    'selesai'     => ['label' => 'Selesai',     'icon' => 'circle-check-big','count' => $stats['selesai'],     'active' => 'dashboard-filter-active-emerald'],
                    'dibatalkan'  => ['label' => 'Dibatalkan',  'icon' => 'circle-x',        'count' => $stats['dibatalkan'],  'active' => 'dashboard-filter-active-red'],
                ] as $value => $item)
                    <a href="{{ route('agenda.index', array_filter(['status' => $value === 'semua' ? null : $value, 'search' => request('search'), 'month' => request('month'), 'year' => request('year')])) }}"
                       class="dashboard-filter {{ $active === $value ? $item['active'] : '' }}">
                        <i data-lucide="{{ $item['icon'] }}" class="h-4 w-4"></i>
                        {{ $item['label'] }}
                        <span class="text-xs opacity-75">{{ $item['count'] }}</span>
                    </a>
                @endforeach
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="border-b border-slate-200 bg-slate-50">
                        <tr>
                            <th class="w-12 px-4 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-600">No</th>
                            <th class="px-4 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-600">Agenda & Waktu</th>
                            <th class="hidden px-4 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-600 md:table-cell">Tempat</th>
                            <th class="w-32 px-4 py-3.5 text-center text-xs font-bold uppercase tracking-wider text-slate-600">Status</th>
                            <th class="w-20 px-4 py-3.5 text-center text-xs font-bold uppercase tracking-wider text-slate-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse ($agendas as $agenda)
                            <tr class="dashboard-row">
                                <td class="px-4 py-4 font-medium text-slate-500">{{ $agendas->firstItem() + $loop->index }}</td>
                                <td class="px-4 py-4">
                                    <a href="{{ route('agenda.show', $agenda) }}" class="font-semibold text-slate-800 hover:text-blue-600 transition-colors">{{ $agenda->perihal_kegiatan }}</a>
                                    <div class="mt-1 flex flex-wrap items-center gap-3 text-xs text-slate-500">
                                        <span class="inline-flex items-center gap-1"><i data-lucide="calendar" class="h-3.5 w-3.5 text-slate-400"></i>{{ optional($agenda->waktu_mulai)->format('d M Y') }}</span>
                                        <span class="inline-flex items-center gap-1"><i data-lucide="clock" class="h-3.5 w-3.5 text-slate-400"></i>{{ optional($agenda->waktu_mulai)->format('H:i') }} WIB</span>
                                        @if($agenda->documents->count() > 0)
                                            <span class="inline-flex items-center gap-1 text-blue-500"><i data-lucide="paperclip" class="h-3.5 w-3.5"></i>{{ $agenda->documents->count() }} Dokumen</span>
                                        @endif
                                    </div>
                                    <div class="mt-1.5 flex items-center gap-1 text-xs text-slate-500 md:hidden">
                                        <i data-lucide="map-pin" class="h-3.5 w-3.5 text-slate-400"></i>{{ $agenda->tempat }}
                                    </div>
                                </td>
                                <td class="hidden px-4 py-4 text-slate-600 md:table-cell">
                                    <span class="inline-flex items-center gap-1.5"><i data-lucide="map-pin" class="h-3.5 w-3.5 text-slate-400"></i>{{ $agenda->tempat }}</span>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <span class="{{ $agenda->status_badge_class }}">{{ ucfirst($agenda->effective_status) }}</span>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center justify-center gap-1">
                                        <a href="{{ route('agenda.show', $agenda) }}" class="dashboard-action-icon text-blue-600 hover:bg-blue-50" title="Detail"><i data-lucide="eye" class="h-4 w-4"></i></a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-16 text-center text-slate-400">
                                    <i data-lucide="inbox" class="mx-auto h-12 w-12 opacity-50"></i>
                                    <p class="mt-3 font-medium">Tidak ada agenda</p>
                                    @if(request()->anyFilled(['search', 'month', 'year', 'status']))
                                        <a href="{{ route('agenda.index') }}" class="mt-4 inline-block text-sm font-bold text-blue-600 hover:underline">Reset Semua Filter</a>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <div class="mt-6">{{ $agendas->links() }}</div>

    </main>

    @include('partials.public-footer')

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();

        function updateClock() {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }).replace('.', ':');
            const dateStr = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long' });
            document.getElementById('currentTime').innerText = timeStr + ' WIB';
            document.getElementById('currentDate').innerText = dateStr;
        }
        setInterval(updateClock, 1000);
        updateClock();

        // Fetch weather based on user location
        function fetchWeather(lat, lon, cityName) {
            const locationEl = document.getElementById('weatherLocation');
            const contentEl = document.getElementById('weatherContent');
            
            locationEl.textContent = 'Cuaca ' + cityName;
            
            fetch(`{{ route('api.weather') }}?lat=${lat}&lon=${lon}`)
                .then(res => res.json())
                .then(data => {
                    if (data.temp) {
                        contentEl.innerHTML = `
                            <div class="flex items-baseline gap-1.5">
                                <span class="text-base font-extrabold text-white">${data.temp}&deg;C</span>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">${data.condition}</span>
                            </div>
                            <div class="flex items-center gap-3 mt-0.5">
                                <span class="text-[10px] text-slate-400 flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 inline text-sky-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v6M12 22v-6M4.93 4.93l4.24 4.24M14.83 14.83l4.24 4.24M2 12h6M22 12h-6M4.93 19.07l4.24-4.24M14.83 9.17l4.24-4.24"/></svg>
                                    ${data.humidity}%
                                </span>
                                <span class="text-[10px] text-slate-400 flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 inline text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.59 4.59A2 2 0 1 1 11 8H2m10.59 11.41A2 2 0 1 0 14 16H2m15.73-8.27A2.5 2.5 0 1 1 19.5 12H2"/></svg>
                                    ${data.wind} m/s
                                </span>
                            </div>`;
                    } else {
                        contentEl.innerHTML = '<span class="text-xs text-slate-400">Gagal memuat cuaca</span>';
                    }
                })
                .catch(() => {
                    contentEl.innerHTML = '<span class="text-xs text-slate-400">Cuaca tidak tersedia</span>';
                });
        }

        // Get user location and fetch weather
        function initWeather() {
            const contentEl = document.getElementById('weatherContent');
            const locationEl = document.getElementById('weatherLocation');
            
            if ('geolocation' in navigator) {
                contentEl.innerHTML = '<span class="text-xs opacity-50 font-medium">Mendapatkan lokasi...</span>';
                
                navigator.geolocation.getCurrentPosition(
                    // Success - got user location
                    (position) => {
                        const lat = position.coords.latitude.toFixed(4);
                        const lon = position.coords.longitude.toFixed(4);
                        
                        // Reverse geocoding to get city name
                        fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lon}&format=json&zoom=10`)
                            .then(res => res.json())
                            .then(geo => {
                                const city = geo.address?.city || geo.address?.town || geo.address?.county || geo.address?.state || 'Lokasi Anda';
                                fetchWeather(lat, lon, city);
                            })
                            .catch(() => {
                                fetchWeather(lat, lon, 'Lokasi Anda');
                            });
                    },
                    // Error or denied - use default Sambas
                    () => {
                        locationEl.textContent = 'Cuaca Sambas';
                        fetchWeather(1.361, 109.305, 'Sambas');
                    },
                    { timeout: 10000, maximumAge: 300000 }
                );
            } else {
                // Geolocation not supported - use default Sambas
                locationEl.textContent = 'Cuaca Sambas';
                fetchWeather(1.361, 109.305, 'Sambas');
            }
        }
        
        initWeather();

        const searchInput = document.querySelector('input[name="search"]');
        if (searchInput) {
            const form = searchInput.closest('form');
            let debounceTimer;
            searchInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => form.submit(), 600);
            });
        }

        // Modal open/close handlers are defined in the notification script below
        document.addEventListener('keydown', e => { if (e.key === 'Escape') closeNotifModal(); });
    </script>

    {{-- Notification Modal - Global One-Time Subscription --}}
<div id="notifModal" class="fixed inset-0 z-[60] hidden items-end sm:items-center justify-center p-0 sm:p-4" role="dialog" aria-modal="true">
    <div class="absolute inset-0 bg-slate-950/60 backdrop-blur-sm" onclick="closeNotifModal()"></div>

    <div class="relative w-full sm:w-[420px] max-h-[90vh] flex flex-col rounded-t-2xl sm:rounded-2xl bg-white shadow-2xl border border-slate-200/80 overflow-hidden"
         style="animation: slideUp .22s cubic-bezier(.22,1,.36,1);">

        {{-- Header --}}
        <div class="px-5 pt-5 pb-3 shrink-0">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-600">
                        <i data-lucide="bell-ring" class="h-4 w-4 text-white"></i>
                    </div>
                    <div>
                        <p class="font-bold text-slate-900 text-sm">Notifikasi Agenda</p>
                        <p class="text-[11px] text-slate-500">Pengingat 6 jam & 1 jam sebelum agenda</p>
                    </div>
                </div>
                <button onclick="closeNotifModal()" class="p-1.5 rounded-lg text-slate-400 hover:bg-slate-100">
                    <i data-lucide="x" class="h-4 w-4"></i>
                </button>
            </div>
        </div>

        {{-- Scrollable Body --}}
        <div class="flex-1 overflow-y-auto min-h-0 px-5 pb-5">

            {{-- Subscription Status --}}
            <div id="subStatusBox" class="hidden mb-4 p-3 rounded-xl bg-emerald-50 border border-emerald-200">
                <div class="flex items-center gap-2">
                    <i data-lucide="check-circle" class="h-4 w-4 text-emerald-600"></i>
                    <p class="text-sm font-semibold text-emerald-800">Sudah berlangganan</p>
                </div>
                <p class="text-[11px] text-emerald-600 mt-1">Anda akan otomatis menerima semua notifikasi agenda.</p>
            </div>

            {{-- Toggles --}}
            <div class="space-y-2 mb-4">
                <label class="flex items-center justify-between p-3 rounded-xl border border-slate-200 bg-slate-50 cursor-pointer">
                    <div class="flex items-center gap-3">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100 text-blue-600">
                            <i data-lucide="bell" class="h-4 w-4"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-800">Notifikasi Browser</p>
                            <p class="text-[10px] text-slate-500">Default & rekomendasi</p>
                        </div>
                    </div>
                    <input type="checkbox" id="fcmOptIn" onchange="handleFcmToggle()" class="h-5 w-5 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                </label>

                <label class="flex items-center justify-between p-3 rounded-xl border border-slate-200 bg-slate-50 cursor-pointer">
                    <div class="flex items-center gap-3">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-100 text-emerald-600">
                            <i data-lucide="message-circle" class="h-4 w-4"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-800">WhatsApp</p>
                            <p class="text-[10px] text-slate-500">Opsional</p>
                        </div>
                    </div>
                    <input type="checkbox" id="whatsappOptIn" onchange="toggleWhatsappInput()" class="h-5 w-5 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                </label>

                {{-- WhatsApp Input --}}
                <div id="whatsappInputSection" class="hidden space-y-2 pl-2">
                    <input id="whatsappName" type="text" placeholder="Nama Anda" autocomplete="name"
                           class="w-full px-3 py-2 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 text-slate-800">
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 text-sm font-medium">+62</span>
                        <input id="whatsappPhone" type="tel" placeholder="812-3456-7890" autocomplete="tel"
                               class="w-full pl-12 pr-3 py-2 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 text-slate-800">
                    </div>
                </div>

                {{-- Bulk WhatsApp Representative Toggle --}}
                <label class="flex items-center justify-between p-3 rounded-xl border border-slate-200 bg-slate-50 cursor-pointer">
                    <div class="flex items-center gap-3">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600">
                            <i data-lucide="users" class="h-4 w-4"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-800">Daftarkan Perwakilan OPD</p>
                            <p class="text-[10px] text-slate-500">Banyak nomor sekaligus</p>
                        </div>
                    </div>
                    <input type="checkbox" id="bulkWhatsappOptIn" onchange="toggleBulkWhatsappInput()" class="h-5 w-5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                </label>

                {{-- Bulk WhatsApp Input --}}
                <div id="bulkWhatsappInputSection" class="hidden space-y-2 pl-2">
                    <input id="bulkOpdName" type="text" placeholder="Nama OPD / Perwakilan" autocomplete="organization"
                           class="w-full px-3 py-2 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 text-slate-800">
                    <textarea id="bulkContacts" rows="4" placeholder="Dimas, 08123456789&#10;Iqbal, 08129876543&#10;08521234567"
                              class="w-full px-3 py-2 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 text-slate-800 font-mono resize-y max-h-40"></textarea>
                    <p class="text-[10px] text-slate-500">Satu nomor per baris. Format opsional: <code class="bg-slate-100 px-1 rounded">Nama, 08123456789</code></p>
                </div>
            </div>

            {{-- Incoming Agendas (show if subscribed) --}}
            <div id="incomingAgendasSection" class="hidden mb-4">
                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-2">Agenda Mendatang</p>
                <div id="incomingAgendasList" class="max-h-40 overflow-y-auto space-y-1.5">
                    <div class="flex items-center justify-center py-4">
                        <div class="h-4 w-4 border-2 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
                    </div>
                </div>
            </div>

            {{-- Message & Submit --}}
            <div id="notifFormMsg" class="hidden mb-3 rounded-lg px-3 py-2 text-xs font-semibold"></div>
            <button id="notifSubmit" type="button" onclick="submitGlobalSubscribe()"
                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed text-white text-sm font-semibold rounded-xl transition-colors">
                <i data-lucide="send" class="h-4 w-4"></i>
                Simpan
            </button>
        </div>
    </div>
</div>

<style>
@keyframes slideUp {
    from { opacity: 0; transform: translateY(20px); }
    to   { opacity: 1; transform: translateY(0); }
}
</style>

<script src="/js/firebase-init.js" defer></script>
<script>
// Firebase Config - injected from server
window.FIREBASE_CONFIG = {
    apiKey: '{{ config("services.firebase.client.api_key", "") }}',
    authDomain: '{{ config("services.firebase.client.auth_domain", "") }}',
    projectId: '{{ config("services.firebase.project_id", "") }}',
    storageBucket: '{{ config("services.firebase.client.storage_bucket", "") }}',
    messagingSenderId: '{{ config("services.firebase.client.messaging_sender_id", "") }}',
    appId: '{{ config("services.firebase.client.app_id", "") }}',
};
window.FIREBASE_VAPID_KEY = '{{ config("services.firebase.client.vapid_key", "") }}';

let fcmToken = null;

function hasValidFcmToken(token) {
    return typeof token === 'string'
        && token.length > 40
        && !token.startsWith('browser-notification-');
}

function openNotifModal() {
    const modal = document.getElementById('notifModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';
    refreshModalState();
}

function closeNotifModal() {
    const modal = document.getElementById('notifModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = '';
}

function refreshModalState() {
    const fcmCheckbox = document.getElementById('fcmOptIn');
    const permission = typeof Notification !== 'undefined' ? Notification.permission : 'unsupported';
    fcmCheckbox.checked = permission === 'granted' && hasValidFcmToken(fcmToken);
    loadIncomingAgendas();
    lucide.createIcons();
}

async function handleFcmToggle() {
    const cb = document.getElementById('fcmOptIn');
    if (!cb.checked) return;

    if (!('Notification' in window)) {
        showFormMsg('Browser tidak mendukung notifikasi.', false);
        cb.checked = false;
        return;
    }

    const permission = await Notification.requestPermission();
    if (permission !== 'granted') {
        showFormMsg('Izin notifikasi browser diperlukan.', false);
        cb.checked = false;
        return;
    }

    if (!window.FirebaseNotification) {
        showFormMsg('Script Firebase belum termuat.', false);
        cb.checked = false;
        return;
    }

    try {
        const initialized = await window.FirebaseNotification.init();
        if (!initialized) {
            throw new Error('Konfigurasi Firebase belum lengkap.');
        }
        fcmToken = await window.FirebaseNotification.getToken();
        if (!hasValidFcmToken(fcmToken)) {
            throw new Error('Token browser tidak valid.');
        }
        showFormMsg('Notifikasi browser aktif.', true);
    } catch (err) {
        cb.checked = false;
        showFormMsg('Gagal mengaktifkan notifikasi: ' + err.message, false);
    }
}

function toggleWhatsappInput() {
    const cb = document.getElementById('whatsappOptIn');
    document.getElementById('whatsappInputSection').classList.toggle('hidden', !cb.checked);
}

function toggleBulkWhatsappInput() {
    const cb = document.getElementById('bulkWhatsappOptIn');
    document.getElementById('bulkWhatsappInputSection').classList.toggle('hidden', !cb.checked);
}

function normalizePhone(phone) {
    let cleaned = phone.replace(/[^0-9]/g, '');
    if (cleaned.startsWith('0')) {
        cleaned = '62' + cleaned.substring(1);
    } else if (!cleaned.startsWith('62')) {
        cleaned = '62' + cleaned;
    }
    return cleaned;
}

function parseBulkContacts(text) {
    const lines = text.split(/\r?\n/).map(l => l.trim()).filter(Boolean);
    const contacts = [];
    lines.forEach(line => {
        const parts = line.split(',').map(p => p.trim()).filter(Boolean);
        const numbers = parts.filter(p => /\d/.test(p));
        const names = parts.filter(p => !/\d/.test(p));
        numbers.forEach(num => {
            const normalized = normalizePhone(num);
            if (normalized.length >= 10) {
                contacts.push({
                    name: names[0] || null,
                    phone: normalized,
                });
            }
        });
    });
    return contacts;
}

async function submitGlobalSubscribe() {
    const btn = document.getElementById('notifSubmit');
    const fcmOptIn = document.getElementById('fcmOptIn').checked;
    const whatsappOptIn = document.getElementById('whatsappOptIn').checked;
    const bulkOptIn = document.getElementById('bulkWhatsappOptIn').checked;

    let hasAction = false;
    let payload = {};

    if (fcmOptIn) {
        if (!hasValidFcmToken(fcmToken)) {
            showFormMsg('Izinkan notifikasi browser terlebih dahulu.', false);
            return;
        }
        payload.fcm_token = fcmToken;
        hasAction = true;
    }

    if (whatsappOptIn) {
        const name = document.getElementById('whatsappName').value.trim();
        const phone = normalizePhone(document.getElementById('whatsappPhone').value.trim());
        if (phone.length < 10) {
            showFormMsg('Masukkan nomor WhatsApp yang valid.', false);
            return;
        }
        payload.whatsapp_opt_in = true;
        payload.whatsapp_name = name;
        payload.whatsapp_phone = phone;
        hasAction = true;
    }

    if (bulkOptIn) {
        const raw = document.getElementById('bulkContacts').value;
        const contacts = parseBulkContacts(raw);
        if (contacts.length === 0) {
            showFormMsg('Masukkan minimal satu nomor perwakilan OPD yang valid.', false);
            return;
        }
        payload.bulk_contacts = contacts;
        payload.bulk_opd_name = document.getElementById('bulkOpdName').value.trim();
        hasAction = true;
    }

    if (!hasAction) {
        showFormMsg('Pilih minimal satu metode notifikasi.', false);
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<div class="h-4 w-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div> Memproses...';

    try {
        const res = await fetch('{{ route('api.fcm.register') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify(payload),
        });

        const data = await res.json();
        if (data.success) {
            showFormMsg(data.message || 'Berhasil menyimpan langganan.', true);
            document.getElementById('subStatusBox').classList.remove('hidden');
            document.getElementById('incomingAgendasSection').classList.remove('hidden');
            loadIncomingAgendas();
        } else {
            const errMsg = data.errors
                ? Object.values(data.errors).flat().join(' ')
                : (data.message || 'Terjadi kesalahan.');
            showFormMsg(errMsg, false);
        }
    } catch {
        showFormMsg('Gagal menghubungi server. Coba lagi.', false);
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i data-lucide="send" class="h-4 w-4"></i> Simpan';
        lucide.createIcons();
    }
}

function loadIncomingAgendas() {
    const list = document.getElementById('incomingAgendasList');
    const section = document.getElementById('incomingAgendasSection');
    section.classList.remove('hidden');
    list.innerHTML = '<div class="flex items-center justify-center py-4"><div class="h-4 w-4 border-2 border-blue-500 border-t-transparent rounded-full animate-spin"></div></div>';

    fetch('/api/agenda/incoming?limit=20')
        .then(r => r.json())
        .then(items => {
            if (!items.length) {
                list.innerHTML = '<p class="text-center text-xs text-slate-400 py-3">Tidak ada agenda mendatang.</p>';
                return;
            }
            list.innerHTML = items.map(a => {
                return `<div class="flex items-start gap-2 rounded-lg border border-slate-200 p-2 bg-slate-50">
                    <div class="mt-0.5 shrink-0 w-1.5 h-1.5 rounded-full bg-blue-500"></div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-slate-800 truncate">${a.perihal_kegiatan}</p>
                        <p class="text-[10px] text-slate-500">${a.tanggal_mulai || '-'} ${a.waktu_mulai || ''} &bull; ${a.tempat || '-'}</p>
                    </div>
                </div>`;
            }).join('');
        })
        .catch(() => {
            list.innerHTML = '<p class="text-center text-xs text-slate-400 py-3">Gagal memuat agenda.</p>';
        });
}

function showFormMsg(msg, success) {
    const el = document.getElementById('notifFormMsg');
    el.textContent = msg;
    el.className = `mb-3 rounded-lg px-3 py-2 text-xs font-semibold ${success ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-red-50 text-red-600 border border-red-200'}`;
    el.classList.remove('hidden');
    setTimeout(() => el.classList.add('hidden'), 5000);
}

// Initialize Firebase if available
document.addEventListener('DOMContentLoaded', function() {
    if (window.FirebaseNotification) {
        window.FirebaseNotification.init().then(() => {
            if (Notification.permission === 'granted') {
                window.FirebaseNotification.getToken().then(token => {
                    fcmToken = token;
                });
            }
        });
    }
});
</script>
</body>
</html>