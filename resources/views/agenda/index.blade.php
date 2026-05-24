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
                    <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 px-3 py-1.5 bg-slate-800 hover:bg-slate-900 text-white text-sm font-semibold rounded-lg transition-colors">
                        <i data-lucide="layout-dashboard" class="h-4 w-4"></i>
                        Panel Admin
                    </a>
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

        function openNotifModal() {
            document.getElementById('notifModal').classList.remove('hidden');
            document.getElementById('notifModal').classList.add('flex');
            document.body.style.overflow = 'hidden';
            loadAgendas('');
        }

        function closeNotifModal() {
            document.getElementById('notifModal').classList.add('hidden');
            document.getElementById('notifModal').classList.remove('flex');
            document.body.style.overflow = '';
        }

        document.addEventListener('keydown', e => { if (e.key === 'Escape') closeNotifModal(); });
    </script>
</body>
</html>

{{-- Notification Modal - Multi Channel (WhatsApp + FCM) --}}
<div id="notifModal" class="fixed inset-0 z-[60] hidden items-end sm:items-center justify-center p-0 sm:p-4" role="dialog" aria-modal="true">
    <div class="absolute inset-0 bg-slate-950/60 backdrop-blur-sm" onclick="closeNotifModal()"></div>

    <div class="relative w-full sm:w-[640px] max-h-[90vh] sm:max-h-[600px] flex flex-col rounded-t-2xl sm:rounded-2xl bg-white shadow-2xl border border-slate-200/80 overflow-hidden"
         style="animation: slideUp .22s cubic-bezier(.22,1,.36,1);">

        {{-- macOS title bar --}}
        <div class="flex items-center gap-2 px-3 py-2.5 bg-slate-50 border-b border-slate-200 select-none shrink-0">
            <div class="flex items-center gap-1.5">
                <button onclick="closeNotifModal()" class="h-3 w-3 rounded-full bg-red-400 hover:bg-red-500 transition-colors group" title="Tutup">
                    <svg class="h-3 w-3 opacity-0 group-hover:opacity-100 text-red-800" fill="none" viewBox="0 0 12 12"><path d="M3 3l6 6M9 3l-6 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                </button>
                <span class="h-3 w-3 rounded-full bg-amber-400 cursor-default"></span>
                <span class="h-3 w-3 rounded-full bg-emerald-400 cursor-default"></span>
            </div>
            <span class="flex-1 text-center text-xs font-semibold text-slate-500 -ml-10">Pengingat Agenda</span>
        </div>

        {{-- Body --}}
        <div class="flex flex-col flex-1 overflow-hidden">

            {{-- Header --}}
            <div class="px-4 pt-4 pb-3 shrink-0">
                <div class="flex items-center gap-2.5 mb-2">
                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-blue-600">
                        <i data-lucide="bell-ring" class="h-4 w-4 text-white"></i>
                    </div>
                    <div>
                        <p class="font-bold text-slate-900 text-sm leading-tight">Dapatkan Pengingat Agenda</p>
                        <p class="text-[11px] text-slate-500 mt-0.5">Pilih metode & agenda — notifikasi dikirim 1 jam sebelum agenda dimulai.</p>
                    </div>
                </div>
            </div>

            {{-- Channel Selection --}}
            <div class="px-4 pb-3 shrink-0">
                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-2">Metode Pengingat</p>
                <div class="flex gap-2">
                    <button type="button" onclick="setChannel('whatsapp')" id="channelWhatsapp"
                            class="flex-1 flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl border-2 text-sm font-semibold transition-all border-emerald-500 bg-emerald-50 text-emerald-700">
                        <i data-lucide="message-circle" class="h-4 w-4"></i>
                        WhatsApp
                    </button>
                    <button type="button" onclick="setChannel('fcm')" id="channelFcm"
                            class="flex-1 flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl border-2 text-sm font-semibold transition-all border-slate-200 text-slate-600 hover:border-slate-300">
                        <i data-lucide="bell" class="h-4 w-4"></i>
                        Notif Web
                    </button>
                    <button type="button" onclick="setChannel('both')" id="channelBoth"
                            class="flex-1 flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl border-2 text-sm font-semibold transition-all border-slate-200 text-slate-600 hover:border-slate-300">
                        <i data-lucide="layers" class="h-4 w-4"></i>
                        Gabungan
                    </button>
                </div>
            </div>

            {{-- Input Section Container - switches between single column and grid for "both" --}}
            <div id="inputSectionContainer" class="px-4 pb-3 shrink-0">
                {{-- Single mode: WhatsApp only --}}
                <div id="waInputSection">
                    <label class="text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-1.5 block">Nomor WhatsApp</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 text-sm font-medium">+62</span>
                        <input id="notifPhone" type="tel" placeholder="812-3456-7890" autocomplete="tel"
                               class="w-full pl-12 pr-3 py-2 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent placeholder-slate-400 text-slate-800">
                    </div>
                </div>

                {{-- Single mode: FCM only --}}
                <div id="fcmSection" class="hidden">
                    <label class="text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-1.5 block">Notifikasi Browser</label>
                    <div id="fcmPermissionBox" class="flex items-center gap-3 p-2.5 rounded-xl border border-slate-200 bg-slate-50">
                        <div id="fcmStatusIcon" class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-slate-200 text-slate-500">
                            <i data-lucide="bell-off" class="h-3.5 w-3.5"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p id="fcmStatusText" class="text-xs font-semibold text-slate-700 leading-tight">Notifikasi belum diizinkan</p>
                            <p id="fcmStatusDesc" class="text-[10px] text-slate-500">Klik tombol untuk mengizinkan</p>
                        </div>
                        <button type="button" onclick="requestFcmPermission()" id="fcmPermitBtn"
                                class="px-2.5 py-1 bg-blue-600 hover:bg-blue-700 text-white text-[11px] font-semibold rounded-lg transition-colors whitespace-nowrap">
                            Izinkan
                        </button>
                    </div>
                </div>

                {{-- Combined mode: Grid layout for both WA + FCM --}}
                <div id="bothSection" class="hidden">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        {{-- WhatsApp Column --}}
                        <div>
                            <label class="text-[10px] font-bold uppercase tracking-widest text-emerald-600 mb-1.5 flex items-center gap-1">
                                <i data-lucide="message-circle" class="h-3 w-3"></i> WhatsApp
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-2.5 flex items-center text-slate-400 text-xs font-medium">+62</span>
                                <input id="notifPhoneBoth" type="tel" placeholder="812-3456-7890" autocomplete="tel"
                                       class="w-full pl-10 pr-2.5 py-2 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent placeholder-slate-400 text-slate-800">
                            </div>
                        </div>
                        {{-- FCM Column --}}
                        <div>
                            <label class="text-[10px] font-bold uppercase tracking-widest text-blue-600 mb-1.5 flex items-center gap-1">
                                <i data-lucide="bell" class="h-3 w-3"></i> Notif Browser
                            </label>
                            <div id="fcmPermissionBoxBoth" class="flex items-center gap-2 p-2 rounded-xl border border-slate-200 bg-slate-50 h-[38px]">
                                <div id="fcmStatusIconBoth" class="flex h-6 w-6 shrink-0 items-center justify-center rounded-md bg-slate-200 text-slate-500">
                                    <i data-lucide="bell-off" class="h-3 w-3"></i>
                                </div>
                                <p id="fcmStatusTextBoth" class="text-[11px] font-medium text-slate-600 flex-1 truncate">Belum diizinkan</p>
                                <button type="button" onclick="requestFcmPermission()" id="fcmPermitBtnBoth"
                                        class="px-2 py-0.5 bg-blue-600 hover:bg-blue-700 text-white text-[10px] font-semibold rounded transition-colors">
                                    Izinkan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Waktu Pengingat --}}
            <div class="px-4 pb-3 shrink-0">
                <label class="text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-1.5 block">Waktu Pengingat</label>
                <div class="flex gap-2">
                    <select id="reminderMinutes" onchange="handleReminderChange(this.value)"
                            class="flex-1 px-3 py-2 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-slate-800 bg-white">
                        <option value="15">15 menit sebelum</option>
                        <option value="30">30 menit sebelum</option>
                        <option value="60" selected>1 jam sebelum</option>
                        <option value="120">2 jam sebelum</option>
                        <option value="180">3 jam sebelum</option>
                        <option value="360">6 jam sebelum</option>
                        <option value="1440">1 hari sebelum</option>
                        <option value="custom">Custom...</option>
                    </select>
                </div>
                {{-- Custom input (hidden by default) --}}
                <div id="customReminderSection" class="mt-2 hidden">
                    <div class="flex gap-2 items-center">
                        <input id="customReminderValue" type="number" min="5" max="10080" value="60" 
                               class="w-20 px-3 py-2 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-800">
                        <select id="customReminderUnit" onchange="updateCustomReminder()"
                                class="px-3 py-2 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-800 bg-white">
                            <option value="1">menit</option>
                            <option value="60" selected>jam</option>
                            <option value="1440">hari</option>
                        </select>
                        <span class="text-xs text-slate-500">sebelum</span>
                    </div>
                </div>
            </div>

            {{-- Search --}}
            <div class="px-4 pb-2.5 shrink-0 border-t border-slate-100 pt-3">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i data-lucide="search" class="h-3.5 w-3.5 text-slate-400"></i>
                    </div>
                    <input id="notifSearch" type="text" placeholder="Cari agenda..." autocomplete="off"
                           class="w-full pl-8 pr-3 py-1.5 text-sm border border-slate-200 rounded-lg bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder-slate-400 text-slate-800">
                </div>
                <p class="mt-1.5 text-[10px] text-slate-400">Pilih maksimal 5 agenda untuk diingatkan.</p>
            </div>

            {{-- Agenda list --}}
            <div id="notifAgendaList" class="flex-1 overflow-y-auto px-4 pb-2 space-y-1.5 min-h-0">
                <div class="flex items-center justify-center py-6">
                    <div class="h-5 w-5 border-2 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
                </div>
            </div>

            {{-- Selected count --}}
            <div class="px-4 py-1.5 shrink-0 border-t border-slate-100 bg-slate-50/60">
                <p id="notifSelectedCount" class="text-[11px] font-semibold text-slate-500">0 agenda dipilih</p>
            </div>

            {{-- Submit --}}
            <div class="px-4 py-3 shrink-0 border-t border-slate-200 bg-white">
                <div id="notifFormMsg" class="hidden mb-2 rounded-lg px-3 py-1.5 text-xs font-semibold"></div>
                <button id="notifSubmit" type="button" onclick="submitNotifSubscribe()"
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed text-white text-sm font-semibold rounded-xl transition-colors">
                    <i data-lucide="send" class="h-4 w-4"></i>
                    Daftar Pengingat
                </button>
            </div>
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
    apiKey: '{{ config("services.firebase.api_key", "") }}',
    authDomain: '{{ config("services.firebase.auth_domain", "") }}',
    projectId: '{{ config("services.firebase.project_id", "") }}',
    storageBucket: '{{ config("services.firebase.storage_bucket", "") }}',
    messagingSenderId: '{{ config("services.firebase.messaging_sender_id", "") }}',
    appId: '{{ config("services.firebase.app_id", "") }}',
};
window.FIREBASE_VAPID_KEY = '{{ config("services.firebase.vapid_key", "") }}';

const selectedAgendaIds = new Set();
let selectedChannel = 'whatsapp';
let fcmToken = null;
let selectedReminderMinutes = 60; // Default 1 jam

// Handle reminder time selection
function handleReminderChange(value) {
    const customSection = document.getElementById('customReminderSection');
    
    if (value === 'custom') {
        customSection.classList.remove('hidden');
        updateCustomReminder();
    } else {
        customSection.classList.add('hidden');
        selectedReminderMinutes = parseInt(value);
    }
}

function updateCustomReminder() {
    const value = parseInt(document.getElementById('customReminderValue').value) || 60;
    const unit = parseInt(document.getElementById('customReminderUnit').value) || 1;
    selectedReminderMinutes = value * unit;
    
    // Validate: min 5 minutes, max 7 days (10080 minutes)
    if (selectedReminderMinutes < 5) selectedReminderMinutes = 5;
    if (selectedReminderMinutes > 10080) selectedReminderMinutes = 10080;
}

const STATUS_STYLES = {
    terjadwal:   { label: 'Terjadwal',   cls: 'bg-amber-100 text-amber-700 border-amber-200' },
    berlangsung: { label: 'Berlangsung', cls: 'bg-blue-100 text-blue-700 border-blue-200' },
    selesai:     { label: 'Selesai',     cls: 'bg-emerald-100 text-emerald-700 border-emerald-200' },
    dibatalkan:  { label: 'Dibatalkan',  cls: 'bg-red-100 text-red-600 border-red-200' },
};

function setChannel(channel) {
    selectedChannel = channel;
    
    // Update button styles
    const channels = ['whatsapp', 'fcm', 'both'];
    const activeColors = {
        whatsapp: 'border-emerald-500 bg-emerald-50 text-emerald-700',
        fcm: 'border-blue-500 bg-blue-50 text-blue-700',
        both: 'border-purple-500 bg-purple-50 text-purple-700',
    };
    
    channels.forEach(ch => {
        const btn = document.getElementById('channel' + ch.charAt(0).toUpperCase() + ch.slice(1));
        if (ch === channel) {
            btn.className = `flex-1 flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl border-2 text-sm font-semibold transition-all ${activeColors[ch]}`;
        } else {
            btn.className = 'flex-1 flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl border-2 text-sm font-semibold transition-all border-slate-200 text-slate-600 hover:border-slate-300';
        }
    });
    
    // Show/hide input sections - 3 mutually exclusive views
    const waSection = document.getElementById('waInputSection');
    const fcmSection = document.getElementById('fcmSection');
    const bothSection = document.getElementById('bothSection');
    
    if (channel === 'whatsapp') {
        waSection.classList.remove('hidden');
        fcmSection.classList.add('hidden');
        bothSection.classList.add('hidden');
    } else if (channel === 'fcm') {
        waSection.classList.add('hidden');
        fcmSection.classList.remove('hidden');
        bothSection.classList.add('hidden');
        checkFcmPermission();
    } else { // both - compact grid layout
        waSection.classList.add('hidden');
        fcmSection.classList.add('hidden');
        bothSection.classList.remove('hidden');
        checkFcmPermission();
    }
    
    lucide.createIcons();
}

function checkFcmPermission() {
    // Single mode elements
    const statusIcon = document.getElementById('fcmStatusIcon');
    const statusText = document.getElementById('fcmStatusText');
    const statusDesc = document.getElementById('fcmStatusDesc');
    const permitBtn = document.getElementById('fcmPermitBtn');
    
    // Both mode elements (compact version)
    const statusIconBoth = document.getElementById('fcmStatusIconBoth');
    const statusTextBoth = document.getElementById('fcmStatusTextBoth');
    const permitBtnBoth = document.getElementById('fcmPermitBtnBoth');
    
    if (!('Notification' in window)) {
        // Single mode
        statusIcon.innerHTML = '<i data-lucide="alert-circle" class="h-3.5 w-3.5"></i>';
        statusIcon.className = 'flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-red-100 text-red-600';
        statusText.textContent = 'Browser tidak mendukung';
        statusDesc.textContent = 'Gunakan browser modern seperti Chrome atau Firefox';
        permitBtn.classList.add('hidden');
        // Both mode
        statusIconBoth.innerHTML = '<i data-lucide="alert-circle" class="h-3 w-3"></i>';
        statusIconBoth.className = 'flex h-6 w-6 shrink-0 items-center justify-center rounded-md bg-red-100 text-red-600';
        statusTextBoth.textContent = 'Tidak didukung';
        permitBtnBoth.classList.add('hidden');
        return;
    }
    
    const permission = Notification.permission;
    
    if (permission === 'granted' && fcmToken) {
        // Single mode
        statusIcon.innerHTML = '<i data-lucide="check-circle" class="h-3.5 w-3.5"></i>';
        statusIcon.className = 'flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-emerald-100 text-emerald-600';
        statusText.textContent = 'Notifikasi diizinkan';
        statusDesc.textContent = 'Anda akan menerima notifikasi di browser ini';
        permitBtn.classList.add('hidden');
        // Both mode
        statusIconBoth.innerHTML = '<i data-lucide="check-circle" class="h-3 w-3"></i>';
        statusIconBoth.className = 'flex h-6 w-6 shrink-0 items-center justify-center rounded-md bg-emerald-100 text-emerald-600';
        statusTextBoth.textContent = 'Diizinkan';
        permitBtnBoth.classList.add('hidden');
    } else if (permission === 'denied') {
        // Single mode
        statusIcon.innerHTML = '<i data-lucide="x-circle" class="h-3.5 w-3.5"></i>';
        statusIcon.className = 'flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-red-100 text-red-600';
        statusText.textContent = 'Notifikasi diblokir';
        statusDesc.textContent = 'Ubah di pengaturan browser Anda';
        permitBtn.classList.add('hidden');
        // Both mode
        statusIconBoth.innerHTML = '<i data-lucide="x-circle" class="h-3 w-3"></i>';
        statusIconBoth.className = 'flex h-6 w-6 shrink-0 items-center justify-center rounded-md bg-red-100 text-red-600';
        statusTextBoth.textContent = 'Diblokir';
        permitBtnBoth.classList.add('hidden');
    } else {
        // Single mode
        statusIcon.innerHTML = '<i data-lucide="bell-off" class="h-3.5 w-3.5"></i>';
        statusIcon.className = 'flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-slate-200 text-slate-500';
        statusText.textContent = 'Notifikasi belum diizinkan';
        statusDesc.textContent = 'Klik tombol untuk mengizinkan';
        permitBtn.classList.remove('hidden');
        // Both mode
        statusIconBoth.innerHTML = '<i data-lucide="bell-off" class="h-3 w-3"></i>';
        statusIconBoth.className = 'flex h-6 w-6 shrink-0 items-center justify-center rounded-md bg-slate-200 text-slate-500';
        statusTextBoth.textContent = 'Belum diizinkan';
        permitBtnBoth.classList.remove('hidden');
    }
    
    lucide.createIcons();
}

async function requestFcmPermission() {
    const permitBtn = document.getElementById('fcmPermitBtn');
    const permitBtnBoth = document.getElementById('fcmPermitBtnBoth');
    
    // Disable both buttons
    permitBtn.disabled = true;
    permitBtn.textContent = 'Memproses...';
    permitBtnBoth.disabled = true;
    permitBtnBoth.textContent = '...';
    
    try {
        const permission = await Notification.requestPermission();
        
        if (permission === 'granted') {
            // Try to get FCM token
            if (window.FirebaseNotification) {
                await window.FirebaseNotification.init();
                fcmToken = await window.FirebaseNotification.getToken();
            } else {
                // Fallback: just mark as granted without FCM
                fcmToken = 'browser-notification-' + Date.now();
            }
        }
        
        checkFcmPermission();
    } catch (error) {
        console.error('FCM permission error:', error);
        showFormMsg('Gagal mengaktifkan notifikasi: ' + error.message, false);
    } finally {
        permitBtn.disabled = false;
        permitBtn.textContent = 'Izinkan';
        permitBtnBoth.disabled = false;
        permitBtnBoth.textContent = 'Izinkan';
    }
}

let notifDebounce;
document.getElementById('notifSearch').addEventListener('input', function() {
    clearTimeout(notifDebounce);
    notifDebounce = setTimeout(() => loadAgendas(this.value.trim()), 350);
});

function loadAgendas(q) {
    const list = document.getElementById('notifAgendaList');
    list.innerHTML = `<div class="flex items-center justify-center py-8"><div class="h-5 w-5 border-2 border-blue-500 border-t-transparent rounded-full animate-spin"></div></div>`;

    fetch(`{{ route('agenda.notify.search') }}?q=${encodeURIComponent(q)}`)
        .then(r => r.json())
        .then(items => renderAgendaList(items))
        .catch(() => {
            list.innerHTML = `<p class="text-center text-sm text-slate-400 py-8">Gagal memuat agenda.</p>`;
        });
}

function renderAgendaList(items) {
    const list = document.getElementById('notifAgendaList');
    if (!items.length) {
        list.innerHTML = `<p class="text-center text-sm text-slate-400 py-8">Tidak ada agenda mendatang.</p>`;
        return;
    }

    list.innerHTML = items.map(a => {
        const st = STATUS_STYLES[a.status] || { label: a.status, cls: 'bg-slate-100 text-slate-600 border-slate-200' };
        const checked = selectedAgendaIds.has(a.id);
        return `<label class="flex items-start gap-3 rounded-xl border cursor-pointer p-3 transition-all ${checked ? 'border-blue-400 bg-blue-50' : 'border-slate-200 hover:border-slate-300 hover:bg-slate-50'}" data-agenda-id="${a.id}">
            <div class="pt-0.5 shrink-0">
                <div class="h-4 w-4 rounded border-2 flex items-center justify-center transition-all ${checked ? 'bg-blue-600 border-blue-600' : 'border-slate-300 bg-white'}">
                    ${checked ? '<svg class="h-2.5 w-2.5 text-white" fill="none" viewBox="0 0 10 10"><path d="M1.5 5l2.5 2.5L8.5 2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>' : ''}
                </div>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-bold text-slate-900 leading-tight truncate">${a.perihal_kegiatan}</p>
                <p class="text-[11px] text-slate-500 mt-0.5 truncate">${a.waktu_mulai || '-'} &bull; ${a.tempat || '-'}</p>
                <span class="mt-1 inline-block text-[10px] font-semibold px-2 py-0.5 rounded-full border ${st.cls}">${st.label}</span>
            </div>
        </label>`;
    }).join('');

    list.querySelectorAll('label[data-agenda-id]').forEach(el => {
        el.addEventListener('click', () => toggleAgendaSelection(parseInt(el.dataset.agendaId)));
    });
}

function toggleAgendaSelection(id) {
    if (selectedAgendaIds.has(id)) {
        selectedAgendaIds.delete(id);
    } else {
        if (selectedAgendaIds.size >= 5) {
            showFormMsg('Maksimal 5 agenda dapat dipilih.', false);
            return;
        }
        selectedAgendaIds.add(id);
    }
    updateSelectedCount();
    loadAgendas(document.getElementById('notifSearch').value.trim());
}

function updateSelectedCount() {
    const n = selectedAgendaIds.size;
    document.getElementById('notifSelectedCount').textContent = n === 0 ? '0 agenda dipilih' : `${n} agenda dipilih`;
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

async function submitNotifSubscribe() {
    const btn = document.getElementById('notifSubmit');
    const phoneInput = document.getElementById('notifPhone');
    const phoneInputBoth = document.getElementById('notifPhoneBoth');
    
    // Validation
    if (selectedAgendaIds.size === 0) {
        showFormMsg('Pilih minimal satu agenda.', false);
        return;
    }
    
    const needsPhone = selectedChannel === 'whatsapp' || selectedChannel === 'both';
    const needsFcm = selectedChannel === 'fcm' || selectedChannel === 'both';
    
    let phone = null;
    if (needsPhone) {
        // Get phone from the correct input based on channel
        const rawPhone = selectedChannel === 'both' 
            ? phoneInputBoth.value.trim() 
            : phoneInput.value.trim();
        phone = normalizePhone(rawPhone);
        if (phone.length < 10) {
            showFormMsg('Masukkan nomor WhatsApp yang valid.', false);
            return;
        }
    }
    
    if (needsFcm && !fcmToken && Notification.permission !== 'granted') {
        showFormMsg('Izinkan notifikasi browser terlebih dahulu.', false);
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<div class="h-4 w-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div> Memproses...';

    try {
        if (needsFcm && !fcmToken && window.FirebaseNotification) {
            try {
                await window.FirebaseNotification.init();
                await window.FirebaseNotification.requestPermission();
                fcmToken = await window.FirebaseNotification.getToken();
            } catch (tokenErr) {
                console.error('FCM token init error:', tokenErr);
                showFormMsg('Gagal mendapatkan token browser. Coba aktifkan notifikasi ulang.', false);
                return;
            }
        }

        const payload = {
            channel: selectedChannel,
            agenda_ids: [...selectedAgendaIds],
            reminder_minutes: selectedReminderMinutes,
        };
        
        if (phone) payload.phone_number = phone;
        if (fcmToken) payload.fcm_token = fcmToken;

        const res = await fetch('{{ route('notify.subscribe') }}', {
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
            showFormMsg(data.message || 'Berhasil! Anda akan menerima pengingat.', true);
            selectedAgendaIds.clear();
            phoneInput.value = '';
            phoneInputBoth.value = '';
            updateSelectedCount();
            loadAgendas('');
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
        btn.innerHTML = '<i data-lucide="send" class="h-4 w-4"></i> Daftar Pengingat';
        lucide.createIcons();
    }
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
