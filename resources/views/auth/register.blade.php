<x-guest-layout>
    <div class="mb-8 text-center lg:text-left">
        @if ($isFirstSetup)
            <div class="mb-4 inline-flex items-center gap-2 rounded-lg bg-amber-50 border border-amber-200 px-3 py-2 text-xs font-semibold text-amber-700">
                <i data-lucide="shield-alert" class="h-3.5 w-3.5"></i>
                Mode Pengaturan Awal
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Buat Akun Admin</h1>
            <p class="mt-2 text-slate-500 text-sm">Belum ada pengguna. Akun pertama yang didaftarkan akan menjadi <span class="font-bold text-amber-700 underline decoration-amber-200 underline-offset-4">administrator</span>.</p>
        @else
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Buat Akun Baru</h1>
            <p class="mt-2 text-slate-500 text-sm">Daftar sebagai <span class="font-bold text-slate-700 underline decoration-blue-200 underline-offset-4">pengguna</span> untuk memantau agenda.</p>
        @endif
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf
        <div>
            <label class="label flex items-center gap-2 mb-1.5" for="name">
                <i data-lucide="user" class="h-4 w-4 text-slate-400"></i>
                Nama Lengkap
            </label>
            <input id="name" class="input py-2" type="text" name="name" value="{{ old('name') }}" placeholder="Contoh: Budi Santoso" required autofocus>
            @error('name')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="label flex items-center gap-2 mb-1.5" for="username">
                <i data-lucide="at-sign" class="h-4 w-4 text-slate-400"></i>
                Username
            </label>
            <input id="username" class="input py-2" type="text" name="username" value="{{ old('username') }}" placeholder="contoh: budi_santoso" required autocomplete="username">
            <p class="mt-1 text-xs text-slate-400">Hanya huruf, angka, tanda hubung, dan garis bawah.</p>
            @error('username')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="label flex items-center gap-2 mb-1.5" for="email">
                <i data-lucide="mail" class="h-4 w-4 text-slate-400"></i>
                Email
            </label>
            <input id="email" class="input py-2" type="email" name="email" value="{{ old('email') }}" placeholder="nama@instansi.go.id" required>
            @error('email')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="label flex items-center gap-2 mb-1.5" for="password">
                    <i data-lucide="lock" class="h-4 w-4 text-slate-400"></i>
                    Password
                </label>
                <div class="relative">
                    <input id="password" class="input py-2 pr-12" type="password" name="password" placeholder="••••••••" required>
                    <button type="button" onclick="togglePassword('password', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none">
                        <i data-lucide="eye" class="h-4.5 w-4.5"></i>
                    </button>
                </div>
                @error('password')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="label flex items-center gap-2 mb-1.5" for="password_confirmation">
                    <i data-lucide="shield-check" class="h-4 w-4 text-slate-400"></i>
                    Konfirmasi
                </label>
                <div class="relative">
                    <input id="password_confirmation" class="input py-2 pr-12" type="password" name="password_confirmation" placeholder="••••••••" required>
                    <button type="button" onclick="togglePassword('password_confirmation', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none">
                        <i data-lucide="eye" class="h-4.5 w-4.5"></i>
                    </button>
                </div>
            </div>
        </div>

        <button class="btn-primary mt-2 w-full py-2.5 shadow-lg shadow-blue-500/25">
            {{ $isFirstSetup ? 'Buat Akun Admin' : 'Daftar Sekarang' }}
            <i data-lucide="{{ $isFirstSetup ? 'shield' : 'user-plus' }}" class="ml-2 h-4 w-4"></i>
        </button>
    </form>

    <script>
        function togglePassword(inputId, button) {
            const input = document.getElementById(inputId);
            const icon = button.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.setAttribute('data-lucide', 'eye-off');
            } else {
                input.type = 'password';
                icon.setAttribute('data-lucide', 'eye');
            }
            
            lucide.createIcons();
        }
    </script>

    <div class="mt-8 border-t border-slate-100 pt-6 text-center">
        <p class="text-sm text-slate-500">
            Sudah memiliki akun? 
            <a href="{{ route('login') }}" class="font-bold text-blue-600 hover:text-blue-700">Masuk di sini</a>
        </p>
    </div>
</x-guest-layout>
