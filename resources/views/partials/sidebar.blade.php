@php($user = auth()->user())
@php($isAdmin = $user && $user->hasRole('admin'))
@php($mainLinks = [
    ['label' => 'Agenda',  'icon' => 'calendar-days', 'route' => 'admin.dashboard', 'active' => request()->routeIs('admin.dashboard', 'admin.agendas.*')],
])
@php($adminLinks = [
    ['label' => 'Pengguna', 'icon' => 'users', 'route' => 'admin.users.index', 'active' => request()->routeIs('admin.users.*')],
    ['label' => 'Test Notifikasi', 'icon' => 'bell-ring', 'route' => 'admin.notifications.test', 'active' => request()->routeIs('admin.notifications.*')],
])
@php($profileLink = ['label' => 'Profil', 'icon' => 'user-round', 'route' => 'profile.edit', 'active' => request()->routeIs('profile.*')])
<aside class="fixed inset-y-0 left-0 z-50 flex w-72 flex-col -translate-x-full border-r border-slate-800 bg-slate-900 transition duration-200 lg:translate-x-0 sidebar-panel">

    {{-- Brand --}}
    <div class="flex items-center justify-between p-6 lg:p-8">
        <a href="{{ route('agenda.index') }}" class="block">
            <div class="text-xs font-semibold uppercase tracking-[0.2em] text-blue-400">Agenda eGov</div>
            <div class="mt-1 text-lg font-semibold text-white">Diskominfo Sambas</div>
        </a>
        <button type="button" data-sidebar-close class="rounded-xl p-2 text-slate-400 hover:bg-slate-800 lg:hidden">
            <i data-lucide="x" class="h-5 w-5"></i>
        </button>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto px-4 pb-4 lg:px-6">

        @if($isAdmin)
        {{-- Menu Utama (admin only) --}}
        <p class="mb-2 px-2 text-[10px] font-bold uppercase tracking-widest text-slate-500">Menu Utama</p>
        <div class="space-y-1">
            @foreach ($mainLinks as $link)
                <a href="{{ route($link['route']) }}"
                   class="{{ $link['active'] ? 'flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium bg-blue-600 text-white' : 'flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="{{ $link['icon'] }}" class="h-4 w-4 shrink-0"></i>
                    <span>{{ $link['label'] }}</span>
                </a>
            @endforeach
        </div>

        {{-- Divider --}}
        <div class="my-4 border-t border-slate-700/60"></div>

        {{-- Menu Lainnya (admin: users + profile) --}}
        <p class="mb-2 px-2 text-[10px] font-bold uppercase tracking-widest text-slate-500">Menu Lainnya</p>
        <div class="space-y-1">
            @foreach ($adminLinks as $link)
                <a href="{{ route($link['route']) }}"
                   class="{{ $link['active'] ? 'flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium bg-blue-600 text-white' : 'flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="{{ $link['icon'] }}" class="h-4 w-4 shrink-0"></i>
                    <span>{{ $link['label'] }}</span>
                </a>
            @endforeach
            <a href="{{ route($profileLink['route']) }}"
               class="{{ $profileLink['active'] ? 'flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium bg-blue-600 text-white' : 'flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <i data-lucide="{{ $profileLink['icon'] }}" class="h-4 w-4 shrink-0"></i>
                <span>{{ $profileLink['label'] }}</span>
            </a>
        </div>
        @else
        {{-- User role: only profile --}}
        <p class="mb-2 px-2 text-[10px] font-bold uppercase tracking-widest text-slate-500">Akun Saya</p>
        <div class="space-y-1">
            <a href="{{ route($profileLink['route']) }}"
               class="{{ $profileLink['active'] ? 'flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium bg-blue-600 text-white' : 'flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <i data-lucide="{{ $profileLink['icon'] }}" class="h-4 w-4 shrink-0"></i>
                <span>{{ $profileLink['label'] }}</span>
            </a>
        </div>
        <div class="mt-4 mx-2 rounded-xl bg-slate-800/60 border border-slate-700/50 p-3">
            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-1">Peran Akun</p>
            <p class="text-xs text-slate-400">Anda masuk sebagai <span class="font-semibold text-slate-300">Pengguna</span>. Fitur manajemen hanya tersedia untuk Admin.</p>
        </div>
        @endif

    </nav>

    {{-- Profile Card --}}
    <div class="border-t border-slate-700/60 p-4 lg:p-5">
        <div class="relative" id="profile-menu-wrapper">

            {{-- Popup actions --}}
            <div id="profile-menu-popup"
                 class="hidden absolute bottom-full left-0 right-0 mb-2 rounded-xl border border-slate-700 bg-slate-800 p-2 shadow-xl z-10">
                <a href="{{ route('agenda.index') }}"
                   class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-slate-300 hover:bg-slate-700 hover:text-white transition-colors">
                    <i data-lucide="home" class="h-4 w-4 shrink-0"></i>
                    <span>Beranda Publik</span>
                </a>
                <form method="POST" action="{{ route('logout') }}"
                      data-confirm="Yakin ingin keluar dari sesi aplikasi?"
                      class="mt-1">
                    @csrf
                    <button type="submit"
                            class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-red-400 hover:bg-red-500/10 hover:text-red-300 transition-colors">
                        <i data-lucide="log-out" class="h-4 w-4 shrink-0"></i>
                        <span>Keluar</span>
                    </button>
                </form>
            </div>

            {{-- Profile trigger button --}}
            <button type="button"
                    id="profile-menu-btn"
                    class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm hover:bg-slate-800 transition-colors group">
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-600 font-bold text-white text-xs uppercase">
                    {{ substr($user->name ?? 'U', 0, 1) }}
                </div>
                <div class="min-w-0 flex-1 text-left">
                    <p class="truncate text-sm font-medium text-white">{{ $user->name ?? 'Pengguna' }}</p>
                    <p class="truncate text-xs text-slate-400">{{ $user->email ?? '' }}</p>
                </div>
                <i data-lucide="chevrons-up-down" class="h-4 w-4 shrink-0 text-slate-500 group-hover:text-slate-300 transition-colors"></i>
            </button>
        </div>
    </div>

</aside>
