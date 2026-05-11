<?php
$pageTitle = 'Kelola Banner Pop-up';
require_once __DIR__ . '/../../includes/config.php';
$db = getDB();
$errors = [];
$editing = null;

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $row = $db->query("SELECT image FROM popup_banners WHERE id=$id")->fetch();
    if ($row && $row['image']) { $f=UPLOAD_PATH.$row['image']; if(file_exists($f)) unlink($f); }
    $db->prepare("DELETE FROM popup_banners WHERE id=?")->execute([$id]);
    redirect(SITE_URL . '/admin/pages/popup-banners.php?msg=deleted');
}
if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $db->prepare("UPDATE popup_banners SET is_active = NOT is_active WHERE id=?")->execute([$id]);
    redirect(SITE_URL . '/admin/pages/popup-banners.php');
}
if (isset($_GET['edit'])) {
    $stmt = $db->prepare("SELECT * FROM popup_banners WHERE id=?");
    $stmt->execute([(int)$_GET['edit']]);
    $editing = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $editId = (int)($_POST['id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $link = trim($_POST['link'] ?? '');

    $imgPath = null;
    if (!empty($_FILES['image']['tmp_name']) && $_FILES['image']['error'] === 0) {
        $imgPath = uploadImage($_FILES['image'], 'banners/');
        if (!$imgPath) $errors[] = 'Gagal upload gambar.';
    } elseif (!$editId) {
        $errors[] = 'Gambar banner wajib diupload.';
    }

    if (empty($errors)) {
        if ($editId) {
            if ($imgPath) {
                $old = $db->query("SELECT image FROM popup_banners WHERE id=$editId")->fetchColumn();
                if ($old) { $f=UPLOAD_PATH.$old; if(file_exists($f)) unlink($f); }
                $db->prepare("UPDATE popup_banners SET title=?,image=?,link=? WHERE id=?")
                   ->execute([$title,$imgPath,$link,$editId]);
            } else {
                $db->prepare("UPDATE popup_banners SET title=?,link=? WHERE id=?")
                   ->execute([$title,$link,$editId]);
            }
        } else {
            $db->prepare("INSERT INTO popup_banners (title,image,link) VALUES (?,?,?)")
               ->execute([$title,$imgPath,$link]);
        }
        redirect(SITE_URL . '/admin/pages/popup-banners.php?msg=saved');
    }
}

$banners = $db->query("SELECT * FROM popup_banners ORDER BY created_at DESC")->fetchAll();

include __DIR__ . '/../includes/header.php';
$msg = $_GET['msg'] ?? '';
?>

<?php if ($msg==='saved'): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> Banner berhasil disimpan.</div><?php endif; ?>
<?php if ($msg==='deleted'): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> Banner berhasil dihapus.</div><?php endif; ?>
<?php foreach ($errors as $e): ?><div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?= sanitize($e) ?></div><?php endforeach; ?>

<div style="display:grid;grid-template-columns:1fr 2fr;gap:1.5rem;align-items:start">
    <div class="card">
        <div class="card-header"><span class="card-title"><?= $editing ? 'Edit Banner Pop-up' : 'Tambah Banner Pop-up' ?></span></div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <?php if ($editing): ?><input type="hidden" name="id" value="<?= $editing['id'] ?>"><?php endif; ?>
                <div class="form-group">
                    <label>Judul Banner</label>
                    <input type="text" name="title" value="<?= sanitize($editing['title'] ?? '') ?>" placeholder="Opsional">
                </div>
                <div class="form-group">
                    <label>Link (Opsional)</label>
                    <input type="url" name="link" value="<?= sanitize($editing['link'] ?? '') ?>" placeholder="https://...">
                </div>
                <div class="form-group">
                    <label>Gambar Banner <?= $editing ? '(kosongkan jika tidak ingin ganti)' : '*' ?></label>
                    <?php if ($editing && $editing['image']): ?>
                    <img src="<?= SITE_URL ?>/assets/images/uploads/<?= sanitize($editing['image']) ?>" class="img-preview" id="bannerPreview" style="display:block">
                    <?php else: ?>
                    <img id="bannerPreview" class="img-preview" style="display:none">
                    <?php endif; ?>
                    <input type="file" name="image" accept="image/*" onchange="previewImage(this,'bannerPreview')">
                    <p class="form-hint">Rekomendasi: 800×600px, JPG/PNG/WebP</p>
                </div>
                <div style="display:flex;gap:0.5rem">
                    <button type="submit" class="btn btn-gold"><i class="fas fa-save"></i> Simpan</button>
                    <?php if ($editing): ?>
                    <a href="<?= SITE_URL ?>/admin/pages/popup-banners.php" class="btn btn-outline">Batal</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><span class="card-title">Daftar Banner Pop-up (<?= count($banners) ?>)</span></div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Preview</th><th>Judul</th><th>Link</th><th>Status</th><th>Aksi</th></tr></thead>
                <tbody>
                    <?php if (empty($banners)): ?>
                    <tr><td colspan="5" style="text-align:center;padding:2rem;color:var(--text-light)">Belum ada banner pop-up.</td></tr>
                    <?php else: ?>
                    <?php foreach ($banners as $b): ?>
                    <tr>
                        <td><img src="<?= SITE_URL ?>/assets/images/uploads/<?= sanitize($b['image']) ?>" style="width:80px;height:40px;object-fit:cover;border-radius:4px;border:1px solid #e0dbd0"></td>
                        <td><?= sanitize($b['title'] ?: '(Tanpa judul)') ?></td>
                        <td><?= sanitize($b['link'] ?: '-') ?></td>
                        <td><span class="badge <?= $b['is_active'] ? 'badge-success' : 'badge-danger' ?>"><?= $b['is_active'] ? 'Aktif' : 'Nonaktif' ?></span></td>
                        <td>
                            <div style="display:flex;gap:0.4rem">
                                <a href="?edit=<?= $b['id'] ?>" class="btn btn-sm btn-outline"><i class="fas fa-edit"></i></a>
                                <a href="?toggle=<?= $b['id'] ?>" class="btn btn-sm btn-outline"><i class="fas fa-toggle-<?= $b['is_active']?'on':'off' ?>"></i></a>
                                <a href="?delete=<?= $b['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus banner pop-up ini?')"><i class="fas fa-trash"></i></a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>