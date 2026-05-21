/**
 * Firebase Cloud Messaging - Client Initialization
 * Agenda eGov - Diskominfo Kabupaten Sambas
 * 
 * Config diinject dari Blade template via window.FIREBASE_CONFIG
 */

const FirebaseNotification = {
    messaging: null,
    token: null,
    isSupported: false,
    config: null,
    vapidKey: null,

    async init() {
        // Get config from window (injected by Blade template)
        if (!window.FIREBASE_CONFIG) {
            console.warn('Firebase config not found. Make sure to set window.FIREBASE_CONFIG');
            return false;
        }

        this.config = window.FIREBASE_CONFIG;
        this.vapidKey = window.FIREBASE_VAPID_KEY || '';

        // Check if Firebase is configured
        if (!this.config.apiKey || !this.config.projectId) {
            console.warn('Firebase not configured - missing apiKey or projectId');
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

            // Register service worker
            const registration = await navigator.serviceWorker.register('/firebase-messaging-sw.js');
            
            // Send config to service worker
            if (registration.active) {
                registration.active.postMessage({
                    type: 'FIREBASE_CONFIG',
                    config: this.config,
                });
            }

            // Wait for service worker to be ready
            await navigator.serviceWorker.ready;

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

        if (!this.vapidKey) {
            throw new Error('VAPID Key tidak dikonfigurasi');
        }

        try {
            const { getToken } = await import('https://www.gstatic.com/firebasejs/10.7.0/firebase-messaging.js');
            
            const registration = await navigator.serviceWorker.ready;
            
            this.token = await getToken(this.messaging, {
                vapidKey: this.vapidKey,
                serviceWorkerRegistration: registration,
            });

            if (this.token) {
                console.log('FCM Token obtained:', this.token.substring(0, 20) + '...');
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
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            
            const response = await fetch('/api/fcm/register', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || '',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    token: token,
                    device_name: this.getDeviceName(),
                }),
            });

            if (response.ok) {
                console.log('FCM token registered with backend');
            }
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

// Export to window for global access
window.FirebaseNotification = FirebaseNotification;
