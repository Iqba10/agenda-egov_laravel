@extends('layouts.app')

@section('title', 'Tambah Grup OPD')

@section('content')
<div class="p-4 lg:p-6">
    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-xl font-bold text-slate-800">Tambah Grup OPD</h1>
        <p class="text-sm text-slate-500 mt-1">Tambahkan grup WhatsApp untuk notifikasi agenda ke OPD</p>
    </div>

    {{-- Form --}}
    <div class="max-w-2xl">
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="px-4 py-3 border-b border-slate-100 bg-slate-50">
                <div class="flex items-center gap-2">
                    <i data-lucide="users" class="h-4 w-4 text-slate-500"></i>
                    <span class="font-semibold text-slate-700 text-sm">Form Grup OPD</span>
                </div>
            </div>

            <form action="{{ route('admin.opd-groups.store') }}" method="POST" class="p-4">
                @csrf

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Nama Grup *</label>
                        <input type="text" name="name" required
                               class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Contoh: Diskominfo Sambas">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Group ID dari Fonnte *</label>
                        <input type="text" name="group_id" required
                               class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono"
                               placeholder="Contoh: 120363379743282885@g.us">
                        <p class="text-xs text-slate-500 mt-1">Group ID format: xxx@g.us. Dapatkan dari tombol "Refresh Grup dari Fonnte".</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Deskripsi</label>
                        <textarea name="description" rows="3"
                                  class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="Deskripsi grup (opsional)"></textarea>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 mt-6 pt-4 border-t border-slate-100">
                    <a href="{{ route('admin.opd-groups.index') }}"
                       class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium rounded-lg transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                            class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition-colors">
                        <i data-lucide="save" class="h-4 w-4 inline mr-2"></i>
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
});
</script>
@endsection
