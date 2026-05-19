<x-guest-layout>
    <div class="mb-8 text-center lg:text-left">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">Selamat datang</h1>
        <p class="mt-2 text-slate-500">Silakan masuk untuk mengelola agenda.</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf
        <div>
            <label class="label flex items-center gap-2" for="login">
                <i data-lucide="user" class="h-4 w-4 text-slate-400"></i>
                Username atau Email
            </label>
            <input id="login" class="input" type="text" name="login" value="{{ old('login') }}" placeholder="username atau email" required autofocus autocomplete="username">
        </div>

        <div>
            <div class="flex items-center justify-between">
                <label class="label flex items-center gap-2" for="password">
                    <i data-lucide="lock" class="h-4 w-4 text-slate-400"></i>
                    Password
                </label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="mb-2 text-xs font-semibold text-blue-600 hover:text-blue-700">Lupa password?</a>
                @endif
            </div>
            <div class="relative">
                <input id="password" class="input pr-12" type="password" name="password" placeholder="••••••••" required>
                <button type="button" onclick="togglePassword('password', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none">
                    <i data-lucide="eye" class="h-5 w-5"></i>
                </button>
            </div>
            @if ($errors->has('login') || $errors->has('password'))
                <p class="mt-2 text-[11px] font-semibold text-red-600 leading-tight">{{ $errors->first('login') ?: 'Username/email atau password tidak sesuai, silakan periksa kembali.' }}</p>
            @endif
        </div>

        <div class="flex items-center">
            <label class="relative flex cursor-pointer items-center gap-3">
                <input type="checkbox" name="remember" class="peer sr-only">
                <div class="h-5 w-5 rounded border border-slate-300 bg-white transition peer-checked:border-blue-600 peer-checked:bg-blue-600">
                    <i data-lucide="check" class="absolute inset-0 m-auto h-3.5 w-3.5 text-white opacity-0 transition peer-checked:opacity-100"></i>
                </div>
                <span class="text-xs font-medium text-slate-600">Ingat perangkat ini</span>
            </label>
        </div>

        <button class="btn-primary w-full py-2.5 shadow-lg shadow-blue-500/25">
            Masuk ke Panel
            <i data-lucide="arrow-right" class="ml-2 h-4 w-4"></i>
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
            Belum memiliki akun? 
            <a href="{{ route('register') }}" class="font-bold text-blue-600 hover:text-blue-700">Daftar sekarang</a>
        </p>
    </div>
</x-guest-layout>
