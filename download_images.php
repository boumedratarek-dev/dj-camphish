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
        'event' => 'download_images session check',
        'session_id' => session_id(),
        'csrf_token' => $_SESSION['csrf_token'] ?? 'none',
        'csrf_random' => $_SESSION['csrf_random'] ?? 'none'
    ]) . PHP_EOL, FILE_APPEND | LOCK_EX);
}

// Parse JSON input
$input = json_decode(file_get_contents('php://input'), true);
if (!isset($input['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $input['csrf_token'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'CSRF token mismatch']);
    if ($config['logging']) {
        file_put_contents('logs.txt', json_encode([
            'timestamp' => date('Y-m-d H:i:s'),
            'error' => 'CSRF token mismatch in download_images',
            'received' => $input['csrf_token'] ?? 'none',
            'expected' => $_SESSION['csrf_token'] ?? 'none',
            'csrf_random' => $_SESSION['csrf_random'] ?? 'none',
            'session_id' => session_id(),
            'raw_input' => file_get_contents('php://input')
        ]) . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SERVER['CONTENT_TYPE']) || $_SERVER['CONTENT_TYPE'] !== 'application/json') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
    if ($config['logging']) {
        file_put_contents('logs.txt', json_encode([
            'timestamp' => date('Y-m-d H:i:s'),
            'error' => 'Invalid request in download_images',
            'method' => $_SERVER['REQUEST_METHOD'],
            'content_type' => $_SERVER['CONTENT_TYPE'] ?? 'none'
        ]) . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
    exit;
}

if (!isset($input['images']) || !is_array($input['images']) || empty($input['images'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'No images specified']);
    if ($config['logging']) {
        file_put_contents('logs.txt', json_encode([
            'timestamp' => date('Y-m-d H:i:s'),
            'error' => 'No images specified in download_images',
            'input' => $input
        ]) . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
    exit;
}

$dir = 'captures/';
$zip = new ZipArchive();
$zipName = 'captured_images_' . time() . '.zip';
$tempFile = sys_get_temp_dir() . '/' . $zipName;

if ($zip->open($tempFile, ZipArchive::CREATE) !== true) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to create ZIP file']);
    if ($config['logging']) {
        file_put_contents('logs.txt', json_encode([
            'timestamp' => date('Y-m-d H:i:s'),
            'error' => 'Failed to create ZIP file in download_images',
            'temp_file' => $tempFile
        ]) . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
    exit;
}

$errors = [];
foreach ($input['images'] as $image) {
    $image = basename($image);
    $filePath = $dir . $image;

    if (file_exists($filePath) && is_file($filePath) && preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $image)) {
        $zip->addFile($filePath, $image);
    } else {
        $errors[] = "Invalid or non-existent file: $image";
    }
}

$zip->close();

if ($config['logging']) {
    file_put_contents('logs.txt', json_encode([
        'timestamp' => date('Y-m-d H:i:s'),
        'event' => 'Download images attempt',
        'images' => $input['images'],
        'errors' => $errors
    ]) . PHP_EOL, FILE_APPEND | LOCK_EX);
}

if (empty($errors) && file_exists($tempFile)) {
    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="captured_images.zip"');
    header('Content-Length: ' . filesize($tempFile));
    readfile($tempFile);
    unlink($tempFile);
    exit;
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to create ZIP', 'details' => $errors]);
}
?>