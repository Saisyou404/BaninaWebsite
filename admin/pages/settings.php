<?php
$pageTitle = 'Pengaturan Toko';
require_once __DIR__ . '/../../includes/config.php';
$db = getDB();
$success = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fields = [
        'site_name', 'site_tagline', 'site_description', 'whatsapp_number',
        'whatsapp_greeting', 'address', 'email', 'instagram',
        'shopee', 'about_text', 'hero_title', 'hero_subtitle'
    ];
    foreach ($fields as $key) {
        $val = trim($_POST[$key] ?? '');
        $stmt = $db->prepare("INSERT INTO settings (`key`, value) VALUES (?,?) ON DUPLICATE KEY UPDATE value=?");
        $stmt->execute([$key, $val, $val]);
    }

    if (!empty($_FILES['logo']['tmp_name']) && $_FILES['logo']['error'] === 0) {
        $oldLogo = getSetting('logo');
        if ($oldLogo) { $f=UPLOAD_PATH.$oldLogo; if(file_exists($f)) unlink($f); }
        $logoPath = uploadImage($_FILES['logo'], 'logo/');
        if ($logoPath) {
            $db->prepare("INSERT INTO settings (`key`,value) VALUES ('logo',?) ON DUPLICATE KEY UPDATE value=?")->execute([$logoPath,$logoPath]);
        }
    }
    $success = 'Pengaturan berhasil disimpan!';
}

require_once __DIR__ . '/../includes/header.php';
?>

<?php if ($success): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $success ?></div><?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;align-items:start">

        <div class="card">
            <div class="card-header"><span class="card-title"><i class="fas fa-store"></i> Profil Toko</span></div>
            <div class="card-body">
                <div class="form-group">
                    <label>Nama Toko</label>
                    <input type="text" name="site_name" value="<?= sanitize(getSetting('site_name')) ?>">
                </div>
                <div class="form-group">
                    <label>Tagline</label>
                    <input type="text" name="site_tagline" value="<?= sanitize(getSetting('site_tagline')) ?>" placeholder="Men Wear Since 2019">
                </div>
                <div class="form-group">
                    <label>Deskripsi Singkat</label>
                    <textarea name="site_description"><?= sanitize(getSetting('site_description')) ?></textarea>
                </div>
                <div class="form-group">
                    <label>Tentang Kami (Halaman About)</label>
                    <textarea name="about_text" rows="5"><?= sanitize(getSetting('about_text')) ?></textarea>
                </div>
                <div class="form-group">
                    <label>Logo (Opsional)</label>
                    <?php $logo = getSetting('logo'); if ($logo): ?>
                    <img src="<?= UPLOAD_URL . sanitize($logo) ?>" style="height:50px;display:block;margin-bottom:0.5rem">
                    <?php endif; ?>
                    <input type="file" name="logo" accept="image/*">
                    <p class="form-hint">PNG transparan direkomendasikan. Kosongkan = tampilkan nama toko sebagai teks.</p>
                </div>
            </div>
        </div>

        <div>
            <div class="card" style="margin-bottom:1.5rem">
                <div class="card-header"><span class="card-title"><i class="fas fa-address-book"></i> Kontak & Media Sosial</span></div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Nomor WhatsApp</label>
                        <input type="text" name="whatsapp_number" value="<?= sanitize(getSetting('whatsapp_number')) ?>" placeholder="6281234567890">
                        <p class="form-hint">Format internasional tanpa + (contoh: 6281234567890)</p>
                    </div>
                    <div class="form-group">
                        <label>Pesan Sapaan WhatsApp Default</label>
                        <textarea name="whatsapp_greeting" rows="2"><?= sanitize(getSetting('whatsapp_greeting')) ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Alamat</label>
                        <textarea name="address" rows="2"><?= sanitize(getSetting('address')) ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" value="<?= sanitize(getSetting('email')) ?>">
                    </div>
                    <div class="form-group">
                        <label>Instagram</label>
                        <input type="text" name="instagram" value="<?= sanitize(getSetting('instagram')) ?>" placeholder="@banina.fact">
                    </div>
                    <div class="form-group">
                        <label>Link Shopee</label>
                        <input type="url" name="shopee" value="<?= sanitize(getSetting('shopee')) ?>" placeholder="https://shopee.co.id/...">
                    </div>
                </div>
            </div>

            <div class="card" style="margin-bottom:1.5rem">
                <div class="card-header"><span class="card-title"><i class="fas fa-image"></i> Teks Hero / Banner Utama</span></div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Judul Hero</label>
                        <input type="text" name="hero_title" value="<?= sanitize(getSetting('hero_title')) ?>">
                    </div>
                    <div class="form-group">
                        <label>Sub Judul Hero</label>
                        <input type="text" name="hero_subtitle" value="<?= sanitize(getSetting('hero_subtitle')) ?>">
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-gold" style="width:100%;justify-content:center;padding:0.85rem;font-size:1rem">
                <i class="fas fa-save"></i> Simpan Semua Pengaturan
            </button>
        </div>
    </div>
</form>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>