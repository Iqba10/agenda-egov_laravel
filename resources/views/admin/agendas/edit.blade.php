<x-app-layout>
    <x-slot name="header">Edit Agenda</x-slot>
    <x-slot name="subheader">Perbarui informasi agenda yang sudah ada.</x-slot>

    @php
        $statusBadge = [
            'terjadwal'   => 'bg-amber-100 text-amber-700 border border-amber-200',
            'berlangsung' => 'bg-blue-100 text-blue-700 border border-blue-200',
            'selesai'     => 'bg-emerald-100 text-emerald-700 border border-emerald-200',
            'dibatalkan'  => 'bg-red-100 text-red-700 border border-red-200',
        ];
        $badgeClass = $statusBadge[$agenda->effective_status] ?? 'bg-slate-100 text-slate-600 border border-slate-200';
    @endphp

    <div class="flex flex-col gap-6 lg:flex-row">

        <aside class="order-2 flex-shrink-0 space-y-4 lg:order-1 lg:w-64">

            <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="flex items-center gap-2 border-b border-slate-100 px-4 py-3">
                    <i data-lucide="info" class="h-4 w-4 text-slate-400"></i>
                    <h3 class="text-sm font-semibold text-slate-700">Info Agenda</h3>
                </div>
                <div class="space-y-3 p-4">
                    <div>
                        <span class="mb-1 block text-xs text-slate-400">Status Saat Ini</span>
                        <span class="inline-block rounded px-2 py-0.5 text-xs font-semibold {{ $badgeClass }}">
                            {{ ucfirst($agenda->effective_status) }}
                        </span>
                    </div>
                    <div>
                        <span class="mb-0.5 block text-xs text-slate-400">Jenis</span>
                        <span class="text-sm text-slate-700">{{ ucfirst($agenda->jenis_agenda ?? '-') }}</span>
                    </div>
                    <div>
                        <span class="mb-0.5 block text-xs text-slate-400">Waktu Mulai</span>
                        <span class="text-sm text-slate-700">{{ optional($agenda->waktu_mulai)->translatedFormat('d M Y, H:i') ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="mb-0.5 block text-xs text-slate-400">Tempat</span>
                        <span class="text-sm text-slate-700 line-clamp-2">{{ $agenda->tempat ?? '-' }}</span>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="flex items-center gap-2 border-b border-slate-100 px-4 py-3">
                    <i data-lucide="zap" class="h-4 w-4 text-slate-400"></i>
                    <h3 class="text-sm font-semibold text-slate-700">Aksi Cepat</h3>
                </div>
                <div class="p-2 space-y-1">
                    <a href="{{ route('admin.agendas.show', $agenda) }}"
                       class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm text-slate-600 hover:bg-slate-50 transition-colors">
                        <i data-lucide="eye" class="h-4 w-4 text-blue-500"></i>
                        Lihat Detail
                    </a>
                    <form method="POST" action="{{ route('admin.agendas.destroy', $agenda) }}"
                          data-confirm="Agenda ini akan dihapus permanen. Lanjutkan?">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                            <i data-lucide="trash-2" class="h-4 w-4"></i>
                            Hapus Agenda
                        </button>
                    </form>
                </div>
            </div>

        </aside>

        <main class="order-1 min-w-0 flex-1 lg:order-2">
            <form method="POST" action="{{ route('admin.agendas.update', $agenda) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @include('admin.agendas._form')
            </form>
        </main>

    </div>
</x-app-layout>
