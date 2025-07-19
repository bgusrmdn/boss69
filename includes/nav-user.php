<?php
// File: includes/nav-user.php (REVISI FINAL FULL - Tombol Toggle Sidebar Mobile)
// Ambil data saldo user dari database
$user_id_from_session = $_SESSION['user_id'];
$balance_stmt = $conn->prepare("SELECT balance FROM users WHERE id = ?");
$balance_stmt->bind_param("i", $user_id_from_session);
$balance_stmt->execute();
$balance_result = $balance_stmt->get_result();
$user_balance = ($balance_result->num_rows > 0) ? $balance_result->fetch_assoc()['balance'] : 0;
$balance_stmt->close();
?>
<header class="user-main-header sticky-top">
    <div class="super-top-bar">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="beranda" class="header-logo-link">
                <img src="<?php echo $base_url; ?>assets/images/<?php echo htmlspecialchars($settings['main_logo'] ?? 'logo.png'); ?>" alt="Logo Situs" class="main-logo-animated" style="height: 40px;">
            </a>

            <div class="header-actions">
                <button class="btn btn-header-icon"><i class="fas fa-bell"></i></button>
                <button class="btn btn-header-icon d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#userSidebar" aria-controls="userSidebar">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </div>
    <div class="bottom-bar">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="deposit" class="header-icon-link">
                <i class="fas fa-wallet" style="color:#28a745;"></i>
                <span>Depo</span>
            </a>
            <a href="withdraw" class="header-icon-link">
                <i class="fas fa-hand-holding-usd" style="color:#007bff;"></i>
                <span>With</span>
            </a>
            <a href="rekening" class="header-icon-link">
                <i class="fas fa-university" style="color:#ffc107;"></i>
                <span>Akun Bank</span>
            </a>
            <div class="user-wallet-block ms-auto">
                <div class="user-icon-wrapper">
                    <i class="fas fa-user-circle" style="color:#ff5722;"></i>
                </div>
                <div class="user-details-wrapper">
                    <span class="username" style="color:#00bcd4;"><?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?></span>
                    <div class="balance" id="user-balance" style="color:#4caf50; font-weight:bold;">
                        <i class="fas fa-coins" style="color:#ffc107;"></i>
                        <span>IDR <?php echo number_format($user_balance, 0, ',', '.'); ?></span>
                    </div>
                </div>
                <button id="refresh-balance" class="btn btn-sm btn-refresh-wallet"><i class="fas fa-sync-alt" style="color:#2196f3;"></i></button>
            </div>
        </div>
    </div>
</header>
<script>
    window.USER_SALDO = <?php echo isset($user_balance) ? (int)$user_balance : 0; ?>;
</script>