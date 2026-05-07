<?php
$pageTitle = 'Ganti Password';
require_once __DIR__ . '/../../includes/config.php';
$db = getDB();
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $oldPass     = $_POST['old_password'] ?? '';
    $newPass     = $_POST['new_password'] ?? '';
    $confirmPass = $_POST['confirm_password'] ?? '';

    $admin = $db->prepare("SELECT * FROM admins WHERE id=?");
    $admin->execute([$_SESSION['admin_id']]);
    $admin = $admin->fetch();

    if (!password_verify($oldPass, $admin['password'])) {
        $errors[] = 'Password lama tidak benar.';
    } elseif (strlen($newPass) < 6) {
        $errors[] = 'Password baru minimal 6 karakter.';
    } elseif ($newPass !== $confirmPass) {
        $errors[] = 'Konfirmasi password tidak cocok.';
    } else {
        $hash = password_hash($newPass, PASSWORD_DEFAULT);
        $db->prepare("UPDATE admins SET password=? WHERE id=?")->execute([$hash, $_SESSION['admin_id']]);
        $success = 'Password berhasil diubah!';
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div style="max-width:500px">
    <?php if ($success): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $success ?></div><?php endif; ?>
    <?php foreach ($errors as $e): ?><div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?= sanitize($e) ?></div><?php endforeach; ?>

    <div class="card">
        <div class="card-header"><span class="card-title">Ganti Password Admin</span></div>
        <div class="card-body">
            <form method="POST">
                <div class="form-group">
                    <label>Password Lama</label>
                    <input type="password" name="old_password" required>
                </div>
                <div class="form-group">
                    <label>Password Baru</label>
                    <input type="password" name="new_password" required>
                    <p class="form-hint">Minimal 6 karakter</p>
                </div>
                <div class="form-group">
                    <label>Konfirmasi Password Baru</label>
                    <input type="password" name="confirm_password" required>
                </div>
                <button type="submit" class="btn btn-gold"><i class="fas fa-save"></i> Ubah Password</button>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>