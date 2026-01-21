// Service Worker - Push bildirimleri için

self.addEventListener('push', (event) => {
    console.log('Push event received:', event);

    let data = {
        title: 'Bildirim',
        body: 'Yeni mesaj',
        icon: '/icon-192.png',
        badge: '/icon-192.png'
    };

    if (event.data) {
        try {
            data = { ...data, ...event.data.json() };
        } catch (e) {
            data.body = event.data.text();
        }
    }

    const options = {
        body: data.body,
        icon: data.icon,
        badge: data.badge,
        vibrate: [200, 100, 200],
        tag: 'push-notification',
        renotify: true,
        requireInteraction: false,
        data: {
            url: self.location.origin,
            dateOfArrival: Date.now()
        }
    };

    event.waitUntil(
        self.registration.showNotification(data.title, options)
    );
});

self.addEventListener('notificationclick', (event) => {
    console.log('Notification clicked:', event);
    event.notification.close();

    if (event.action === 'open' || !event.action) {
        event.waitUntil(
            clients.openWindow('/')
        );
    }
});
