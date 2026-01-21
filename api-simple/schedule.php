<?php
// Türkiye saat dilimi
date_default_timezone_set('Europe/Istanbul');

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$input = json_decode(file_get_contents('php://input'), true);

$subscription = $input['subscription'] ?? null;
$scheduledTime = $input['scheduled_time'] ?? null; // Format: "HH:MM"
$title = $input['title'] ?? 'Zamanlanmış Bildirim';
$body = $input['body'] ?? 'Belirlenen saat geldi!';

if (!$subscription || !$scheduledTime) {
    echo json_encode(['error' => 'subscription ve scheduled_time gerekli']);
    exit;
}

// Zamanlanmış bildirimi kaydet
$file = __DIR__ . '/schedules.json';
$schedules = [];

if (file_exists($file)) {
    $schedules = json_decode(file_get_contents($file), true) ?? [];
}

$schedules[] = [
    'id' => uniqid(),
    'subscription' => $subscription,
    'scheduled_time' => $scheduledTime,
    'title' => $title,
    'body' => $body,
    'created_at' => date('Y-m-d H:i:s'),
    'sent' => false
];

file_put_contents($file, json_encode($schedules, JSON_PRETTY_PRINT));

echo json_encode([
    'success' => true,
    'message' => "Bildirim $scheduledTime için zamanlandı. Site kapalı olsa bile gelecek!"
]);
