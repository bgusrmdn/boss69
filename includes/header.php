<?php
// File: includes/header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Mendapatkan nama file halaman saat ini
$current_page = basename($_SERVER['PHP_SELF']);

// ========================================================
// === LOGIKA PENJAGA AKSES HALAMAN UNTUK PENGGUNA ===
// ========================================================

// Daftar halaman yang membutuhkan user login
$pages_requiring_login = [
    'beranda',
    'profil',
    'deposit',
    'withdraw',
    'rekening',
    'transaksi',
    'memo',
    'referral',
    'bantuan',
];

// Daftar halaman yang TIDAK BOLEH diakses setelah user login (hanya untuk guest)
$pages_for_logged_out_only = [
    'index',
    'login',
    'daftar',
];

// Logika redirect
if (isset($_SESSION['user_id'])) {
    // Validasi user_id di database
    require_once __DIR__ . '/db_connect.php';
    $check_user_stmt = $conn->prepare("SELECT id FROM users WHERE id = ?");
    $check_user_stmt->bind_param("i", $_SESSION['user_id']);
    $check_user_stmt->execute();
    $check_user_stmt->store_result();
    if ($check_user_stmt->num_rows === 0) {
        // User tidak ditemukan, hapus session dan redirect ke index.php
        session_unset();
        session_destroy();
        header("Location: index");
        exit();
    }
    $check_user_stmt->close();
    // User SUDAH login
    if (in_array(str_replace('.php', '', $current_page), $pages_for_logged_out_only)) {
        header("Location: beranda"); // Redirect ke beranda jika mencoba akses halaman khusus logout
        exit();
    }
} else {
    // User BELUM login
    if (in_array(str_replace('.php', '', $current_page), $pages_requiring_login)) {
        header("Location: login"); // Redirect ke login jika mencoba akses halaman yang butuh login
        exit();
    }
}

// Memanggil koneksi database
require_once __DIR__ . '/db_connect.php';
// Memanggil pengaturan situs (untuk $settings array)
require_once __DIR__ . '/site_config.php';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($settings['site_title'] ?? 'HOKIRAJA'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.8/dist/sweetalert2.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" href="<?php echo $base_url; ?>assets/css/style.css">
<!-- 🚀 ENHANCED OPTIMIZATIONS CSS - Animasi dan Responsif Keren! -->
<link rel="stylesheet" href="<?php echo $base_url; ?>assets/css/optimization.css">
<!-- Preload untuk performa lebih cepat -->
<link rel="preload" href="<?php echo $base_url; ?>assets/js/optimization.js" as="script">
<link rel="preload" href="<?php echo $base_url; ?>assets/js/script.js" as="script">
    <?php if ($current_page == 'index.php' || $current_page == 'beranda.php'): ?>
        <div class="floating-social-menu">
            <div class="social-icons-container">
                <a href="#" class="social-icon" style="--i:1;"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="social-icon" style="--i:2;"><i class="fas fa-envelope"></i></a>
                <a href="#" class="social-icon" style="--i:3;"><i class="fab fa-telegram-plane"></i></a>
                <a href="livechat.php" class="social-icon" style="--i:4;"><i class="fas fa-headset"></i></a>
            </div>
            <button class="menu-toggle-button">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    <?php endif; ?>
</head>

<body class="dark-theme">
    <?php
    // ========================================================
    // === PUSAT KONTROL NOTIFIKASI (SWEETALERT) ===
    // ========================================================

    // Notifikasi untuk LOGIN BERHASIL
    if (isset($_SESSION['login_success'])) {
        $username = $_SESSION['login_username'];
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Login Berhasil!',
                    html: 'Selamat datang kembali, <strong style=\"color: #ffc107;\">" . htmlspecialchars($username) . "</strong>!',
                    icon: 'success',
                    timer: 3000,
                    showConfirmButton: false,
                    background: '#212529',
                    color: '#fff'
                });
            });
        </script>";
        unset($_SESSION['login_success']);
        unset($_SESSION['login_username']);
    }

// Notifikasi untuk PENDAFTARAN BERHASIL
if (isset($_SESSION['registration_success'])) {
    $username = $_SESSION['username'];
    echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Pendaftaran Berhasil!',
                    html: 'Selamat datang, <strong style=\"color: #ffc107;\">" . htmlspecialchars($username) . "</strong>! Akun Anda telah dibuat.',
                    icon: 'success',
                    timer: 4000,
                    showConfirmButton: false,
                    background: '#212529',
                    color: '#fff'
                });
            });
        </script>";
    unset($_SESSION['registration_success']);
}
if (isset($_SESSION['pending_deposit_alert'])) {
    $message = $_SESSION['pending_deposit_alert'];
    echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Akses Dibatasi',
                    text: '" . htmlspecialchars($message) . "',
                    icon: 'warning',
                    background: '#212529',
                    color: '#fff',
                    confirmButtonColor: '#ffc107'
                });
            });
        </script>";
    unset($_SESSION['pending_deposit_alert']);
}

// === BARU: Notifikasi setelah BERHASIL mengirim permintaan deposit ===
if (isset($_SESSION['deposit_submitted_alert'])) {
    $message = $_SESSION['deposit_submitted_alert'];
    echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Permintaan Terkirim!',
                    text: '" . htmlspecialchars($message) . "',
                    icon: 'success',
                    timer: 4000,
                    showConfirmButton: false,
                    background: '#212529',
                    color: '#fff'
                });
            });
        </script>";
    unset($_SESSION['deposit_submitted_alert']);
}

// === BARU: Notifikasi setelah BERHASIL mengirim permintaan WITHDRAW ===
if (isset($_SESSION['withdraw_submitted_alert'])) {
    $message = $_SESSION['withdraw_submitted_alert'];
    echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Permintaan Terkirim!',
                    text: '" . htmlspecialchars($message) . "',
                    icon: 'success',
                    timer: 4000,
                    showConfirmButton: false,
                    background: '#212529',
                    color: '#fff'
                });
            });
        </script>";
    unset($_SESSION['withdraw_submitted_alert']);
}

// === BARU: PENJAGA HALAMAN WITHDRAW ===
// Cek hanya jika pengguna mencoba mengakses halaman withdraw.php
if ($current_page == 'withdraw.php' && isset($_SESSION['user_id'])) {
    $check_pending_wd_stmt = $conn->prepare("SELECT id FROM transactions WHERE user_id = ? AND type = 'withdraw' AND status = 'pending'");
    $check_pending_wd_stmt->bind_param("i", $_SESSION['user_id']);
    $check_pending_wd_stmt->execute();
    $has_pending_wd = $check_pending_wd_stmt->get_result()->num_rows > 0;
    $check_pending_wd_stmt->close();

    if ($has_pending_wd) {
        // Atur notifikasi dan redirect ke beranda
        $_SESSION['pending_withdraw_alert'] = "Anda masih memiliki transaksi penarikan yang sedang diproses. Mohon tunggu hingga selesai sebelum membuat permintaan baru.";
        header("Location: beranda.php");
        exit();
    }
}

// Notifikasi untuk PENDING WITHDRAW (ditampilkan di beranda.php)
if (isset($_SESSION['pending_withdraw_alert'])) {
    $message = $_SESSION['pending_withdraw_alert'];
    echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Akses Dibatasi',
                    text: '" . htmlspecialchars($message) . "',
                    icon: 'warning',
                    background: '#212529',
                    color: '#fff',
                    confirmButtonColor: '#ffc107'
                });
            });
        </script>";
    unset($_SESSION['pending_withdraw_alert']);
}
?>
    <?php

// Memanggil navigasi (header) berdasarkan status login
if (isset($_SESSION['user_id'])) {
    require_once __DIR__ . '/nav-user.php'; // Header untuk user yang login
    require_once __DIR__ . '/sidebar-user.php'; // Sidebar Offcanvas untuk user yang login
} else {
    require_once __DIR__ . '/nav-guest.php'; // Header untuk guest
}
// === BARU: Notifikasi untuk Halaman Profil ===
if (isset($_SESSION['profil_success'])) {
    $message = $_SESSION['profil_success'];
    echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Berhasil!', text: '" . htmlspecialchars($message) . "', icon: 'success',
                    background: '#212529', color: '#fff', confirmButtonColor: '#ffc107'
                });
            });
        </script>";
    unset($_SESSION['profil_success']);
}
if (isset($_SESSION['profil_error'])) {
    $message = $_SESSION['profil_error'];
    echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Gagal!', text: '" . htmlspecialchars($message) . "', icon: 'error',
                    background: '#212529', color: '#fff', confirmButtonColor: '#ffc107'
                });
            });
        </script>";
    unset($_SESSION['profil_error']);
}
?>