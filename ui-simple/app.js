// VAPID Public Key - generate_keys.php'den alacaksın
const VAPID_PUBLIC_KEY = 'BM3pCFEOKDxr7QRuIF7xYZYIQeT2WXtxC41nRGEujx5SjBr6UuQWC8e6HFAUGbo4cykb_4rEyNP6V2btO7z8_7k';

// API URL - Hostinger
const API_BASE = 'https://lightcoral-ram-205098.hostingersite.com';

let subscription = null;

// Log
function log(msg, type = 'info') {
    const logDiv = document.getElementById('log');
    const entry = document.createElement('div');
    entry.className = `log-entry ${type}`;
    entry.textContent = `[${new Date().toLocaleTimeString()}] ${msg}`;
    logDiv.insertBefore(entry, logDiv.firstChild);
}

// Base64 to Uint8Array (VAPID key için)
function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - base64String.length % 4) % 4);
    const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
    const rawData = window.atob(base64);
    const outputArray = new Uint8Array(rawData.length);
    for (let i = 0; i < rawData.length; ++i) {
        outputArray[i] = rawData.charCodeAt(i);
    }
    return outputArray;
}

// Service worker kaydet ve subscribe ol
async function subscribeUser() {
    try {
        const registration = await navigator.serviceWorker.register('sw.js');
        log('Service Worker kayıtlı', 'success');

        subscription = await registration.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: urlBase64ToUint8Array(VAPID_PUBLIC_KEY)
        });

        log('Push subscription alındı', 'success');

        // Sunucuya kaydet
        await fetch(`${API_BASE}/save_subscription.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ subscription: subscription.toJSON() })
        });

        log('Subscription sunucuya kaydedildi', 'success');

    } catch (err) {
        log('Hata: ' + err.message, 'error');
    }
}

// İzin kontrolü
async function checkPermission() {
    const status = document.getElementById('permission-status');

    if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
        status.textContent = 'Durum: ❌ Push desteklenmiyor';
        return;
    }

    if (Notification.permission === 'granted') {
        status.textContent = 'Durum: ✅ İzin verildi';
        await subscribeUser();
    } else if (Notification.permission === 'denied') {
        status.textContent = 'Durum: ❌ İzin reddedildi';
    } else {
        status.textContent = 'Durum: ⏳ İzin bekleniyor';
    }
}

// İzin iste
document.getElementById('enable-btn').addEventListener('click', async () => {
    const permission = await Notification.requestPermission();
    if (permission === 'granted') {
        log('İzin verildi', 'success');
        await subscribeUser();
    }
    checkPermission();
});

// Bildirim gönder
document.getElementById('send-btn').addEventListener('click', async () => {
    if (!subscription) {
        log('Önce bildirimleri etkinleştir!', 'error');
        return;
    }

    const title = document.getElementById('title').value;
    const body = document.getElementById('body').value;

    try {
        const res = await fetch(`${API_BASE}/send_push.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                subscription: subscription.toJSON(),
                title,
                body
            })
        });

        const result = await res.json();
        log(result.success ? 'Bildirim gönderildi!' : 'Hata: ' + JSON.stringify(result), result.success ? 'success' : 'error');
    } catch (err) {
        log('Gönderim hatası: ' + err.message, 'error');
    }
});

// Zamanlama - SUNUCU TARAFINDA (site kapalıyken bile çalışır!)
document.getElementById('schedule-btn').addEventListener('click', async () => {
    if (!subscription) {
        log('Önce bildirimleri etkinleştir!', 'error');
        return;
    }

    const time = document.getElementById('schedule-time').value;
    if (!time) {
        log('Saat seç!', 'error');
        return;
    }

    const title = document.getElementById('title').value || 'Zamanlanmış Bildirim';
    const body = document.getElementById('body').value || 'Belirlenen saat geldi!';

    try {
        const res = await fetch(`${API_BASE}/schedule.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                subscription: subscription.toJSON(),
                scheduled_time: time,
                title,
                body
            })
        });

        const result = await res.json();

        if (result.success) {
            log(`✅ ${time} için zamanlandı! Siteyi kapatsan bile bildirim gelecek.`, 'success');
        } else {
            log('Hata: ' + (result.error || result.message), 'error');
        }
    } catch (err) {
        log('Zamanlama hatası: ' + err.message, 'error');
    }
});

checkPermission();
