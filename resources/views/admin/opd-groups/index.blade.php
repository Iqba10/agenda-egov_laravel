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

    {{-- Table Missing Warning --}}
    @if(!empty($tableMissing))
    <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 flex items-start gap-3">
        <i data-lucide="alert-triangle" class="h-5 w-5 text-red-500 shrink-0 mt-0.5"></i>
        <div>
            <p class="text-sm font-bold text-red-800">Tabel <code>opd_groups</code> belum tersedia</p>
            <p class="text-xs text-red-600 mt-1">Jalankan migrasi database terlebih dahulu. Di Railway, redeploy aplikasi agar entrypoint menjalankan <code>php artisan migrate</code>, atau jalankan manual:</p>
            <code class="block mt-2 text-xs bg-red-100 text-red-800 px-3 py-2 rounded-lg font-mono">php artisan migrate --force</code>
        </div>
    </div>
    @endif

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
                        <th class="px-4 py-3 text-left font-semibold text-slate-700">Anggota</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-700">Status</th>
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
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                <i data-lucide="user" class="h-3 w-3"></i>
                                {{ $group->members_count ?? $group->members()->count() }} anggota
                            </span>
                        </td>
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
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-1">
                                <button type="button" onclick="openMembersModal({{ $group->id }}, '{{ addslashes($group->name) }}')"
                                        class="p-1.5 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" title="Kelola Anggota">
                                    <i data-lucide="users" class="h-4 w-4"></i>
                                </button>
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

{{-- Modal Kelola Anggota --}}
<div id="membersModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-950/60 backdrop-blur-sm" onclick="closeMembersModal()"></div>
    <div class="relative w-full max-w-2xl max-h-[85vh] flex flex-col rounded-2xl bg-white shadow-2xl border border-slate-200 overflow-hidden">
        {{-- Header --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-200 bg-slate-50">
            <div class="flex items-center gap-2">
                <i data-lucide="users" class="h-5 w-5 text-emerald-600"></i>
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Kelola Anggota</h3>
                    <p id="membersModalGroupName" class="text-xs text-slate-500"></p>
                </div>
            </div>
            <button onclick="closeMembersModal()" class="p-1.5 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                <i data-lucide="x" class="h-5 w-5"></i>
            </button>
        </div>

        {{-- Body --}}
        <div class="flex-1 overflow-y-auto p-5 space-y-4">
            {{-- Tambah Anggota --}}
            <div class="rounded-xl border border-slate-200 p-4">
                <p class="text-xs font-bold uppercase tracking-widest text-slate-500 mb-3">Tambah Anggota Baru</p>
                <div id="memberRows" class="space-y-2">
                    <div class="flex gap-2 member-row">
                        <input type="text" placeholder="Nama" class="member-nama flex-1 px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <input type="text" placeholder="No. WhatsApp (08xxx)" class="member-phone w-44 px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 font-mono">
                        <button type="button" onclick="removeMemberRow(this)" class="p-2 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                            <i data-lucide="trash-2" class="h-4 w-4"></i>
                        </button>
                    </div>
                </div>
                <div class="flex gap-2 mt-3">
                    <button type="button" onclick="addMemberRow()" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg transition-colors">
                        <i data-lucide="plus" class="h-3.5 w-3.5"></i> Tambah Baris
                    </button>
                    <button type="button" onclick="submitMembers()" id="submitMembersBtn"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition-colors">
                        <i data-lucide="save" class="h-3.5 w-3.5"></i> Simpan Anggota
                    </button>
                </div>
                <p id="membersMsg" class="hidden mt-2 text-xs font-semibold rounded-lg px-3 py-2"></p>
            </div>

            {{-- Daftar Anggota --}}
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Daftar Anggota (<span id="memberCount">0</span>)</p>
                <div id="membersList" class="space-y-1.5 max-h-64 overflow-y-auto">
                    <p class="text-sm text-slate-400 text-center py-4">Memuat...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentGroupId = null;

function openMembersModal(groupId, groupName) {
    currentGroupId = groupId;
    document.getElementById('membersModalGroupName').textContent = groupName;
    document.getElementById('membersModal').classList.remove('hidden');
    document.getElementById('membersModal').classList.add('flex');
    document.body.style.overflow = 'hidden';
    loadMembers(groupId);
    lucide.createIcons();
}

function closeMembersModal() {
    document.getElementById('membersModal').classList.add('hidden');
    document.getElementById('membersModal').classList.remove('flex');
    document.body.style.overflow = '';
    currentGroupId = null;
}

function addMemberRow() {
    const container = document.getElementById('memberRows');
    const row = document.createElement('div');
    row.className = 'flex gap-2 member-row';
    row.innerHTML = `
        <input type="text" placeholder="Nama" class="member-nama flex-1 px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
        <input type="text" placeholder="No. WhatsApp (08xxx)" class="member-phone w-44 px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 font-mono">
        <button type="button" onclick="removeMemberRow(this)" class="p-2 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
            <i data-lucide="trash-2" class="h-4 w-4"></i>
        </button>
    `;
    container.appendChild(row);
    lucide.createIcons();
}

function removeMemberRow(btn) {
    const rows = document.querySelectorAll('.member-row');
    if (rows.length > 1) {
        btn.closest('.member-row').remove();
    }
}

async function submitMembers() {
    const rows = document.querySelectorAll('.member-row');
    const members = [];

    rows.forEach(row => {
        const nama = row.querySelector('.member-nama').value.trim();
        const phone = row.querySelector('.member-phone').value.trim();
        if (nama && phone) {
            members.push({ nama, phone_number: phone });
        }
    });

    if (members.length === 0) {
        showMembersMsg('Isi minimal satu nama dan nomor WhatsApp.', false);
        return;
    }

    const btn = document.getElementById('submitMembersBtn');
    btn.disabled = true;
    btn.innerHTML = '<div class="h-3.5 w-3.5 border-2 border-white border-t-transparent rounded-full animate-spin"></div> Menyimpan...';

    try {
        const res = await fetch(`/admin/opd-groups/${currentGroupId}/members`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ members }),
        });

        const data = await res.json();

        if (data.success) {
            showMembersMsg(data.message, true);
            // Clear inputs
            document.querySelectorAll('.member-nama').forEach(el => el.value = '');
            document.querySelectorAll('.member-phone').forEach(el => el.value = '');
            loadMembers(currentGroupId);
            // Refresh page to update count
            setTimeout(() => location.reload(), 1500);
        } else {
            showMembersMsg(data.message || 'Gagal menyimpan.', false);
        }
    } catch (err) {
        showMembersMsg('Gagal menghubungi server.', false);
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i data-lucide="save" class="h-3.5 w-3.5"></i> Simpan Anggota';
        lucide.createIcons();
    }
}

async function loadMembers(groupId) {
    const list = document.getElementById('membersList');
    list.innerHTML = '<p class="text-sm text-slate-400 text-center py-4">Memuat...</p>';

    try {
        const res = await fetch(`/admin/opd-groups/${groupId}/members`, {
            headers: { 'Accept': 'application/json' },
        });
        const data = await res.json();

        if (data.success && data.members.length > 0) {
            document.getElementById('memberCount').textContent = data.members.length;
            list.innerHTML = data.members.map(m => `
                <div class="flex items-center gap-3 px-3 py-2.5 rounded-lg border border-slate-100 bg-slate-50/50 group">
                    <div class="h-8 w-8 rounded-full bg-emerald-100 flex items-center justify-center shrink-0">
                        <span class="text-xs font-bold text-emerald-700">${m.nama.substring(0, 2).toUpperCase()}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-slate-800 truncate">${escapeHtml(m.nama)}</p>
                        <p class="text-xs text-slate-500 font-mono">${escapeHtml(m.phone_number)}</p>
                    </div>
                    <button type="button" onclick="removeMember(${groupId}, ${m.id})"
                            class="p-1.5 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors opacity-0 group-hover:opacity-100" title="Hapus">
                        <i data-lucide="trash-2" class="h-4 w-4"></i>
                    </button>
                </div>
            `).join('');
        } else {
            document.getElementById('memberCount').textContent = '0';
            list.innerHTML = '<p class="text-sm text-slate-400 text-center py-4">Belum ada anggota. Tambahkan di atas.</p>';
        }
    } catch {
        list.innerHTML = '<p class="text-sm text-red-400 text-center py-4">Gagal memuat data.</p>';
    }
    lucide.createIcons();
}

async function removeMember(groupId, memberId) {
    if (!confirm('Hapus anggota ini?')) return;

    try {
        const res = await fetch(`/admin/opd-groups/${groupId}/members/${memberId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
        });
        const data = await res.json();
        if (data.success) {
            loadMembers(groupId);
        }
    } catch {
        alert('Gagal menghapus anggota.');
    }
}

function showMembersMsg(msg, success) {
    const el = document.getElementById('membersMsg');
    el.classList.remove('hidden');
    el.className = `mt-2 text-xs font-semibold rounded-lg px-3 py-2 ${success ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700'}`;
    el.textContent = msg;
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

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
