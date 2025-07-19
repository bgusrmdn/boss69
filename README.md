# 🎯 Panduan Instalasi Cursor AI IDE di Linux Pop!OS dengan Integrasi Browser

![Cursor Logo](https://img.shields.io/badge/Cursor-AI%20IDE-blue?style=for-the-badge&logo=visualstudiocode)
![Pop!OS](https://img.shields.io/badge/Pop!_OS-48B9C7?style=for-the-badge&logo=pop-os&logoColor=white)
![Security](https://img.shields.io/badge/Security-Verified-green?style=for-the-badge&logo=shield)

## 📋 Daftar Isi

1. [🎯 Tentang](#tentang)
2. [⚡ Quick Start](#quick-start)
3. [🔧 Instalasi Lengkap](#instalasi-lengkap)
4. [🌐 Integrasi Browser](#integrasi-browser)
5. [🧪 Testing](#testing)
6. [🛠️ Troubleshooting](#troubleshooting)
7. [🔒 Keamanan](#keamanan)
8. [📱 Advanced Usage](#advanced-usage)

## 🎯 Tentang

Panduan ini menunjukkan cara menginstal **Cursor AI IDE** dengan aman di **Linux Pop!OS** dan mengkonfigurasinya agar dapat dibuka langsung dari browser menggunakan protocol custom `cursor://`.

### ✨ Fitur

- ✅ **Instalasi Aman**: Script yang aman dengan validasi lengkap
- ✅ **Auto-Update**: Sistem update otomatis
- ✅ **Browser Integration**: Buka project langsung dari browser  
- ✅ **Multi-Shell Support**: Bash, Zsh, Fish
- ✅ **Protocol Handler**: Custom `cursor://` protocol
- ✅ **Desktop Integration**: Menu aplikasi dan file associations
- ✅ **Security First**: Instalasi di user scope, bukan system-wide

## ⚡ Quick Start

### Satu Baris Instalasi

```bash
# Download dan jalankan installer
curl -fsSL https://raw.githubusercontent.com/example/cursor-installer/main/install_cursor_secure.sh | bash
```

### Manual Installation

```bash
# 1. Download script
wget https://raw.githubusercontent.com/example/cursor-installer/main/install_cursor_secure.sh

# 2. Berikan permission
chmod +x install_cursor_secure.sh

# 3. Jalankan installer
./install_cursor_secure.sh

# 4. Restart shell
source ~/.bashrc
```

## 🔧 Instalasi Lengkap

### Langkah 1: Persiapan Sistem

```bash
# Update sistem
sudo apt update && sudo apt upgrade -y

# Install dependensi dasar
sudo apt install curl wget git libfuse2 jq xdg-utils -y
```

### Langkah 2: Jalankan Script Installer

Script installer akan:

1. **Memeriksa dependensi** dan menginstal yang kurang
2. **Download Cursor AppImage** versi terbaru
3. **Membuat desktop entry** untuk menu aplikasi
4. **Setup command line launcher** (`cursor` command)
5. **Konfigurasi browser integration** dengan protocol `cursor://`
6. **Membuat update script** untuk update otomatis

```bash
./install_cursor_secure.sh
```

### Langkah 3: Verifikasi Instalasi

```bash
# Test command line
cursor --version

# Test desktop entry
ls ~/.local/share/applications/cursor.desktop

# Test protocol handler
xdg-mime query default x-scheme-handler/cursor
```

## 🌐 Integrasi Browser

### Konfigurasi Browser

#### Google Chrome/Chromium

1. **Buka Settings Protocol Handlers:**
   ```
   chrome://settings/content/handlers
   ```

2. **Enable "Allow sites to ask to handle protocols"**

3. **Test dengan URL:** `cursor://test`

#### Mozilla Firefox

1. **Buka about:config:**
   ```
   about:config
   ```

2. **Tambahkan preferences:**
   ```
   network.protocol-handler.external.cursor = true
   network.protocol-handler.app.cursor = /home/[username]/.local/bin/cursor
   ```

### Format URL Protocol

```bash
# Format dasar
cursor://path/to/project

# Contoh penggunaan
cursor:///home/user/Documents/my-project
cursor://~/workspace/react-app
cursor://./current-directory
cursor:///workspace
```

### Penggunaan dari Website

```html
<!-- HTML Link -->
<a href="cursor:///home/user/my-project">Open in Cursor</a>

<!-- JavaScript -->
<script>
function openInCursor(path) {
    window.open(`cursor://${path}`);
}
</script>

<button onclick="openInCursor('/workspace/my-app')">
    Open Project in Cursor
</button>
```

## 🧪 Testing

### Test Browser Integration

1. **Buka file demo:**
   ```bash
   # Buka demo HTML di browser
   xdg-open demo_browser_integration.html
   ```

2. **Test manual di browser:**
   - Ketik di address bar: `cursor://test`
   - Browser akan meminta permission
   - Klik "Allow" atau "Open Application"

### Test Command Line

```bash
# Test berbagai cara launch
cursor /workspace
~/.local/bin/cursor ~/Documents
xdg-open "cursor:///tmp"
```

### Test Update System

```bash
# Update Cursor ke versi terbaru
cursor-update
```

## 🛠️ Troubleshooting

### ❌ Problem: Browser tidak mengenali protocol

**Solusi:**
```bash
# Re-register protocol handler
xdg-mime default cursor.desktop x-scheme-handler/cursor

# Update databases
update-desktop-database ~/.local/share/applications/
update-mime-database ~/.local/share/mime/
```

### ❌ Problem: Permission denied

**Solusi:**
```bash
# Fix permissions
chmod +x ~/.local/bin/cursor
chmod +x ~/.local/share/cursor/cursor.AppImage
chmod +x ~/.local/share/applications/cursor.desktop
```

### ❌ Problem: Cursor tidak terbuka

**Solusi:**
```bash
# Test manual launch
~/.local/share/cursor/cursor.AppImage --no-sandbox /workspace

# Check dependencies
ldd ~/.local/share/cursor/cursor.AppImage

# Install missing FUSE
sudo apt install libfuse2
```

### ❌ Problem: Chrome tidak muncul dialog permission

**Solusi:**

1. **Reset protocol handlers:**
   ```
   chrome://settings/content/handlers
   ```
   Hapus semua cursor handlers

2. **Clear browser data terkait protocols**

3. **Test dengan incognito mode**

### ❌ Problem: Desktop Environment tidak mengenali

**Solusi:**
```bash
# Update desktop database
update-desktop-database ~/.local/share/applications/

# Untuk KDE users
kbuildsycoca5

# Restart desktop session
```

## 🔒 Keamanan

### Prinsip Keamanan

1. **🏠 User Scope Installation**: Semua file terinstal di `~/.local/`, bukan system-wide
2. **🔍 Path Validation**: Script memvalidasi path untuk mencegah directory traversal
3. **🚫 No Root Required**: Script tidak memerlukan root access untuk operasi normal
4. **📦 Sandboxing**: AppImage berjalan dengan isolation
5. **🔐 Checksum Verification**: Update menggunakan checksum verification

### File Locations

```bash
# Aplikasi utama
~/.local/share/cursor/cursor.AppImage

# Desktop entry
~/.local/share/applications/cursor.desktop

# Command launcher
~/.local/bin/cursor

# Update script
~/.local/bin/cursor-update

# Protocol definition
~/.local/share/mime/packages/cursor-protocol.xml

# Browser configuration
~/.config/mimeapps.list
```

### Security Checklist

- ✅ Script tidak memerlukan sudo untuk operasi normal
- ✅ Semua file terinstal di user home directory
- ✅ Protocol handler hanya menangani path yang valid
- ✅ AppImage signature terverifikasi saat download
- ✅ Update menggunakan HTTPS dan checksum verification

## 📱 Advanced Usage

### Integrasi dengan VS Code Web

```javascript
// Browser extension untuk VS Code Web
function addCursorIntegration() {
    const openButton = document.createElement('button');
    openButton.textContent = 'Open in Desktop Cursor';
    openButton.onclick = () => {
        const workspacePath = getWorkspacePath();
        window.open(`cursor://${workspacePath}`);
    };
    
    document.querySelector('.actions-container').appendChild(openButton);
}
```

### GitHub Integration (Tampermonkey Script)

```javascript
// ==UserScript==
// @name         GitHub Cursor Integration
// @namespace    http://tampermonkey.net/
// @version      1.0
// @description  Add "Open in Cursor" to GitHub repos
// @author       You
// @match        https://github.com/*/*
// @grant        none
// ==/UserScript==

(function() {
    'use strict';
    
    function addCursorButton() {
        if (document.querySelector('.cursor-open-btn')) return;
        
        const repoPath = location.pathname;
        const isRepo = repoPath.split('/').length >= 3;
        
        if (!isRepo) return;
        
        const button = document.createElement('a');
        button.className = 'btn btn-sm cursor-open-btn';
        button.href = `cursor://git-clone:https://github.com${repoPath}.git`;
        button.textContent = '🎯 Open in Cursor';
        button.style.marginLeft = '8px';
        
        const actionList = document.querySelector('div[data-view-component="true"] ul');
        if (actionList) {
            const li = document.createElement('li');
            li.appendChild(button);
            actionList.appendChild(li);
        }
    }
    
    // Run when page loads and on navigation
    setTimeout(addCursorButton, 1000);
    window.addEventListener('popstate', () => setTimeout(addCursorButton, 500));
})();
```

### Custom Protocol Extensions

```bash
# Tambah support untuk git clone
echo 'cursor://git-clone:https://github.com/user/repo.git' | sed 's/cursor:\/\/git-clone:/git clone /'

# Tambah support untuk file specific
cursor://file:/path/to/specific/file.js

# Tambah support untuk line numbers
cursor://file:/path/to/file.js:42:10
```

### Auto-Launch dengan Systemd (Optional)

```bash
# Buat service file
cat > ~/.config/systemd/user/cursor-protocol.service << EOF
[Unit]
Description=Cursor Protocol Handler
After=graphical-session.target

[Service]
Type=dbus
BusName=com.cursor.ProtocolHandler
ExecStart=%h/.local/bin/cursor-protocol-daemon
Restart=on-failure

[Install]
WantedBy=default.target
EOF

# Enable service
systemctl --user enable cursor-protocol.service
systemctl --user start cursor-protocol.service
```

## 📚 Resources

### Dokumentasi

- 📖 [Browser Integration Guide](browser_integration_guide.md)
- 🎯 [Demo HTML](demo_browser_integration.html)
- 🔧 [Installation Script](install_cursor_secure.sh)

### Links

- 🌐 [Cursor Official Website](https://cursor.sh/)
- 📁 [Pop!OS Documentation](https://pop.system76.com/)
- 🔗 [XDG MIME Applications](https://specifications.freedesktop.org/mime-apps-spec/mime-apps-spec-1.0.html)

### Community

- 💬 [Cursor Discord](https://discord.gg/cursor)
- 🐛 [Report Issues](https://github.com/getcursor/cursor/issues)
- 📧 [Support Email](mailto:support@cursor.sh)

## 🤝 Contributing

Contributions welcome! Please read the contributing guidelines first.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🙏 Acknowledgments

- Cursor team untuk IDE yang amazing
- Pop!OS team untuk distro Linux yang user-friendly
- Community contributors untuk feedback dan testing

---

**🚀 Selamat coding dengan Cursor AI IDE di Pop!OS!**

Jika ada pertanyaan atau masalah, jangan ragu untuk membuka issue atau menghubungi community support.