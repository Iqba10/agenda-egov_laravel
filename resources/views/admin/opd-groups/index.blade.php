@extends('layouts.app')

@section('title', 'Grup OPD')

@section('content')
<div class="p-4 lg:p-6">
    {{-- Header --}}
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Grup WhatsApp OPD</h1>
            <p class="text-sm text-slate-500 mt-1">Kelola grup WhatsApp untuk notifikasi agenda ke OPD</p>
        </div>
        <div class="flex gap-2">
            <button type="button" onclick="fetchGroups()" id="fetchGroupsBtn"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                <i data-lucide="refresh-cw" class="h-4 w-4 inline mr-2"></i>
                Refresh Grup dari Fonnte
            </button>
            <a href="{{ route('admin.opd-groups.create') }}"
               class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition-colors">
                <i data-lucide="plus" class="h-4 w-4 inline mr-2"></i>
                Tambah Grup
            </a>
        </div>
    </div>

    {{-- Groups Table --}}
    <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-100 bg-slate-50">
            <div class="flex items-center gap-2">
                <i data-lucide="users" class="h-4 w-4 text-slate-500"></i>
                <span class="font-semibold text-slate-700 text-sm">Daftar Grup OPD</span>
                <span class="text-xs text-slate-500">({{ $groups->count() }} grup)</span>
            </div>
        </div>

        @if($groups->isEmpty())
        <div class="p-8 text-center">
            <i data-lucide="users" class="h-12 w-12 mx-auto text-slate-300 mb-3"></i>
            <p class="text-sm text-slate-500">Belum ada grup OPD yang ditambahkan.</p>
            <p class="text-xs text-slate-400 mt-1">Klik "Refresh Grup dari Fonnte" untuk mengambil daftar grup WhatsApp Anda.</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-slate-700">Nama Grup</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-700">Group ID</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-700">Deskripsi</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-700">Status</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-700">Dibuat</th>
                        <th class="px-4 py-3 text-center font-semibold text-slate-700">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($groups as $group)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <div class="h-8 w-8 rounded-lg bg-emerald-100 flex items-center justify-center">
                                    <i data-lucide="users" class="h-4 w-4 text-emerald-600"></i>
                                </div>
                                <span class="font-medium text-slate-800">{{ $group->name }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <code class="text-xs bg-slate-100 px-2 py-1 rounded text-slate-600">{{ $group->group_id }}</code>
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ $group->description ?? '-' }}</td>
                        <td class="px-4 py-3">
                            @if($group->is_active)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                Aktif
                            </span>
                            @else
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-600">
                                <span class="h-1.5 w-1.5 rounded-full bg-slate-400 mr-1.5"></span>
                                Nonaktif
                            </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-slate-500 text-xs">{{ $group->created_at->translatedFormat('d M Y, H:i') }}</td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-1">
                                <a href="{{ route('admin.opd-groups.edit', $group) }}"
                                   class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit">
                                    <i data-lucide="edit-2" class="h-4 w-4"></i>
                                </a>
                                <form action="{{ route('admin.opd-groups.destroy', $group) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus grup ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                        <i data-lucide="trash-2" class="h-4 w-4"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>

<script>
function fetchGroups() {
    const btn = document.getElementById('fetchGroupsBtn');
    btn.disabled = true;
    btn.innerHTML = '<i data-lucide="loader-2" class="h-4 w-4 inline mr-2 animate-spin"></i> Memuat...';

    fetch('{{ route('admin.opd-groups.fetch') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Berhasil mengambil ' + data.groups.length + ' grup dari Fonnte!');
            location.reload();
        } else {
            alert('Gagal: ' + (data.error || 'Terjadi kesalahan'));
        }
    })
    .catch(error => {
        alert('Gagal menghubungi server: ' + error.message);
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i data-lucide="refresh-cw" class="h-4 w-4 inline mr-2"></i> Refresh Grup dari Fonnte';
        lucide.createIcons();
    });
}

document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
});
</script>
@endsection
