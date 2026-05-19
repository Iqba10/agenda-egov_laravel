// Firebase Messaging Service Worker
// Agenda eGov - Diskominfo Kabupaten Sambas

importScripts('https://www.gstatic.com/firebasejs/10.7.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.7.0/firebase-messaging-compat.js');

// Firebase config akan di-inject saat runtime via postMessage
let firebaseConfig = null;

self.addEventListener('message', (event) => {
    if (event.data && event.data.type === 'FIREBASE_CONFIG') {
        firebaseConfig = event.data.config;
        initializeFirebase();
    }
});

function initializeFirebase() {
    if (!firebaseConfig) {
        console.warn('Firebase config not provided');
        return;
    }

    firebase.initializeApp(firebaseConfig);
    const messaging = firebase.messaging();

    // Handle background messages
    messaging.onBackgroundMessage((payload) => {
        console.log('[SW] Background message received:', payload);

        const notificationTitle = payload.notification?.title || 'Pengingat Agenda';
        const notificationOptions = {
            body: payload.notification?.body || 'Ada agenda yang perlu Anda perhatikan',
            icon: '/favicon.ico',
            badge: '/favicon.ico',
            tag: payload.data?.agenda_id || 'agenda-notification',
            data: payload.data,
            actions: [
                { action: 'view', title: 'Lihat Detail' },
                { action: 'dismiss', title: 'Tutup' }
            ],
            requireInteraction: true,
            vibrate: [200, 100, 200],
        };

        self.registration.showNotification(notificationTitle, notificationOptions);
    });
}

// Handle notification click
self.addEventListener('notificationclick', (event) => {
    console.log('[SW] Notification click:', event.action);

    event.notification.close();

    if (event.action === 'dismiss') {
        return;
    }

    // Get URL from notification data
    const url = event.notification.data?.url || '/';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clientList) => {
            // Check if there's already a window open
            for (const client of clientList) {
                if (client.url === url && 'focus' in client) {
                    return client.focus();
                }
            }
            // Open new window
            if (clients.openWindow) {
                return clients.openWindow(url);
            }
        })
    );
});

// Handle push event (fallback)
self.addEventListener('push', (event) => {
    console.log('[SW] Push event received');

    if (event.data) {
        try {
            const payload = event.data.json();
            const title = payload.notification?.title || 'Pengingat Agenda';
            const options = {
                body: payload.notification?.body || '',
                icon: '/favicon.ico',
                badge: '/favicon.ico',
                data: payload.data,
            };

            event.waitUntil(self.registration.showNotification(title, options));
        } catch (e) {
            console.error('[SW] Error parsing push data:', e);
        }
    }
});
