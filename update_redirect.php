<?php
session_start();
require_once 'config.php';
$config = require 'config.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

// Log session details
if ($config['logging']) {
    file_put_contents('logs.txt', json_encode([
        'timestamp' => date('Y-m-d H:i:s'),
        'event' => 'update_redirect session check',
        'session_id' => session_id(),
        'csrf_token' => $_SESSION['csrf_token'] ?? 'none',
        'csrf_random' => $_SESSION['csrf_random'] ?? 'none'
    ]) . PHP_EOL, FILE_APPEND | LOCK_EX);
}

if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'CSRF token mismatch']);
    if ($config['logging']) {
        file_put_contents('logs.txt', json_encode([
            'timestamp' => date('Y-m-d H:i:s'),
            'error' => 'CSRF token mismatch in update_redirect',
            'received' => $_POST['csrf_token'] ?? 'none',
            'expected' => $_SESSION['csrf_token'] ?? 'none',
            'csrf_random' => $_SESSION['csrf_random'] ?? 'none',
            'session_id' => session_id()
        ]) . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    if ($config['logging']) {
        file_put_contents('logs.txt', json_encode([
            'timestamp' => date('Y-m-d H:i:s'),
            'error' => 'Invalid request method in update_redirect',
            'method' => $_SERVER['REQUEST_METHOD']
        ]) . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
    exit;
}

$redirectFile = 'redirect.txt';
$redirect = isset($_POST['redirect']) ? filter_var($_POST['redirect'], FILTER_VALIDATE_URL) : false;
if ($redirect === false) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid redirect URL']);
    if ($config['logging']) {
        file_put_contents('logs.txt', json_encode([
            'timestamp' => date('Y-m-d H:i:s'),
            'error' => 'Invalid redirect URL',
            'url' => $_POST['redirect'] ?? 'none'
        ]) . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
    exit;
}

if (file_put_contents($redirectFile, $redirect) === false) {
    if ($config['logging']) {
        file_put_contents('logs.txt', json_encode([
            'timestamp' => date('Y-m-d H:i:s'),
            'error' => 'Failed to write redirect URL',
            'url' => $redirect
        ]) . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to save redirect URL']);
    exit;
}

$thumbnailPath = 'thumbnail.png';
$newThumbnailPath = null;
if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['thumbnail'];
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $maxSize = 2 * 1024 * 1024; // 2MB

    if (!in_array($file['type'], $allowedTypes)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid file type']);
        if ($config['logging']) {
            file_put_contents('logs.txt', json_encode([
                'timestamp' => date('Y-m-d H:i:s'),
                'error' => 'Invalid thumbnail file type',
                'type' => $file['type']
            ]) . PHP_EOL, FILE_APPEND | LOCK_EX);
        }
        exit;
    }
    if ($file['size'] > $maxSize) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'File too large']);
        if ($config['logging']) {
            file_put_contents('logs.txt', json_encode([
                'timestamp' => date('Y-m-d H:i:s'),
                'error' => 'Thumbnail file too large',
                'size' => $file['size']
            ]) . PHP_EOL, FILE_APPEND | LOCK_EX);
        }
        exit;
    }

    list($width, $height) = getimagesize($file['tmp_name']);
    if ($width < 1200 || $height < 630) { // Optimized for social media
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Image dimensions too small (minimum 1200x630)']);
        if ($config['logging']) {
            file_put_contents('logs.txt', json_encode([
                'timestamp' => date('Y-m-d H:i:s'),
                'error' => 'Thumbnail dimensions too small',
                'width' => $width,
                'height' => $height
            ]) . PHP_EOL, FILE_APPEND | LOCK_EX);
        }
        exit;
    }

    if (move_uploaded_file($file['tmp_name'], $thumbnailPath)) {
        $newThumbnailPath = $thumbnailPath;
        if ($config['logging']) {
            file_put_contents('logs.txt', json_encode([
                'timestamp' => date('Y-m-d H:i:s'),
                'event' => 'Thumbnail uploaded',
                'file' => $file['name'],
                'size' => $file['size'],
                'type' => $file['type'],
                'dimensions' => [$width, $height]
            ]) . PHP_EOL, FILE_APPEND | LOCK_EX);
        }
    } else {
        if ($config['logging']) {
            file_put_contents('logs.txt', json_encode([
                'timestamp' => date('Y-m-d H:i:s'),
                'error' => 'Failed to upload thumbnail',
                'file' => $file['name']
            ]) . PHP_EOL, FILE_APPEND | LOCK_EX);
        }
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Failed to upload thumbnail']);
        exit;
    }
}

$response = ['success' => true, 'message' => 'Settings updated'];
if ($newThumbnailPath) {
    $response['thumbnail_path'] = $thumbnailPath . '?' . time();
}
if ($config['logging']) {
    file_put_contents('logs.txt', json_encode([
        'timestamp' => date('Y-m-d H:i:s'),
        'success' => 'Settings updated',
        'redirect' => $redirect,
        'thumbnail' => $newThumbnailPath ? 'updated' : 'not updated'
    ]) . PHP_EOL, FILE_APPEND | LOCK_EX);
}
echo json_encode($response);
?>