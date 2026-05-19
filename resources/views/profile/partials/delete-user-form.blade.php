<section class="space-y-6">
    <header class="mb-6 border-b border-slate-100 pb-4">
        <h2 class="text-lg font-bold text-slate-800">
            Hapus Akun
        </h2>
        <p class="mt-1 text-sm text-slate-500">
            Setelah akun Anda dihapus, semua sumber daya dan datanya akan dihapus secara permanen. Sebelum menghapus akun Anda, harap unduh data atau informasi apa pun yang ingin Anda simpan.
        </p>
    </header>

    <button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="btn-danger"
    >
        <i data-lucide="trash-2" class="mr-2 h-4 w-4"></i> Hapus Akun
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6 sm:p-8">
            @csrf
            @method('delete')

            <h2 class="text-lg font-bold text-slate-800">
                Apakah Anda yakin ingin menghapus akun Anda?
            </h2>

            <p class="mt-2 text-sm text-slate-500">
                Setelah akun Anda dihapus, semua sumber daya dan datanya akan dihapus secara permanen. Silakan masukkan password Anda untuk mengonfirmasi bahwa Anda ingin menghapus akun Anda secara permanen.
            </p>

            <div class="mt-6">
                <label class="label flex items-center gap-2" for="password">
                    <i data-lucide="lock" class="h-4 w-4 text-slate-400"></i>
                    Password
                </label>

                <div class="relative">
                    <input
                        id="password"
                        name="password"
                        type="password"
                        class="input pr-12"
                        placeholder="Password"
                    >
                    <button type="button" onclick="if(window.togglePassword) window.togglePassword('password', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none">
                        <i data-lucide="eye" class="h-5 w-5"></i>
                    </button>
                </div>

                @if ($errors->userDeletion->has('password'))
                    <p class="mt-2 text-xs font-semibold text-red-600">{{ $errors->userDeletion->first('password') }}</p>
                @endif
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" class="btn-secondary" x-on:click="$dispatch('close')">
                    Batal
                </button>

                <button type="submit" class="btn-danger">
                    Hapus Akun
                </button>
            </div>
        </form>
    </x-modal>
</section>
