<section>
    <header class="mb-6 border-b border-slate-100 pb-4">
        <h2 class="text-lg font-bold text-slate-800">
            Perbarui Password
        </h2>
        <p class="mt-1 text-sm text-slate-500">
            Pastikan akun Anda menggunakan password yang panjang dan acak untuk menjaga keamanan.
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="space-y-5">
        @csrf
        @method('put')

        <div>
            <label class="label flex items-center gap-2" for="update_password_current_password">
                <i data-lucide="lock" class="h-4 w-4 text-slate-400"></i>
                Password Saat Ini
            </label>
            <div class="relative">
                <input id="update_password_current_password" name="current_password" type="password" class="input pr-12" autocomplete="current-password">
                <button type="button" onclick="togglePassword('update_password_current_password', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none">
                    <i data-lucide="eye" class="h-5 w-5"></i>
                </button>
            </div>
            @if ($errors->updatePassword->has('current_password'))
                <p class="mt-2 text-xs font-semibold text-red-600">{{ $errors->updatePassword->first('current_password') }}</p>
            @endif
        </div>

        <div>
            <label class="label flex items-center gap-2" for="update_password_password">
                <i data-lucide="key" class="h-4 w-4 text-slate-400"></i>
                Password Baru
            </label>
            <div class="relative">
                <input id="update_password_password" name="password" type="password" class="input pr-12" autocomplete="new-password">
                <button type="button" onclick="togglePassword('update_password_password', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none">
                    <i data-lucide="eye" class="h-5 w-5"></i>
                </button>
            </div>
            @if ($errors->updatePassword->has('password'))
                <p class="mt-2 text-xs font-semibold text-red-600">{{ $errors->updatePassword->first('password') }}</p>
            @endif
        </div>

        <div>
            <label class="label flex items-center gap-2" for="update_password_password_confirmation">
                <i data-lucide="check-circle" class="h-4 w-4 text-slate-400"></i>
                Konfirmasi Password
            </label>
            <div class="relative">
                <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="input pr-12" autocomplete="new-password">
                <button type="button" onclick="togglePassword('update_password_password_confirmation', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none">
                    <i data-lucide="eye" class="h-5 w-5"></i>
                </button>
            </div>
            @if ($errors->updatePassword->has('password_confirmation'))
                <p class="mt-2 text-xs font-semibold text-red-600">{{ $errors->updatePassword->first('password_confirmation') }}</p>
            @endif
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button class="btn-primary">
                <i data-lucide="save" class="mr-2 h-4 w-4"></i> Simpan Password
            </button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm font-semibold text-emerald-600"
                >Tersimpan.</p>
            @endif
        </div>
    </form>

    <script>
        if (typeof window.togglePassword !== 'function') {
            window.togglePassword = function(inputId, button) {
                const input = document.getElementById(inputId);
                const icon = button.querySelector('i');
                
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.setAttribute('data-lucide', 'eye-off');
                } else {
                    input.type = 'password';
                    icon.setAttribute('data-lucide', 'eye');
                }
                
                if (window.lucide) {
                    lucide.createIcons();
                }
            }
        }
    </script>
</section>
