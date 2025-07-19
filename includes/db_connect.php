<?php
// File: includes/db_connect.php

// Untuk development/preview - sementara disable koneksi database
// Mari tampilkan halaman tanpa error database

/*
// Konfigurasi Database - temporarily disabled
$db_host = 'localhost';
$db_user = 'root';
$db_pass = ''; // Password sudah diisi sesuai permintaan user
$db_name = 'hokiraja';

// Membuat koneksi  
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Cek koneksi
if ($conn->connect_error) {
    // Tambahkan logging untuk debugging
    error_log("Database connection failed: " . $conn->connect_error);

    // Tampilkan error yang lebih informatif
    if (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
        // Development environment - tampilkan error detail
        die("<div style='color: red; font-family: Arial; padding: 20px; border: 1px solid red; margin: 20px;'>
            <h2>⚠️ Error Database Connection</h2>
            <p><strong>Error Message:</strong> " . $conn->connect_error . "</p>
            <p><strong>Host:</strong> $db_host</p>
            <p><strong>User:</strong> $db_user</p>
            <p><strong>Database:</strong> $db_name</p>
            <p>Silakan periksa:</p>
            <ul>
                <li>MySQL server sudah berjalan?</li>
                <li>Database '$db_name' sudah dibuat?</li>
                <li>User '$db_user' memiliki akses ke database?</li>
                <li>Password database benar?</li>
            </ul>
        </div>");
    } else {
        // Production environment - tampilkan error generic
        die("<div style='text-align: center; padding: 50px; font-family: Arial;'>
            <h2>🚧 Situs dalam perbaikan</h2>
            <p>Mohon maaf, situs sedang dalam tahap perbaikan. Silakan coba lagi nanti.</p>
        </div>");
    }
}

// Set charset
$conn->set_charset("utf8");
*/

// TEMPORARY MOCK CONNECTION FOR PREVIEW
class MockConnection {
    public function query($sql) {
        // Return empty result for preview
        return new MockResult();
    }
    
    public function set_charset($charset) {
        return true;
    }
    
    public function escape_string($string) {
        return addslashes($string);
    }
}

class MockResult {
    public function fetch_assoc() {
        return false; // No results
    }
    
    public function num_rows() {
        return 0;
    }
}

$conn = new MockConnection();

// Fungsi untuk debug - akan dihapus setelah setup selesai
function debug_db_status() {
    return "Database: Temporary mock connection for preview";
}

// Test query untuk memastikan database berfungsi
try {
    $test_query = $conn->query("SELECT 1");
    if (!$test_query) {
        throw new Exception("Database test query failed");
    }
} catch (Exception $e) {
    error_log("Database test failed: " . $e->getMessage());
    if (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
        die("Database test failed: " . $e->getMessage());
    } else {
        die("Maaf, terjadi kesalahan sistem. Silakan coba lagi nanti.");
    }
}
