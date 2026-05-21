@extends('layouts.app')

@section('title', 'Test Notifikasi')

@section('content')
<div class="p-4 lg:p-6">
    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-xl font-bold text-slate-800">Test Notifikasi</h1>
        <p class="text-sm text-slate-500 mt-1">Uji coba pengiriman WhatsApp dan Push Notification</p>
    </div>

    {{-- Status Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        {{-- Fonnte Status --}}
        <div class="rounded-xl border {{ $fonnte_configured ? 'border-emerald-200 bg-emerald-50' : 'border-red-200 bg-red-50' }} p-4">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg {{ $fonnte_configured ? 'bg-emerald-500' : 'bg-red-500' }} text-white">
                    <i data-lucide="message-circle" class="h-5 w-5"></i>
                </div>
                <div>
                    <p class="font-semibold {{ $fonnte_configured ? 'text-emerald-800' : 'text-red-800' }}">Fonnte (WhatsApp)</p>
                    <p class="text-xs {{ $fonnte_configured ? 'text-emerald-600' : 'text-red-600' }}">
                        {{ $fonnte_configured ? 'Token terkonfigurasi' : 'Token belum diset di .env' }}
                    </p>
                </div>
            </div>
        </div>

        {{-- FCM Status --}}
        <div class="rounded-xl border {{ $fcm_configured ? 'border-emerald-200 bg-emerald-50' : 'border-red-200 bg-red-50' }} p-4">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg {{ $fcm_configured ? 'bg-emerald-500' : 'bg-red-500' }} text-white">
                    <i data-lucide="bell" class="h-5 w-5"></i>
                </div>
                <div>
                    <p class="font-semibold {{ $fcm_configured ? 'text-emerald-800' : 'text-red-800' }}">Firebase Cloud Messaging</p>
                    <p class="text-xs {{ $fcm_configured ? 'text-emerald-600' : 'text-red-600' }}">
                        {{ $fcm_configured ? 'Server key terkonfigurasi' : 'Server key belum diset di .env' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- WhatsApp Test --}}
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 bg-slate-50">
                <div class="flex items-center gap-2">
                    <i data-lucide="message-circle" class="h-5 w-5 text-emerald-600"></i>
                    <h2 class="font-bold text-slate-800">Test WhatsApp</h2>
                </div>
            </div>
            <div class="p-5">
                <form id="waTestForm" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Nomor WhatsApp</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 text-sm">+62</span>
                            <input type="tel" name="phone" id="waPhone" placeholder="812-3456-7890"
                                   class="w-full pl-12 pr-3 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Pesan</label>
                        <textarea name="message" id="waMessage" rows="4" placeholder="Tulis pesan test..."
                                  class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent resize-none">Halo! Ini adalah pesan test dari Agenda eGov Diskominfo Sambas.</textarea>
                    </div>
                    <div id="waResult" class="hidden rounded-lg p-3 text-sm"></div>
                    <button type="submit" id="waSubmit"
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 text-white text-sm font-semibold rounded-xl transition-colors">
                        <i data-lucide="send" class="h-4 w-4"></i>
                        Kirim Test WhatsApp
                    </button>
                </form>
            </div>
        </div>

        {{-- FCM Test --}}
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 bg-slate-50">
                <div class="flex items-center gap-2">
                    <i data-lucide="bell" class="h-5 w-5 text-blue-600"></i>
                    <h2 class="font-bold text-slate-800">Test Push Notification</h2>
                </div>
            </div>
            <div class="p-5">
                {{-- Get FCM Token --}}
                <div class="mb-4 p-3 rounded-lg bg-blue-50 border border-blue-200">
                    <p class="text-xs text-blue-700 mb-2">Klik tombol untuk mendapatkan FCM Token browser ini:</p>
                    <button type="button" onclick="getFcmToken()"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg transition-colors">
                        <i data-lucide="key" class="h-3.5 w-3.5"></i>
                        Dapatkan FCM Token
                    </button>
                </div>

                <form id="fcmTestForm" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">FCM Token</label>
                        <textarea name="token" id="fcmToken" rows="2" placeholder="Paste FCM token disini..."
                                  class="w-full px-3 py-2.5 text-xs font-mono border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Judul Notifikasi</label>
                        <input type="text" name="title" id="fcmTitle" value="Test Notifikasi Agenda"
                               class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Isi Notifikasi</label>
                        <textarea name="body" id="fcmBody" rows="3" placeholder="Isi notifikasi..."
                                  class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none">Ini adalah push notification test dari Agenda eGov.</textarea>
                    </div>
                    <div id="fcmResult" class="hidden rounded-lg p-3 text-sm"></div>
                    <button type="submit" id="fcmSubmit"
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white text-sm font-semibold rounded-xl transition-colors">
                        <i data-lucide="send" class="h-4 w-4"></i>
                        Kirim Test Push
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Broadcast Section --}}
    <div class="mt-6 rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 bg-slate-50">
            <div class="flex items-center gap-2">
                <i data-lucide="radio" class="h-5 w-5 text-purple-600"></i>
                <h2 class="font-bold text-slate-800">Broadcast ke Semua Subscriber</h2>
            </div>
        </div>
        <div class="p-5">
            <form id="broadcastForm" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Judul</label>
                        <input type="text" name="title" id="broadcastTitle" placeholder="Judul broadcast..."
                               class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Isi Pesan</label>
                        <input type="text" name="body" id="broadcastBody" placeholder="Isi pesan..."
                               class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>
                </div>
                <div id="broadcastResult" class="hidden rounded-lg p-3 text-sm"></div>
                <button type="submit" id="broadcastSubmit"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-purple-600 hover:bg-purple-700 disabled:opacity-50 text-white text-sm font-semibold rounded-xl transition-colors">
                    <i data-lucide="radio" class="h-4 w-4"></i>
                    Broadcast FCM Topic
                </button>
            </form>
        </div>
    </div>
</div>

<script>
const csrfToken = '{{ csrf_token() }}';

// Firebase Config - injected from server
window.FIREBASE_CONFIG = {
    apiKey: '{{ config("services.firebase.api_key", env("VITE_FIREBASE_API_KEY", "")) }}',
    authDomain: '{{ config("services.firebase.auth_domain", env("VITE_FIREBASE_AUTH_DOMAIN", "")) }}',
    projectId: '{{ config("services.firebase.project_id", env("VITE_FIREBASE_PROJECT_ID", "")) }}',
    storageBucket: '{{ config("services.firebase.storage_bucket", env("VITE_FIREBASE_STORAGE_BUCKET", "")) }}',
    messagingSenderId: '{{ config("services.firebase.messaging_sender_id", env("VITE_FIREBASE_MESSAGING_SENDER_ID", "")) }}',
    appId: '{{ config("services.firebase.app_id", env("VITE_FIREBASE_APP_ID", "")) }}',
};
window.FIREBASE_VAPID_KEY = '{{ config("services.firebase.vapid_key", env("VITE_FIREBASE_VAPID_KEY", "")) }}';

// WhatsApp Test
document.getElementById('waTestForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('waSubmit');
    const result = document.getElementById('waResult');
    
    btn.disabled = true;
    btn.innerHTML = '<div class="h-4 w-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div> Mengirim...';
    
    try {
        const res = await fetch('{{ route("admin.notifications.test.whatsapp") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                phone: document.getElementById('waPhone').value,
                message: document.getElementById('waMessage').value,
            }),
        });
        
        const data = await res.json();
        
        result.classList.remove('hidden', 'bg-emerald-50', 'text-emerald-700', 'bg-red-50', 'text-red-600');
        if (data.success) {
            result.classList.add('bg-emerald-50', 'text-emerald-700');
        } else {
            result.classList.add('bg-red-50', 'text-red-600');
        }
        result.innerHTML = `<strong>${data.success ? 'Berhasil!' : 'Gagal!'}</strong> ${data.message}`;
    } catch (err) {
        result.classList.remove('hidden', 'bg-emerald-50', 'text-emerald-700');
        result.classList.add('bg-red-50', 'text-red-600');
        result.innerHTML = '<strong>Error!</strong> ' + err.message;
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i data-lucide="send" class="h-4 w-4"></i> Kirim Test WhatsApp';
        lucide.createIcons();
    }
});

// FCM Test
document.getElementById('fcmTestForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('fcmSubmit');
    const result = document.getElementById('fcmResult');
    
    btn.disabled = true;
    btn.innerHTML = '<div class="h-4 w-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div> Mengirim...';
    
    try {
        const res = await fetch('{{ route("admin.notifications.test.fcm") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                token: document.getElementById('fcmToken').value,
                title: document.getElementById('fcmTitle').value,
                body: document.getElementById('fcmBody').value,
            }),
        });
        
        const data = await res.json();
        
        result.classList.remove('hidden', 'bg-emerald-50', 'text-emerald-700', 'bg-red-50', 'text-red-600');
        if (data.success) {
            result.classList.add('bg-emerald-50', 'text-emerald-700');
        } else {
            result.classList.add('bg-red-50', 'text-red-600');
        }
        result.innerHTML = `<strong>${data.success ? 'Berhasil!' : 'Gagal!'}</strong> ${data.message}`;
    } catch (err) {
        result.classList.remove('hidden', 'bg-emerald-50', 'text-emerald-700');
        result.classList.add('bg-red-50', 'text-red-600');
        result.innerHTML = '<strong>Error!</strong> ' + err.message;
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i data-lucide="send" class="h-4 w-4"></i> Kirim Test Push';
        lucide.createIcons();
    }
});

// Broadcast Test
document.getElementById('broadcastForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('broadcastSubmit');
    const result = document.getElementById('broadcastResult');
    
    btn.disabled = true;
    btn.innerHTML = '<div class="h-4 w-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div> Broadcasting...';
    
    try {
        const res = await fetch('{{ route("admin.notifications.test.broadcast") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                title: document.getElementById('broadcastTitle').value,
                body: document.getElementById('broadcastBody').value,
            }),
        });
        
        const data = await res.json();
        
        result.classList.remove('hidden', 'bg-emerald-50', 'text-emerald-700', 'bg-red-50', 'text-red-600');
        if (data.success) {
            result.classList.add('bg-emerald-50', 'text-emerald-700');
        } else {
            result.classList.add('bg-red-50', 'text-red-600');
        }
        result.innerHTML = `<strong>${data.success ? 'Berhasil!' : 'Gagal!'}</strong> ${data.message}`;
    } catch (err) {
        result.classList.remove('hidden', 'bg-emerald-50', 'text-emerald-700');
        result.classList.add('bg-red-50', 'text-red-600');
        result.innerHTML = '<strong>Error!</strong> ' + err.message;
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i data-lucide="radio" class="h-4 w-4"></i> Broadcast FCM Topic';
        lucide.createIcons();
    }
});

// Get FCM Token
async function getFcmToken() {
    if (!('Notification' in window)) {
        alert('Browser tidak mendukung notifikasi!');
        return;
    }
    
    try {
        const permission = await Notification.requestPermission();
        
        if (permission === 'granted') {
            // Load Firebase SDK
            if (!window.firebase) {
                await loadFirebaseSDK();
            }
            
            if (window.FirebaseNotification) {
                await window.FirebaseNotification.init();
                const token = await window.FirebaseNotification.getToken();
                document.getElementById('fcmToken').value = token;
                alert('FCM Token berhasil didapatkan!');
            } else {
                // Fallback - generate placeholder
                document.getElementById('fcmToken').value = 'FCM_TOKEN_PLACEHOLDER_' + Date.now();
                alert('Firebase belum diinisialisasi. Gunakan token placeholder untuk testing.');
            }
        } else {
            alert('Notifikasi diblokir. Ubah di pengaturan browser.');
        }
    } catch (err) {
        console.error(err);
        alert('Gagal mendapatkan FCM token: ' + err.message);
    }
}

async function loadFirebaseSDK() {
    return new Promise((resolve) => {
        if (window.firebase) {
            resolve();
            return;
        }
        
        const script = document.createElement('script');
        script.src = '/js/firebase-init.js';
        script.onload = () => setTimeout(resolve, 500);
        document.head.appendChild(script);
    });
}
</script>
@endsection
