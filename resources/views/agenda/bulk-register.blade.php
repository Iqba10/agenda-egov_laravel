<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Registrasi Massal Notifikasi WhatsApp &mdash; Agenda eGov</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .animate-slide-up { animation: slideUp .3s cubic-bezier(.22,1,.36,1); }
        .drag-over { border-color: #3b82f6 !important; background: #eff6ff !important; }
    </style>
</head>
<body class="bg-slate-50 font-sans text-slate-900 min-h-screen flex flex-col">
    @include('partials.toast')

    {{-- Header --}}
    <header class="bg-slate-900 bg-[radial-gradient(circle,rgba(255,255,255,0.05)_1px,transparent_1px)] bg-[length:24px_24px] text-white py-6 shadow-sm border-b border-slate-800">
        <div class="max-w-5xl mx-auto px-4 sm:px-6">
            <div class="flex items-center gap-3">
                <a href="{{ route('agenda.index') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-400 hover:text-white transition-colors">
                    <i data-lucide="arrow-left" class="h-4 w-4"></i>
                    Kembali
                </a>
            </div>
            <div class="mt-4">
                <h1 class="text-2xl md:text-3xl font-extrabold flex items-center gap-3 tracking-tight">
                    <span class="p-2 bg-emerald-600 rounded-xl shadow-lg shadow-emerald-900/20">
                        <i data-lucide="users-plus" class="h-5 w-5 text-white"></i>
                    </span>
                    Registrasi Massal
                </h1>
                <p class="text-slate-400 text-sm font-medium mt-2 flex items-center gap-2">
                    <span class="w-5 h-px bg-slate-700"></span>
                    Daftarkan banyak nomor WhatsApp sekaligus untuk notifikasi agenda
                </p>
            </div>
        </div>
    </header>

    {{-- Main Content --}}
    <main class="max-w-5xl mx-auto px-4 sm:px-6 py-6 w-full flex-1">

        {{-- Form Card --}}
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden animate-slide-up" style="animation-delay: .05s">

            {{-- Step 1: Pilih Agenda --}}
            <div class="px-5 sm:px-6 py-5 border-b border-slate-100">
                <div class="flex items-center gap-3 mb-4">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-blue-600 text-sm font-bold">1</span>
                    <div>
                        <h2 class="text-sm font-bold text-slate-800">Pilih Agenda</h2>
                        <p class="text-xs text-slate-500">Pilih agenda yang ingin didaftarkan untuk notifikasi</p>
                    </div>
                </div>

                @if($agendas->isEmpty())
                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-start gap-3">
                        <i data-lucide="alert-triangle" class="h-5 w-5 text-amber-500 shrink-0 mt-0.5"></i>
                        <div>
                            <p class="text-sm font-semibold text-amber-800">Tidak ada agenda mendatang</p>
                            <p class="text-xs text-amber-600 mt-0.5">Belum ada agenda terjadwal yang tersedia untuk pendaftaran notifikasi.</p>
                        </div>
                    </div>
                @else
                    <div class="space-y-2 max-h-56 overflow-y-auto pr-1" id="agendaList">
                        @foreach($agendas as $agenda)
                            <label class="flex items-start gap-3 rounded-xl border border-slate-200 cursor-pointer p-3 transition-all hover:border-blue-300 hover:bg-blue-50/50 agenda-item"
                                   data-agenda-id="{{ $agenda->id }}">
                                <div class="pt-0.5 shrink-0">
                                    <input type="checkbox" name="agenda_ids[]" value="{{ $agenda->id }}"
                                           class="agenda-checkbox sr-only">
                                    <div class="h-5 w-5 rounded-md border-2 border-slate-300 bg-white flex items-center justify-center transition-all check-indicator">
                                        <svg class="h-3 w-3 text-white opacity-0 transition-opacity check-icon" fill="none" viewBox="0 0 10 10">
                                            <path d="M1.5 5l2.5 2.5L8.5 2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-slate-800 leading-snug">{{ $agenda->perihal_kegiatan }}</p>
                                    <div class="flex flex-wrap items-center gap-3 mt-1 text-xs text-slate-500">
                                        <span class="inline-flex items-center gap-1">
                                            <i data-lucide="calendar" class="h-3 w-3"></i>
                                            {{ \Carbon\Carbon::parse($agenda->waktu_mulai)->translatedFormat('d M Y, H:i') }} WIB
                                        </span>
                                        @if($agenda->tempat)
                                            <span class="inline-flex items-center gap-1">
                                                <i data-lucide="map-pin" class="h-3 w-3"></i>
                                                {{ $agenda->tempat }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    <p class="mt-2 text-xs text-slate-400">Pilih maksimal 10 agenda. <span id="agendaCount" class="font-semibold text-blue-600">0 dipilih</span></p>
                    <p class="mt-1 text-[10px] text-slate-400"><i data-lucide="mouse" class="h-3 w-3 inline"></i> Scroll untuk melihat semua agenda</p>
                @endif
            </div>

            {{-- Step 2: Input Nomor --}}
            <div class="px-5 sm:px-6 py-5 border-b border-slate-100">
                <div class="flex items-center gap-3 mb-4">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600 text-sm font-bold">2</span>
                    <div>
                        <h2 class="text-sm font-bold text-slate-800">Masukkan Nomor WhatsApp</h2>
                        <p class="text-xs text-slate-500">Pilih cara input nomor yang diinginkan</p>
                    </div>
                </div>

                {{-- Input Method Toggle --}}
                <div class="flex gap-2 mb-4">
                    <button type="button" onclick="setInputMethod('manual')" id="btnManual"
                            class="flex-1 flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl border-2 text-sm font-semibold transition-all border-blue-500 bg-blue-50 text-blue-700">
                        <i data-lucide="keyboard" class="h-4 w-4"></i>
                        Ketik Manual
                    </button>
                    <button type="button" onclick="setInputMethod('file')" id="btnFile"
                            class="flex-1 flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl border-2 text-sm font-semibold transition-all border-slate-200 text-slate-600 hover:border-slate-300">
                        <i data-lucide="upload" class="h-4 w-4"></i>
                        Upload File
                    </button>
                </div>

                {{-- Manual Input --}}
                <div id="manualSection">
                    <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Daftar Nomor WhatsApp</label>
                    <textarea id="manualNumbers" rows="6"
                              placeholder="Masukkan nomor WhatsApp, satu per baris atau dipisah koma:&#10;&#10;081234567890&#10;085678901234&#10;+6281211112222&#10;&#10;Atau dengan nama: nama, nomor (dipisah tab/pipe)&#10;Budi Santoso&#9;081234567890&#10;Ani Wijaya|085678901234"
                              class="w-full px-4 py-3 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder-slate-400 text-slate-800 font-mono resize-y max-h-48"></textarea>
                    <div class="flex items-center justify-between mt-2">
                        <p class="text-[11px] text-slate-400">Format: nomor per baris, atau nama + nomor dipisah tab/pipe/semicolon</p>
                        <p id="manualCount" class="text-[11px] font-semibold text-slate-500">0 nomor terdeteksi</p>
                    </div>
                </div>

                {{-- File Upload --}}
                <div id="fileSection" class="hidden">
                    <div id="dropZone"
                         class="border-2 border-dashed border-slate-300 rounded-xl p-8 text-center transition-all cursor-pointer hover:border-blue-400 hover:bg-blue-50/30">
                        <input type="file" id="bulkFile" accept=".csv,.txt,.tsv" class="hidden">
                        <div id="dropPlaceholder">
                            <div class="flex h-12 w-12 mx-auto items-center justify-center rounded-2xl bg-slate-100 mb-3">
                                <i data-lucide="file-up" class="h-6 w-6 text-slate-400"></i>
                            </div>
                            <p class="text-sm font-semibold text-slate-700 mb-1">Klik atau seret file ke sini</p>
                            <p class="text-xs text-slate-400">Format: CSV, TXT, atau TSV &bull; Maks 5 MB</p>
                            <p class="text-[11px] text-slate-400 mt-2">Kolom minimal: nama (opsional) dan nomor_whatsapp</p>
                        </div>
                        <div id="filePreview" class="hidden text-left">
                            <div class="flex items-center gap-3 bg-white rounded-lg border border-slate-200 p-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-emerald-100">
                                    <i data-lucide="file-check" class="h-5 w-5 text-emerald-600"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p id="fileName" class="text-sm font-semibold text-slate-800 truncate"></p>
                                    <p id="fileSize" class="text-[11px] text-slate-500"></p>
                                </div>
                                <button type="button" onclick="clearFile()" class="text-slate-400 hover:text-red-500 transition-colors">
                                    <i data-lucide="x" class="h-4 w-4"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <p class="mt-2 text-[11px] text-slate-400">Header CSV harus berisi kolom <code class="bg-slate-100 px-1 rounded">nomor_whatsapp</code> (atau <code class="bg-slate-100 px-1 rounded">phone</code>, <code class="bg-slate-100 px-1 rounded">whatsapp</code>). Kolom nama bersifat opsional.</p>
                </div>
            </div>

            {{-- Step 3: Pengaturan --}}
            <div class="px-5 sm:px-6 py-5 border-b border-slate-100">
                <div class="flex items-center gap-3 mb-4">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-violet-100 text-violet-600 text-sm font-bold">3</span>
                    <div>
                        <h2 class="text-sm font-bold text-slate-800">Pengaturan Pengingat</h2>
                        <p class="text-xs text-slate-500">Tentukan waktu pengingat yang akan dikirimkan</p>
                    </div>
                </div>

                <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Waktu Pengingat</label>
                <div class="flex gap-2">
                    <select id="reminderMinutes"
                            class="flex-1 px-3 py-2 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-slate-800 bg-white">
                        <option value="15">15 menit sebelum</option>
                        <option value="30">30 menit sebelum</option>
                        <option value="60" selected>1 jam sebelum</option>
                        <option value="120">2 jam sebelum</option>
                        <option value="180">3 jam sebelum</option>
                        <option value="360">6 jam sebelum</option>
                        <option value="1440">1 hari sebelum</option>
                    </select>
                </div>
            </div>

            {{-- Submit --}}
            <div class="px-5 sm:px-6 py-5 bg-slate-50/60">
                <div id="formMsg" class="hidden mb-3 rounded-xl px-4 py-2.5 text-sm font-semibold"></div>
                <button id="submitBtn" type="button" onclick="submitBulkRegistration()"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed text-white text-sm font-bold rounded-xl transition-colors shadow-sm shadow-emerald-200">
                    <i data-lucide="send" class="h-4 w-4"></i>
                    Daftarkan Sekarang
                </button>
            </div>
        </div>

        {{-- Results Card (hidden by default) --}}
        <div id="resultsCard" class="hidden mt-6 bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden animate-slide-up">
            <div class="px-5 sm:px-6 py-5 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-blue-600">
                        <i data-lucide="clipboard-check" class="h-4 w-4"></i>
                    </span>
                    <div>
                        <h2 class="text-sm font-bold text-slate-800">Hasil Registrasi</h2>
                        <p id="resultSummary" class="text-xs text-slate-500"></p>
                    </div>
                </div>
            </div>

            {{-- Summary Stats --}}
            <div class="px-5 sm:px-6 py-4 border-b border-slate-100">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3" id="resultStats"></div>
            </div>

            {{-- Detail Tables --}}
            <div class="px-5 sm:px-6 py-4 space-y-4">
                {{-- Inserted --}}
                <div id="insertedSection" class="hidden">
                    <h3 class="text-xs font-bold uppercase tracking-widest text-emerald-600 mb-2 flex items-center gap-1.5">
                        <i data-lucide="check-circle" class="h-3.5 w-3.5"></i> Berhasil Didaftarkan
                    </h3>
                    <div class="overflow-x-auto rounded-lg border border-slate-200">
                        <table class="w-full text-sm">
                            <thead class="bg-slate-50 border-b border-slate-200">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-bold text-slate-500">No</th>
                                    <th class="px-3 py-2 text-left text-xs font-bold text-slate-500">Nama</th>
                                    <th class="px-3 py-2 text-left text-xs font-bold text-slate-500">Nomor WhatsApp</th>
                                </tr>
                            </thead>
                            <tbody id="insertedBody" class="divide-y divide-slate-100"></tbody>
                        </table>
                    </div>
                </div>

                {{-- Duplicates --}}
                <div id="duplicateSection" class="hidden">
                    <h3 class="text-xs font-bold uppercase tracking-widest text-amber-600 mb-2 flex items-center gap-1.5">
                        <i data-lucide="alert-circle" class="h-3.5 w-3.5"></i> Duplikat (Sudah Terdaftar)
                    </h3>
                    <div class="overflow-x-auto rounded-lg border border-slate-200">
                        <table class="w-full text-sm">
                            <thead class="bg-slate-50 border-b border-slate-200">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-bold text-slate-500">No</th>
                                    <th class="px-3 py-2 text-left text-xs font-bold text-slate-500">Nama</th>
                                    <th class="px-3 py-2 text-left text-xs font-bold text-slate-500">Nomor WhatsApp</th>
                                </tr>
                            </thead>
                            <tbody id="duplicateBody" class="divide-y divide-slate-100"></tbody>
                        </table>
                    </div>
                </div>

                {{-- Errors --}}
                <div id="errorSection" class="hidden">
                    <h3 class="text-xs font-bold uppercase tracking-widest text-red-600 mb-2 flex items-center gap-1.5">
                        <i data-lucide="x-circle" class="h-3.5 w-3.5"></i> Gagal Validasi
                    </h3>
                    <div class="overflow-x-auto rounded-lg border border-slate-200">
                        <table class="w-full text-sm">
                            <thead class="bg-slate-50 border-b border-slate-200">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-bold text-slate-500">Baris</th>
                                    <th class="px-3 py-2 text-left text-xs font-bold text-slate-500">Input</th>
                                    <th class="px-3 py-2 text-left text-xs font-bold text-slate-500">Alasan</th>
                                </tr>
                            </thead>
                            <tbody id="errorBody" class="divide-y divide-slate-100"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="px-5 sm:px-6 py-4 border-t border-slate-100 bg-slate-50/60 flex gap-3">
                <button type="button" onclick="resetForm()"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold bg-white border border-slate-200 text-slate-700 rounded-xl hover:bg-slate-50 transition-colors">
                    <i data-lucide="plus" class="h-4 w-4"></i> Daftar Lagi
                </button>
                <a href="{{ route('agenda.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold bg-slate-800 text-white rounded-xl hover:bg-slate-900 transition-colors">
                    <i data-lucide="home" class="h-4 w-4"></i> Kembali ke Agenda
                </a>
            </div>
        </div>

        {{-- Tips --}}
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-2xl p-5 animate-slide-up" style="animation-delay: .15s">
            <h3 class="text-sm font-bold text-blue-800 flex items-center gap-2 mb-3">
                <i data-lucide="lightbulb" class="h-4 w-4"></i>
                Tips Penggunaan
            </h3>
            <ul class="space-y-2 text-xs text-blue-700">
                <li class="flex items-start gap-2">
                    <i data-lucide="check" class="h-3.5 w-3.5 shrink-0 mt-0.5"></i>
                    <span><strong>Format manual:</strong> Satu nomor per baris, atau dipisah koma. Contoh: <code class="bg-blue-100 px-1 rounded">081234567890</code></span>
                </li>
                <li class="flex items-start gap-2">
                    <i data-lucide="check" class="h-3.5 w-3.5 shrink-0 mt-0.5"></i>
                    <span><strong>Dengan nama:</strong> Gunakan tab, pipe (|), atau semicolon (;) sebagai pemisah. Contoh: <code class="bg-blue-100 px-1 rounded">Budi Santoso&#9;081234567890</code></span>
                </li>
                <li class="flex items-start gap-2">
                    <i data-lucide="check" class="h-3.5 w-3.5 shrink-0 mt-0.5"></i>
                    <span><strong>Format CSV:</strong> Header wajib ada kolom <code class="bg-blue-100 px-1 rounded">nomor_whatsapp</code> (atau <code class="bg-blue-100 px-1 rounded">phone</code>, <code class="bg-blue-100 px-1 rounded">whatsapp</code>). Kolom <code class="bg-blue-100 px-1 rounded">nama</code> opsional.</span>
                </li>
                <li class="flex items-start gap-2">
                    <i data-lucide="check" class="h-3.5 w-3.5 shrink-0 mt-0.5"></i>
                    <span><strong>Nomor otomatis dinormalisasi:</strong> <code class="bg-blue-100 px-1 rounded">0812xxx</code> → <code class="bg-blue-100 px-1 rounded">62812xxx</code>. Spasi dan tanda hubir akan diabaikan.</span>
                </li>
                <li class="flex items-start gap-2">
                    <i data-lucide="check" class="h-3.5 w-3.5 shrink-0 mt-0.5"></i>
                    <span><strong>Duplikat otomatis di-skip:</strong> Nomor yang sudah terdaftar untuk agenda yang sama tidak akan didaftarkan ulang.</span>
                </li>
            </ul>
        </div>
    </main>

    @include('partials.public-footer')

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
    lucide.createIcons();

    let selectedInputMethod = 'manual';
    let selectedAgendaIds = new Set();

    // ── Agenda Selection ──
    document.querySelectorAll('.agenda-item').forEach(item => {
        item.addEventListener('click', (e) => {
            const id = parseInt(item.dataset.agendaId);
            const checkbox = item.querySelector('.agenda-checkbox');
            const indicator = item.querySelector('.check-indicator');
            const icon = item.querySelector('.check-icon');

            if (selectedAgendaIds.has(id)) {
                selectedAgendaIds.delete(id);
                checkbox.checked = false;
                indicator.className = 'h-5 w-5 rounded-md border-2 border-slate-300 bg-white flex items-center justify-center transition-all check-indicator';
                icon.className = 'h-3 w-3 text-white opacity-0 transition-opacity check-icon';
                item.className = item.className.replace(/border-blue-400 bg-blue-50/, 'border-slate-200');
            } else {
                if (selectedAgendaIds.size >= 10) {
                    showMsg('Maksimal 10 agenda dapat dipilih.', false);
                    return;
                }
                selectedAgendaIds.add(id);
                checkbox.checked = true;
                indicator.className = 'h-5 w-5 rounded-md border-2 border-blue-600 bg-blue-600 flex items-center justify-center transition-all check-indicator';
                icon.className = 'h-3 w-3 text-white opacity-100 transition-opacity check-icon';
                item.className = item.className.replace(/border-slate-200/, 'border-blue-400 bg-blue-50');
            }

            document.getElementById('agendaCount').textContent = selectedAgendaIds.size + ' dipilih';
        });
    });

    // ── Input Method Toggle ──
    function setInputMethod(method) {
        selectedInputMethod = method;
        const manualBtn = document.getElementById('btnManual');
        const fileBtn = document.getElementById('btnFile');
        const manualSection = document.getElementById('manualSection');
        const fileSection = document.getElementById('fileSection');

        if (method === 'manual') {
            manualBtn.className = 'flex-1 flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl border-2 text-sm font-semibold transition-all border-blue-500 bg-blue-50 text-blue-700';
            fileBtn.className = 'flex-1 flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl border-2 text-sm font-semibold transition-all border-slate-200 text-slate-600 hover:border-slate-300';
            manualSection.classList.remove('hidden');
            fileSection.classList.add('hidden');
        } else {
            fileBtn.className = 'flex-1 flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl border-2 text-sm font-semibold transition-all border-blue-500 bg-blue-50 text-blue-700';
            manualBtn.className = 'flex-1 flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl border-2 text-sm font-semibold transition-all border-slate-200 text-slate-600 hover:border-slate-300';
            fileSection.classList.remove('hidden');
            manualSection.classList.add('hidden');
        }
    }

    // ── Manual Input Counter ──
    document.getElementById('manualNumbers').addEventListener('input', function() {
        const lines = this.value.split(/[\n,]+/).filter(l => l.trim() !== '');
        document.getElementById('manualCount').textContent = lines.length + ' nomor terdeteksi';
    });

    // ── File Upload ──
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('bulkFile');

    dropZone.addEventListener('click', () => fileInput.click());

    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('drag-over');
    });

    dropZone.addEventListener('dragleave', () => {
        dropZone.classList.remove('drag-over');
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('drag-over');
        const files = e.dataTransfer.files;
        if (files.length) handleFile(files[0]);
    });

    fileInput.addEventListener('change', function() {
        if (this.files.length) handleFile(this.files[0]);
    });

    function handleFile(file) {
        const allowedTypes = ['text/csv', 'text/plain', 'text/tab-separated-values', 'application/csv'];
        const allowedExts = ['.csv', '.txt', '.tsv'];
        const ext = '.' + file.name.split('.').pop().toLowerCase();

        if (!allowedExts.includes(ext)) {
            showMsg('Format file tidak didukung. Gunakan CSV, TXT, atau TSV.', false);
            return;
        }

        if (file.size > 5 * 1024 * 1024) {
            showMsg('Ukuran file terlalu besar. Maksimal 5 MB.', false);
            return;
        }

        document.getElementById('dropPlaceholder').classList.add('hidden');
        document.getElementById('filePreview').classList.remove('hidden');
        document.getElementById('fileName').textContent = file.name;
        document.getElementById('fileSize').textContent = formatFileSize(file.size);
    }

    function clearFile() {
        fileInput.value = '';
        document.getElementById('dropPlaceholder').classList.remove('hidden');
        document.getElementById('filePreview').classList.add('hidden');
    }

    function formatFileSize(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
    }

    // ── Submit ──
    async function submitBulkRegistration() {
        const btn = document.getElementById('submitBtn');

        // Validate agenda
        if (selectedAgendaIds.size === 0) {
            showMsg('Pilih minimal satu agenda terlebih dahulu.', false);
            return;
        }

        // Build FormData
        const formData = new FormData();
        formData.append('input_method', selectedInputMethod);
        formData.append('reminder_minutes', document.getElementById('reminderMinutes').value);

        selectedAgendaIds.forEach(id => formData.append('agenda_ids[]', id));

        if (selectedInputMethod === 'manual') {
            const numbers = document.getElementById('manualNumbers').value.trim();
            if (!numbers) {
                showMsg('Masukkan minimal satu nomor WhatsApp.', false);
                return;
            }
            formData.append('manual_numbers', numbers);
        } else {
            const file = fileInput.files[0];
            if (!file) {
                showMsg('Pilih file untuk di-upload.', false);
                return;
            }
            formData.append('bulk_file', file);
        }

        btn.disabled = true;
        btn.innerHTML = '<div class="h-4 w-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div> Memproses...';

        try {
            const res = await fetch('{{ route("notify.bulk.store") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: formData,
            });

            const data = await res.json();

            if (data.success) {
                showMsg(data.message, true);
                showResults(data);
            } else {
                showMsg(data.message || 'Terjadi kesalahan.', false);
            }
        } catch (err) {
            console.error(err);
            showMsg('Gagal menghubungi server. Coba lagi.', false);
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i data-lucide="send" class="h-4 w-4"></i> Daftarkan Sekarang';
            lucide.createIcons();
        }
    }

    // ── Show Results ──
    function showResults(data) {
        const card = document.getElementById('resultsCard');
        card.classList.remove('hidden');

        document.getElementById('resultSummary').textContent = data.message;

        // Stats
        const s = data.summary;
        document.getElementById('resultStats').innerHTML = `
            <div class="bg-slate-50 rounded-xl p-3 text-center">
                <p class="text-2xl font-bold text-slate-800">${s.total_input}</p>
                <p class="text-[11px] text-slate-500 font-medium">Total Input</p>
            </div>
            <div class="bg-emerald-50 rounded-xl p-3 text-center">
                <p class="text-2xl font-bold text-emerald-700">${s.inserted}</p>
                <p class="text-[11px] text-emerald-600 font-medium">Berhasil</p>
            </div>
            <div class="bg-amber-50 rounded-xl p-3 text-center">
                <p class="text-2xl font-bold text-amber-700">${s.duplicates}</p>
                <p class="text-[11px] text-amber-600 font-medium">Duplikat</p>
            </div>
            <div class="bg-red-50 rounded-xl p-3 text-center">
                <p class="text-2xl font-bold text-red-700">${s.errors}</p>
                <p class="text-[11px] text-red-600 font-medium">Gagal</p>
            </div>
        `;

        // Inserted details
        const insertedSection = document.getElementById('insertedSection');
        const insertedBody = document.getElementById('insertedBody');
        if (data.inserted_details && data.inserted_details.length > 0) {
            insertedSection.classList.remove('hidden');
            insertedBody.innerHTML = data.inserted_details.map((d, i) => `
                <tr class="hover:bg-slate-50">
                    <td class="px-3 py-2 text-slate-500 text-xs">${i + 1}</td>
                    <td class="px-3 py-2 text-slate-700 text-xs">${escapeHtml(d.nama || '-')}</td>
                    <td class="px-3 py-2 font-mono text-xs text-slate-800">${escapeHtml(d.phone)}</td>
                </tr>
            `).join('');
        } else {
            insertedSection.classList.add('hidden');
        }

        // Duplicate details
        const dupSection = document.getElementById('duplicateSection');
        const dupBody = document.getElementById('duplicateBody');
        if (data.duplicate_details && data.duplicate_details.length > 0) {
            dupSection.classList.remove('hidden');
            dupBody.innerHTML = data.duplicate_details.map((d, i) => `
                <tr class="hover:bg-slate-50">
                    <td class="px-3 py-2 text-slate-500 text-xs">${i + 1}</td>
                    <td class="px-3 py-2 text-slate-700 text-xs">${escapeHtml(d.nama || '-')}</td>
                    <td class="px-3 py-2 font-mono text-xs text-slate-800">${escapeHtml(d.phone)}</td>
                </tr>
            `).join('');
        } else {
            dupSection.classList.add('hidden');
        }

        // Error details
        const errSection = document.getElementById('errorSection');
        const errBody = document.getElementById('errorBody');
        if (data.error_details && data.error_details.length > 0) {
            errSection.classList.remove('hidden');
            errBody.innerHTML = data.error_details.map((d, i) => `
                <tr class="hover:bg-slate-50">
                    <td class="px-3 py-2 text-slate-500 text-xs">${d.line}</td>
                    <td class="px-3 py-2 font-mono text-xs text-slate-800">${escapeHtml(d.phone)}</td>
                    <td class="px-3 py-2 text-xs text-red-600">${escapeHtml(d.reason)}</td>
                </tr>
            `).join('');
        } else {
            errSection.classList.add('hidden');
        }

        // Scroll to results
        card.scrollIntoView({ behavior: 'smooth', block: 'start' });
        lucide.createIcons();
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // ── Reset Form ──
    function resetForm() {
        selectedAgendaIds.clear();
        document.querySelectorAll('.agenda-checkbox').forEach(cb => {
            cb.checked = false;
            const item = cb.closest('.agenda-item');
            const indicator = item.querySelector('.check-indicator');
            const icon = item.querySelector('.check-icon');
            indicator.className = 'h-5 w-5 rounded-md border-2 border-slate-300 bg-white flex items-center justify-center transition-all check-indicator';
            icon.className = 'h-3 w-3 text-white opacity-0 transition-opacity check-icon';
            item.className = item.className.replace(/border-blue-400 bg-blue-50/g, 'border-slate-200');
        });
        document.getElementById('agendaCount').textContent = '0 dipilih';
        document.getElementById('manualNumbers').value = '';
        document.getElementById('manualCount').textContent = '0 nomor terdeteksi';
        clearFile();
        setInputMethod('manual');
        document.getElementById('resultsCard').classList.add('hidden');
        document.getElementById('formMsg').classList.add('hidden');
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // ── Helpers ──
    function showMsg(msg, success) {
        const el = document.getElementById('formMsg');
        el.classList.remove('hidden', 'bg-emerald-50', 'text-emerald-700', 'border', 'border-emerald-200', 'bg-red-50', 'text-red-700', 'border', 'border-red-200');
        if (success) {
            el.classList.add('bg-emerald-50', 'text-emerald-700', 'border', 'border-emerald-200');
        } else {
            el.classList.add('bg-red-50', 'text-red-700', 'border', 'border-red-200');
        }
        el.textContent = msg;
    }
    </script>
</body>
</html>
