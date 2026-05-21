@php($isEdit = $agenda->exists)

<div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">

    <div class="border-b border-slate-100 bg-slate-50 px-6 py-5">
        <h2 class="text-xl font-bold text-slate-800 flex items-center gap-3">
            <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-600">
                <i data-lucide="{{ $isEdit ? 'pencil' : 'plus' }}" class="h-5 w-5"></i>
            </span>
            {{ $isEdit ? 'Edit Agenda' : 'Tambah Agenda Baru' }}
        </h2>
        @if ($isEdit)
            <p class="mt-1 ml-[52px] text-sm text-slate-500 line-clamp-1">{{ $agenda->perihal_kegiatan }}</p>
        @endif
    </div>

    <div class="p-6 space-y-8">

        <div>
            <h3 class="mb-4 flex items-center gap-2 text-sm font-bold text-slate-800">
                <span class="flex h-6 w-6 items-center justify-center rounded bg-blue-100 text-xs font-bold text-blue-600">1</span>
                Informasi Dasar
            </h3>
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="label" for="jenis_agenda">Jenis Agenda <span class="text-red-500">*</span></label>
                    <select id="jenis_agenda" name="jenis_agenda" class="input" required>
                        <option value="">-- Pilih Jenis --</option>
                        @foreach (['internal' => 'Internal', 'eksternal' => 'Eksternal'] as $val => $lbl)
                            <option value="{{ $val }}" @selected(old('jenis_agenda', $agenda->jenis_agenda) === $val)>{{ $lbl }}</option>
                        @endforeach
                    </select>
                    @error('jenis_agenda')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="label" for="status">Status <span class="text-red-500">*</span></label>
                    <select id="status" name="status" class="input" required>
                        @foreach (['terjadwal' => 'Terjadwal', 'selesai' => 'Selesai', 'dibatalkan' => 'Dibatalkan'] as $val => $lbl)
                            <option value="{{ $val }}" @selected(old('status', $agenda->status ?: 'terjadwal') === $val)>{{ $lbl }}</option>
                        @endforeach
                    </select>
                    @error('status')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="md:col-span-2">
                    <label class="label" for="perihal_kegiatan">Perihal Kegiatan <span class="text-red-500">*</span></label>
                    <textarea id="perihal_kegiatan" name="perihal_kegiatan" rows="2" class="input resize-none" placeholder="Masukkan judul atau perihal kegiatan..." required>{{ old('perihal_kegiatan', $agenda->perihal_kegiatan) }}</textarea>
                    @error('perihal_kegiatan')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <div class="border-t border-slate-100 pt-8">
            <h3 class="mb-4 flex items-center gap-2 text-sm font-bold text-slate-800">
                <span class="flex h-6 w-6 items-center justify-center rounded bg-emerald-100 text-xs font-bold text-emerald-600">2</span>
                Waktu & Tempat
            </h3>
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="label" for="waktu_mulai">Waktu Mulai <span class="text-red-500">*</span></label>
                    <input id="waktu_mulai" type="datetime-local" name="waktu_mulai" class="input" value="{{ old('waktu_mulai', optional($agenda->waktu_mulai)->format('Y-m-d\TH:i')) }}" required>
                    @error('waktu_mulai')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="label" for="waktu_selesai">Waktu Selesai <span class="text-red-500">*</span></label>
                    <input id="waktu_selesai" type="datetime-local" name="waktu_selesai" class="input" value="{{ old('waktu_selesai', optional($agenda->waktu_selesai)->format('Y-m-d\TH:i')) }}" required>
                    @error('waktu_selesai')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="md:col-span-2">
                    <label class="label" for="tempat">Tempat <span class="text-red-500">*</span></label>
                    <input id="tempat" name="tempat" class="input" placeholder="Lokasi kegiatan..." value="{{ old('tempat', $agenda->tempat) }}" required>
                    @error('tempat')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <div class="border-t border-slate-100 pt-8">
            <h3 class="mb-4 flex items-center gap-2 text-sm font-bold text-slate-800">
                <span class="flex h-6 w-6 items-center justify-center rounded bg-violet-100 text-xs font-bold text-violet-600">3</span>
                Detail Surat & Penugasan
            </h3>
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="label" for="asal_surat">Asal Surat / Penyelenggara <span class="text-red-500">*</span></label>
                    <input id="asal_surat" name="asal_surat" class="input" placeholder="Nama instansi atau penyelenggara..." value="{{ old('asal_surat', $agenda->asal_surat) }}" required>
                    @error('asal_surat')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="label" for="tanggal_surat">Tanggal Surat</label>
                    <input id="tanggal_surat" type="date" name="tanggal_surat" class="input" value="{{ old('tanggal_surat', optional($agenda->tanggal_surat)->format('Y-m-d')) }}">
                    @error('tanggal_surat')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="label" for="pakaian">Pakaian</label>
                    <input id="pakaian" name="pakaian" class="input" placeholder="Seragam atau pakaian yang digunakan..." value="{{ old('pakaian', $agenda->pakaian) }}">
                    @error('pakaian')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="label" for="petugas_ditugaskan">Petugas Ditugaskan</label>
                    <input id="petugas_ditugaskan" name="petugas_ditugaskan" class="input" value="{{ old('petugas_ditugaskan', $agenda->petugas_ditugaskan) }}">
                    @error('petugas_ditugaskan')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <div class="border-t border-slate-100 pt-8">
            <h3 class="mb-4 flex items-center gap-2 text-sm font-bold text-slate-800">
                <span class="flex h-6 w-6 items-center justify-center rounded bg-slate-200 text-xs font-bold text-slate-600">4</span>
                Keterangan Tambahan
            </h3>
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="label" for="disposisi">Disposisi</label>
                    <textarea id="disposisi" name="disposisi" rows="4" class="input">{{ old('disposisi', $agenda->disposisi) }}</textarea>
                    @error('disposisi')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="label" for="keterangan">Keterangan</label>
                    <textarea id="keterangan" name="keterangan" rows="4" class="input">{{ old('keterangan', $agenda->keterangan) }}</textarea>
                    @error('keterangan')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <div id="documents-section" class="border-t border-slate-100 pt-8">
            <h3 class="mb-4 flex items-center gap-2 text-sm font-bold text-slate-800">
                <span class="flex h-6 w-6 items-center justify-center rounded bg-indigo-100 text-xs font-bold text-indigo-600">5</span>
                Dokumen Terlampir
            </h3>
            
            @if($isEdit && $agenda->documents->count() > 0)
                <div id="docs-list" class="mb-6 grid gap-3 sm:grid-cols-2">
                    @foreach($agenda->documents as $doc)
                        <div id="doc-card-{{ $doc->id }}" class="flex items-center justify-between rounded-lg border border-slate-200 p-3 bg-slate-50">
                            <div class="flex items-center gap-3 overflow-hidden">
                                <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded bg-white shadow-sm">
                                    @if($doc->type === 'image')
                                        <i data-lucide="image" class="h-5 w-5 text-emerald-500"></i>
                                    @elseif($doc->type === 'pdf')
                                        <i data-lucide="file-text" class="h-5 w-5 text-red-500"></i>
                                    @else
                                        <i data-lucide="file" class="h-5 w-5 text-blue-500"></i>
                                    @endif
                                </div>
                                <div class="overflow-hidden">
                                    <p class="text-sm font-medium text-slate-700 truncate" title="{{ $doc->original_name }}">{{ $doc->original_name }}</p>
                                    <p class="text-xs text-slate-400">{{ strtoupper($doc->extension) }} • {{ $doc->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            <button type="button"
                                    class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-red-50 hover:text-red-500 transition-colors"
                                    onclick="openDeleteDocModal({{ $doc->id }}, '{{ addslashes($doc->original_name) }}', '{{ route('admin.agendas.documents.destroy', [$agenda, $doc]) }}')">
                                <i data-lucide="trash-2" class="h-4 w-4"></i>
                            </button>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="rounded-xl border-2 border-dashed border-slate-200 p-8 text-center hover:border-indigo-300 transition-colors bg-slate-50/50">
                <input type="file" name="documents[]" id="documents" class="hidden" multiple accept=".pdf,.jpg,.jpeg,.png,.docx,.xlsx">
                <label for="documents" class="cursor-pointer group">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-indigo-100 text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition-all">
                        <i data-lucide="upload-cloud" class="h-6 w-6"></i>
                    </div>
                    <div class="mt-4">
                        <span class="text-sm font-semibold text-slate-700">Klik untuk upload dokumen</span>
                        <p class="mt-1 text-xs text-slate-400">PDF, JPG, PNG, DOCX, XLSX (Maks. 30MB per file)</p>
                    </div>
                </label>
                <div id="file-list" class="mt-4 space-y-2 text-left hidden">
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">File terpilih:</p>
                    <div id="selected-files" class="space-y-1"></div>
                </div>
            </div>
            @error('documents.*')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="flex flex-wrap items-center justify-end gap-3 border-t border-slate-100 pt-6">
            <a href="{{ $isEdit ? route('admin.agendas.show', $agenda) : route('admin.dashboard') }}" class="btn-secondary">Batal</a>
            <button class="btn-primary">
                <i data-lucide="{{ $isEdit ? 'save' : 'plus' }}" class="h-4 w-4"></i>
                {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Agenda' }}
            </button>
        </div>

    </div>
</div>

{{-- Delete Document Modal --}}
<div id="delete-doc-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeDeleteDocModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="relative w-full max-w-sm rounded-2xl bg-white shadow-2xl border border-slate-200">
            <div class="flex items-center gap-4 border-b border-slate-100 px-6 py-5">
                <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-red-100">
                    <i data-lucide="trash-2" class="h-5 w-5 text-red-600"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Hapus Dokumen</h3>
                    <p id="delete-doc-name" class="mt-0.5 text-xs text-slate-500 truncate max-w-xs"></p>
                </div>
            </div>
            <div class="px-6 py-4">
                <p class="text-sm text-slate-600">Dokumen ini akan dihapus permanen dan tidak dapat dikembalikan.</p>
            </div>
            <div class="flex items-center justify-end gap-3 border-t border-slate-100 px-6 py-4">
                <button type="button" onclick="closeDeleteDocModal()"
                        class="btn-secondary text-xs px-4 py-2">Batal</button>
                <button type="button" id="delete-doc-confirm" onclick="executeDeleteDoc()"
                        class="btn-danger text-xs px-4 py-2">
                    <i data-lucide="trash-2" class="h-3.5 w-3.5"></i> Hapus
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('documents').addEventListener('change', function(e) {
        const fileList = document.getElementById('file-list');
        const selectedFiles = document.getElementById('selected-files');
        selectedFiles.innerHTML = '';
        
        if (this.files.length > 0) {
            fileList.classList.remove('hidden');
            Array.from(this.files).forEach(file => {
                const div = document.createElement('div');
                div.className = 'flex items-center gap-2 text-xs text-slate-600 bg-white border border-slate-200 rounded px-2 py-1';
                div.innerHTML = `<i data-lucide="file" class="h-3 w-3 text-slate-400"></i> ${file.name} (${(file.size / 1024).toFixed(1)} KB)`;
                selectedFiles.appendChild(div);
            });
            if (window.lucide) lucide.createIcons();
        } else {
            fileList.classList.add('hidden');
        }
    });

    let _deleteDocId = null;
    let _deleteDocUrl = null;

    function openDeleteDocModal(id, name, url) {
        _deleteDocId = id;
        _deleteDocUrl = url;
        document.getElementById('delete-doc-name').textContent = name;
        document.getElementById('delete-doc-modal').classList.remove('hidden');
        if (window.lucide) lucide.createIcons();
    }

    function closeDeleteDocModal() {
        _deleteDocId = null;
        _deleteDocUrl = null;
        document.getElementById('delete-doc-modal').classList.add('hidden');
    }

    function executeDeleteDoc() {
        if (!_deleteDocUrl) return;
        const btn = document.getElementById('delete-doc-confirm');
        btn.disabled = true;
        btn.textContent = 'Menghapus...';

        const formData = new FormData();
        formData.append('_method', 'DELETE');
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

        fetch(_deleteDocUrl, {
            method: 'POST',
            headers: { 'Accept': 'application/json' },
            body: formData,
        })
        .then(r => r.json().catch(() => ({ success: r.ok })))
        .then(data => {
            if (data.success) {
                const card = document.getElementById('doc-card-' + _deleteDocId);
                if (card) card.remove();
                closeDeleteDocModal();
            } else {
                alert('Gagal menghapus dokumen. Silakan coba lagi.');
                btn.disabled = false;
                btn.innerHTML = '<i data-lucide="trash-2" class="h-3.5 w-3.5 inline"></i> Hapus';
            }
        })
        .catch(() => {
            alert('Terjadi kesalahan. Silakan coba lagi.');
            btn.disabled = false;
            btn.innerHTML = '<i data-lucide="trash-2" class="h-3.5 w-3.5 inline"></i> Hapus';
        });
    }
</script>
@endpush
