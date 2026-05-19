<x-app-layout>
    <x-slot name="header">Agenda Kegiatan</x-slot>
    <x-slot name="subheader">Kelola agenda dan pantau status kegiatan terbaru.</x-slot>

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

    <section class="dashboard-panel mt-6 animate-dashboard" style="animation-delay: .25s">
        <div class="dashboard-panel-header">
            <h2 class="dashboard-panel-title">
                <span class="dashboard-icon-tile"><i data-lucide="list" class="h-5 w-5"></i></span>
                Daftar Agenda Kegiatan
            </h2>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.agendas.print', request()->query()) }}" target="_blank" class="btn-danger">
                    <i data-lucide="printer" class="h-4 w-4"></i>
                    <span class="hidden sm:inline">Cetak Laporan</span>
                </a>
                <a href="{{ route('admin.agendas.create') }}" class="btn-primary">
                    <i data-lucide="plus" class="h-4 w-4"></i>
                    <span class="hidden sm:inline">Tambah Agenda</span>
                </a>
            </div>
        </div>

        <form action="{{ route('admin.dashboard') }}" method="GET" class="border-b border-slate-100 bg-slate-50/50 p-4 flex flex-col md:flex-row gap-3">
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
                    <a href="{{ route('admin.dashboard', array_filter(['status' => request('status')])) }}" class="px-4 py-2 bg-red-100 hover:bg-red-200 text-red-600 text-sm font-medium rounded-lg transition-colors flex items-center gap-2" title="Reset Filter">
                        <i data-lucide="x" class="h-4 w-4"></i>
                    </a>
                @endif
            </div>
        </form>

        <div class="dashboard-filter-bar">
            @php($active = $status ?: 'semua')
            @foreach ([
                'semua'       => ['label' => 'Semua',       'icon' => 'list',            'count' => $stats['total'],       'active' => 'dashboard-filter-active-slate'],
                'terjadwal'   => ['label' => 'Terjadwal',   'icon' => 'clock',           'count' => $stats['terjadwal'],   'active' => 'dashboard-filter-active-amber'],
                'berlangsung' => ['label' => 'Berlangsung', 'icon' => 'radio',           'count' => $stats['berlangsung'], 'active' => 'dashboard-filter-active-blue'],
                'selesai'     => ['label' => 'Selesai',     'icon' => 'circle-check-big','count' => $stats['selesai'],     'active' => 'dashboard-filter-active-emerald'],
                'dibatalkan'  => ['label' => 'Dibatalkan',  'icon' => 'circle-x',        'count' => $stats['dibatalkan'],  'active' => 'dashboard-filter-active-red'],
            ] as $value => $item)
                <a href="{{ route('admin.dashboard', array_filter(['status' => $value === 'semua' ? null : $value, 'search' => request('search'), 'month' => request('month'), 'year' => request('year')])) }}"
                   class="dashboard-filter {{ $active === $value ? $item['active'] : '' }}">
                    <i data-lucide="{{ $item['icon'] }}" class="h-4 w-4"></i>
                    {{ $item['label'] }}
                    <span class="text-xs opacity-75">{{ $item['count'] }}</span>
                </a>
            @endforeach
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-slate-200 bg-slate-50">
                    <tr>
                        <th class="w-12 px-4 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-600">No</th>
                        <th class="px-4 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-600">Agenda & Waktu</th>
                        <th class="hidden px-4 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-600 md:table-cell">Tempat</th>
                        <th class="w-32 px-4 py-3.5 text-center text-xs font-bold uppercase tracking-wider text-slate-600">Status</th>
                        <th class="w-32 px-4 py-3.5 text-center text-xs font-bold uppercase tracking-wider text-slate-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse ($agendas as $agenda)
                        <tr class="dashboard-row">
                            <td class="px-4 py-4 font-medium text-slate-500">{{ $agendas->firstItem() + $loop->index }}</td>
                            <td class="px-4 py-4">
                                <a href="{{ route('admin.agendas.show', $agenda) }}" class="font-semibold text-slate-800 hover:text-blue-600 transition-colors">{{ $agenda->perihal_kegiatan }}</a>
                                <div class="mt-1 flex flex-wrap items-center gap-3 text-xs text-slate-500">
                                    <span class="inline-flex items-center gap-1"><i data-lucide="calendar" class="h-3.5 w-3.5 text-slate-400"></i>{{ optional($agenda->waktu_mulai)->format('d M Y') }}</span>
                                    <span class="inline-flex items-center gap-1"><i data-lucide="clock" class="h-3.5 w-3.5 text-slate-400"></i>{{ optional($agenda->waktu_mulai)->format('H:i') }} WIB</span>
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
                                    <a href="{{ route('admin.agendas.show', $agenda) }}" class="dashboard-action-icon text-blue-600 hover:bg-blue-50" title="Detail"><i data-lucide="eye" class="h-4 w-4"></i></a>
                                    <a href="{{ route('admin.agendas.edit', $agenda) }}" class="dashboard-action-icon text-amber-600 hover:bg-amber-50" title="Edit"><i data-lucide="pencil" class="h-4 w-4"></i></a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-16 text-center text-slate-400">
                                <i data-lucide="inbox" class="mx-auto h-12 w-12 opacity-50"></i>
                                <p class="mt-3 font-medium">Tidak ada agenda</p>
                                <p class="mt-1 text-sm">Klik Tambah Agenda untuk membuat agenda baru</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div class="mt-6">{{ $agendas->links() }}</div>

    @push('scripts')
    <script>
        const adminSearchInput = document.querySelector('input[name="search"]');
        if (adminSearchInput) {
            const form = adminSearchInput.closest('form');
            let debounceTimer;
            adminSearchInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => form.submit(), 600);
            });
        }
    </script>
    @endpush
</x-app-layout>
