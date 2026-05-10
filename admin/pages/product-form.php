<?php
require_once __DIR__ . '/../../includes/config.php';
$db = getDB();

$id = (int)($_GET['id'] ?? 0);
$product = null;
$images = [];

if ($id) {
    $product = $db->prepare("SELECT * FROM products WHERE id=?");
    $product->execute([$id]);
    $product = $product->fetch();
    if (!$product) redirect(SITE_URL . '/admin/pages/products.php');

    $images = $db->prepare("SELECT * FROM product_images WHERE product_id=? ORDER BY is_primary DESC, sort_order ASC");
    $images->execute([$id]);
    $images = $images->fetchAll();
}

$pageTitle = $id ? 'Edit Produk' : 'Tambah Produk';
$errors = [];
$success = '';

if (isset($_GET['del_img'])) {
    $imgId = (int)$_GET['del_img'];
    $img = $db->prepare("SELECT * FROM product_images WHERE id=? AND product_id=?");
    $img->execute([$imgId, $id]);
    $img = $img->fetch();
    if ($img) {
        $f = UPLOAD_PATH . $img['image'];
        if (file_exists($f)) unlink($f);
        $db->prepare("DELETE FROM product_images WHERE id=?")->execute([$imgId]);
    }
    redirect(SITE_URL . '/admin/pages/product-form.php?id=' . $id . '&msg=img_deleted');
}

if (isset($_GET['set_primary'])) {
    $imgId = (int)$_GET['set_primary'];
    $db->prepare("UPDATE product_images SET is_primary=0 WHERE product_id=?")->execute([$id]);
    $db->prepare("UPDATE product_images SET is_primary=1 WHERE id=?")->execute([$imgId]);
    redirect(SITE_URL . '/admin/pages/product-form.php?id=' . $id);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $catId    = (int)($_POST['category_id'] ?? 0);
    $desc     = trim($_POST['description'] ?? '');
    $priceMin = (float)($_POST['price_min'] ?? 0);
    $priceMax = $priceMin;
    $waMsg    = trim($_POST['whatsapp_message'] ?? '');
    $isFeatured = isset($_POST['is_featured']) ? 1 : 0;
    $isActive   = isset($_POST['is_active']) ? 1 : 0;

    if (!$name) $errors[] = 'Nama produk wajib diisi.';

    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));
    $slugCheck = $db->prepare("SELECT id FROM products WHERE slug=? AND id!=?");
    $slugCheck->execute([$slug, $id ?: 0]);
    if ($slugCheck->fetch()) $slug .= '-' . time();

    if (empty($errors)) {
        if ($id) {
            $db->prepare("UPDATE products SET category_id=?,name=?,slug=?,description=?,price_min=?,price_max=?,whatsapp_message=?,is_featured=?,is_active=? WHERE id=?")
               ->execute([$catId,$name,$slug,$desc,$priceMin,$priceMax,$waMsg,$isFeatured,$isActive,$id]);
        } else {
            $db->prepare("INSERT INTO products (category_id,name,slug,description,price_min,price_max,whatsapp_message,is_featured,is_active) VALUES (?,?,?,?,?,?,?,?,?)")
               ->execute([$catId,$name,$slug,$desc,$priceMin,$priceMax,$waMsg,$isFeatured,$isActive]);
            $id = $db->lastInsertId();
        }

        // Upload images
        if (!empty($_FILES['images']['tmp_name'][0])) {
            $isFirst = empty($images);
            foreach ($_FILES['images']['tmp_name'] as $k => $tmp) {
                if ($_FILES['images']['error'][$k] !== 0) continue;
                $file = [
                    'name' => $_FILES['images']['name'][$k],
                    'tmp_name' => $tmp,
                    'size' => $_FILES['images']['size'][$k],
                    'error' => $_FILES['images']['error'][$k],
                ];
                $imgPath = uploadImage($file, 'products/');
                if ($imgPath) {
                    $isPrimary = ($isFirst && $k === 0) ? 1 : 0;
                    $db->prepare("INSERT INTO product_images (product_id,image,is_primary,sort_order) VALUES (?,?,?,?)")
                       ->execute([$id, $imgPath, $isPrimary, $k]);
                }
            }
        }

        redirect(SITE_URL . '/admin/pages/product-form.php?id=' . $id . '&msg=saved');
    }
}

$allCats = $db->query("SELECT * FROM categories WHERE is_active=1 ORDER BY sort_order ASC")->fetchAll();

require_once __DIR__ . '/../includes/header.php';
$msg = $_GET['msg'] ?? '';
?>

<?php if ($msg==='saved'): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> Produk berhasil disimpan.</div><?php endif; ?>
<?php if ($msg==='img_deleted'): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> Gambar berhasil dihapus.</div><?php endif; ?>
<?php foreach ($errors as $e): ?><div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?= sanitize($e) ?></div><?php endforeach; ?>

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem">
    <a href="<?= SITE_URL ?>/admin/pages/products.php" class="btn btn-outline btn-sm"><i class="fas fa-arrow-left"></i> Kembali</a>
</div>

<form method="POST" enctype="multipart/form-data">
    <div style="display:grid;grid-template-columns:2fr 1fr;gap:1.5rem;align-items:start">

        <div>
            <!-- Info Produk -->
            <div class="card" style="margin-bottom:1.5rem">
                <div class="card-header"><span class="card-title">Informasi Produk</span></div>
                <div class="card-body">
                    <div class="form-grid">
                        <div class="form-group full">
                            <label>Nama Produk *</label>
                            <input type="text" name="name" value="<?= sanitize($product['name'] ?? '') ?>" required>
                        </div>
                        <div class="form-group full">
                            <label>Kategori</label>
                            <select name="category_id">
                                <option value="">-- Pilih Kategori --</option>
                                <?php foreach ($allCats as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= ($product['category_id'] ?? 0) == $cat['id'] ? 'selected' : '' ?>>
                                    <?= sanitize($cat['name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Harga (Rp)</label>
                            <input type="number" name="price_min" value="<?= $product['price_min'] ?? 0 ?>" min="0">
                        </div>
                        <div class="form-group full">
                            <label>Deskripsi Produk</label>
                            <textarea name="description" rows="5"><?= sanitize($product['description'] ?? '') ?></textarea>
                        </div>
                        <div class="form-group full">
                            <label>Pesan WhatsApp Default</label>
                            <textarea name="whatsapp_message" rows="2"><?= sanitize($product['whatsapp_message'] ?? '') ?></textarea>
                            <p class="form-hint">Pesan yang dikirim saat pelanggan klik tombol WhatsApp</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gambar -->
            <div class="card">
                <div class="card-header"><span class="card-title">Gambar Produk</span></div>
                <div class="card-body">
                    <?php if (!empty($images)): ?>
                    <div class="img-thumb-row" style="margin-bottom:1.5rem">
                        <?php foreach ($images as $img): ?>
                        <div class="img-thumb-item">
                            <img src="<?= UPLOAD_URL . sanitize($img['image']) ?>" style="border-color:<?= $img['is_primary'] ? 'var(--gold)' : '' ?>">
                            <?php if (!$img['is_primary']): ?>
                            <a href="?id=<?= $id ?>&set_primary=<?= $img['id'] ?>" title="Jadikan Utama" style="position:absolute;bottom:-6px;right:14px;background:var(--gold);color:var(--black);border:none;border-radius:50%;width:18px;height:18px;font-size:0.55rem;cursor:pointer;display:flex;align-items:center;justify-content:center;text-decoration:none"><i class="fas fa-star"></i></a>
                            <?php endif; ?>
                            <a href="?id=<?= $id ?>&del_img=<?= $img['id'] ?>" onclick="return confirm('Hapus gambar?')" class="img-thumb-del"><i class="fas fa-times"></i></a>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                    <div class="form-group">
                        <label>Upload Gambar Baru (bisa multiple)</label>
                        <input type="file" name="images[]" accept="image/*" multiple>
                        <p class="form-hint">Rekomendasi: rasio 4:5, JPG/PNG/WebP, maks 5MB per gambar. Gambar pertama otomatis jadi gambar utama jika belum ada.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div>
            <div class="card" style="margin-bottom:1.5rem">
                <div class="card-header"><span class="card-title">Status & Visibilitas</span></div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-check">
                            <input type="checkbox" name="is_active" value="1" <?= ($product['is_active'] ?? 1) ? 'checked' : '' ?>>
                            Produk Aktif (Tampil di katalog)
                        </label>
                    </div>
                    <div class="form-group">
                        <label class="form-check">
                            <input type="checkbox" name="is_featured" value="1" <?= ($product['is_featured'] ?? 0) ? 'checked' : '' ?>>
                            Produk Unggulan (Tampil di beranda)
                        </label>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-gold" style="width:100%;justify-content:center;padding:0.85rem;font-size:1rem">
                <i class="fas fa-save"></i> <?= $id ? 'Simpan Perubahan' : 'Tambah Produk' ?>
            </button>

            <?php if ($id): ?>
            <div style="margin-top:1rem;text-align:center">
                <a href="<?= SITE_URL ?>/pages/product.php?slug=<?= $product['slug'] ?>" class="btn btn-outline btn-sm" target="_blank">
                    <i class="fas fa-eye"></i> Lihat di Website
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</form>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>