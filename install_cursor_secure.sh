#!/bin/bash

# Cursor AI IDE Secure Installer untuk Linux Pop!OS
# Script ini menginstal Cursor dengan aman dan mengkonfigurasi integrasi browser

set -e  # Exit jika ada error

# Warna untuk output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}=== Cursor AI IDE Secure Installer untuk Pop!OS ===${NC}"
echo -e "${YELLOW}Script ini akan menginstal Cursor dengan aman dan mengkonfigurasi integrasi browser${NC}"
echo

# Fungsi untuk mencetak pesan
print_status() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Cek apakah script dijalankan sebagai root
if [[ $EUID -eq 0 ]]; then
   print_error "Jangan jalankan script ini sebagai root untuk keamanan!"
   exit 1
fi

# Deteksi shell dan konfigurasi file
SHELL_NAME=$(basename "$SHELL")
case "$SHELL_NAME" in
    bash)
        RC_FILE="$HOME/.bashrc"
        ;;
    zsh)
        RC_FILE="$HOME/.zshrc"
        ;;
    fish)
        RC_FILE="$HOME/.config/fish/config.fish"
        ;;
    *)
        print_warning "Shell $SHELL_NAME mungkin tidak didukung sepenuhnya"
        RC_FILE="$HOME/.bashrc"
        ;;
esac

print_status "Menggunakan shell: $SHELL_NAME"
print_status "File konfigurasi: $RC_FILE"

# Fungsi instalasi dependensi
install_dependencies() {
    print_status "Memeriksa dan menginstal dependensi..."
    
    # Update package list
    if ! sudo apt update; then
        print_error "Gagal mengupdate package list"
        exit 1
    fi
    
    # Instal dependensi yang diperlukan
    local packages=("curl" "libfuse2" "jq" "xdg-utils")
    for package in "${packages[@]}"; do
        if ! dpkg -s "$package" >/dev/null 2>&1; then
            print_status "Menginstal $package..."
            if ! sudo apt install -y "$package"; then
                print_error "Gagal menginstal $package"
                exit 1
            fi
        else
            print_status "$package sudah terinstal"
        fi
    done
}

# Fungsi untuk mendapatkan URL download terbaru
get_latest_cursor_url() {
    print_status "Mendapatkan informasi versi terbaru..."
    
    # Coba API resmi Cursor
    local api_response
    api_response=$(curl -s "https://www.cursor.com/api/download?platform=linux-x64&releaseTrack=stable" || echo "")
    
    if [[ -n "$api_response" ]]; then
        local download_url
        download_url=$(echo "$api_response" | jq -r '.downloadUrl' 2>/dev/null || echo "")
        if [[ -n "$download_url" && "$download_url" != "null" ]]; then
            echo "$download_url"
            return 0
        fi
    fi
    
    # Fallback ke GitHub repository
    print_warning "API utama tidak tersedia, menggunakan fallback..."
    local github_response
    github_response=$(curl -s "https://api.github.com/repos/oslook/cursor-ai-downloads/contents/README.md" || echo "")
    
    if [[ -n "$github_response" ]]; then
        local fallback_url
        fallback_url=$(echo "$github_response" | grep -o 'https://downloads.cursor.com/production/[^"]*linux/x64/[^"]*AppImage' | head -1 || echo "")
        if [[ -n "$fallback_url" ]]; then
            echo "$fallback_url"
            return 0
        fi
    fi
    
    print_error "Tidak dapat mendapatkan URL download. Periksa koneksi internet Anda."
    return 1
}

# Fungsi instalasi Cursor
install_cursor() {
    print_status "Memulai instalasi Cursor..."
    
    # Buat direktori aplikasi
    local app_dir="$HOME/.local/share/cursor"
    local bin_dir="$HOME/.local/bin"
    mkdir -p "$app_dir" "$bin_dir"
    
    # Dapatkan URL download
    local cursor_url
    cursor_url=$(get_latest_cursor_url)
    if [[ $? -ne 0 ]]; then
        exit 1
    fi
    
    print_status "Download URL: $cursor_url"
    
    # Download Cursor AppImage
    local temp_appimage="/tmp/cursor.AppImage"
    print_status "Mendownload Cursor AppImage..."
    if ! curl -L "$cursor_url" -o "$temp_appimage"; then
        print_error "Gagal mendownload Cursor AppImage"
        exit 1
    fi
    
    # Download icon
    local icon_url="https://registry.npmmirror.com/@lobehub/icons-static-png/1.13.0/files/light/cursor.png"
    local temp_icon="/tmp/cursor.png"
    print_status "Mendownload icon Cursor..."
    if ! curl -L "$icon_url" -o "$temp_icon"; then
        print_warning "Gagal mendownload icon, menggunakan icon default"
        # Buat icon sederhana jika download gagal
        convert -size 128x128 xc:blue "$temp_icon" 2>/dev/null || touch "$temp_icon"
    fi
    
    # Pindahkan file ke lokasi final
    print_status "Menginstal file..."
    mv "$temp_appimage" "$app_dir/cursor.AppImage"
    chmod +x "$app_dir/cursor.AppImage"
    mv "$temp_icon" "$app_dir/cursor.png"
    
    # Buat desktop entry
    print_status "Membuat desktop entry..."
    cat > "$HOME/.local/share/applications/cursor.desktop" << EOF
[Desktop Entry]
Name=Cursor AI IDE
GenericName=AI Code Editor
Comment=AI-powered code editor for developers
Exec=$app_dir/cursor.AppImage --no-sandbox %F
Icon=$app_dir/cursor.png
Terminal=false
Type=Application
Categories=Development;IDE;TextEditor;
MimeType=text/plain;inode/directory;application/x-cursor-workspace;x-scheme-handler/cursor;
Keywords=cursor;code;editor;ai;development;programming;
StartupWMClass=Cursor
Actions=new-empty-window;
X-Desktop-File-Install-Version=0.26

[Desktop Action new-empty-window]
Name=New Empty Window
Exec=$app_dir/cursor.AppImage --no-sandbox --new-window
EOF

    # Buat script launcher di bin
    print_status "Membuat command line launcher..."
    cat > "$bin_dir/cursor" << EOF
#!/bin/bash
# Cursor launcher script
exec "$app_dir/cursor.AppImage" --no-sandbox "\$@" > /dev/null 2>&1 &
disown
EOF
    chmod +x "$bin_dir/cursor"
    
    # Tambahkan ke PATH jika belum ada
    if ! echo "$PATH" | grep -q "$bin_dir"; then
        print_status "Menambahkan $bin_dir ke PATH..."
        if [[ "$SHELL_NAME" == "fish" ]]; then
            if ! grep -q "set -gx PATH \$HOME/.local/bin \$PATH" "$RC_FILE" 2>/dev/null; then
                echo 'set -gx PATH $HOME/.local/bin $PATH' >> "$RC_FILE"
            fi
        else
            if ! grep -q 'export PATH="$HOME/.local/bin:$PATH"' "$RC_FILE" 2>/dev/null; then
                echo 'export PATH="$HOME/.local/bin:$PATH"' >> "$RC_FILE"
            fi
        fi
    fi
}

# Fungsi konfigurasi browser integration
setup_browser_integration() {
    print_status "Mengkonfigurasi integrasi browser..."
    
    # Buat MIME type untuk protocol cursor://
    mkdir -p "$HOME/.local/share/mime/packages"
    cat > "$HOME/.local/share/mime/packages/cursor-protocol.xml" << EOF
<?xml version="1.0" encoding="UTF-8"?>
<mime-info xmlns="http://www.freedesktop.org/standards/shared-mime-info">
  <mime-type type="x-scheme-handler/cursor">
    <comment>Cursor Protocol</comment>
    <generic-icon name="cursor"/>
  </mime-type>
</mime-info>
EOF

    # Update MIME database
    update-mime-database "$HOME/.local/share/mime" 2>/dev/null || true
    
    # Konfigurasi default applications
    local mimeapps_file="$HOME/.config/mimeapps.list"
    mkdir -p "$HOME/.config"
    
    if [[ ! -f "$mimeapps_file" ]]; then
        cat > "$mimeapps_file" << EOF
[Default Applications]

[Added Associations]
EOF
    fi
    
    # Tambahkan handler untuk protocol cursor://
    if ! grep -q "x-scheme-handler/cursor=cursor.desktop" "$mimeapps_file"; then
        print_status "Menambahkan protocol handler..."
        
        # Tambah ke Default Applications
        if grep -q "\[Default Applications\]" "$mimeapps_file"; then
            sed -i '/\[Default Applications\]/a x-scheme-handler/cursor=cursor.desktop' "$mimeapps_file"
        fi
        
        # Tambah ke Added Associations
        if grep -q "\[Added Associations\]" "$mimeapps_file"; then
            sed -i '/\[Added Associations\]/a x-scheme-handler/cursor=cursor.desktop;' "$mimeapps_file"
        fi
    fi
    
    # Update desktop database
    update-desktop-database "$HOME/.local/share/applications" 2>/dev/null || true
    
    # Tes protocol handler
    print_status "Mendaftarkan protocol handler untuk browser..."
    xdg-mime default cursor.desktop x-scheme-handler/cursor
}

# Fungsi membuat update script
create_update_script() {
    print_status "Membuat script update..."
    
    cat > "$HOME/.local/bin/cursor-update" << 'EOF'
#!/bin/bash
# Cursor Update Script

set -e

print_status() {
    echo -e "\033[0;32m[INFO]\033[0m $1"
}

print_error() {
    echo -e "\033[0;31m[ERROR]\033[0m $1"
}

APP_DIR="$HOME/.local/share/cursor"
CURRENT_APPIMAGE="$APP_DIR/cursor.AppImage"

# Fungsi untuk mendapatkan URL terbaru (sama seperti di installer)
get_latest_cursor_url() {
    local api_response
    api_response=$(curl -s "https://www.cursor.com/api/download?platform=linux-x64&releaseTrack=stable" || echo "")
    
    if [[ -n "$api_response" ]]; then
        local download_url
        download_url=$(echo "$api_response" | jq -r '.downloadUrl' 2>/dev/null || echo "")
        if [[ -n "$download_url" && "$download_url" != "null" ]]; then
            echo "$download_url"
            return 0
        fi
    fi
    
    # Fallback
    local github_response
    github_response=$(curl -s "https://api.github.com/repos/oslook/cursor-ai-downloads/contents/README.md" || echo "")
    
    if [[ -n "$github_response" ]]; then
        local fallback_url
        fallback_url=$(echo "$github_response" | grep -o 'https://downloads.cursor.com/production/[^"]*linux/x64/[^"]*AppImage' | head -1 || echo "")
        if [[ -n "$fallback_url" ]]; then
            echo "$fallback_url"
            return 0
        fi
    fi
    
    return 1
}

print_status "Memeriksa update Cursor..."

CURSOR_URL=$(get_latest_cursor_url)
if [[ $? -ne 0 ]]; then
    print_error "Tidak dapat mendapatkan URL download"
    exit 1
fi

# Download ke file temporary
TEMP_FILE="/tmp/cursor-update.AppImage"
print_status "Mendownload versi terbaru..."

if curl -L "$CURSOR_URL" -o "$TEMP_FILE"; then
    # Bandingkan checksum
    if [[ -f "$CURRENT_APPIMAGE" ]]; then
        OLD_CHECKSUM=$(sha256sum "$CURRENT_APPIMAGE" | cut -d' ' -f1)
        NEW_CHECKSUM=$(sha256sum "$TEMP_FILE" | cut -d' ' -f1)
        
        if [[ "$OLD_CHECKSUM" == "$NEW_CHECKSUM" ]]; then
            print_status "Cursor sudah versi terbaru"
            rm "$TEMP_FILE"
            exit 0
        fi
    fi
    
    # Update
    print_status "Mengupdate Cursor..."
    mv "$TEMP_FILE" "$CURRENT_APPIMAGE"
    chmod +x "$CURRENT_APPIMAGE"
    print_status "Update berhasil!"
else
    print_error "Gagal mendownload update"
    exit 1
fi
EOF
    
    chmod +x "$HOME/.local/bin/cursor-update"
}

# Fungsi testing
test_installation() {
    print_status "Testing instalasi..."
    
    # Test desktop entry
    if [[ -f "$HOME/.local/share/applications/cursor.desktop" ]]; then
        print_status "✓ Desktop entry berhasil dibuat"
    else
        print_warning "✗ Desktop entry tidak ditemukan"
    fi
    
    # Test command line
    if command -v cursor >/dev/null 2>&1; then
        print_status "✓ Command line launcher berhasil"
    else
        print_warning "✗ Command line launcher tidak tersedia (restart shell mungkin diperlukan)"
    fi
    
    # Test protocol handler
    if xdg-mime query default x-scheme-handler/cursor | grep -q cursor.desktop; then
        print_status "✓ Protocol handler terdaftar"
    else
        print_warning "✗ Protocol handler belum terdaftar dengan benar"
    fi
    
    print_status "Testing selesai"
}

# Fungsi utama
main() {
    echo -e "${BLUE}Memulai instalasi Cursor AI IDE...${NC}"
    echo
    
    # Konfirmasi dari user
    read -p "Lanjutkan instalasi? (y/N): " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        echo "Instalasi dibatalkan"
        exit 1
    fi
    
    install_dependencies
    install_cursor
    setup_browser_integration
    create_update_script
    test_installation
    
    echo
    print_status "🎉 Instalasi Cursor berhasil!"
    echo
    echo -e "${GREEN}Cara menggunakan:${NC}"
    echo "1. Melalui Applications Menu: Cari 'Cursor AI IDE'"
    echo "2. Melalui Terminal: Ketik 'cursor' (setelah restart shell)"
    echo "3. Melalui Browser: cursor://path/to/project"
    echo "4. Update: Jalankan 'cursor-update'"
    echo
    echo -e "${YELLOW}Untuk mengaktifkan command line, restart terminal atau jalankan:${NC}"
    echo "source $RC_FILE"
    echo
    echo -e "${BLUE}Untuk testing browser integration, coba buka URL: cursor://test${NC}"
}

# Jalankan fungsi utama
main "$@"