<?php
session_start();
require_once 'config.php';
// Assuming config.php returns the configuration array:
// $config = ['admin' => ['username' => '...', 'password_hash' => '...']];
$config = require 'config.php'; 

// Check if user is already logged in (using 'logged_in' session key)
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header("Location: gallery.php"); // Redirect to DJ Camphish gallery
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Extract admin details from the config array
    $admin_username = $config['admin']['username'] ?? '';
    $admin_password_hash = $config['admin']['password_hash'] ?? '';

    // Authentication check
    if ($username === $admin_username && password_verify($password, $admin_password_hash)) {
        $_SESSION['logged_in'] = true;
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Generate CSRF token for security
        header("Location: gallery.php"); // Redirect to DJ Camphish gallery
        exit;
    } else {
        $error = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DJ Camphish Login</title>
    <!-- Load Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style> 
        /* Deep Dark Background */
        body { 
            background-color: #0c121e;
        } 
        /* Subtle Fade-In Animation for the card */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fadeIn 0.6s ease-out forwards;
        }
    </style>
</head>
<!-- Main container for centering content -->
<body class="flex items-center justify-center min-h-screen p-4">
    <div class="w-full max-w-md animate-fade-in">
        
        <!-- Login Card -->
        <div class="bg-gray-800 shadow-2xl rounded-xl px-6 sm:px-8 pt-8 pb-8 mb-4 border border-gray-700">
            
            <!-- Title: Updated to DJ Camphish with Teal Accent -->
            <h1 class="text-4xl text-center font-extrabold mb-8 text-teal-400 tracking-wider">DJ CamPhish</h1>
            
            <!-- Error Message Display -->
            <?php if ($error): ?>
                <div class="bg-red-600 text-white p-3 rounded-lg mb-6 text-sm font-medium shadow-lg">
                    <span class="font-bold">Error:</span> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                
                <!-- Username Field -->
                <div class="mb-4">
                    <label class="block text-gray-300 text-sm font-semibold mb-2" for="username">Username</label>
                    <input 
                        class="shadow appearance-none rounded w-full py-3 px-4 text-gray-100 leading-tight focus:outline-none focus:shadow-outline bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-teal-500 transition-all duration-200" 
                        id="username" 
                        type="text" 
                        name="username" 
                        placeholder="Admin Username"
                        required
                    >
                </div>
                
                <!-- Password Field -->
                <div class="mb-6">
                    <label class="block text-gray-300 text-sm font-semibold mb-2" for="password">Password</label>
                    <input 
                        class="shadow appearance-none rounded w-full py-3 px-4 text-gray-100 leading-tight focus:outline-none focus:shadow-outline bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-teal-500 transition-all duration-200" 
                        id="password" 
                        type="password" 
                        name="password" 
                        placeholder="Password"
                        required
                    >
                </div>
                
                <!-- Login Button -->
                <div class="flex items-center justify-center mt-8">
                    <button 
                        class="bg-teal-600 hover:bg-teal-700 text-white font-bold py-3 px-6 rounded-lg focus:outline-none focus:shadow-outline w-full transition-all duration-300 hover:shadow-xl hover:scale-[1.01] transform" 
                        type="submit"
                    >
                        LOGIN
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Footer/Credit -->
        <p class="text-center text-gray-500 text-xs mt-6">
            &copy; 2025 DJ Camphish.
        </p>
    </div>
</body>
</html>
