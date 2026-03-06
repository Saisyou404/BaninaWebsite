<?php
$pageTitle = 'Kelola Kategori';
require_once __DIR__ . '/../../includes/config.php';
$db = getDB();
$errors = [];
$editing = null;

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $row = $db->query("SELECT image FROM categories WHERE id=$id")->fetch();
    if ($row && $row['image']) { $f = UPLOAD_PATH . $row['image']; if(file_exists($f)) unlink($f); }
    $db->prepare("DELETE FROM categories WHERE id=?")->execute([$id]);
    redirect(SITE_URL . '/admin/pages/categories.php?msg=deleted');
}
if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $db->prepare("UPDATE categories SET is_active = NOT is_active WHERE id=?")->execute([$id]);
    redirect(SITE_URL . '/admin/pages/categories.php');
}
if (isset($_GET['edit'])) {
    $stmt = $db->prepare("SELECT * FROM categories WHERE id=?");
    $stmt->execute([(int)$_GET['edit']]);
    $editing = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    $sort = (int)($_POST['sort_order'] ?? 0);
    if (!$name) $errors[] = 'Nama kategori wajib diisi.';

    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));
    $slugCheck = $db->prepare("SELECT id FROM categories WHERE slug=? AND id!=?");
    $slugCheck->execute([$slug, $id ?: 0]);
    if ($slugCheck->fetch()) $slug .= '-' . time();

    $imgPath = null;
    if (!empty($_FILES['image']['tmp_name']) && $_FILES['image']['error'] === 0) {
        $imgPath = uploadImage($_FILES['image'], 'categories/');
    }

    if (empty($errors)) {
        if ($id) {
            if ($imgPath) {
                $old = $db->query("SELECT image FROM categories WHERE id=$id")->fetchColumn();
                if ($old) { $f=UPLOAD_PATH.$old; if(file_exists($f)) unlink($f); }
                $db->prepare("UPDATE categories SET name=?,slug=?,description=?,image=?,sort_order=? WHERE id=?")
                   ->execute([$name,$slug,$desc,$imgPath,$sort,$id]);
            } else {
                $db->prepare("UPDATE categories SET name=?,slug=?,description=?,sort_order=? WHERE id=?")
                   ->execute([$name,$slug,$desc,$sort,$id]);
            }
        } else {
            $db->prepare("INSERT INTO categories (name,slug,description,image,sort_order) VALUES (?,?,?,?,?)")
               ->execute([$name,$slug,$desc,$imgPath,$sort]);
        }
        redirect(SITE_URL . '/admin/pages/categories.php?msg=saved');
    }
}

$categories = $db->query("
    SELECT c.*, COUNT(p.id) as product_count
    FROM categories c
    LEFT JOIN products p ON p.category_id=c.id
    GROUP BY c.id ORDER BY c.sort_order ASC
")->fetchAll();

require_once __DIR__ . '/../includes/header.php';
$msg = $_GET['msg'] ?? '';
?>

<?php if ($msg==='saved'): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> Kategori berhasil disimpan.</div><?php endif; ?>
<?php if ($msg==='deleted'): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> Kategori berhasil dihapus.</div><?php endif; ?>
<?php foreach ($errors as $e): ?><div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?= sanitize($e) ?></div><?php endforeach; ?>

<div style="display:grid;grid-template-columns:1fr 2fr;gap:1.5rem;align-items:start">
    <div class="card">
        <div class="card-header"><span class="card-title"><?= $editing ? 'Edit Kategori' : 'Tambah Kategori' ?></span></div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <?php if ($editing): ?><input type="hidden" name="id" value="<?= $editing['id'] ?>"><?php endif; ?>
                <div class="form-group">
                    <label>Nama Kategori *</label>
                    <input type="text" name="name" value="<?= sanitize($editing['name'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea name="description" rows="3"><?= sanitize($editing['description'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label>Urutan Tampil</label>
                    <input type="number" name="sort_order" value="<?= $editing['sort_order'] ?? 0 ?>">
                </div>
                <div class="form-group">
                    <label>Gambar Kategori</label>
                    <?php if ($editing && $editing['image']): ?>
                    <img src="<?= UPLOAD_URL . sanitize($editing['image']) ?>" class="img-preview" style="display:block" id="catPreview">
                    <?php else: ?>
                    <img id="catPreview" class="img-preview" style="display:none">
                    <?php endif; ?>
                    <input type="file" name="image" accept="image/*" onchange="previewImage(this,'catPreview')">
                </div>
                <div style="display:flex;gap:0.5rem">
                    <button type="submit" class="btn btn-gold"><i class="fas fa-save"></i> Simpan</button>
                    <?php if ($editing): ?>
                    <a href="<?= SITE_URL ?>/admin/pages/categories.php" class="btn btn-outline">Batal</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><span class="card-title">Daftar Kategori (<?= count($categories) ?>)</span></div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Kategori</th><th>Produk</th><th>Status</th><th>Aksi</th></tr></thead>
                <tbody>
                    <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:0.75rem">
                                <?php if ($cat['image']): ?>
                                <img src="<?= UPLOAD_URL . sanitize($cat['image']) ?>" style="width:36px;height:36px;border-radius:6px;object-fit:cover">
                                <?php else: ?>
                                <div style="width:36px;height:36px;border-radius:6px;background:#f0ece4;display:flex;align-items:center;justify-content:center;color:var(--gold);font-size:0.8rem"><i class="fas fa-tag"></i></div>
                                <?php endif; ?>
                                <div>
                                    <div style="font-weight:500"><?= sanitize($cat['name']) ?></div>
                                    <div style="font-size:0.72rem;color:var(--text-light)"><?= sanitize($cat['slug']) ?></div>
                                </div>
                            </div>
                        </td>
                        <td><?= $cat['product_count'] ?> produk</td>
                        <td><span class="badge <?= $cat['is_active'] ? 'badge-success' : 'badge-danger' ?>"><?= $cat['is_active'] ? 'Aktif' : 'Nonaktif' ?></span></td>
                        <td>
                            <div style="display:flex;gap:0.4rem">
                                <a href="?edit=<?= $cat['id'] ?>" class="btn btn-sm btn-outline"><i class="fas fa-edit"></i></a>
                                <a href="?toggle=<?= $cat['id'] ?>" class="btn btn-sm btn-outline"><i class="fas fa-toggle-<?= $cat['is_active']?'on':'off' ?>"></i></a>
                                <a href="?delete=<?= $cat['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus kategori ini?')"><i class="fas fa-trash"></i></a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
