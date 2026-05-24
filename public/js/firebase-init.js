/**
 * Firebase Cloud Messaging - Client Initialization
 * Agenda eGov - Diskominfo Kabupaten Sambas
 */

const FirebaseNotification = {
    messaging: null,
    token: null,
    isSupported: false,

    // Firebase config - read from meta tags (server-rendered) or Vite env
    getConfig() {
        // Try meta tags first (works in production)
        const getMeta = (name) => document.querySelector(`meta[name="${name}"]`)?.content || '';
        
        return {
            apiKey: getMeta('firebase-api-key') || (typeof import.meta !== 'undefined' ? import.meta.env?.VITE_FIREBASE_API_KEY : ''),
            authDomain: getMeta('firebase-auth-domain') || (typeof import.meta !== 'undefined' ? import.meta.env?.VITE_FIREBASE_AUTH_DOMAIN : ''),
            projectId: getMeta('firebase-project-id') || (typeof import.meta !== 'undefined' ? import.meta.env?.VITE_FIREBASE_PROJECT_ID : ''),
            storageBucket: getMeta('firebase-storage-bucket') || (typeof import.meta !== 'undefined' ? import.meta.env?.VITE_FIREBASE_STORAGE_BUCKET : ''),
            messagingSenderId: getMeta('firebase-messaging-sender-id') || (typeof import.meta !== 'undefined' ? import.meta.env?.VITE_FIREBASE_MESSAGING_SENDER_ID : ''),
            appId: getMeta('firebase-app-id') || (typeof import.meta !== 'undefined' ? import.meta.env?.VITE_FIREBASE_APP_ID : ''),
        };
    },

    getVapidKey() {
        const meta = document.querySelector('meta[name="firebase-vapid-key"]');
        return meta?.content || (typeof import.meta !== 'undefined' ? import.meta.env?.VITE_FIREBASE_VAPID_KEY : '');
    },

    config: null,
    vapidKey: null,

    async init() {
        // Load config from meta tags
        this.config = this.getConfig();
        this.vapidKey = this.getVapidKey();

        console.log('Firebase config loaded:', {
            hasApiKey: !!this.config.apiKey,
            hasProjectId: !!this.config.projectId,
            hasVapidKey: !!this.vapidKey,
        });

        // Check if Firebase is configured
        if (!this.config.apiKey || !this.config.projectId) {
            console.warn('Firebase not configured - missing apiKey or projectId');
            return false;
        }

        if (!this.vapidKey) {
            console.warn('Firebase not configured - missing VAPID key');
            return false;
        }

        // Check browser support
        if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
            console.warn('Push notifications not supported');
            return false;
        }

        try {
            // Dynamically import Firebase
            const { initializeApp } = await import('https://www.gstatic.com/firebasejs/10.7.0/firebase-app.js');
            const { getMessaging, getToken, onMessage } = await import('https://www.gstatic.com/firebasejs/10.7.0/firebase-messaging.js');

            const app = initializeApp(this.config);
            this.messaging = getMessaging(app);
            this.isSupported = true;

            // Register service worker and pass config
            const registration = await navigator.serviceWorker.register('/firebase-messaging-sw.js');
            
            // Send config to service worker
            if (registration.active) {
                registration.active.postMessage({
                    type: 'FIREBASE_CONFIG',
                    config: this.config,
                });
            }

            // Listen for messages when app is in foreground
            onMessage(this.messaging, (payload) => {
                console.log('Foreground message:', payload);
                this.showForegroundNotification(payload);
            });

            console.log('Firebase initialized successfully');
            return true;
        } catch (error) {
            console.error('Firebase init error:', error);
            return false;
        }
    },

    async requestPermission() {
        if (!this.isSupported) {
            throw new Error('Notifikasi tidak didukung di browser ini');
        }

        const permission = await Notification.requestPermission();
        
        if (permission !== 'granted') {
            throw new Error('Izin notifikasi ditolak');
        }

        return permission;
    },

    async getToken() {
        if (!this.isSupported || !this.messaging) {
            throw new Error('Firebase belum diinisialisasi');
        }

        try {
            const { getToken } = await import('https://www.gstatic.com/firebasejs/10.7.0/firebase-messaging.js');
            
            const registration = await navigator.serviceWorker.ready;
            
            this.token = await getToken(this.messaging, {
                vapidKey: this.vapidKey,
                serviceWorkerRegistration: registration,
            });

            if (this.token) {
                // Register token with backend
                await this.registerTokenWithBackend(this.token);
            }

            return this.token;
        } catch (error) {
            console.error('Error getting FCM token:', error);
            throw error;
        }
    },

    async registerTokenWithBackend(token) {
        try {
            const response = await fetch('/api/fcm/register', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    token: token,
                    device_name: this.getDeviceName(),
                }),
            });

            return response.ok;
        } catch (error) {
            console.error('Error registering FCM token:', error);
            return false;
        }
    },

    showForegroundNotification(payload) {
        if (Notification.permission !== 'granted') return;

        const title = payload.notification?.title || 'Pengingat Agenda';
        const options = {
            body: payload.notification?.body || '',
            icon: '/favicon.ico',
            tag: payload.data?.agenda_id || 'agenda',
            data: payload.data,
        };

        const notification = new Notification(title, options);
        
        notification.onclick = () => {
            const url = payload.data?.url || '/';
            window.open(url, '_blank');
            notification.close();
        };
    },

    getDeviceName() {
        const ua = navigator.userAgent;
        if (ua.includes('Chrome')) return 'Chrome';
        if (ua.includes('Firefox')) return 'Firefox';
        if (ua.includes('Safari')) return 'Safari';
        if (ua.includes('Edge')) return 'Edge';
        return 'Web Browser';
    },

    getPermissionState() {
        if (!('Notification' in window)) return 'unsupported';
        return Notification.permission;
    },

    hasToken() {
        return !!this.token;
    },
};

// Export for use in Blade templates
window.FirebaseNotification = FirebaseNotification;

export default FirebaseNotification;
