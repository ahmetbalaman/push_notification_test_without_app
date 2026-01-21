# 📱❌ Push Notification Without Mobile App

> **Mobil uygulamaya gerek kalmadan, tarayıcı üzerinden push notification gönderimi**

![Web Push](https://img.shields.io/badge/Web%20Push-API-blue?style=for-the-badge)
![PHP](https://img.shields.io/badge/PHP-8.x-purple?style=for-the-badge&logo=php)
![PWA](https://img.shields.io/badge/PWA-Ready-green?style=for-the-badge)
![No Firebase](https://img.shields.io/badge/Firebase-Not%20Required-red?style=for-the-badge)

---

## 🤔 Neden Bu Proje?

2026'da hâlâ her şey için mobil uygulama geliştirmek zorunda mısınız? **Hayır!**

Modern web teknolojileri (PWA + Web Push API) sayesinde:
- ✅ App Store/Play Store onay süreci yok
- ✅ Kullanıcı uygulama indirmek zorunda değil
- ✅ Anlık güncelleme - deploy et, herkes görsün
- ✅ Tek codebase - tüm platformlar
- ✅ Push notification desteği (bu proje!)

---

## 🎯 Bu Proje Ne Yapıyor?

Tarayıcı üzerinden **gerçek push notification** göndermenizi sağlıyor. Telefon kilitli olsa bile bildirim gelir - tıpkı native uygulama gibi!

### Demo Akışı
```
[Kullanıcı siteyi açar] → [İzin verir] → [Siteyi kapatır] → [Bildirim gelir!] 📲
```

---

## 📁 Proje Yapısı

```
├── api-simple/             # Backend (PHP)
│   ├── send_push.php       # Bildirim gönderici
│   ├── save_subscription.php
│   ├── schedule.php        # Zamanlanmış bildirimler
│   └── generate_keys.php   # VAPID key üretici
│
└── ui-simple/              # Frontend (PWA)
    ├── index.html          # Ana sayfa
    ├── app.js              # Push subscription logic
    ├── sw.js               # Service Worker
    └── manifest.json       # PWA manifest
```

---

## 🚀 Hızlı Başlangıç

### 1. Bağımlılıkları Kur

```bash
cd api-simple
composer install
```

### 2. VAPID Key Oluştur

```bash
php generate_keys.php
```

Çıkan keyleri kopyala:
- **Public Key** → `ui-simple/app.js` içine
- **Private Key** → `api-simple/send_push.php` içine

### 3. Sunucuları Başlat

**API:**
```bash
cd api-simple
php -S localhost:8000
```

**UI (ayrı terminal):**
```bash
cd ui-simple
npx serve -p 3000
```

### 4. Test Et

1. `http://localhost:3000` aç
2. "Bildirimleri Etkinleştir" butonuna tıkla
3. İzin ver
4. "Gönder" butonuna tıkla
5. 🎉 Bildirim geldi!

---

## 🛠️ Teknolojiler

| Katman | Teknoloji |
|--------|-----------|
| Backend | PHP 8.x + minishlink/web-push |
| Frontend | Vanilla JS + Service Worker |
| Protokol | Web Push API + VAPID |
| Tip | Progressive Web App (PWA) |

---

## 📱 Platform Desteği

| Platform | Durum | Not |
|----------|-------|-----|
| Android Chrome | ✅ | Tam destek |
| Desktop Chrome/Edge/Firefox | ✅ | Tam destek |
| iOS Safari 16.4+ | ⚠️ | Sadece PWA olarak |
| iOS Safari < 16.4 | ❌ | Desteklenmiyor |

---

## 🔐 Güvenlik

- VAPID key'ler ile kimlik doğrulama
- HTTPS zorunlu (production'da)
- Subscription endpoint'ler benzersiz ve geçici

---

## 📄 Lisans

MIT License - İstediğiniz gibi kullanın!

---

## 🤝 Katkıda Bulunun

PR'lar açıktır! Özellikle:
- [ ] Database entegrasyonu (subscription saklama)
- [ ] Admin panel
- [ ] Toplu bildirim gönderimi
- [ ] Analytics

---

<details>
<summary>📝 LinkedIn Paylaşım Yazısı</summary>

---

### 🚀 Mobil Uygulamaya Gerçekten İhtiyacımız Var mı?

2026'da hâlâ her proje için App Store'a submit edip, 2 hafta onay beklememiz gerekiyor mu?

Basit bir push notification göndermek için bile mobil uygulama geliştirmek mantıklı mı?

**Cevabım: Artık hayır.**

Web Push API + PWA ile:
→ App store yok
→ İndirme yok  
→ Güncelleme anında
→ Tek codebase

Bu konsepti test etmek için küçük bir proje hazırladım: PHP backend + vanilla JS frontend ile **mobil uygulama olmadan push notification**.

Telefon kilitliyken bile bildirim geliyor. Native gibi.

🔗 GitHub: [repo linki]

#WebDevelopment #PWA #MobileDevelopment #PHP #JavaScript #PushNotification

---

</details>
