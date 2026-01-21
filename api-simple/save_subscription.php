<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$input = json_decode(file_get_contents('php://input'), true);
$subscription = $input['subscription'] ?? null;

if (!$subscription) {
    echo json_encode(['error' => 'Subscription gerekli']);
    exit;
}

// Subscription'ı dosyaya kaydet
$file = __DIR__ . '/subscriptions.json';
$subscriptions = [];

if (file_exists($file)) {
    $subscriptions = json_decode(file_get_contents($file), true) ?? [];
}

// Aynı endpoint varsa güncelle
$found = false;
foreach ($subscriptions as &$sub) {
    if ($sub['endpoint'] === $subscription['endpoint']) {
        $sub = $subscription;
        $found = true;
        break;
    }
}

if (!$found) {
    $subscriptions[] = $subscription;
}

file_put_contents($file, json_encode($subscriptions, JSON_PRETTY_PRINT));

echo json_encode([
    'success' => true,
    'message' => 'Subscription kaydedildi'
]);
