<?php

/**
 * HOKIRAJA Auto-Deploy Script
 * Script untuk deploy otomatis ke free hosting
 */

echo "🚀 HOKIRAJA Auto-Deploy Script\n";
echo "===============================\n\n";

// Configuration
$config = [
    'github_repo' => 'https://github.com/bgusrmdn/boss69',
    'branch' => 'cursor/proses-sapaan-dan-permintaan-bahasa-1633',
    'local_backup' => './backup_' . date('Y-m-d_H-i-s'),
    'exclude_files' => ['.git', '.env', 'node_modules', 'deploy-to-freehosting.php']
];

function downloadFromGithub($repo, $branch)
{
    $zip_url = $repo . '/archive/refs/heads/' . urlencode($branch) . '.zip';
    $zip_file = 'hokiraja_website.zip';

    echo "📥 Downloading dari GitHub...\n";
    echo "URL: $zip_url\n";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $zip_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $zip_content = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code === 200 && $zip_content) {
        file_put_contents($zip_file, $zip_content);
        echo "✅ Download berhasil: $zip_file\n\n";
        return $zip_file;
    } else {
        echo "❌ Download gagal! HTTP Code: $http_code\n";
        return false;
    }
}

function extractZip($zip_file)
{
    if (!extension_loaded('zip')) {
        echo "❌ PHP Zip extension tidak tersedia!\n";
        return false;
    }

    $zip = new ZipArchive();
    if ($zip->open($zip_file) === true) {
        echo "📦 Extracting files...\n";

        // Create extraction directory
        $extract_dir = './extracted_website';
        if (!is_dir($extract_dir)) {
            mkdir($extract_dir, 0755, true);
        }

        $zip->extractTo($extract_dir);
        $zip->close();

        echo "✅ Extract berhasil ke: $extract_dir\n\n";
        return $extract_dir;
    } else {
        echo "❌ Gagal membuka ZIP file!\n";
        return false;
    }
}

function prepareFiles($extract_dir, $exclude_files)
{
    echo "🔧 Mempersiapkan files untuk deployment...\n";

    // Find the extracted folder (usually has repo name)
    $dirs = glob($extract_dir . '/*', GLOB_ONLYDIR);
    if (empty($dirs)) {
        echo "❌ Tidak ada folder yang diekstrak!\n";
        return false;
    }

    $source_dir = $dirs[0]; // Take first directory
    $deploy_dir = './deploy_ready';

    // Create deploy directory
    if (!is_dir($deploy_dir)) {
        mkdir($deploy_dir, 0755, true);
    }

    // Copy files excluding unwanted ones
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($source_dir),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($iterator as $file) {
        if ($file->isDir()) {
            continue;
        }

        $relative_path = str_replace($source_dir . DIRECTORY_SEPARATOR, '', $file->getPathname());

        // Skip excluded files/folders
        $should_exclude = false;
        foreach ($exclude_files as $exclude) {
            if (strpos($relative_path, $exclude) === 0) {
                $should_exclude = true;
                break;
            }
        }

        if (!$should_exclude) {
            $dest_path = $deploy_dir . DIRECTORY_SEPARATOR . $relative_path;
            $dest_dir = dirname($dest_path);

            if (!is_dir($dest_dir)) {
                mkdir($dest_dir, 0755, true);
            }

            copy($file->getPathname(), $dest_path);
        }
    }

    echo "✅ Files siap untuk deployment di: $deploy_dir\n\n";
    return $deploy_dir;
}

function createZipForUpload($deploy_dir)
{
    if (!extension_loaded('zip')) {
        echo "❌ PHP Zip extension tidak tersedia untuk membuat upload ZIP!\n";
        return false;
    }

    $zip_name = 'hokiraja_upload_ready.zip';
    $zip = new ZipArchive();

    if ($zip->open($zip_name, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
        echo "❌ Tidak bisa membuat ZIP file untuk upload!\n";
        return false;
    }

    echo "📦 Membuat ZIP file untuk upload...\n";

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($deploy_dir),
        RecursiveIteratorIterator::LEAVES_ONLY
    );

    foreach ($iterator as $file) {
        if (!$file->isDir()) {
            $relative_path = str_replace($deploy_dir . DIRECTORY_SEPARATOR, '', $file->getRealPath());
            $zip->addFile($file->getRealPath(), $relative_path);
        }
    }

    $zip->close();
    echo "✅ ZIP upload siap: $zip_name\n\n";
    return $zip_name;
}

function showDeploymentInstructions($zip_file)
{
    echo "🎯 INSTRUKSI DEPLOYMENT\n";
    echo "=======================\n\n";

    echo "📁 File ZIP siap upload: $zip_file\n\n";

    echo "🌟 PILIHAN HOSTING GRATIS:\n";
    echo "1. InfinityFree: https://www.infinityfree.net/\n";
    echo "2. AeonFree: https://aeonfree.com/\n";
    echo "3. 000WebHost: https://www.000webhost.com/\n\n";

    echo "📋 LANGKAH DEPLOYMENT:\n";
    echo "1. Daftar di salah satu hosting gratis di atas\n";
    echo "2. Login ke Control Panel / cPanel\n";
    echo "3. Buka File Manager\n";
    echo "4. Masuk ke folder 'public_html' atau 'www'\n";
    echo "5. Upload file: $zip_file\n";
    echo "6. Extract file ZIP tersebut\n";
    echo "7. Website Anda akan live di subdomain yang dipilih!\n\n";

    echo "🚀 FITUR YANG AKAN AKTIF:\n";
    echo "✅ Halaman utama HOKIRAJA\n";
    echo "✅ Sistem login/register (demo mode)\n";
    echo "✅ Responsive design\n";
    echo "✅ Menu game interaktif\n";
    echo "✅ Social media floating menu\n";
    echo "✅ SSL Certificate otomatis\n\n";

    echo "💡 TIPS:\n";
    echo "- Pilih subdomain yang mudah diingat\n";
    echo "- Untuk production, ganti DEMO_MODE=false di .env.production\n";
    echo "- Setup database MySQL jika diperlukan\n\n";
}

// Main execution
try {
    // Download from GitHub
    $zip_file = downloadFromGithub($config['github_repo'], $config['branch']);
    if (!$zip_file) {
        throw new Exception("Gagal download dari GitHub");
    }

    // Extract files
    $extract_dir = extractZip($zip_file);
    if (!$extract_dir) {
        throw new Exception("Gagal extract ZIP file");
    }

    // Prepare deployment files
    $deploy_dir = prepareFiles($extract_dir, $config['exclude_files']);
    if (!$deploy_dir) {
        throw new Exception("Gagal mempersiapkan files");
    }

    // Create upload-ready ZIP
    $upload_zip = createZipForUpload($deploy_dir);
    if (!$upload_zip) {
        throw new Exception("Gagal membuat ZIP untuk upload");
    }

    // Show deployment instructions
    showDeploymentInstructions($upload_zip);

    // Cleanup
    echo "🧹 Cleaning up temporary files...\n";
    unlink($zip_file);

    echo "✅ DEPLOYMENT SCRIPT SELESAI!\n";
    echo "File siap upload: $upload_zip\n";

} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}
