<section>
    <header class="mb-6 border-b border-slate-100 pb-4">
        <h2 class="text-lg font-bold text-slate-800">
            Informasi Profil
        </h2>
        <p class="mt-1 text-sm text-slate-500">
            Perbarui informasi profil dan alamat email akun Anda.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-5">
        @csrf
        @method('patch')

        <div>
            <label class="label flex items-center gap-2" for="name">
                <i data-lucide="user" class="h-4 w-4 text-slate-400"></i>
                Nama Lengkap
            </label>
            <input id="name" name="name" type="text" class="input" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
            @if ($errors->has('name'))
                <p class="mt-2 text-xs font-semibold text-red-600">{{ $errors->first('name') }}</p>
            @endif
        </div>

        <div>
            <label class="label flex items-center gap-2" for="email">
                <i data-lucide="mail" class="h-4 w-4 text-slate-400"></i>
                Email
            </label>
            <input id="email" name="email" type="email" class="input" value="{{ old('email', $user->email) }}" required autocomplete="username">
            @if ($errors->has('email'))
                <p class="mt-2 text-xs font-semibold text-red-600">{{ $errors->first('email') }}</p>
            @endif

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-3 rounded-lg bg-amber-50 p-3">
                    <p class="text-sm font-medium text-amber-800">
                        Alamat email Anda belum diverifikasi.
                        <button form="send-verification" class="text-amber-600 underline hover:text-amber-900 focus:outline-none">
                            Klik di sini untuk mengirim ulang email verifikasi.
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-sm font-semibold text-emerald-600">
                            Tautan verifikasi baru telah dikirim ke alamat email Anda.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button class="btn-primary">
                <i data-lucide="save" class="mr-2 h-4 w-4"></i> Simpan Perubahan
            </button>

            @if (session('status') === 'profile-updated')
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
</section>
