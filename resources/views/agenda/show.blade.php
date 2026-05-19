<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $agenda->perihal_kegiatan }} &mdash; Agenda eGov</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 font-sans text-slate-900 min-h-screen flex flex-col">
    @include('partials.toast')

    {{-- Breadcrumb --}}
    <div class="bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-3">
            <div class="flex items-center justify-between">
                <nav class="flex items-center gap-2 text-sm">
                    <a href="{{ route('agenda.index') }}" class="text-slate-500 hover:text-blue-600 transition">
                        <i data-lucide="home" class="h-4 w-4"></i>
                    </a>
                    <i data-lucide="chevron-right" class="h-3 w-3 text-slate-300"></i>
                    <a href="{{ route('agenda.index') }}" class="text-slate-500 hover:text-blue-600 transition">Agenda</a>
                    <i data-lucide="chevron-right" class="h-3 w-3 text-slate-300"></i>
                    <span class="text-slate-800 font-medium truncate max-w-[200px] sm:max-w-none">Detail</span>
                </nav>
                <div class="flex items-center gap-2">
                    @auth
                        @if(auth()->user()->hasRole('admin'))
                            <a href="{{ route('admin.agendas.edit', $agenda) }}"
                               class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-semibold bg-white text-slate-700 hover:bg-slate-50 border border-slate-200 rounded-lg transition">
                                <i data-lucide="pencil" class="h-3.5 w-3.5"></i> Edit
                            </a>
                        @endif
                        <a href="{{ route('admin.dashboard') }}"
                           class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-semibold bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition shadow-sm">
                            <i data-lucide="layout-dashboard" class="h-3.5 w-3.5"></i> Panel Admin
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                           class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-semibold bg-white text-slate-700 hover:bg-slate-50 border border-slate-200 rounded-lg transition">
                            <i data-lucide="log-in" class="h-3.5 w-3.5"></i> Login
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 w-full flex-1">
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

        {{-- Header Card — 2 kolom di desktop --}}
        <div class="bg-slate-900 rounded-xl p-5 sm:p-6 mb-6">
            <div class="flex flex-col lg:flex-row lg:items-start lg:gap-10 gap-5">

                {{-- Kiri: Judul & sumber --}}
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2 mb-3">
                        <span class="text-xs px-2 py-1 rounded font-semibold {{ $jenisColor }}">{{ ucfirst($agenda->jenis_agenda) }}</span>
                        <span class="text-xs px-2 py-1 rounded font-semibold {{ $statusColor }}">{{ ucfirst($effStatus) }}</span>
                    </div>
                    <h1 class="text-xl lg:text-2xl font-bold text-white leading-snug mb-3">{{ $agenda->perihal_kegiatan }}</h1>
                    <p class="text-slate-400 text-sm flex items-center gap-2">
                        <i data-lucide="building" class="h-4 w-4 flex-shrink-0"></i>
                        {{ $agenda->asal_surat ?: 'Dinas Komunikasi dan Informatika Kabupaten Sambas' }}
                    </p>
                </div>

                {{-- Kanan: Facts grid —hanya muncul pada lg+ --}}
                <div class="lg:flex-shrink-0 lg:w-80 grid grid-cols-2 gap-3">
                    <div class="bg-white/5 border border-white/10 rounded-lg p-3">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1.5 flex items-center gap-1.5">
                            <i data-lucide="calendar" class="h-3 w-3"></i> Mulai
                        </p>
                        <p class="text-sm font-semibold text-white">{{ \Carbon\Carbon::parse($agenda->waktu_mulai)->translatedFormat('d M Y') }}</p>
                        <p class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($agenda->waktu_mulai)->format('H:i') }} WIB</p>
                    </div>
                    <div class="bg-white/5 border border-white/10 rounded-lg p-3">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1.5 flex items-center gap-1.5">
                            <i data-lucide="calendar-check" class="h-3 w-3"></i> Selesai
                        </p>
                        <p class="text-sm font-semibold text-white">{{ \Carbon\Carbon::parse($agenda->waktu_selesai)->translatedFormat('d M Y') }}</p>
                        <p class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($agenda->waktu_selesai)->format('H:i') }} WIB</p>
                    </div>
                    <div class="col-span-2 bg-white/5 border border-white/10 rounded-lg p-3">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1.5 flex items-center gap-1.5">
                            <i data-lucide="map-pin" class="h-3 w-3"></i> Lokasi
                        </p>
                        <p class="text-sm font-semibold text-white">{{ $agenda->tempat ?: '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Body --}}
        <div class="grid gap-6 lg:grid-cols-[1fr_320px]">

            {{-- Kolom Kiri --}}
            <div class="space-y-5">

                {{-- Info Grid --}}
                <div class="grid grid-cols-2 xl:grid-cols-4 gap-4">
                    @php
                        $publicInfo = [
                            ['label' => 'Asal Surat',    'value' => $agenda->asal_surat,                                                              'icon' => 'mail'],
                            ['label' => 'Tanggal Surat', 'value' => optional($agenda->tanggal_surat)->translatedFormat('d F Y'),                       'icon' => 'calendar-days'],
                            ['label' => 'Pakaian',       'value' => $agenda->pakaian,                                                                  'icon' => 'shirt'],
                            ['label' => 'Diinput Oleh',  'value' => $agenda->diinput_oleh,                                                             'icon' => 'user-pen'],
                        ];
                    @endphp

                    @foreach($publicInfo as $item)
                        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm">
                            <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400 flex items-center gap-1.5 mb-2">
                                <i data-lucide="{{ $item['icon'] }}" class="h-3 w-3"></i>
                                {{ $item['label'] }}
                            </span>
                            <span class="text-sm font-semibold text-slate-800 block leading-snug">{{ $item['value'] ?: '-' }}</span>
                        </div>
                    @endforeach
                </div>

                {{-- Rincian Kegiatan --}}
                <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50">
                        <h2 class="text-xs font-bold uppercase tracking-widest text-slate-500">Rincian Kegiatan</h2>
                    </div>
                    <div class="divide-y divide-slate-100">
                        @php
                            $publicDetails = [
                                ['label' => 'Petugas Ditugaskan', 'value' => $agenda->petugas_ditugaskan, 'icon' => 'user-check'],
                                ['label' => 'Disposisi',          'value' => $agenda->disposisi,          'icon' => 'share-2'],
                                ['label' => 'Keterangan',         'value' => $agenda->keterangan,         'icon' => 'info'],
                            ];
                        @endphp

                        @foreach($publicDetails as $detail)
                            <div class="px-5 py-4 flex gap-4">
                                <span class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-lg bg-slate-100 text-slate-500">
                                    <i data-lucide="{{ $detail['icon'] }}" class="h-4 w-4"></i>
                                </span>
                                <div>
                                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1">{{ $detail['label'] }}</p>
                                    <p class="text-sm text-slate-700 leading-relaxed">{{ $detail['value'] ?: '-' }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Dokumen --}}
                @if($agenda->documents->count() > 0)
                    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                        <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                            <h2 class="text-xs font-bold uppercase tracking-widest text-slate-500">Dokumen Terlampir</h2>
                            <span class="text-xs font-bold text-slate-400">{{ $agenda->documents->count() }} file</span>
                        </div>
                        <div class="divide-y divide-slate-100">
                            @foreach($agenda->documents as $doc)
                                <div class="p-4">
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
                                            <p class="text-sm font-semibold text-slate-800 truncate">{{ $doc->original_name }}</p>
                                            <p class="text-[10px] font-bold text-slate-400 uppercase mt-0.5 tracking-wider">{{ $doc->extension }} &bull; {{ $doc->created_at->translatedFormat('d M Y') }}</p>
                                        </div>
                                        <button onclick="docDownload('{{ $doc->download_url }}','{{ addslashes($doc->original_name) }}')"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold bg-slate-800 text-white rounded-lg hover:bg-slate-700 transition-colors flex-shrink-0">
                                            <i data-lucide="download" class="h-3.5 w-3.5"></i> Unduh
                                        </button>
                                    </div>

                                    @if($doc->type === 'pdf')
                                        <div class="rounded-lg overflow-hidden border border-slate-200 bg-slate-50 relative">
                                            <div id="doc-loading-{{ $doc->id }}" class="absolute inset-0 flex items-center justify-center text-xs text-slate-400">Memuat dokumen...</div>
                                            <iframe id="doc-frame-{{ $doc->id }}" class="w-full relative z-10" style="height:480px;" frameborder="0"></iframe>
                                        </div>
                                    @elseif($doc->type === 'image')
                                        <div class="rounded-lg overflow-hidden border border-slate-200 bg-slate-50 flex items-center justify-center p-2 min-h-24">
                                            <div id="doc-loading-{{ $doc->id }}" class="text-xs text-slate-400">Memuat gambar...</div>
                                            <img id="doc-img-{{ $doc->id }}" alt="{{ $doc->original_name }}" class="max-h-80 w-auto object-contain rounded hidden">
                                        </div>
                                    @else
                                        <div class="rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 flex items-center gap-3 text-sm text-slate-500">
                                            <i data-lucide="info" class="h-4 w-4 flex-shrink-0"></i>
                                            Preview tidak tersedia. Gunakan tombol Unduh.
                                        </div>
                                    @endif
                                </div>
                                @if($doc->type === 'pdf' || $doc->type === 'image')
                                    <script>
                                        fetch('{{ $doc->url }}')
                                            .then(r => r.blob())
                                            .then(blob => {
                                                const blobUrl = URL.createObjectURL(blob);
                                                @if($doc->type === 'pdf')
                                                    const frame = document.getElementById('doc-frame-{{ $doc->id }}');
                                                    if (frame) { frame.src = blobUrl; }
                                                @else
                                                    const img = document.getElementById('doc-img-{{ $doc->id }}');
                                                    if (img) { img.src = blobUrl; img.classList.remove('hidden'); }
                                                @endif
                                                const loader = document.getElementById('doc-loading-{{ $doc->id }}');
                                                if (loader) loader.remove();
                                            })
                                            .catch(() => {
                                                const loader = document.getElementById('doc-loading-{{ $doc->id }}');
                                                if (loader) loader.textContent = 'Gagal memuat dokumen.';
                                            });
                                    </script>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Kolom Kanan (Sidebar) --}}
            <aside class="space-y-5">

                {{-- Ringkasan --}}
                <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50">
                        <h2 class="text-xs font-bold uppercase tracking-widest text-slate-500">Ringkasan</h2>
                    </div>
                    <div class="divide-y divide-slate-100 text-sm">
                        <div class="px-5 py-3 flex items-center justify-between">
                            <span class="text-slate-500">Status</span>
                            <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-bold {{ $statusColor }}">{{ ucfirst($effStatus) }}</span>
                        </div>
                        <div class="px-5 py-3 flex items-center justify-between">
                            <span class="text-slate-500">Jenis</span>
                            <span class="font-semibold text-slate-800">{{ ucfirst($agenda->jenis_agenda) }}</span>
                        </div>
                        <div class="px-5 py-3 flex items-center justify-between">
                            <span class="text-slate-500">Durasi</span>
                            <span class="font-semibold text-slate-800">
                                @php $durasi = \Carbon\Carbon::parse($agenda->waktu_mulai)->diffForHumans(\Carbon\Carbon::parse($agenda->waktu_selesai), true); @endphp
                                {{ $durasi }}
                            </span>
                        </div>
                        <div class="px-5 py-3 flex items-start justify-between gap-2">
                            <span class="text-slate-500 flex-shrink-0">Waktu</span>
                            <span class="font-semibold text-slate-800 text-right text-xs leading-relaxed">
                                {{ \Carbon\Carbon::parse($agenda->waktu_mulai)->translatedFormat('d M Y, H:i') }}<br>
                                <span class="text-slate-400">s/d</span>
                                {{ \Carbon\Carbon::parse($agenda->waktu_selesai)->translatedFormat('d M Y, H:i') }} WIB
                            </span>
                        </div>
                    </div>
                    @auth
                        @if(auth()->user()->hasRole('admin'))
                            <div class="px-5 py-4 border-t border-slate-100 bg-slate-50">
                                <a href="{{ route('admin.agendas.edit', $agenda) }}"
                                   class="flex items-center justify-center gap-2 w-full py-2.5 text-sm font-bold bg-slate-900 text-white rounded-lg hover:bg-slate-800 transition-all">
                                    <i data-lucide="pencil" class="h-4 w-4"></i> Edit Agenda
                                </a>
                            </div>
                        @endif
                    @endauth
                </div>

                {{-- Agenda Lainnya --}}
                <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50">
                        <h2 class="text-xs font-bold uppercase tracking-widest text-slate-500">Agenda Lainnya</h2>
                    </div>
                    <div class="divide-y divide-slate-100">
                        @forelse ($relatedAgendas->take(6) as $item)
                            <a href="{{ route('agenda.show', $item) }}" class="group flex items-start gap-3 px-5 py-3.5 hover:bg-slate-50 transition-colors">
                                <div class="flex-shrink-0 mt-0.5 w-2 h-2 rounded-full bg-slate-300 group-hover:bg-blue-500 transition-colors"></div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-slate-800 leading-snug line-clamp-2 group-hover:text-blue-600 transition-colors">{{ $item->perihal_kegiatan }}</p>
                                    <div class="mt-1 flex items-center justify-between gap-2">
                                        <span class="text-[10px] text-slate-400 flex items-center gap-1"><i data-lucide="calendar" class="h-3 w-3"></i>{{ optional($item->waktu_mulai)->translatedFormat('d M Y') }}</span>
                                        @php $relStatus = ['terjadwal'=>'text-amber-600','berlangsung'=>'text-blue-600','selesai'=>'text-emerald-600','dibatalkan'=>'text-red-600'][$item->effective_status] ?? 'text-slate-400'; @endphp
                                        <span class="text-[10px] font-bold {{ $relStatus }}">{{ ucfirst($item->effective_status) }}</span>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <p class="text-xs text-slate-400 py-6 text-center px-5">Tidak ada agenda lain.</p>
                        @endforelse
                    </div>
                </div>
            </aside>
        </div>
    </div>

    @include('partials.public-footer')

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();

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
</body>
</html>
