<?php
require_once __DIR__ . '/../includes/config.php';

if (isAdmin()) redirect(SITE_URL . '/admin/dashboard.php');

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM admins WHERE username = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            redirect(SITE_URL . '/admin/dashboard.php');
        } else {
            $error = 'Username atau password salah.';
        }
    } else {
        $error = 'Mohon isi semua field.';
    }
}

$siteName = getSetting('site_name');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - <?= sanitize($siteName) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --black:#0a0a0a; --black-light:#1e1e1e; --gold:#7a8c2a; --gold-light:#9aad3a; --border:rgba(122,140,42,0.25); }
        * { margin:0;padding:0;box-sizing:border-box; }
        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--black);
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
        }
        body::before {
            content: '';
            position: fixed; inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%237a8c2a' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
        .login-box {
            background: #fff; border-radius: 16px;
            padding: 3rem; width: 100%; max-width: 420px;
            position: relative;
            box-shadow: 0 30px 80px rgba(0,0,0,0.6);
            border: 1px solid rgba(201,151,42,0.15);
        }
        .login-logo { text-align: center; margin-bottom: 2rem; }
        .login-logo .icon {
            width: 64px; height: 64px;
            background: linear-gradient(135deg, var(--black), var(--black-light));
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1rem;
            color: var(--gold); font-size: 1.5rem;
            border: 1px solid var(--border);
        }
        .login-logo h1 { font-family: 'Playfair Display', serif; color: var(--black); font-size: 1.8rem; letter-spacing:0.06em; }
        .login-logo p { color: var(--gold); font-size: 0.7rem; letter-spacing: 0.2em; text-transform: uppercase; margin-top: 0.25rem; }
        .form-group { margin-bottom: 1.25rem; }
        label { display: block; font-size: 0.82rem; font-weight: 600; color: var(--black); margin-bottom: 0.4rem; letter-spacing: 0.05em; }
        input { width: 100%; padding: 0.75rem 1rem; border: 1.5px solid #e0dbd0; border-radius: 8px; font-size: 0.95rem; font-family: inherit; color: #333; transition: border-color 0.2s; outline: none; }
        input:focus { border-color: var(--gold); }
        .btn-login {
            width: 100%; padding: 0.85rem;
            background: linear-gradient(135deg, var(--black), var(--black-light));
            color: var(--gold-light); border: none; border-radius: 8px;
            font-size: 1rem; font-weight: 700; font-family: inherit;
            cursor: pointer; transition: opacity 0.2s;
            letter-spacing: 0.05em;
        }
        .btn-login:hover { opacity: 0.85; }
        .error-msg { background: #fee2e2; border: 1px solid #fecaca; color: #b91c1c; padding: 0.75rem 1rem; border-radius: 8px; font-size: 0.85rem; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem; }
        .back-link { display: block; text-align: center; margin-top: 1.5rem; color: #999; font-size: 0.82rem; text-decoration: none; }
        .back-link:hover { color: var(--gold); }
    </style>
</head>
<body>
    <div class="login-box">
        <div class="login-logo">
            <div class="icon"><i class="fas fa-lock"></i></div>
            <h1><?= sanitize($siteName) ?></h1>
            <p>Admin Panel</p>
        </div>

        <?php if ($error): ?>
        <div class="error-msg"><i class="fas fa-exclamation-circle"></i> <?= sanitize($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" value="<?= sanitize($_POST['username'] ?? '') ?>" autofocus required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn-login">Masuk ke Panel Admin</button>
        </form>

        <a href="<?= SITE_URL ?>/index.php" class="back-link"><i class="fas fa-arrow-left"></i> Kembali ke Website</a>
    </div>
</body>
</html>