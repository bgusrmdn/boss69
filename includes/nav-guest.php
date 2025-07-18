<?php
// File: includes/nav-guest.php (REVISI FINAL FULL - Header Tamu)

// Query ini mengambil semua kategori beserta provider yang terkait dalam satu kali panggilan
$menu_data_query = "
    SELECT 
        c.name AS category_name, 
        p.nama_provider
    FROM categories c
    LEFT JOIN providers p ON LOWER(p.kategori) = LOWER(c.name)
    WHERE c.is_active = 1 AND p.id IS NOT NULL
    ORDER BY c.sort_order, p.sort_order
";

$menu_result = $conn->query($menu_data_query);

// Mengolah data menjadi struktur yang mudah digunakan
$menu_items = [];
if ($menu_result) {
    while ($row = $menu_result->fetch_assoc()) {
        $menu_items[$row['category_name']][] = $row['nama_provider'];
    }
}
?>

<header class="main-header sticky-top">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="index" class="logo">
            <img src="<?php echo $base_url; ?>assets/images/<?php echo htmlspecialchars($settings['main_logo'] ?? 'logo.png'); ?>" alt="Logo Situs" class="main-logo-animated" style="height: 40px;">
        </a>

        <nav class="main-nav d-none d-lg-flex">
            <a href="index">Home</a>
            <?php foreach ($menu_items as $category => $providers): ?>
                <div class="nav-item-dropdown">
                    <a href="#" class="dropdown-toggle"><?php echo htmlspecialchars($category); ?></a>
                    <div class="dropdown-menu-custom">
                        <?php foreach ($providers as $provider): ?>
                            <a href="#" class="dropdown-item provider-link"><?php echo htmlspecialchars($provider); ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
            <a href="promo">Bonus</a>
        </nav>

        <div class="auth-buttons-desktop d-none d-lg-block">
            <a href="login" class="btn btn-outline-light btn-sm">Login</a>
            <a href="daftar" class="btn btn-warning btn-sm fw-bold">Daftar</a>
        </div>
        <button class="btn d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu" aria-controls="mobileMenu">
            <i class="fas fa-bars text-white fs-4"></i>
        </button>
    </div>
</header>

<div class="offcanvas offcanvas-start bg-dark text-white" tabindex="-1" id="mobileMenu" aria-labelledby="mobileMenuLabel">
    <div class="offcanvas-header border-bottom border-secondary">
        <h5 class="offcanvas-title" id="mobileMenuLabel">MENU</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <nav class="mobile-nav">
            <a href="index">Home</a>
            <?php foreach (
                $menu_items as $category => $providers
            ): ?>
                <div class="mobile-nav-item-dropdown">
                    <a href="#" class="mobile-dropdown-toggle d-flex justify-content-between align-items-center">
                        <span><?php echo htmlspecialchars($category); ?></span>
                        <i class="fas fa-chevron-down"></i>
                    </a>
                    <div class="mobile-dropdown-menu">
                        <?php foreach ($providers as $provider): ?>
                            <a href="#" class="mobile-dropdown-item provider-link"><?php echo htmlspecialchars($provider); ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
            <a href="promo">Bonus</a>
        </nav>
    </div>
</div>