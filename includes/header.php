<?php
require_once __DIR__ . '/../includes/config.php';
$siteName = getSetting('site_name');
$siteTagline = getSetting('site_tagline');
$whatsapp = getSetting('whatsapp_number');
$logo = getSetting('logo');

$db = getDB();
$categories = $db->query("SELECT * FROM categories WHERE is_active=1 ORDER BY sort_order ASC")->fetchAll();

$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= sanitize($siteName) ?> - <?= sanitize($siteTagline) ?></title>
    <meta name="description" content="<?= sanitize(getSetting('site_description')) ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Cormorant+Garamond:wght@300;400;500;600&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
</head>
<body>

<!-- Top Bar -->
<div class="topbar">
    <div class="container">
        <span><i class="fas fa-map-marker-alt"></i> <?= sanitize(getSetting('address')) ?></span>
        <span><i class="fab fa-whatsapp"></i> <?= sanitize($whatsapp) ?></span>
    </div>
</div>

<!-- Navbar -->
<header class="navbar" id="navbar">
    <div class="container nav-container">
        <a href="<?= SITE_URL ?>/index.php" class="logo-wrap">
            <?php if ($logo): ?>
                <img src="<?= UPLOAD_URL . sanitize($logo) ?>" alt="<?= sanitize($siteName) ?>" class="logo-img">
            <?php else: ?>
                <div class="logo-text">
                    <span class="logo-main"><?= sanitize($siteName) ?></span>
                    <span class="logo-sub"><?= sanitize($siteTagline) ?></span>
                </div>
            <?php endif; ?>
        </a>

        <nav class="nav-links">
            <a href="<?= SITE_URL ?>/index.php" class="<?= $currentPage == 'index.php' ? 'active' : '' ?>">Beranda</a>
            <div class="dropdown">
                <a href="<?= SITE_URL ?>/pages/catalog.php" class="dropdown-toggle <?= $currentPage == 'catalog.php' ? 'active' : '' ?>">
                    Produk <i class="fas fa-chevron-down"></i>
                </a>
                <div class="dropdown-menu">
                    <a href="<?= SITE_URL ?>/pages/catalog.php">Semua Produk</a>
                    <?php foreach ($categories as $cat): ?>
                    <a href="<?= SITE_URL ?>/pages/catalog.php?category=<?= $cat['slug'] ?>"><?= sanitize($cat['name']) ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
            <a href="<?= SITE_URL ?>/pages/about.php" class="<?= $currentPage == 'about.php' ? 'active' : '' ?>">Tentang</a>
            <a href="<?= SITE_URL ?>/pages/contact.php" class="<?= $currentPage == 'contact.php' ? 'active' : '' ?>">Kontak</a>
        </nav>

        <a href="https://wa.me/<?= sanitize($whatsapp) ?>?text=<?= urlencode(getSetting('whatsapp_greeting')) ?>"
           class="btn-wa" target="_blank">
            <i class="fab fa-whatsapp"></i> Hubungi Kami
        </a>

        <button class="nav-toggle" id="navToggle">
            <span></span><span></span><span></span>
        </button>
    </div>
</header>

<!-- Mobile Menu -->
<div class="mobile-menu" id="mobileMenu">
    <a href="<?= SITE_URL ?>/index.php">Beranda</a>
    <a href="<?= SITE_URL ?>/pages/catalog.php">Semua Produk</a>
    <?php foreach ($categories as $cat): ?>
    <a href="<?= SITE_URL ?>/pages/catalog.php?category=<?= $cat['slug'] ?>" style="padding-left:2rem;font-size:0.88rem;">↳ <?= sanitize($cat['name']) ?></a>
    <?php endforeach; ?>
    <a href="<?= SITE_URL ?>/pages/about.php">Tentang Kami</a>
    <a href="<?= SITE_URL ?>/pages/contact.php">Kontak</a>
</div>
