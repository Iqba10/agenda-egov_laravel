<x-app-layout>
    <x-slot name="header">Dashboard Admin</x-slot>
    <x-slot name="subheader">Ringkasan sistem, aktivitas terbaru, dan kontrol operasional utama.</x-slot>

    <div class="dashboard-grid">
        <div class="dashboard-stat-card animate-dashboard" style="animation-delay: .05s">
            <div class="relative z-10">
                <p class="dashboard-stat-label">Total Agenda</p>
                <p class="dashboard-stat-value">{{ $stats['agendas'] }}</p>
                <p class="dashboard-stat-helper">Semua kegiatan</p>
            </div>
            <i data-lucide="calendar-check" class="dashboard-stat-icon text-blue-100"></i>
        </div>
        <div class="dashboard-stat-card animate-dashboard" style="animation-delay: .1s">
            <div class="relative z-10">
                <p class="dashboard-stat-label">Terjadwal</p>
                <p class="dashboard-stat-value">{{ $stats['terjadwal'] }}</p>
                <p class="dashboard-stat-helper">Akan datang</p>
            </div>
            <i data-lucide="clock" class="dashboard-stat-icon text-amber-100"></i>
        </div>
        <div class="dashboard-stat-card animate-dashboard" style="animation-delay: .15s">
            <div class="relative z-10">
                <p class="dashboard-stat-label">Selesai</p>
                <p class="dashboard-stat-value">{{ $stats['selesai'] }}</p>
                <p class="dashboard-stat-helper">Terlaksana</p>
            </div>
            <i data-lucide="circle-check-big" class="dashboard-stat-icon text-emerald-100"></i>
        </div>
        <div class="dashboard-stat-card animate-dashboard" style="animation-delay: .2s">
            <div class="relative z-10">
                <p class="dashboard-stat-label">Dibatalkan</p>
                <p class="dashboard-stat-value">{{ $stats['dibatalkan'] }}</p>
                <p class="dashboard-stat-helper">Tidak jadi</p>
            </div>
            <i data-lucide="circle-x" class="dashboard-stat-icon text-red-100"></i>
        </div>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-[1.25fr_.75fr]">
        <section class="dashboard-panel animate-dashboard" style="animation-delay: .25s">
            <div class="dashboard-panel-header">
                <h2 class="dashboard-panel-title">
                    <span class="dashboard-icon-tile"><i data-lucide="list" class="h-5 w-5"></i></span>
                    Agenda Terbaru
                </h2>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('admin.reports.index') }}" class="btn-danger">
                        <i data-lucide="printer" class="h-4 w-4"></i>
                        <span class="hidden sm:inline">Cetak Laporan</span>
                    </a>
                    <a href="{{ route('admin.agendas.create') }}" class="btn-primary">
                        <i data-lucide="plus" class="h-4 w-4"></i>
                        <span class="hidden sm:inline">Tambah Agenda</span>
                    </a>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="border-b border-slate-200 bg-slate-50">
                        <tr>
                            <th class="w-12 px-4 py-2.5 text-left text-xs font-bold uppercase tracking-wider text-slate-600">No</th>
                            <th class="px-4 py-2.5 text-left text-xs font-bold uppercase tracking-wider text-slate-600">Agenda & Waktu</th>
                            <th class="hidden px-4 py-2.5 text-left text-xs font-bold uppercase tracking-wider text-slate-600 md:table-cell">Tempat</th>
                            <th class="w-32 px-4 py-2.5 text-center text-xs font-bold uppercase tracking-wider text-slate-600">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse ($recentAgendas as $agenda)
                            <tr class="dashboard-row">
                                <td class="px-4 py-3 font-medium text-slate-500">{{ $loop->iteration }}</td>
                                <td class="px-4 py-3">
                                    <div class="font-semibold text-slate-800">{{ $agenda->perihal_kegiatan }}</div>
                                    <div class="mt-1 flex flex-wrap items-center gap-3 text-xs text-slate-500">
                                        <span class="inline-flex items-center gap-1"><i data-lucide="calendar" class="h-3 w-3 text-slate-400"></i>{{ optional($agenda->waktu_mulai)->format('d M Y') }}</span>
                                        <span class="inline-flex items-center gap-1"><i data-lucide="clock" class="h-3 w-3 text-slate-400"></i>{{ optional($agenda->waktu_mulai)->format('H:i') }} WIB</span>
                                    </div>
                                    <div class="mt-1 flex items-center gap-1 text-xs text-slate-500 md:hidden">
                                        <i data-lucide="map-pin" class="h-3 w-3 text-slate-400"></i>{{ $agenda->tempat }}
                                    </div>
                                </td>
                                <td class="hidden px-4 py-3 text-slate-600 md:table-cell"><span class="inline-flex items-center gap-1.5"><i data-lucide="map-pin" class="h-3 w-3 text-slate-400"></i>{{ $agenda->tempat }}</span></td>
                                <td class="px-4 py-3 text-center"><span class="{{ $agenda->status_badge_class }}">{{ ucfirst($agenda->effective_status) }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-12 text-center text-slate-400">
                                    <i data-lucide="inbox" class="mx-auto h-10 w-10 opacity-50"></i>
                                    <p class="mt-3 font-medium">Tidak ada agenda</p>
                                    <p class="mt-1 text-sm">Klik Tambah Agenda untuk membuat agenda baru</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="dashboard-panel animate-dashboard" style="animation-delay: .3s">
            <div class="dashboard-panel-header">
                <h2 class="dashboard-panel-title">
                    <span class="dashboard-icon-tile"><i data-lucide="users" class="h-5 w-5"></i></span>
                    Pengguna Terbaru
                </h2>
                <a href="{{ route('admin.users.index') }}" class="btn-secondary">Kelola</a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse ($recentUsers as $user)
                    <div class="flex items-center justify-between gap-3 px-5 py-3 transition hover:bg-slate-50">
                        <div>
                            <div class="font-semibold text-slate-800">{{ $user->name }}</div>
                            <div class="mt-1 text-xs text-slate-500">{{ $user->email }}</div>
                        </div>
                        <span class="badge-blue">{{ $user->role }}</span>
                    </div>
                @empty
                    <div class="px-5 py-8 text-center text-sm text-slate-500">Belum ada pengguna terbaru.</div>
                @endforelse
            </div>
        </section>
    </div>
</x-app-layout>
