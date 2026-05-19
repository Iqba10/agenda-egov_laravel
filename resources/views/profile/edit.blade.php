<x-app-layout>
    <x-slot name="header">Profil Pengguna</x-slot>
    <x-slot name="subheader">Kelola informasi profil dan pengaturan keamanan akun Anda.</x-slot>

    <div class="grid gap-6 lg:grid-cols-2 max-w-5xl">
        <div class="surface p-6 sm:p-8 rounded-2xl border border-slate-200 bg-white">
            @include('profile.partials.update-profile-information-form')
        </div>

        <div class="surface p-6 sm:p-8 rounded-2xl border border-slate-200 bg-white">
            @include('profile.partials.update-password-form')
        </div>

        <div class="surface p-6 sm:p-8 rounded-2xl border border-slate-100 bg-slate-50 lg:col-span-2">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center">
                    <i data-lucide="lightbulb" class="w-5 h-5 text-blue-600"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-slate-800 mb-3">Tips Keamanan Akun</h3>
                    <ul class="space-y-2 text-sm text-slate-600">
                        <li class="flex items-start gap-2">
                            <i data-lucide="check-circle" class="w-4 h-4 text-emerald-500 mt-0.5 flex-shrink-0"></i>
                            <span>Gunakan password yang kuat, minimal 8 karakter dengan kombinasi huruf, angka, dan simbol.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i data-lucide="check-circle" class="w-4 h-4 text-emerald-500 mt-0.5 flex-shrink-0"></i>
                            <span>Jangan bagikan kredensial akun kepada siapapun, termasuk sesama staff.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i data-lucide="check-circle" class="w-4 h-4 text-emerald-500 mt-0.5 flex-shrink-0"></i>
                            <span>Selalu logout setelah selesai menggunakan sistem, terutama di perangkat bersama.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i data-lucide="check-circle" class="w-4 h-4 text-emerald-500 mt-0.5 flex-shrink-0"></i>
                            <span>Perbarui password secara berkala untuk menjaga keamanan akun Anda.</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
