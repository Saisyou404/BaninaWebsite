<?php
$pageTitle = 'Dashboard';
require_once __DIR__ . '/includes/header.php';
$db = getDB();

$totalProducts    = $db->query("SELECT COUNT(*) FROM products WHERE is_active=1")->fetchColumn();
$totalCategories  = $db->query("SELECT COUNT(*) FROM categories WHERE is_active=1")->fetchColumn();
$totalBanners     = $db->query("SELECT COUNT(*) FROM banners WHERE is_active=1")->fetchColumn();
$featuredProducts = $db->query("SELECT COUNT(*) FROM products WHERE is_featured=1 AND is_active=1")->fetchColumn();

$recentProducts = $db->query("
    SELECT p.*, c.name as cat_name,
        (SELECT image FROM product_images WHERE product_id=p.id ORDER BY is_primary DESC, sort_order ASC LIMIT 1) as thumb
    FROM products p
    LEFT JOIN categories c ON c.id = p.category_id
    ORDER BY p.created_at DESC LIMIT 6
")->fetchAll();
?>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon black"><i class="fas fa-tshirt"></i></div>
        <div>
            <div class="stat-number"><?= $totalProducts ?></div>
            <div class="stat-label">Total Produk</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon gold"><i class="fas fa-tags"></i></div>
        <div>
            <div class="stat-number"><?= $totalCategories ?></div>
            <div class="stat-label">Kategori Aktif</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-images"></i></div>
        <div>
            <div class="stat-number"><?= $totalBanners ?></div>
            <div class="stat-label">Banner Aktif</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-star"></i></div>
        <div>
            <div class="stat-number"><?= $featuredProducts ?></div>
            <div class="stat-label">Produk Unggulan</div>
        </div>
    </div>
</div>

<div style="display:flex;gap:0.75rem;margin-bottom:2rem;flex-wrap:wrap">
    <a href="<?= SITE_URL ?>/admin/pages/product-form.php" class="btn btn-gold"><i class="fas fa-plus"></i> Tambah Produk</a>
    <a href="<?= SITE_URL ?>/admin/pages/categories.php" class="btn btn-primary"><i class="fas fa-tags"></i> Kelola Kategori</a>
    <a href="<?= SITE_URL ?>/admin/pages/banners.php" class="btn btn-primary"><i class="fas fa-images"></i> Kelola Banner</a>
    <a href="<?= SITE_URL ?>/admin/pages/settings.php" class="btn btn-outline"><i class="fas fa-cog"></i> Pengaturan</a>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Produk Terbaru</span>
        <a href="<?= SITE_URL ?>/admin/pages/products.php" class="btn btn-outline btn-sm">Lihat Semua</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>Produk</th><th>Kategori</th><th>Harga</th><th>Status</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                <?php foreach ($recentProducts as $p): ?>
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:0.75rem">
                            <?php if ($p['thumb']): ?>
                            <img src="<?= UPLOAD_URL . sanitize($p['thumb']) ?>" class="product-thumb" alt="">
                            <?php else: ?>
                            <div class="product-thumb-placeholder"><i class="fas fa-tshirt"></i></div>
                            <?php endif; ?>
                            <span style="font-weight:500"><?= sanitize($p['name']) ?></span>
                        </div>
                    </td>
                    <td><?= sanitize($p['cat_name'] ?? '-') ?></td>
                    <td><?= formatPrice($p['price_min']) ?></td>
                    <td>
                        <span class="badge <?= $p['is_active'] ? 'badge-success' : 'badge-danger' ?>"><?= $p['is_active'] ? 'Aktif' : 'Nonaktif' ?></span>
                        <?php if ($p['is_featured']): ?><span class="badge badge-gold">Unggulan</span><?php endif; ?>
                    </td>
                    <td>
                        <a href="<?= SITE_URL ?>/admin/pages/product-form.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-outline"><i class="fas fa-edit"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
