<?php
// dj-camphish Configuration - October 2025 Update
return [
    'admin' => [
        'username' => 'admin',
        'password_hash' => password_hash('12345', PASSWORD_DEFAULT), // Change this!
    ],
    'capture' => [
        'max_captures' => 2,
        'interval_ms' => 1500,
        'canvas_width' => 640,
        'canvas_height' => 480,
    ],
    'security' => [
        'csrf_secret' => '7f9a2b8c4d6e3f1a9b0c2d4e5f67890123456789abcdef0123456789abcdef12', // Replace with your secure random string
    ],
    'logging' => true, // Enable/disable metadata logging
];
?>