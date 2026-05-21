<x-app-layout>
    <x-slot name="header">Detail Agenda</x-slot>
    <x-slot name="subheader">Tinjau dan kelola data agenda kegiatan.</x-slot>

    @php
        $effStatus = $agenda->effective_status;
        $statusColors = [
            'terjadwal'   => 'bg-amber-100 text-amber-700',
            'berlangsung' => 'bg-blue-100 text-blue-700',
            'selesai'     => 'bg-emerald-100 text-emerald-700',
            'dibatalkan'  => 'bg-red-100 text-red-700',
        ];
        $statusColor = $statusColors[$effStatus] ?? 'bg-slate-100 text-slate-600';
        $jenisColor = $agenda->jenis_agenda === 'internal' ? 'bg-emerald-100 text-emerald-700' : 'bg-purple-100 text-purple-700';
    @endphp

    {{-- Header Card (native_old style) --}}
    <div class="bg-gradient-to-r from-slate-900 to-slate-800 rounded-lg p-6 mb-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div class="flex-1 min-w-0">
                <div class="flex flex-wrap items-center gap-2 mb-3">
                    <span class="text-xs px-2 py-1 rounded {{ $jenisColor }}">{{ ucfirst($agenda->jenis_agenda) }}</span>
                    <span class="text-xs px-2 py-1 rounded {{ $statusColor }}">{{ ucfirst($effStatus) }}</span>
                </div>
                <h2 class="text-xl sm:text-2xl font-bold text-white mb-2">{{ $agenda->perihal_kegiatan }}</h2>
                <p class="text-slate-400 text-sm">
                    <i data-lucide="building" class="h-3.5 w-3.5 inline-block mr-1"></i>
                    {{ $agenda->asal_surat ?: 'Dinas Komunikasi dan Informatika Kabupaten Sambas' }}
                </p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('agenda.show', $agenda) }}" target="_blank"
                   class="inline-flex items-center gap-2 px-3 py-2 text-xs font-semibold bg-white/10 text-white hover:bg-white/20 border border-white/20 rounded-lg transition-all">
                    <i data-lucide="external-link" class="h-3.5 w-3.5"></i> Lihat Publik
                </a>
                <a href="{{ route('admin.agendas.edit', $agenda) }}"
                   class="inline-flex items-center gap-2 px-3 py-2 text-xs font-semibold bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all shadow-sm">
                    <i data-lucide="pencil" class="h-3.5 w-3.5"></i> Edit
                </a>
            </div>
        </div>
    </div>

    {{-- Main content --}}
    <div class="grid gap-6 xl:grid-cols-[1fr_300px]">

        {{-- Left Column --}}
        <div class="space-y-6">
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                @php
                    $infoItems = [
                        ['label' => 'Asal Surat', 'value' => $agenda->asal_surat, 'icon' => 'mail'],
                        ['label' => 'Tgl. Surat', 'value' => optional($agenda->tanggal_surat)->translatedFormat('d M Y'), 'icon' => 'calendar-days'],
                        ['label' => 'Pakaian', 'value' => $agenda->pakaian, 'icon' => 'shirt'],
                        ['label' => 'Diinput Oleh', 'value' => $agenda->diinput_oleh, 'icon' => 'user'],
                        ['label' => 'Dibuat', 'value' => optional($agenda->created_at)->translatedFormat('d M Y'), 'icon' => 'clock'],
                        ['label' => 'Diupdate', 'value' => optional($agenda->updated_at)->translatedFormat('d M Y'), 'icon' => 'history'],
                    ];
                @endphp

                @foreach($infoItems as $item)
                    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1.5 mb-1">
                            <i data-lucide="{{ $item['icon'] }}" class="h-3 w-3"></i>
                            {{ $item['label'] }}
                        </span>
                        <span class="text-sm font-semibold text-slate-700 leading-tight block">{{ $item['value'] ?: '-' }}</span>
                    </div>
                @endforeach
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4 pb-2 border-b border-slate-50">Rincian Tambahan</h3>
                <div class="divide-y divide-slate-50">
                    @php
                        $details = [
                            ['label' => 'Petugas Ditugaskan', 'value' => $agenda->petugas_ditugaskan, 'icon' => 'user-check'],
                            ['label' => 'Disposisi', 'value' => $agenda->disposisi, 'icon' => 'share-2'],
                            ['label' => 'Keterangan', 'value' => $agenda->keterangan, 'icon' => 'sticky-note'],
                        ];
                    @endphp

                    @foreach($details as $detail)
                        <div class="py-4 first:pt-0 last:pb-0">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1.5 mb-1">
                                <i data-lucide="{{ $detail['icon'] }}" class="h-3 w-3"></i>
                                {{ $detail['label'] }}
                            </span>
                            <div class="text-sm text-slate-600 leading-relaxed whitespace-pre-line">{{ $detail['value'] ?: '-' }}</div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-50">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Dokumen Terlampir</h3>
                    <a href="{{ route('admin.agendas.edit', $agenda) }}#documents-section" class="text-xs font-bold text-indigo-600 hover:text-indigo-700 flex items-center gap-1">
                        <i data-lucide="plus" class="h-3 w-3"></i> Kelola
                    </a>
                </div>
                
                @if($agenda->documents->count() > 0)
                    <div class="divide-y divide-slate-100 -mx-6 -mb-6">
                        @foreach($agenda->documents as $doc)
                            <div class="px-6 py-4">
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg {{ $doc->type === 'pdf' ? 'bg-red-50' : ($doc->type === 'image' ? 'bg-emerald-50' : 'bg-blue-50') }}">
                                        @if($doc->type === 'image')
                                            <i data-lucide="image" class="h-5 w-5 text-emerald-500"></i>
                                        @elseif($doc->type === 'pdf')
                                            <i data-lucide="file-text" class="h-5 w-5 text-red-500"></i>
                                        @else
                                            <i data-lucide="file" class="h-5 w-5 text-blue-500"></i>
                                        @endif
                                    </div>
                                    <div class="flex-1 overflow-hidden">
                                        <p class="text-sm font-bold text-slate-700 truncate">{{ $doc->original_name }}</p>
                                        <p class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">{{ $doc->extension }} &bull; {{ $doc->created_at->translatedFormat('d M Y') }}</p>
                                    </div>
                                    <button onclick="docDownload('{{ $doc->download_url }}','{{ addslashes($doc->original_name) }}')"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold bg-slate-800 text-white rounded-lg hover:bg-slate-700 transition-colors flex-shrink-0">
                                        <i data-lucide="download" class="h-3.5 w-3.5"></i> Unduh
                                    </button>
                                </div>
                                @if($doc->type === 'pdf')
                                    <div class="rounded-lg overflow-hidden border border-slate-200 bg-slate-50 relative">
                                        <div id="doc-loading-{{ $doc->id }}" class="absolute inset-0 flex items-center justify-center text-xs text-slate-400">Memuat dokumen...</div>
                                        <iframe id="doc-frame-{{ $doc->id }}" src="{{ $doc->url }}#toolbar=0&navpanes=0&scrollbar=0" class="w-full relative z-10" style="height:480px;" frameborder="0"></iframe>
                                    </div>
                                @elseif($doc->type === 'image')
                                    <div class="rounded-lg overflow-hidden border border-slate-200 bg-slate-50 flex items-center justify-center p-2 min-h-24">
                                        <div id="doc-loading-{{ $doc->id }}" class="text-xs text-slate-400">Memuat gambar...</div>
                                        <img id="doc-img-{{ $doc->id }}" alt="{{ $doc->original_name }}" class="max-h-80 w-auto object-contain rounded hidden">
                                    </div>
                                @else
                                    <div class="rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 flex items-center gap-3 text-sm text-slate-500">
                                        <i data-lucide="info" class="h-4 w-4 flex-shrink-0"></i>
                                        Preview tidak tersedia untuk tipe file ini.
                                    </div>
                                @endif
                            </div>
                            @if($doc->type === 'pdf' || $doc->type === 'image')
                                <script>
                                    @if($doc->type === 'image')
                                        fetch('{{ $doc->url }}')
                                            .then(r => r.blob())
                                            .then(blob => {
                                                const blobUrl = URL.createObjectURL(blob);
                                                const img = document.getElementById('doc-img-{{ $doc->id }}');
                                                if (img) { img.src = blobUrl; img.classList.remove('hidden'); }
                                                const loader = document.getElementById('doc-loading-{{ $doc->id }}');
                                                if (loader) loader.remove();
                                            })
                                            .catch(() => {
                                                const loader = document.getElementById('doc-loading-{{ $doc->id }}');
                                                if (loader) loader.textContent = 'Gagal memuat dokumen.';
                                            });
                                    @else
                                        const loader = document.getElementById('doc-loading-{{ $doc->id }}');
                                        if (loader) loader.remove();
                                    @endif
                                </script>
                            @endif
                        @endforeach
                    </div>
                @else
                    <div class="py-12 text-center border-2 border-dashed border-slate-100 rounded-2xl bg-slate-50/50">
                        <div class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-300 mb-3">
                            <i data-lucide="file-text" class="h-6 w-6"></i>
                        </div>
                        <p class="text-xs font-medium text-slate-400">Belum ada dokumen yang dilampirkan.</p>
                        <a href="{{ route('admin.agendas.edit', $agenda) }}#documents-section" class="mt-4 inline-flex items-center gap-1.5 text-xs font-bold text-indigo-600 hover:underline">
                            <i data-lucide="paperclip" class="h-3 w-3"></i> Lampirkan Dokumen
                        </a>
                    </div>
                @endif
            </div>
        </div>

        {{-- Right Column --}}
        <aside class="space-y-6">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4">Aksi Cepat</h3>
                <div class="space-y-2">
                    <a href="{{ route('admin.agendas.edit', $agenda) }}" class="flex items-center gap-3 w-full px-4 py-2.5 rounded-xl bg-slate-900 text-white text-sm font-bold hover:bg-slate-800 transition-all shadow-lg shadow-slate-950/10">
                        <i data-lucide="pencil" class="h-4 w-4"></i> Edit Agenda
                    </a>
                    <a href="{{ route('admin.agendas.create') }}" class="flex items-center gap-3 w-full px-4 py-2.5 rounded-xl border border-slate-200 text-slate-700 text-sm font-bold hover:bg-slate-50 transition-all">
                        <i data-lucide="plus" class="h-4 w-4"></i> Tambah Baru
                    </a>
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 w-full px-4 py-2.5 rounded-xl border border-slate-200 text-slate-700 text-sm font-bold hover:bg-slate-50 transition-all">
                        <i data-lucide="list" class="h-4 w-4"></i> Semua Agenda
                    </a>
                    <form method="POST" action="{{ route('admin.agendas.destroy', $agenda) }}"
                          data-confirm="Agenda ini akan dihapus permanen. Lanjutkan?">
                        @csrf @method('DELETE')
                        <button class="flex items-center gap-3 w-full px-4 py-2.5 rounded-xl text-red-600 text-sm font-bold hover:bg-red-50 transition-all">
                            <i data-lucide="trash-2" class="h-4 w-4"></i> Hapus Agenda
                        </button>
                    </form>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4">Agenda Lainnya</h3>
                <div class="space-y-3">
                    @forelse ($relatedAgendas->take(5) as $item)
                        <a href="{{ route('admin.agendas.show', $item) }}" class="group block p-3 rounded-xl border border-slate-100 hover:border-indigo-200 hover:bg-indigo-50/30 transition-all">
                            <div class="text-xs font-bold text-slate-700 leading-snug line-clamp-2 group-hover:text-indigo-600 transition-colors">{{ $item->perihal_kegiatan }}</div>
                            <div class="mt-2 flex items-center justify-between gap-3 text-[10px]">
                                <span class="text-slate-400 flex items-center gap-1"><i data-lucide="calendar" class="h-3 w-3"></i>{{ optional($item->waktu_mulai)->translatedFormat('d M Y') }}</span>
                                @php 
                                    $relatedStatus = ['terjadwal'=>'text-amber-600','berlangsung'=>'text-blue-600','selesai'=>'text-emerald-600','dibatalkan'=>'text-red-600'][$item->effective_status] ?? 'text-slate-400'; 
                                @endphp
                                <span class="font-bold {{ $relatedStatus }}">{{ ucfirst($item->effective_status) }}</span>
                            </div>
                        </a>
                    @empty
                        <div class="text-center py-6">
                            <p class="text-xs text-slate-400">Tidak ada agenda lain.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </aside>
    </div>
</x-app-layout>
