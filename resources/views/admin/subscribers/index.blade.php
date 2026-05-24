@extends('layouts.app')

@section('title', 'Subscribers Notifikasi')

@section('content')
<div class="p-4 lg:p-6">
    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-xl font-bold text-slate-800">Subscribers Notifikasi</h1>
        <p class="text-sm text-slate-500 mt-1">Kelola pendaftar notifikasi WhatsApp dan Push Browser</p>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-6">
        <div class="rounded-xl border border-slate-200 bg-white p-4">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Total</p>
            <p class="text-2xl font-bold text-slate-800 mt-1">{{ $stats['total'] }}</p>
        </div>
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4">
            <p class="text-xs font-semibold text-emerald-600 uppercase tracking-wide">WA Terkirim</p>
            <p class="text-2xl font-bold text-emerald-700 mt-1">{{ $stats['wa_sent'] }}</p>
        </div>
        <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
            <p class="text-xs font-semibold text-amber-600 uppercase tracking-wide">WA Pending</p>
            <p class="text-2xl font-bold text-amber-700 mt-1">{{ $stats['wa_pending'] }}</p>
        </div>
        <div class="rounded-xl border border-blue-200 bg-blue-50 p-4">
            <p class="text-xs font-semibold text-blue-600 uppercase tracking-wide">FCM Terkirim</p>
            <p class="text-2xl font-bold text-blue-700 mt-1">{{ $stats['fcm_sent'] }}</p>
        </div>
        <div class="rounded-xl border border-purple-200 bg-purple-50 p-4">
            <p class="text-xs font-semibold text-purple-600 uppercase tracking-wide">FCM Pending</p>
            <p class="text-2xl font-bold text-purple-700 mt-1">{{ $stats['fcm_pending'] }}</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="rounded-xl border border-slate-200 bg-white shadow-sm mb-6">
        <div class="px-4 py-3 border-b border-slate-100 bg-slate-50">
            <div class="flex items-center gap-2">
                <i data-lucide="filter" class="h-4 w-4 text-slate-500"></i>
                <span class="font-semibold text-slate-700 text-sm">Filter & Pencarian</span>
            </div>
        </div>
        <form action="{{ route('admin.subscribers.index') }}" method="GET" class="p-4">
            <div class="flex flex-col md:flex-row gap-3">
                <div class="flex-1">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Cari nomor, nama, atau agenda..."
                           class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <select name="status" class="px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Status</option>
                    <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Terkirim</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                </select>
                <select name="channel" class="px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Channel</option>
                    <option value="whatsapp" {{ request('channel') === 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                    <option value="fcm" {{ request('channel') === 'fcm' ? 'selected' : '' }}>Push Browser</option>
                    <option value="both" {{ request('channel') === 'both' ? 'selected' : '' }}>Gabungan</option>
                </select>
                <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white text-sm font-medium rounded-lg transition-colors">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'status', 'channel']))
                <a href="{{ route('admin.subscribers.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium rounded-lg transition-colors">
                    Reset
                </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Subscribers Table --}}
    <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden mb-6">
        <div class="px-4 py-3 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i data-lucide="users" class="h-4 w-4 text-slate-500"></i>
                <span class="font-semibold text-slate-700 text-sm">Daftar Subscribers</span>
                <span class="text-xs text-slate-500">({{ $subscribers->total() }} data)</span>
            </div>
            <button type="button" onclick="bulkResendSelected()" id="bulkResendBtn"
                    class="hidden px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-medium rounded-lg transition-colors">
                <i data-lucide="send" class="h-3.5 w-3.5 inline mr-1"></i>
                Kirim Ulang Terpilih
            </button>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 text-left">
                            <input type="checkbox" id="selectAll" onchange="toggleSelectAll()" class="rounded border-slate-300">
                        </th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Agenda</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Subscriber</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Channel</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Reminder</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Waktu Kirim</th>
                        <th class="px-4 py-3 text-center font-semibold text-slate-600">WA</th>
                        <th class="px-4 py-3 text-center font-semibold text-slate-600">FCM</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Terdaftar</th>
                        <th class="px-4 py-3 text-center font-semibold text-slate-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($subscribers as $sub)
                    <tr class="hover:bg-slate-50" data-id="{{ $sub->id }}">
                        <td class="px-4 py-3">
                            <input type="checkbox" class="subscriber-checkbox rounded border-slate-300" 
                                   value="{{ $sub->id }}" onchange="updateBulkButton()">
                        </td>
                        <td class="px-4 py-3">
                            <div class="max-w-[200px]">
                                <p class="font-medium text-slate-800 truncate" title="{{ $sub->agenda?->perihal_kegiatan }}">
                                    {{ Str::limit($sub->agenda?->perihal_kegiatan ?? '-', 30) }}
                                </p>
                                <p class="text-xs text-slate-500">
                                    {{ $sub->agenda?->waktu_mulai?->format('d/m/Y H:i') ?? '-' }}
                                </p>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <p class="font-medium text-slate-800">{{ $sub->phone_number ?: '-' }}</p>
                            @if($sub->nama)
                            <p class="text-xs text-slate-500">{{ $sub->nama }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $channelColors = [
                                    'whatsapp' => 'bg-emerald-100 text-emerald-700',
                                    'fcm' => 'bg-blue-100 text-blue-700',
                                    'both' => 'bg-purple-100 text-purple-700',
                                ];
                                $channelLabels = [
                                    'whatsapp' => 'WhatsApp',
                                    'fcm' => 'Browser',
                                    'both' => 'Gabungan',
                                ];
                                $hasPlaceholderFcm = $sub->fcmToken
                                    && str_starts_with($sub->fcmToken->token, 'browser-notification-');
                            @endphp
                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full {{ $channelColors[$sub->channel_preference] ?? 'bg-slate-100 text-slate-600' }}">
                                {{ $channelLabels[$sub->channel_preference] ?? $sub->channel_preference }}
                            </span>
                            @if($hasPlaceholderFcm)
                            <p class="mt-1 text-[10px] font-medium text-red-500">Token browser tidak valid</p>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-xs text-slate-600">{{ $sub->reminder_minutes ?? 60 }} menit</span>
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $reminderMinutes = $sub->reminder_minutes ?? 60;
                                $reminderTime = $sub->agenda?->waktu_mulai?->copy()->subMinutes($reminderMinutes);
                                $now = now();
                                $isPast = $reminderTime && $now->gt($reminderTime);
                                $minutesUntil = $reminderTime ? $now->diffInMinutes($reminderTime, false) : null;
                            @endphp
                            @if($reminderTime)
                                <p class="text-xs font-medium {{ $isPast ? 'text-slate-400' : 'text-slate-700' }}">
                                    {{ $reminderTime->format('d/m H:i') }}
                                </p>
                                @if($isPast)
                                    <p class="text-[10px] text-red-500">Lewat {{ abs(round($minutesUntil)) }} menit</p>
                                @else
                                    <p class="text-[10px] text-emerald-600">Dalam {{ round($minutesUntil) }} menit</p>
                                @endif
                            @else
                                <span class="text-xs text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if(in_array($sub->channel_preference, ['whatsapp', 'both']))
                                @if($sub->whatsapp_sent)
                                <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-emerald-100 text-emerald-600">
                                    <i data-lucide="check" class="h-3.5 w-3.5"></i>
                                </span>
                                @else
                                <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-amber-100 text-amber-600">
                                    <i data-lucide="clock" class="h-3.5 w-3.5"></i>
                                </span>
                                @endif
                            @else
                            <span class="text-slate-300">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if(in_array($sub->channel_preference, ['fcm', 'both']))
                                @if($sub->fcm_sent)
                                <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-emerald-100 text-emerald-600">
                                    <i data-lucide="check" class="h-3.5 w-3.5"></i>
                                </span>
                                @elseif($hasPlaceholderFcm)
                                <div class="inline-flex items-center gap-1 rounded-full bg-red-100 px-2 py-1 text-[10px] font-medium text-red-600">
                                    <i data-lucide="triangle-alert" class="h-3 w-3"></i>
                                    Invalid
                                </div>
                                @else
                                <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-amber-100 text-amber-600">
                                    <i data-lucide="clock" class="h-3.5 w-3.5"></i>
                                </span>
                                @endif
                            @else
                            <span class="text-slate-300">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-xs text-slate-500">{{ $sub->created_at?->format('d/m/Y H:i') }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-1">
                                <button type="button" onclick="resendSubscriber({{ $sub->id }})" 
                                        class="p-1.5 rounded-lg text-emerald-600 hover:bg-emerald-50 transition-colors" title="Kirim Ulang">
                                    <i data-lucide="send" class="h-4 w-4"></i>
                                </button>
                                <button type="button" onclick="deleteSubscriber({{ $sub->id }})" 
                                        class="p-1.5 rounded-lg text-red-600 hover:bg-red-50 transition-colors" title="Hapus">
                                    <i data-lucide="trash-2" class="h-4 w-4"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-4 py-8 text-center text-slate-500">
                            Belum ada subscriber.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($subscribers->hasPages())
        <div class="px-4 py-3 border-t border-slate-100 bg-slate-50">
            {{ $subscribers->links() }}
        </div>
        @endif
    </div>

    {{-- FCM Tokens Section --}}
    <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-100 bg-slate-50">
            <div class="flex items-center gap-2">
                <i data-lucide="smartphone" class="h-4 w-4 text-blue-500"></i>
                <span class="font-semibold text-slate-700 text-sm">FCM Tokens Terdaftar</span>
                <span class="text-xs text-slate-500">({{ $fcmTokens->count() }} token)</span>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">ID</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Device</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Token</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Subscribed Agendas</th>
                        <th class="px-4 py-3 text-center font-semibold text-slate-600">Status</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Terdaftar</th>
                        <th class="px-4 py-3 text-center font-semibold text-slate-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($fcmTokens as $token)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 text-slate-600">#{{ $token->id }}</td>
                        <td class="px-4 py-3 font-medium text-slate-800">{{ $token->device_name }}</td>
                        <td class="px-4 py-3">
                            <code class="text-xs bg-slate-100 px-2 py-1 rounded text-slate-600">
                                {{ Str::limit($token->token, 30) }}
                            </code>
                        </td>
                        <td class="px-4 py-3">
                            @if($token->subscribed_agendas && count($token->subscribed_agendas) > 0)
                            <span class="text-xs text-slate-600">{{ implode(', ', $token->subscribed_agendas) }}</span>
                            @else
                            <span class="text-xs text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($token->is_active)
                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-emerald-100 text-emerald-700">Aktif</span>
                            @else
                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-slate-100 text-slate-600">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-xs text-slate-500">{{ $token->created_at?->format('d/m/Y H:i') }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <button type="button" onclick="deleteFcmToken({{ $token->id }})" 
                                    class="p-1.5 rounded-lg text-red-600 hover:bg-red-50 transition-colors" title="Hapus">
                                <i data-lucide="trash-2" class="h-4 w-4"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-slate-500">
                            Belum ada FCM token terdaftar.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Toast for notifications --}}
<div id="toast" class="fixed bottom-4 right-4 z-50 hidden">
    <div class="px-4 py-3 rounded-xl shadow-lg border" id="toastContent"></div>
</div>

<script>
const csrfToken = '{{ csrf_token() }}';

function showToast(message, success = true) {
    const toast = document.getElementById('toast');
    const content = document.getElementById('toastContent');
    content.textContent = message;
    content.className = `px-4 py-3 rounded-xl shadow-lg border text-sm font-medium ${success ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-red-50 border-red-200 text-red-800'}`;
    toast.classList.remove('hidden');
    setTimeout(() => toast.classList.add('hidden'), 4000);
}

function resendSubscriber(id) {
    if (!confirm('Kirim ulang notifikasi ke subscriber ini?')) return;
    
    fetch(`/admin/subscribers/${id}/resend`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
        },
    })
    .then(r => r.json())
    .then(data => {
        showToast(data.message, data.success);
        if (data.success) setTimeout(() => location.reload(), 1500);
    })
    .catch(() => showToast('Gagal mengirim request', false));
}

function deleteSubscriber(id) {
    if (!confirm('Hapus subscriber ini?')) return;
    
    fetch(`/admin/subscribers/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
        },
    })
    .then(r => r.json())
    .then(data => {
        showToast(data.message, data.success);
        if (data.success) {
            document.querySelector(`tr[data-id="${id}"]`)?.remove();
        }
    })
    .catch(() => showToast('Gagal mengirim request', false));
}

function deleteFcmToken(id) {
    if (!confirm('Hapus FCM token ini?')) return;
    
    fetch(`/admin/fcm-tokens/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
        },
    })
    .then(r => r.json())
    .then(data => {
        showToast(data.message, data.success);
        if (data.success) setTimeout(() => location.reload(), 1500);
    })
    .catch(() => showToast('Gagal mengirim request', false));
}

function toggleSelectAll() {
    const checked = document.getElementById('selectAll').checked;
    document.querySelectorAll('.subscriber-checkbox').forEach(cb => cb.checked = checked);
    updateBulkButton();
}

function updateBulkButton() {
    const checked = document.querySelectorAll('.subscriber-checkbox:checked');
    const btn = document.getElementById('bulkResendBtn');
    if (checked.length > 0) {
        btn.classList.remove('hidden');
        btn.textContent = `Kirim Ulang (${checked.length})`;
    } else {
        btn.classList.add('hidden');
    }
}

function bulkResendSelected() {
    const ids = Array.from(document.querySelectorAll('.subscriber-checkbox:checked')).map(cb => cb.value);
    if (ids.length === 0) return;
    
    if (!confirm(`Kirim ulang notifikasi ke ${ids.length} subscriber?`)) return;
    
    fetch('/admin/subscribers/bulk-resend', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
        },
        body: JSON.stringify({ ids }),
    })
    .then(r => r.json())
    .then(data => {
        showToast(data.message, data.success);
        if (data.success) setTimeout(() => location.reload(), 1500);
    })
    .catch(() => showToast('Gagal mengirim request', false));
}
</script>
@endsection
