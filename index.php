<?php
require_once __DIR__ . '/includes/config.php';
$db = getDB();

$banners = $db->query("SELECT * FROM banners WHERE is_active=1 ORDER BY sort_order ASC")->fetchAll();
$categories = $db->query("
    SELECT c.*, 
        (SELECT COUNT(*) FROM products p WHERE p.category_id = c.id AND p.is_active = 1) as product_count
    FROM categories c
    WHERE c.is_active = 1
    ORDER BY c.sort_order ASC
")->fetchAll();
$featured = $db->query("
    SELECT p.*, c.name as cat_name,
        (SELECT image FROM product_images WHERE product_id=p.id AND is_primary=1 LIMIT 1) as primary_image,
        (SELECT image FROM product_images WHERE product_id=p.id ORDER BY sort_order ASC LIMIT 1) as first_image
    FROM products p
    LEFT JOIN categories c ON c.id = p.category_id
    WHERE p.is_featured = 1 AND p.is_active = 1
    ORDER BY p.sort_order ASC LIMIT 8
")->fetchAll();

$whatsapp = getSetting('whatsapp_number');
$heroTitle = getSetting('hero_title');
$heroSubtitle = getSetting('hero_subtitle');

include __DIR__ . '/includes/header.php';
?>

<!-- ===== HERO SECTION ===== -->
<section class="hero <?= empty($banners) ? 'hero-no-banner' : '' ?>">
    <?php if (!empty($banners)): ?>
    <div class="hero-slider">
        <?php foreach ($banners as $banner): ?>
        <div class="hero-slide">
            <img src="<?= UPLOAD_URL . sanitize($banner['image']) ?>" alt="<?= sanitize($banner['title']) ?>">
            <div class="hero-overlay"></div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="hero-overlay" style="position:absolute;inset:0;background:rgba(0,0,0,0.3)"></div>
    <?php endif; ?>

    <div class="container hero-content">
        <div class="hero-badge">✦ Men Wear Since 2019</div>
        <h1><?= sanitize($heroTitle) ?> <em>BANINA</em></h1>
        <p><?= sanitize($heroSubtitle) ?></p>
        <div class="hero-actions">
            <a href="<?= SITE_URL ?>/pages/catalog.php" class="btn-primary">
                <i class="fas fa-th-large"></i> Lihat Koleksi
            </a>
            <a href="https://wa.me/<?= sanitize($whatsapp) ?>" class="btn-outline" target="_blank">
                <i class="fab fa-whatsapp"></i> Tanya via WA
            </a>
        </div>
    </div>

    <?php if (count($banners) > 1): ?>
    <div class="hero-dots">
        <?php foreach ($banners as $i => $b): ?>
        <button class="hero-dot <?= $i === 0 ? 'active' : '' ?>" data-slide="<?= $i ?>"></button>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</section>

<!-- ===== CATEGORIES SECTION ===== -->
<?php if (!empty($categories)): ?>
<section class="section">
    <div class="container">
        <div class="section-header fade-in">
            <span class="section-label">Koleksi BANINA</span>
            <h2 class="section-title">Jelajahi Kategori</h2>
            <div class="divider"><span class="divider-icon">✦</span></div>
            <p class="section-subtitle">Temukan berbagai koleksi busana muslim pria pilihan dengan kualitas premium</p>
        </div>
        <div class="categories-grid">
            <?php foreach ($categories as $cat): ?>
            <a href="<?= SITE_URL ?>/pages/catalog.php?category=<?= $cat['slug'] ?>" class="category-card fade-in">
                <?php if (!empty($cat['image'])): ?>
                <img src="<?= UPLOAD_URL . sanitize($cat['image']) ?>" alt="<?= sanitize($cat['name']) ?>">
                <?php else: ?>
                <div class="cat-placeholder">
                    <?php
                    $icons = ['songkok'=>'fa-hat-cowboy','kemeja'=>'fa-shirt','sarung'=>'fa-scroll','celana'=>'fa-person','sajadah'=>'fa-mosque'];
                    $icon = $icons[$cat['slug']] ?? 'fa-tshirt';
                    ?>
                    <i class="fas <?= $icon ?>"></i>
                </div>
                <?php endif; ?>
                <div class="category-overlay">
                    <div class="category-name"><?= sanitize($cat['name']) ?></div>
                    <div class="category-count"><?= (int)($cat['product_count'] ?? 0) ?> Produk</div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ===== FEATURED PRODUCTS ===== -->
<?php if (!empty($featured)): ?>
<section class="section section-bg-dark">
    <div class="container">
        <div class="section-header fade-in">
            <span class="section-label">Pilihan Terbaik</span>
            <h2 class="section-title">Produk Unggulan</h2>
            <div class="divider"><span class="divider-icon">✦</span></div>
            <p class="section-subtitle">Koleksi terpilih yang paling diminati pelanggan kami</p>
        </div>
        <div class="products-grid">
            <?php foreach ($featured as $prod):
                $img = $prod['primary_image'] ?: $prod['first_image'];
                $waMsg = urlencode($prod['whatsapp_message'] ?: 'Halo BANINA, saya tertarik dengan produk ' . $prod['name']);
            ?>
            <div class="product-card fade-in">
                <a href="<?= SITE_URL ?>/pages/product.php?slug=<?= $prod['slug'] ?>" style="text-decoration:none;color:inherit;display:contents">
                    <div class="product-img-wrap">
                        <?php if ($img): ?>
                        <img src="<?= UPLOAD_URL . sanitize($img) ?>" alt="<?= sanitize($prod['name']) ?>" loading="lazy">
                        <?php else: ?>
                        <div class="product-placeholder"><i class="fas fa-tshirt"></i></div>
                        <?php endif; ?>
                        <span class="product-badge">Unggulan</span>
                    </div>
                    <div class="product-info">
                        <div class="product-cat"><?= sanitize($prod['cat_name'] ?? '') ?></div>
                        <div class="product-name"><?= sanitize($prod['name']) ?></div>
                        <div class="product-price">
                            <?= formatPrice($prod['price_min']) ?>
                            <?php if ($prod['price_max'] > $prod['price_min']): ?>
                            <span>– <?= formatPrice($prod['price_max']) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </a>
                <div style="padding:0 1.25rem 1.25rem">
                    <a href="https://wa.me/<?= sanitize($whatsapp) ?>?text=<?= $waMsg ?>"
                       class="product-wa-btn" target="_blank">
                        <i class="fab fa-whatsapp"></i> Tanya via WhatsApp
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div style="text-align:center;margin-top:3rem">
            <a href="<?= SITE_URL ?>/pages/catalog.php" class="btn-primary">
                Lihat Semua Produk <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ===== WHY BANINA ===== -->
<section class="section">
    <div class="container">
        <div class="section-header fade-in">
            <span class="section-label">Keunggulan Kami</span>
            <h2 class="section-title">Mengapa Memilih BANINA?</h2>
            <div class="divider"><span class="divider-icon">✦</span></div>
        </div>
        <div class="values-grid">
            <div class="value-card fade-in">
                <div class="value-icon"><i class="fas fa-award"></i></div>
                <h3>Kualitas Premium</h3>
                <p>Bahan pilihan berkualitas tinggi untuk kenyamanan dan ketahanan produk terbaik</p>
            </div>
            <div class="value-card fade-in">
                <div class="value-icon"><i class="fas fa-mosque"></i></div>
                <h3>Sesuai Syariat</h3>
                <p>Desain elegan yang tetap memenuhi ketentuan busana muslim yang baik</p>
            </div>
            <div class="value-card fade-in">
                <div class="value-icon"><i class="fas fa-palette"></i></div>
                <h3>Desain Modern</h3>
                <p>Koleksi up-to-date mengikuti tren fashion muslim pria masa kini</p>
            </div>
            <div class="value-card fade-in">
                <div class="value-icon"><i class="fas fa-shipping-fast"></i></div>
                <h3>Layanan Cepat</h3>
                <p>Pemesanan mudah via WhatsApp dan pengiriman ke seluruh Indonesia</p>
            </div>
        </div>
    </div>
</section>

<!-- ===== CTA SECTION ===== -->
<section style="background:linear-gradient(135deg,var(--black) 0%,#1a1000 100%);padding:5rem 0;text-align:center;position:relative;overflow:hidden;border-top:1px solid rgba(201,151,42,0.2)">
    <div style="position:absolute;inset:0;background-image:url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23c9972a\' fill-opacity=\'0.06\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')"></div>
    <div class="container fade-in" style="position:relative;z-index:1">
        <span class="section-label">Butuh Bantuan?</span>
        <h2 style="font-family:'Playfair Display',serif;font-size:2.5rem;color:#fff;margin:0.75rem 0 1rem">Konsultasi Gratis via WhatsApp</h2>
        <p style="color:rgba(255,255,255,0.6);font-family:'Cormorant Garamond',serif;font-size:1.1rem;margin-bottom:2rem">Tim BANINA siap membantu Anda memilih busana muslim yang tepat</p>
        <a href="https://wa.me/<?= sanitize($whatsapp) ?>?text=<?= urlencode(getSetting('whatsapp_greeting')) ?>"
           class="btn-primary" target="_blank" style="font-size:1.1rem;padding:1rem 2.5rem">
            <i class="fab fa-whatsapp"></i> Chat Sekarang
        </a>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>