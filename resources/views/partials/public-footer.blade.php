<footer class="bg-slate-900 text-white mt-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-10">
        <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">

            {{-- Identitas --}}
            <div class="lg:col-span-2">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-9 h-9 bg-blue-600 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i data-lucide="building-2" class="h-5 w-5 text-white"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-white leading-tight">Dinas Komunikasi dan Informatika</p>
                        <p class="text-xs text-slate-400">Kabupaten Sambas</p>
                    </div>
                </div>
                <div class="space-y-2 text-sm text-slate-400">
                    <div class="flex items-start gap-2.5">
                        <i data-lucide="map-pin" class="h-4 w-4 flex-shrink-0 mt-0.5 text-slate-500"></i>
                        <span>Jl. Sukaramai, Dalam Kaum, Kec. Sambas, Kab. Sambas, Kalimantan Barat 79462</span>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <i data-lucide="phone" class="h-4 w-4 flex-shrink-0 text-slate-500"></i>
                        <span>(0562) 393124</span>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <i data-lucide="globe" class="h-4 w-4 flex-shrink-0 text-slate-500"></i>
                        <a href="https://diskominfo.sambas.go.id" target="_blank" class="hover:text-blue-400 transition-colors">diskominfo.sambas.go.id</a>
                    </div>
                </div>
            </div>

            {{-- Tautan --}}
            <div>
                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-4">Tautan</p>
                <nav class="space-y-2">
                    <a href="{{ route('agenda.index') }}" class="flex items-center gap-2 text-sm text-slate-400 hover:text-white transition-colors">
                        <i data-lucide="home" class="h-3.5 w-3.5"></i> Beranda
                    </a>
                    <a href="{{ route('login') }}" class="flex items-center gap-2 text-sm text-slate-400 hover:text-white transition-colors">
                        <i data-lucide="log-in" class="h-3.5 w-3.5"></i> Login Admin
                    </a>
                    <a href="https://sambas.go.id" target="_blank" class="flex items-center gap-2 text-sm text-slate-400 hover:text-white transition-colors">
                        <i data-lucide="external-link" class="h-3.5 w-3.5"></i> Situs Resmi Sambas
                    </a>
                    <a href="https://diskominfo.sambas.go.id" target="_blank" class="flex items-center gap-2 text-sm text-slate-400 hover:text-white transition-colors">
                        <i data-lucide="external-link" class="h-3.5 w-3.5"></i> Situs Diskominfo
                    </a>
                </nav>
            </div>
        </div>

        <div class="mt-8 pt-6 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-3">
            <p class="text-[11px] text-slate-500">
                &copy; {{ date('Y') }} Dinas Komunikasi dan Informatika Kabupaten Sambas. Hak cipta dilindungi.
            </p>
            <p class="text-[11px] text-slate-600">Sistem Informasi Agenda Kegiatan Pemerintah Daerah</p>
        </div>
    </div>
</footer>
