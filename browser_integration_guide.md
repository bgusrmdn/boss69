# 🌐 Panduan Integrasi Browser dengan Cursor di Linux Pop!OS

## 📋 Daftar Isi
1. [Instalasi Script](#instalasi-script)
2. [Konfigurasi Chrome/Browser](#konfigurasi-chrome-browser)
3. [Cara Menggunakan Protocol cursor://](#cara-menggunakan-protocol-cursor)
4. [Testing Integration](#testing-integration)
5. [Troubleshooting](#troubleshooting)

## 🚀 Instalasi Script

### Langkah 1: Download dan Jalankan Script
```bash
# Download script installer
wget https://raw.githubusercontent.com/your-repo/cursor-installer/main/install_cursor_secure.sh

# Berikan permission execute
chmod +x install_cursor_secure.sh

# Jalankan script
./install_cursor_secure.sh
```

### Langkah 2: Restart Shell
```bash
# Restart terminal atau reload shell config
source ~/.bashrc  # untuk bash
source ~/.zshrc   # untuk zsh
```

## 🔧 Konfigurasi Chrome/Browser

### Untuk Google Chrome/Chromium

1. **Buka Chrome Settings**
   ```
   chrome://settings/content/handlers
   ```

2. **Atau melalui Menu:**
   - Chrome → Settings → Privacy and security → Site Settings → Additional permissions → Protocol handlers

3. **Allow sites to ask to handle protocols** harus diaktifkan

### Untuk Firefox

1. **Buka about:config**
   ```
   about:config
   ```

2. **Tambahkan preference baru:**
   ```
   network.protocol-handler.external.cursor = true
   network.protocol-handler.app.cursor = /home/[username]/.local/bin/cursor
   ```

3. **Atau melalui Settings:**
   - Firefox → Settings → General → Applications
   - Scroll down dan cari "cursor" protocol

## 🔗 Cara Menggunakan Protocol cursor://

### Format URL

```bash
# Format dasar
cursor://path/to/your/project

# Contoh penggunaan
cursor:///home/user/my-project
cursor://~/Documents/code-project
cursor://./relative-path
```

### Dari Website/Aplikasi Web

```html
<!-- HTML Link -->
<a href="cursor:///home/user/my-project">Open in Cursor</a>

<!-- JavaScript -->
<button onclick="window.open('cursor:///home/user/my-project')">
  Open Project in Cursor
</button>
```

### Dari Terminal

```bash
# Test protocol handler
xdg-open "cursor:///home/user/my-project"

# Buka browser dengan cursor URL
google-chrome "cursor:///home/user/my-project"
firefox "cursor:///home/user/my-project"
```

## 🧪 Testing Integration

### Test 1: Manual URL Test
1. Buka browser
2. Ketik di address bar: `cursor://test`
3. Browser akan bertanya permission untuk membuka aplikasi eksternal
4. Klik "Allow" atau "Open Application"

### Test 2: HTML Test File
Buat file test.html:

```html
<!DOCTYPE html>
<html>
<head>
    <title>Cursor Integration Test</title>
</head>
<body>
    <h1>Test Cursor Integration</h1>
    
    <h2>Click links below to test:</h2>
    
    <p><a href="cursor:///home/user/Documents">Open Documents in Cursor</a></p>
    <p><a href="cursor://~/Desktop">Open Desktop in Cursor</a></p>
    
    <button onclick="openInCursor()">Open Current Directory</button>
    
    <script>
    function openInCursor() {
        const currentPath = prompt('Enter path to open:', '/home/user/');
        if (currentPath) {
            window.open('cursor://' + currentPath);
        }
    }
    </script>
</body>
</html>
```

### Test 3: Command Line Test
```bash
# Test dengan berbagai cara
xdg-open "cursor:///home/user/Documents"
gio open "cursor:///home/user/Documents"

# Check apakah protocol terdaftar
xdg-mime query default x-scheme-handler/cursor
```

## 🛠️ Troubleshooting

### Problem 1: Browser tidak mengenali protocol cursor://

**Solusi:**
```bash
# Re-register protocol
xdg-mime default cursor.desktop x-scheme-handler/cursor

# Update database
update-desktop-database ~/.local/share/applications/
update-mime-database ~/.local/share/mime/
```

### Problem 2: Permission denied saat buka aplikasi

**Solusi:**
```bash
# Periksa permission file
ls -la ~/.local/share/applications/cursor.desktop
ls -la ~/.local/bin/cursor

# Fix permission jika perlu
chmod +x ~/.local/share/applications/cursor.desktop
chmod +x ~/.local/bin/cursor
chmod +x ~/.local/share/cursor/cursor.AppImage
```

### Problem 3: Chrome tidak muncul dialog "Open Application"

**Solusi:**
1. **Reset Chrome protocol handlers:**
   ```
   chrome://settings/content/handlers
   ```
   Hapus semua handler untuk cursor protocol dan coba lagi

2. **Check Chrome flags:**
   ```
   chrome://flags/#enable-experimental-web-platform-features
   ```
   Pastikan tidak disabled

3. **Manual registration di Chrome:**
   - Buka website yang berisi link cursor://
   - Chrome akan otomatis bertanya untuk register handler

### Problem 4: Firefox tidak bisa handle protocol

**Solusi:**
```bash
# Tambah ke Firefox preferences
echo 'user_pref("network.protocol-handler.external.cursor", true);' >> ~/.mozilla/firefox/*/prefs.js
echo 'user_pref("network.protocol-handler.app.cursor", "/home/user/.local/bin/cursor");' >> ~/.mozilla/firefox/*/prefs.js
```

### Problem 5: GNOME/KDE tidak mengenali desktop file

**Solusi:**
```bash
# Update desktop database
update-desktop-database ~/.local/share/applications/

# Untuk KDE
kbuildsycoca5

# Restart desktop environment
# Atau logout/login
```

## 📱 Integrasi dengan Aplikasi Web

### Contoh untuk VS Code Web / GitHub Codespaces

```javascript
// Tambahkan button "Open in Desktop Cursor"
function addCursorButton() {
    const button = document.createElement('button');
    button.textContent = 'Open in Cursor';
    button.onclick = () => {
        const repoPath = getRepositoryPath(); // implement sesuai kebutuhan
        window.open(`cursor://${repoPath}`);
    };
    
    // Tambahkan ke UI
    document.querySelector('.action-bar').appendChild(button);
}
```

### Contoh untuk GitHub

```javascript
// Userscript untuk GitHub
// ==UserScript==
// @name         GitHub Cursor Integration
// @namespace    http://tampermonkey.net/
// @version      0.1
// @description  Add "Open in Cursor" button to GitHub repos
// @author       You
// @match        https://github.com/*/*
// @grant        none
// ==/UserScript==

(function() {
    'use strict';
    
    function addCursorButton() {
        const repoName = location.pathname;
        const button = document.createElement('a');
        button.href = `cursor://git-clone:https://github.com${repoName}.git`;
        button.textContent = '🎯 Open in Cursor';
        button.className = 'btn btn-sm';
        
        const buttonContainer = document.querySelector('.repository-content .file-navigation');
        if (buttonContainer) {
            buttonContainer.appendChild(button);
        }
    }
    
    // Run when page loads
    setTimeout(addCursorButton, 1000);
})();
```

## 🔒 Catatan Keamanan

1. **Validasi Path:** Cursor script menggunakan validasi path untuk mencegah akses ke file sistem yang sensitif

2. **Sandbox Mode:** AppImage dijalankan dengan `--no-sandbox` flag untuk kompatibilitas

3. **User Scope:** Semua instalasi dilakukan di user scope (`~/.local/`) bukan system-wide

4. **Protocol Isolation:** Protocol handler hanya menangani path yang valid

## 🆘 Support & Help

Jika mengalami masalah:

1. **Check logs:**
   ```bash
   journalctl --user -f
   ```

2. **Check desktop file:**
   ```bash
   desktop-file-validate ~/.local/share/applications/cursor.desktop
   ```

3. **Manual test:**
   ```bash
   ~/.local/share/cursor/cursor.AppImage --no-sandbox /path/to/test
   ```

4. **Reset installation:**
   ```bash
   # Hapus semua file terkait
   rm -rf ~/.local/share/cursor
   rm ~/.local/share/applications/cursor.desktop
   rm ~/.local/bin/cursor
   rm ~/.local/share/mime/packages/cursor-protocol.xml
   
   # Jalankan ulang installer
   ./install_cursor_secure.sh
   ```

---

**📝 Tips:** Setelah instalasi, coba buka website yang memiliki repository code dan gunakan URL `cursor://path/to/your/project` untuk test integrasi!