#!/bin/bash

# Quick Fix untuk Protocol Handler Cursor
# Script ini akan memperbaiki masalah "No Apps available" saat membuka cursor://

echo "🔧 Quick Fix untuk Protocol Handler Cursor..."

# Warna untuk output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

print_status() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Fungsi untuk memeriksa apakah Cursor sudah terinstal
check_cursor_installation() {
    local cursor_locations=(
        "$HOME/.local/share/cursor/cursor.AppImage"
        "$HOME/Applications/cursor.AppImage"
        "/opt/cursor.appimage"
        "$(which cursor 2>/dev/null)"
    )
    
    for location in "${cursor_locations[@]}"; do
        if [[ -f "$location" && -x "$location" ]]; then
            echo "$location"
            return 0
        fi
    done
    
    return 1
}

# Fungsi untuk download Cursor jika belum ada
download_cursor() {
    print_status "Cursor tidak ditemukan. Downloading..."
    
    local app_dir="$HOME/.local/share/cursor"
    mkdir -p "$app_dir"
    
    # Download dari API Cursor
    local api_response
    api_response=$(curl -s "https://www.cursor.com/api/download?platform=linux-x64&releaseTrack=stable" || echo "")
    
    if [[ -n "$api_response" ]]; then
        local download_url
        download_url=$(echo "$api_response" | grep -o '"downloadUrl":"[^"]*"' | cut -d'"' -f4)
        if [[ -n "$download_url" ]]; then
            print_status "Downloading Cursor AppImage..."
            if curl -L "$download_url" -o "$app_dir/cursor.AppImage"; then
                chmod +x "$app_dir/cursor.AppImage"
                echo "$app_dir/cursor.AppImage"
                return 0
            fi
        fi
    fi
    
    print_error "Gagal download Cursor. Install manual terlebih dahulu."
    return 1
}

# Fungsi utama untuk fix protocol handler
fix_protocol_handler() {
    print_status "Memeriksa instalasi Cursor..."
    
    # Cari lokasi Cursor
    local cursor_path
    cursor_path=$(check_cursor_installation)
    
    if [[ $? -ne 0 ]]; then
        cursor_path=$(download_cursor)
        if [[ $? -ne 0 ]]; then
            return 1
        fi
    fi
    
    print_status "Cursor ditemukan di: $cursor_path"
    
    # Buat direktori yang diperlukan
    mkdir -p "$HOME/.local/share/applications"
    mkdir -p "$HOME/.local/share/mime/packages"
    mkdir -p "$HOME/.local/bin"
    
    # 1. Buat Desktop Entry
    print_status "Membuat desktop entry..."
    cat > "$HOME/.local/share/applications/cursor.desktop" << EOF
[Desktop Entry]
Name=Cursor AI IDE
GenericName=AI Code Editor
Comment=AI-powered code editor for developers
Exec=$cursor_path --no-sandbox %F
Icon=cursor
Terminal=false
Type=Application
Categories=Development;IDE;TextEditor;
MimeType=text/plain;inode/directory;x-scheme-handler/cursor;
Keywords=cursor;code;editor;ai;development;programming;
StartupWMClass=Cursor
EOF
    
    chmod +x "$HOME/.local/share/applications/cursor.desktop"
    
    # 2. Buat MIME Type untuk Protocol
    print_status "Registering protocol handler..."
    cat > "$HOME/.local/share/mime/packages/cursor-protocol.xml" << EOF
<?xml version="1.0" encoding="UTF-8"?>
<mime-info xmlns="http://www.freedesktop.org/standards/shared-mime-info">
  <mime-type type="x-scheme-handler/cursor">
    <comment>Cursor Protocol</comment>
    <generic-icon name="cursor"/>
  </mime-type>
</mime-info>
EOF
    
    # 3. Update MIME Database
    print_status "Updating MIME database..."
    update-mime-database "$HOME/.local/share/mime" 2>/dev/null || true
    
    # 4. Register Protocol Handler
    print_status "Setting default application for cursor:// protocol..."
    xdg-mime default cursor.desktop x-scheme-handler/cursor
    
    # 5. Update Desktop Database
    print_status "Updating desktop database..."
    update-desktop-database "$HOME/.local/share/applications" 2>/dev/null || true
    
    # 6. Buat Command Line Launcher
    print_status "Creating command line launcher..."
    cat > "$HOME/.local/bin/cursor" << EOF
#!/bin/bash
# Cursor launcher script
exec "$cursor_path" --no-sandbox "\$@" > /dev/null 2>&1 &
disown
EOF
    chmod +x "$HOME/.local/bin/cursor"
    
    # 7. Update mimeapps.list
    print_status "Updating mimeapps.list..."
    local mimeapps_file="$HOME/.config/mimeapps.list"
    mkdir -p "$HOME/.config"
    
    if [[ ! -f "$mimeapps_file" ]]; then
        cat > "$mimeapps_file" << EOF
[Default Applications]

[Added Associations]
EOF
    fi
    
    # Tambahkan handler untuk cursor protocol
    if ! grep -q "x-scheme-handler/cursor=cursor.desktop" "$mimeapps_file"; then
        # Tambah ke Default Applications
        if grep -q "\[Default Applications\]" "$mimeapps_file"; then
            sed -i '/\[Default Applications\]/a x-scheme-handler/cursor=cursor.desktop' "$mimeapps_file"
        fi
        
        # Tambah ke Added Associations
        if grep -q "\[Added Associations\]" "$mimeapps_file"; then
            sed -i '/\[Added Associations\]/a x-scheme-handler/cursor=cursor.desktop;' "$mimeapps_file"
        fi
    fi
    
    print_status "✅ Protocol handler berhasil disetup!"
    return 0
}

# Fungsi untuk test protocol handler
test_protocol_handler() {
    print_status "Testing protocol handler..."
    
    # Test 1: Check if registered
    local default_app
    default_app=$(xdg-mime query default x-scheme-handler/cursor 2>/dev/null)
    
    if [[ "$default_app" == "cursor.desktop" ]]; then
        print_status "✅ Protocol handler terdaftar dengan benar"
    else
        print_warning "⚠️ Protocol handler belum terdaftar: $default_app"
        return 1
    fi
    
    # Test 2: Check desktop file
    if [[ -f "$HOME/.local/share/applications/cursor.desktop" ]]; then
        print_status "✅ Desktop file exists"
    else
        print_warning "⚠️ Desktop file tidak ditemukan"
        return 1
    fi
    
    # Test 3: Check if Cursor executable exists
    local cursor_exec
    cursor_exec=$(grep "^Exec=" "$HOME/.local/share/applications/cursor.desktop" | cut -d'=' -f2 | cut -d' ' -f1)
    
    if [[ -f "$cursor_exec" && -x "$cursor_exec" ]]; then
        print_status "✅ Cursor executable valid: $cursor_exec"
    else
        print_warning "⚠️ Cursor executable tidak valid: $cursor_exec"
        return 1
    fi
    
    print_status "🎉 Semua test berhasil!"
    return 0
}

# Main execution
main() {
    echo "🎯 Quick Fix untuk Cursor Protocol Handler"
    echo "=========================================="
    echo
    
    # Install dependencies jika diperlukan
    if ! command -v curl >/dev/null 2>&1; then
        print_status "Installing curl..."
        sudo apt update && sudo apt install -y curl
    fi
    
    if ! command -v xdg-mime >/dev/null 2>&1; then
        print_status "Installing xdg-utils..."
        sudo apt update && sudo apt install -y xdg-utils
    fi
    
    # Fix protocol handler
    if fix_protocol_handler; then
        echo
        print_status "🔧 Fix completed! Testing..."
        echo
        
        if test_protocol_handler; then
            echo
            print_status "🎉 SUCCESS! Protocol handler berhasil diperbaiki!"
            echo
            echo -e "${GREEN}Cara test:${NC}"
            echo "1. Restart browser Anda"
            echo "2. Coba buka URL: cursor://test"
            echo "3. Atau gunakan command: xdg-open 'cursor:///workspace'"
            echo
            echo -e "${YELLOW}Jika masih tidak work:${NC}"
            echo "- Restart desktop session (logout/login)"
            echo "- Clear browser cache"
            echo "- Jalankan: xdg-mime default cursor.desktop x-scheme-handler/cursor"
        else
            print_warning "Fix completed tapi ada warning. Coba restart browser."
        fi
    else
        print_error "Fix gagal. Periksa error di atas."
        return 1
    fi
}

# Run main function
main "$@"