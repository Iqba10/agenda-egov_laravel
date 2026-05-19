<x-app-layout>
    <x-slot name="header">Manajemen Pengguna</x-slot>
    <x-slot name="subheader">Atur role pengguna sesuai kebutuhan operasional aplikasi.</x-slot>

    <section class="dashboard-panel mt-6 animate-dashboard" style="animation-delay: .05s">
        <div class="dashboard-panel-header">
            <h2 class="dashboard-panel-title">
                <span class="dashboard-icon-tile"><i data-lucide="users" class="h-5 w-5"></i></span>
                Daftar Pengguna Sistem
            </h2>
        </div>

        <div class="overflow-x-auto">
            <table class="table-ui">
                <thead>
                    <tr>
                        <th class="w-1/3">Pengguna</th>
                        <th>Email</th>
                        <th>Role Saat Ini</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($users as $user)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 text-slate-500 font-bold border border-slate-200 shadow-sm">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-semibold text-slate-900">{{ $user->name }}</div>
                                        <div class="mt-0.5 text-xs text-slate-500">@{{ $user->username ?? 'ID #'.$user->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="flex items-center gap-2 text-slate-600 text-sm">
                                    <i data-lucide="mail" class="h-4 w-4 text-slate-400"></i>
                                    {{ $user->email }}
                                </div>
                            </td>
                            <td>
                                @if($user->role === 'admin')
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-indigo-100 px-2.5 py-1 text-xs font-semibold text-indigo-700">
                                        <i data-lucide="shield-check" class="h-3.5 w-3.5"></i> Admin
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700 border border-slate-200">
                                        <i data-lucide="user" class="h-3.5 w-3.5"></i> User
                                    </span>
                                @endif
                            </td>
                            <td class="text-right">
                                @if($user->id !== auth()->id())
                                    <div class="flex items-center justify-end gap-2">
                                        <form method="POST" action="{{ route('admin.users.role', $user) }}" class="flex items-center gap-2">
                                            @csrf
                                            @method('PATCH')
                                            <select name="role" class="py-1.5 pl-3 pr-8 text-xs font-medium border-slate-200 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-slate-700 bg-white shadow-sm">
                                                @foreach (['user', 'admin'] as $role)
                                                    <option value="{{ $role }}" @selected($user->role === $role)>{{ ucfirst($role) }}</option>
                                                @endforeach
                                            </select>
                                            <button type="submit" class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white transition-colors border border-indigo-100 hover:border-indigo-600 shadow-sm" title="Simpan Role">
                                                <i data-lucide="save" class="h-4 w-4"></i>
                                            </button>
                                        </form>
                                        <button type="button"
                                                onclick="confirmDeleteUser({{ $user->id }}, '{{ addslashes($user->name) }}')"
                                                class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-50 text-red-500 hover:bg-red-600 hover:text-white transition-colors border border-red-100 hover:border-red-600 shadow-sm"
                                                title="Hapus Pengguna">
                                            <i data-lucide="trash-2" class="h-4 w-4"></i>
                                        </button>
                                    </div>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-xs text-slate-400 italic bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-100">
                                        <i data-lucide="check-circle-2" class="h-3.5 w-3.5"></i> Akun Anda
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <div class="flex flex-col items-center justify-center py-12">
                                    <div class="rounded-full bg-slate-100 p-3 mb-3 shadow-sm border border-slate-200">
                                        <i data-lucide="users" class="h-6 w-6 text-slate-400"></i>
                                    </div>
                                    <p class="text-sm font-medium text-slate-900">Belum ada data pengguna</p>
                                    <p class="text-xs text-slate-500 mt-1">Sistem belum memiliki pengguna lain.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
            <div class="border-t border-slate-100 bg-slate-50/50 px-6 py-4">
                {{ $users->links() }}
            </div>
        @endif
    </section>

    {{-- Delete Confirmation Modal --}}
    <div id="deleteUserModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-950/50 backdrop-blur-sm" onclick="closeDeleteUserModal()"></div>
        <div class="relative w-full max-w-md rounded-2xl bg-white shadow-2xl border border-slate-200 p-6">
            <div class="flex items-start gap-4">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-red-100">
                    <i data-lucide="triangle-alert" class="h-5 w-5 text-red-600"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-base font-semibold text-slate-900">Hapus Pengguna</h3>
                    <p class="mt-1 text-sm text-slate-500">Akun <strong id="deleteUserName" class="text-slate-800"></strong> akan dihapus secara permanen. Tindakan ini tidak dapat dibatalkan.</p>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="closeDeleteUserModal()" class="btn-secondary">
                    Batal
                </button>
                <form id="deleteUserForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-danger">
                        <i data-lucide="trash-2" class="h-4 w-4"></i>
                        Hapus Akun
                    </button>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function confirmDeleteUser(userId, userName) {
            document.getElementById('deleteUserName').textContent = userName;
            document.getElementById('deleteUserForm').action = '/admin/users/' + userId;
            const modal = document.getElementById('deleteUserModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeDeleteUserModal() {
            const modal = document.getElementById('deleteUserModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeDeleteUserModal();
        });
    </script>
    @endpush
</x-app-layout>
