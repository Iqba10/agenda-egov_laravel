<x-guest-layout>
    <div class="mb-8 text-center lg:text-left">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">Atur Ulang Password</h1>
        <p class="mt-2 text-slate-500 text-sm">Lupa password? Masukkan email Anda dan kami akan mengirimkan tautan pemulihan.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-6" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <!-- Email Address -->
        <div>
            <label class="label flex items-center gap-2 mb-1.5" for="email">
                <i data-lucide="mail" class="h-4 w-4 text-slate-400"></i>
                Email Pemulihan
            </label>
            <input id="email" class="input py-2" type="email" name="email" value="{{ old('email') }}" placeholder="nama@instansi.go.id" required autofocus />
        </div>

        <button class="btn-primary w-full py-2.5 shadow-lg shadow-blue-500/25">
            Kirim Link Pemulihan
            <i data-lucide="send" class="ml-2 h-4 w-4"></i>
        </button>
    </form>

    <div class="mt-8 border-t border-slate-100 pt-6 text-center">
        <a href="{{ route('login') }}" class="inline-flex items-center gap-2 text-sm font-bold text-blue-600 hover:text-blue-700">
            <i data-lucide="arrow-left" class="h-4 w-4"></i>
            Kembali ke Login
        </a>
    </div>
</x-guest-layout>
