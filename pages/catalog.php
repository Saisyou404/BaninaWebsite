<?php
require_once __DIR__ . '/../includes/config.php';
$db = getDB();

$categorySlug = $_GET['category'] ?? '';
$search = $_GET['q'] ?? '';

$where = ['p.is_active = 1'];
$params = [];

if ($categorySlug) { $where[] = 'c.slug = ?'; $params[] = $categorySlug; }
if ($search) {
    $where[] = '(p.name LIKE ? OR p.description LIKE ?)';
    $params[] = "%$search%"; $params[] = "%$search%";
}

$whereSQL = 'WHERE ' . implode(' AND ', $where);

$products = $db->prepare("
    SELECT p.*, c.name as cat_name, c.slug as cat_slug,
        (SELECT image FROM product_images WHERE product_id=p.id AND is_primary=1 LIMIT 1) as primary_image,
        (SELECT image FROM product_images WHERE product_id=p.id ORDER BY sort_order ASC LIMIT 1) as first_image
    FROM products p
    LEFT JOIN categories c ON c.id = p.category_id
    $whereSQL
    ORDER BY p.sort_order ASC, p.created_at DESC
");
$products->execute($params);
$products = $products->fetchAll();

$allCategories = $db->query("SELECT * FROM categories WHERE is_active=1 ORDER BY sort_order ASC")->fetchAll();

$activeCatName = '';
if ($categorySlug) {
    foreach ($allCategories as $cat) {
        if ($cat['slug'] === $categorySlug) { $activeCatName = $cat['name']; break; }
    }
}

$whatsapp = getSetting('whatsapp_number');
include __DIR__ . '/../includes/header.php';
?>

<div class="catalog-header">
    <div class="container">
        <h1><?= $activeCatName ?: 'Semua Produk' ?></h1>
        <p><?= $activeCatName ? 'Koleksi ' . sanitize($activeCatName) . ' pilihan terbaik BANINA' : 'Temukan koleksi busana muslim pria terbaik kami' ?></p>
    </div>
</div>

<div class="filter-bar">
    <div class="container filter-container">
        <span class="filter-label"><i class="fas fa-filter"></i> Filter:</span>
        <div class="filter-tags">
            <a href="<?= SITE_URL ?>/pages/catalog.php" class="filter-tag <?= !$categorySlug ? 'active' : '' ?>">Semua</a>
            <?php foreach ($allCategories as $cat): ?>
            <a href="<?= SITE_URL ?>/pages/catalog.php?category=<?= $cat['slug'] ?>"
               class="filter-tag <?= $categorySlug === $cat['slug'] ? 'active' : '' ?>">
                <?= sanitize($cat['name']) ?>
            </a>
            <?php endforeach; ?>
        </div>
        <form method="GET" action="" style="display:flex;gap:0.5rem">
            <?php if ($categorySlug): ?>
            <input type="hidden" name="category" value="<?= sanitize($categorySlug) ?>">
            <?php endif; ?>
            <input type="text" name="q" value="<?= sanitize($search) ?>" placeholder="Cari produk..."
                   style="padding:0.4rem 0.8rem;border:1.5px solid #d8d0c0;border-radius:50px;font-size:0.82rem;outline:none;width:160px;font-family:inherit;color:var(--text)">
            <button type="submit" style="background:var(--black);color:var(--gold);border:none;padding:0.4rem 0.9rem;border-radius:50px;cursor:pointer;font-size:0.82rem">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>
</div>

<section class="section" style="padding-top:2rem">
    <div class="container">
        <p class="products-count">
            Menampilkan <strong><?= count($products) ?></strong> produk
            <?= $activeCatName ? 'kategori <strong>' . sanitize($activeCatName) . '</strong>' : '' ?>
            <?= $search ? 'untuk "<strong>' . sanitize($search) . '</strong>"' : '' ?>
        </p>

        <?php if (!empty($products)): ?>
        <div class="products-grid">
            <?php foreach ($products as $prod):
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
                        <?php if ($prod['is_featured']): ?>
                        <span class="product-badge">Unggulan</span>
                        <?php endif; ?>
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
                    <a href="https://id.shp.ee/y9timn2w" class="product-wa-btn shopee-btn" target="_blank" rel="noopener">
                        <i class="fas fa-shopping-bag"></i> Beli di Shopee
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-search"></i>
            <h3>Produk tidak ditemukan</h3>
            <p>Coba filter atau pencarian yang berbeda</p>
            <br>
            <a href="<?= SITE_URL ?>/pages/catalog.php" class="btn-primary">Lihat Semua Produk</a>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>