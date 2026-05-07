<?php
$pageTitle = 'Kelola Produk';
require_once __DIR__ . '/../../includes/config.php';
$db = getDB();

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $imgs = $db->prepare("SELECT image FROM product_images WHERE product_id=?");
    $imgs->execute([$id]);
    foreach ($imgs->fetchAll() as $img) {
        $f = UPLOAD_PATH . $img['image'];
        if (file_exists($f)) unlink($f);
    }
    $db->prepare("DELETE FROM products WHERE id=?")->execute([$id]);
    redirect(SITE_URL . '/admin/pages/products.php?msg=deleted');
}

if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $db->prepare("UPDATE products SET is_active = NOT is_active WHERE id=?")->execute([$id]);
    redirect(SITE_URL . '/admin/pages/products.php');
}

if (isset($_GET['feature'])) {
    $id = (int)$_GET['feature'];
    $db->prepare("UPDATE products SET is_featured = NOT is_featured WHERE id=?")->execute([$id]);
    redirect(SITE_URL . '/admin/pages/products.php');
}

$search = trim($_GET['q'] ?? '');
$catFilter = (int)($_GET['cat'] ?? 0);
$where = [];
$params = [];
if ($search) { $where[] = 'p.name LIKE ?'; $params[] = "%$search%"; }
if ($catFilter) { $where[] = 'p.category_id = ?'; $params[] = $catFilter; }
$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$products = $db->prepare("
    SELECT p.*, c.name as cat_name,
        (SELECT image FROM product_images WHERE product_id=p.id ORDER BY is_primary DESC, sort_order ASC LIMIT 1) as thumb
    FROM products p
    LEFT JOIN categories c ON c.id = p.category_id
    $whereSQL
    ORDER BY p.sort_order ASC, p.created_at DESC
");
$products->execute($params);
$products = $products->fetchAll();

$allCats = $db->query("SELECT * FROM categories ORDER BY sort_order ASC")->fetchAll();

require_once __DIR__ . '/../includes/header.php';
$msg = $_GET['msg'] ?? '';
?>

<?php if ($msg==='deleted'): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> Produk berhasil dihapus.</div><?php endif; ?>

<div class="card">
    <div class="card-header">
        <span class="card-title">Daftar Produk (<?= count($products) ?>)</span>
        <a href="<?= SITE_URL ?>/admin/pages/product-form.php" class="btn btn-gold btn-sm"><i class="fas fa-plus"></i> Tambah Produk</a>
    </div>

    <!-- Filter -->
    <div style="padding:1rem 1.5rem;border-bottom:1px solid #e8e4dc;display:flex;gap:0.75rem;flex-wrap:wrap">
        <form method="GET" action="" style="display:flex;gap:0.5rem;flex-wrap:wrap">
            <input type="text" name="q" value="<?= sanitize($search) ?>" placeholder="Cari nama produk..."
                   style="padding:0.45rem 0.85rem;border:1.5px solid #e0dbd0;border-radius:8px;font-size:0.85rem;outline:none;font-family:inherit;color:var(--text);min-width:220px">
            <select name="cat" style="padding:0.45rem 0.85rem;border:1.5px solid #e0dbd0;border-radius:8px;font-size:0.85rem;outline:none;font-family:inherit;color:var(--text)">
                <option value="">Semua Kategori</option>
                <?php foreach ($allCats as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= $catFilter === $cat['id'] ? 'selected' : '' ?>><?= sanitize($cat['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i> Filter</button>
            <a href="<?= SITE_URL ?>/admin/pages/products.php" class="btn btn-outline btn-sm">Reset</a>
        </form>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>Produk</th><th>Kategori</th><th>Harga</th><th>Status</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                <?php if (empty($products)): ?>
                <tr><td colspan="5" style="text-align:center;padding:2rem;color:var(--text-light)">Belum ada produk.</td></tr>
                <?php else: ?>
                <?php foreach ($products as $p): ?>
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:0.75rem">
                            <?php if ($p['thumb']): ?>
                            <img src="<?= UPLOAD_URL . sanitize($p['thumb']) ?>" class="product-thumb">
                            <?php else: ?>
                            <div class="product-thumb-placeholder"><i class="fas fa-tshirt"></i></div>
                            <?php endif; ?>
                            <div>
                                <div style="font-weight:500;font-size:0.875rem"><?= sanitize($p['name']) ?></div>
                                <div style="font-size:0.72rem;color:var(--text-light)"><?= sanitize($p['slug']) ?></div>
                            </div>
                        </div>
                    </td>
                    <td><?= sanitize($p['cat_name'] ?? '-') ?></td>
                    <td>
                        <?= formatPrice($p['price_min']) ?>
                        <?php if ($p['price_max'] > $p['price_min']): ?>
                        <span style="color:var(--text-light)"> – <?= formatPrice($p['price_max']) ?></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div style="display:flex;gap:0.3rem;flex-wrap:wrap">
                            <span class="badge <?= $p['is_active'] ? 'badge-success' : 'badge-danger' ?>"><?= $p['is_active'] ? 'Aktif' : 'Nonaktif' ?></span>
                            <?php if ($p['is_featured']): ?><span class="badge badge-gold">Unggulan</span><?php endif; ?>
                        </div>
                    </td>
                    <td>
                        <div style="display:flex;gap:0.3rem">
                            <a href="<?= SITE_URL ?>/admin/pages/product-form.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-outline" title="Edit"><i class="fas fa-edit"></i></a>
                            <a href="?feature=<?= $p['id'] ?>" class="btn btn-sm btn-outline" title="<?= $p['is_featured']?'Hapus dari Unggulan':'Jadikan Unggulan' ?>"><i class="fas fa-star" style="color:<?= $p['is_featured']?'var(--gold)':'inherit' ?>"></i></a>
                            <a href="?toggle=<?= $p['id'] ?>" class="btn btn-sm btn-outline" title="Aktif/Nonaktif"><i class="fas fa-toggle-<?= $p['is_active']?'on':'off' ?>"></i></a>
                            <a href="<?= SITE_URL ?>/pages/product.php?slug=<?= $p['slug'] ?>" class="btn btn-sm btn-outline" target="_blank" title="Lihat"><i class="fas fa-eye"></i></a>
                            <a href="?delete=<?= $p['id'] ?>" class="btn btn-sm btn-danger" title="Hapus" onclick="return confirm('Yakin hapus produk ini?')"><i class="fas fa-trash"></i></a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>