<?php
header('Content-Type: text/plain; charset=utf-8');

echo "=== DEBUG ===\n\n";

// 1. schedules.json kontrol
$file = __DIR__ . '/schedules.json';
echo "1. Dosya yolu: $file\n";
echo "2. Dosya var mı: " . (file_exists($file) ? 'EVET' : 'HAYIR') . "\n";

if (file_exists($file)) {
    echo "3. Dosya içeriği:\n";
    echo file_get_contents($file);
} else {
    echo "3. Dosya yok, oluşturuyorum...\n";

    // Test verisi yaz
    $testData = [
        [
            'id' => 'test123',
            'scheduled_time' => date('H:i', strtotime('+1 minute')),
            'title' => 'Test Bildirimi',
            'body' => 'Bu bir test',
            'sent' => false
        ]
    ];

    $result = file_put_contents($file, json_encode($testData, JSON_PRETTY_PRINT));

    if ($result === false) {
        echo "❌ YAZMA HATASI! Klasör yazılabilir değil.\n";
        echo "Klasör izinleri: " . substr(sprintf('%o', fileperms(__DIR__)), -4) . "\n";
    } else {
        echo "✅ Test dosyası oluşturuldu ($result byte)\n";
        echo "İçerik:\n" . file_get_contents($file);
    }
}

echo "\n\n4. Subscriptions dosyası: ";
$subFile = __DIR__ . '/subscriptions.json';
if (file_exists($subFile)) {
    echo "VAR\n";
    $subs = json_decode(file_get_contents($subFile), true);
    echo "Kayıtlı subscription sayısı: " . count($subs) . "\n";
} else {
    echo "YOK - Henüz kimse bildirim izni vermemiş\n";
}

echo "\n5. Sunucu saati: " . date('Y-m-d H:i:s') . "\n";
echo "6. Timezone: " . date_default_timezone_get() . "\n";
