<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once 'vendor/autoload.php';

use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

// VAPID Keys - Bunları bir kere oluştur ve sakla
// php generate_keys.php ile oluşturabilirsin
$vapidKeys = [
    'publicKey' => 'BM3pCFEOKDxr7QRuIF7xYZYIQeT2WXtxC41nRGEujx5SjBr6UuQWC8e6HFAUGbo4cykb_4rEyNP6V2btO7z8_7k',
    'privateKey' => '7hVWznDX367mdqKApGpjtYgytW0h035w9HVwH864Apc'
];

$input = json_decode(file_get_contents('php://input'), true);

$subscription = $input['subscription'] ?? null;
$title = $input['title'] ?? 'Bildirim';
$body = $input['body'] ?? 'Test mesajı';

if (!$subscription) {
    echo json_encode(['error' => 'Subscription gerekli']);
    exit;
}

$auth = [
    'VAPID' => [
        'subject' => 'mailto:your-email@example.com',
        'publicKey' => $vapidKeys['publicKey'],
        'privateKey' => $vapidKeys['privateKey']
    ]
];

$webPush = new WebPush($auth);

$payload = json_encode([
    'title' => $title,
    'body' => $body,
    'icon' => '/icon.png',
    'timestamp' => time()
]);

$sub = Subscription::create($subscription);

$webPush->queueNotification($sub, $payload);

$results = [];
foreach ($webPush->flush() as $report) {
    $results[] = [
        'success' => $report->isSuccess(),
        'reason' => $report->getReason()
    ];
}

echo json_encode([
    'success' => true,
    'results' => $results
]);
