<?php
/**
 * CRON JOB - Her dakika çalıştır
 * Hostinger'da: Cron Jobs -> Her dakika: php /path/to/cron.php
 * 
 * Manuel test: php cron.php
 */

// Türkiye saat dilimi
date_default_timezone_set('Europe/Istanbul');

require_once __DIR__ . '/vendor/autoload.php';

use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

// VAPID Keys
$vapidKeys = [
    'publicKey' => 'BM3pCFEOKDxr7QRuIF7xYZYIQeT2WXtxC41nRGEujx5SjBr6UuQWC8e6HFAUGbo4cykb_4rEyNP6V2btO7z8_7k',
    'privateKey' => '7hVWznDX367mdqKApGpjtYgytW0h035w9HVwH864Apc'
];

$auth = [
    'VAPID' => [
        'subject' => 'mailto:test@example.com',
        'publicKey' => $vapidKeys['publicKey'],
        'privateKey' => $vapidKeys['privateKey']
    ]
];

$schedulesFile = __DIR__ . '/schedules.json';

if (!file_exists($schedulesFile)) {
    echo "No schedules\n";
    exit;
}

$schedules = json_decode(file_get_contents($schedulesFile), true) ?? [];
$currentTime = date('H:i');
$updated = false;

$webPush = new WebPush($auth);

foreach ($schedules as &$schedule) {
    // Henüz gönderilmemiş ve saati gelmiş bildirimleri gönder
    if (!$schedule['sent'] && $schedule['scheduled_time'] === $currentTime) {

        $payload = json_encode([
            'title' => $schedule['title'],
            'body' => $schedule['body'],
            'icon' => 'https://palegoldenrod-wolverine-140052.hostingersite.com/icon-192.png',
            'badge' => 'https://palegoldenrod-wolverine-140052.hostingersite.com/icon-192.png'
        ]);

        $sub = Subscription::create($schedule['subscription']);
        $webPush->queueNotification($sub, $payload);

        $schedule['sent'] = true;
        $schedule['sent_at'] = date('Y-m-d H:i:s');
        $updated = true;

        echo "Bildirim gönderildi: {$schedule['title']} - {$currentTime}\n";
    }
}

// Bildirimleri gönder
foreach ($webPush->flush() as $report) {
    $endpoint = $report->getRequest()->getUri()->__toString();
    if ($report->isSuccess()) {
        echo "✅ Başarılı: $endpoint\n";
    } else {
        echo "❌ Başarısız: {$report->getReason()}\n";
    }
}

if ($updated) {
    file_put_contents($schedulesFile, json_encode($schedules, JSON_PRETTY_PRINT));
}

// Eski (gönderilmiş) kayıtları temizle - 24 saatten eski
$schedules = array_filter($schedules, function ($s) {
    if (!$s['sent'])
        return true;
    $sentTime = strtotime($s['sent_at']);
    return (time() - $sentTime) < 86400; // 24 saat
});
file_put_contents($schedulesFile, json_encode(array_values($schedules), JSON_PRETTY_PRINT));
