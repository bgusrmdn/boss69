<?php
// File: includes/db_connect.php

// Load environment variables
function loadEnv($path) {
    if (!file_exists($path)) return;
    
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        
        if (!array_key_exists($name, $_ENV)) {
            $_ENV[$name] = $value;
        }
    }
}

// Load production environment if exists
if (file_exists(__DIR__ . '/../.env.production')) {
    loadEnv(__DIR__ . '/../.env.production');
}

// Database configuration with environment variables
$db_host = $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?? 'localhost';
$db_user = $_ENV['DB_USER'] ?? getenv('DB_USER') ?? 'root';
$db_pass = $_ENV['DB_PASS'] ?? getenv('DB_PASS') ?? '';
$db_name = $_ENV['DB_NAME'] ?? getenv('DB_NAME') ?? 'hokiraja';

// Check if we're in production environment
$is_production = ($_ENV['APP_ENV'] ?? getenv('APP_ENV') ?? 'development') === 'production';
$is_demo_mode = ($_ENV['DEMO_MODE'] ?? getenv('DEMO_MODE') ?? 'true') === 'true';

if ($is_demo_mode) {
    // Mock connection untuk demo mode
    $conn = (object) [
        'connect_error' => false,
        'error' => false,
        'query' => function($sql) {
            return (object) [
                'num_rows' => 0,
                'fetch_assoc' => function() { return false; }
            ];
        },
        'prepare' => function($sql) {
            return (object) [
                'bind_param' => function() { return true; },
                'execute' => function() { return true; },
                'get_result' => function() {
                    return (object) [
                        'fetch_assoc' => function() { return false; },
                        'num_rows' => 0
                    ];
                }
            ];
        },
        'real_escape_string' => function($str) { return addslashes($str); },
        'insert_id' => 1,
        'affected_rows' => 0,
        'set_charset' => function($charset) { return true; },
        'close' => function() { return true; }
    ];
} else {
    // Real database connection untuk production
    try {
        $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
        
        // Cek koneksi
        if ($conn->connect_error) {
            // Log error untuk debugging
            error_log("Database connection failed: " . $conn->connect_error);
            
            if (!$is_production) {
                // Development environment - tampilkan error detail
                die("
                    <div style='background: #f8d7da; color: #721c24; padding: 20px; margin: 20px; border-radius: 5px; font-family: Arial, sans-serif;'>
                        <h3>🔴 Database Connection Error</h3>
                        <p><strong>Error:</strong> " . $conn->connect_error . "</p>
                        <p><strong>Host:</strong> $db_host</p>
                        <p><strong>Database:</strong> $db_name</p>
                        <p><strong>User:</strong> $db_user</p>
                        <hr>
                        <small>
                            <strong>Solusi:</strong><br>
                            1. Pastikan MySQL server berjalan<br>
                            2. Periksa username dan password database<br>
                            3. Pastikan database '$db_name' sudah dibuat<br>
                            4. Periksa konfigurasi di file ini: " . __FILE__ . "
                        </small>
                    </div>
                ");
            } else {
                // Production environment - tampilkan pesan generic
                die("
                    <div style='background: #f8d7da; color: #721c24; padding: 20px; margin: 20px; border-radius: 5px; font-family: Arial, sans-serif; text-align: center;'>
                        <h3>⚠️ Maintenance Mode</h3>
                        <p>Website sedang dalam perbaikan. Silakan coba lagi beberapa saat.</p>
                        <p><small>Error ID: DB_CONN_" . date('YmdHis') . "</small></p>
                    </div>
                ");
            }
        }
        
        // Set charset
        $conn->set_charset("utf8");
        
        // Database connection berhasil
        if (isset($_GET['test_db']) && !$is_production) {
            echo "<div style='background: #d4edda; color: #155724; padding: 10px; margin: 10px; border-radius: 5px;'>
                    ✅ Database connection successful!<br>
                    <small>Host: $db_host | Database: $db_name | User: $db_user</small>
                  </div>";
        }
        
    } catch (Exception $e) {
        error_log("Database connection exception: " . $e->getMessage());
        
        if (!$is_production) {
            die("Database Error: " . $e->getMessage());
        } else {
            die("Service temporarily unavailable. Please try again later.");
        }
    }
}

?>
