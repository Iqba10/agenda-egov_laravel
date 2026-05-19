@php($toast = session('toast') ?? (session('status') ? ['type' => 'success', 'message' => session('status')] : null))
@if ($toast)
    <div data-toast data-toast-type="{{ $toast['type'] }}" class="fixed right-4 top-4 z-[90] max-w-sm rounded-2xl border border-slate-200 bg-white p-4 shadow-2xl transition duration-200">
        <div class="flex items-start gap-3">
            <div class="{{ match($toast['type']) { 'success' => 'text-emerald-600', 'error' => 'text-red-600', 'warning' => 'text-amber-600', default => 'text-blue-700' } }}">
                <i data-lucide="{{ match($toast['type']) { 'success' => 'circle-check-big', 'error' => 'circle-x', 'warning' => 'triangle-alert', default => 'info' } }}" class="h-5 w-5"></i>
            </div>
            <div class="flex-1">
                <div class="text-sm font-semibold text-slate-900">Notifikasi</div>
                <p class="mt-1 text-sm text-slate-600">{{ $toast['message'] }}</p>
            </div>
            <button type="button" data-toast-close class="text-slate-400 hover:text-slate-700">
                <i data-lucide="x" class="h-4 w-4"></i>
            </button>
        </div>
    </div>
@endif
