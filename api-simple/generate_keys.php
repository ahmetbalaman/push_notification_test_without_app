<?php
/**
 * VAPID Key Generator
 * Bu dosyayı bir kere çalıştır ve keyleri kaydet
 * 
 * Kullanım: php generate_keys.php
 */

require_once 'vendor/autoload.php';

use Minishlink\WebPush\VAPID;

$keys = VAPID::createVapidKeys();

echo "VAPID Keys Generated:\n";
echo "=====================\n\n";
echo "Public Key:\n" . $keys['publicKey'] . "\n\n";
echo "Private Key:\n" . $keys['privateKey'] . "\n\n";
echo "Bu keyleri send_push.php ve ui/app-simple.js dosyalarına kopyala!\n";
