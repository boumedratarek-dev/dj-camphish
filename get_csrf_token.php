<?php
session_start();
require_once 'config.php';
$config = require 'config.php';

// Generate or retrieve CSRF token
if (empty($_SESSION['csrf_token']) || empty($_SESSION['csrf_random'])) {
    $_SESSION['csrf_random'] = bin2hex(random_bytes(16));
    $_SESSION['csrf_token'] = hash_hmac('sha256', $_SESSION['csrf_random'], $config['security']['csrf_secret']);
    if ($config['logging']) {
        file_put_contents('logs.txt', json_encode([
            'timestamp' => date('Y-m-d H:i:s'),
            'event' => 'CSRF token generated via get_csrf_token',
            'token' => $_SESSION['csrf_token'],
            'random' => $_SESSION['csrf_random'],
            'session_id' => session_id()
        ]) . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
}

header('Content-Type: application/json');
echo json_encode(['csrf_token' => $_SESSION['csrf_token']]);
exit;
?>