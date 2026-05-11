<?php
require_once __DIR__ . '/../../includes/config.php';
if (!isAdmin()) redirect(SITE_URL . '/admin/login.php');

$siteName = getSetting('site_name');
$currentFile = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - <?= sanitize($siteName) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --sidebar-w: 250px;
            --black:     #0a0a0a;
            --black-mid: #141414;
            --black-light: #1e1e1e;
            --gold:      #7a8c2a !important;
            --gold-light: #9aad3a !important;
            --gold-dim:  rgba(122,140,42,0.15);
            --border:    #2e2e2e;
            --bg:        #f7f5f2;
            --white:     #fff;
            --text:      #1a1a1a;
            --text-light:#888;
            --success:   #16a34a;
            --danger:    #dc2626;
            --info:      #2563eb;
        }
        * { margin:0;padding:0;box-sizing:border-box; }
        body { font-family:'DM Sans',sans-serif;background:var(--bg);color:var(--text);display:flex;min-height:100vh; }

        /* SIDEBAR */
        .sidebar {
            width: var(--sidebar-w);
            background: var(--black);
            display: flex; flex-direction: column;
            position: fixed; top: 0; left: 0;
            height: 100vh; z-index: 100;
            overflow-y: auto;
            border-right: 1px solid var(--border);
        }
        .sidebar-brand {
            padding: 1.5rem 1.25rem;
            border-bottom: 1px solid var(--border);
        }
        .sidebar-brand h2 {
            font-family: 'Playfair Display', serif;
            color: var(--gold);
            font-size: 1.3rem;
            letter-spacing: 0.08em;
        }
        .sidebar-brand p { color: rgba(255,255,255,0.35); font-size: 0.68rem; letter-spacing: 0.2em; text-transform: uppercase; margin-top:0.25rem; }
        .sidebar-nav { flex: 1; padding: 1rem 0; }
        .nav-section-title {
            font-size: 0.62rem; font-weight: 700; letter-spacing: 0.2em;
            text-transform: uppercase; color: rgba(255,255,255,0.25);
            padding: 1rem 1.25rem 0.4rem;
        }
        .sidebar-nav a {
            display: flex; align-items: center; gap: 0.75rem;
            padding: 0.65rem 1.25rem;
            color: rgba(255,255,255,0.55);
            text-decoration: none; font-size: 0.875rem;
            transition: all 0.2s; border-left: 3px solid transparent;
        }
        .sidebar-nav a:hover { background: rgba(255,255,255,0.04); color: rgba(255,255,255,0.9); }
        .sidebar-nav a.active { background: var(--gold-dim); color: var(--gold-light); border-left-color: var(--gold); }
        .sidebar-nav a i { width: 16px; text-align: center; font-size: 0.85rem; }
        .sidebar-footer {
            padding: 1rem 1.25rem;
            border-top: 1px solid var(--border);
        }
        .sidebar-footer a {
            display: flex; align-items: center; gap: 0.5rem;
            color: rgba(255,255,255,0.4); text-decoration: none;
            font-size: 0.82rem; transition: color 0.2s;
        }
        .sidebar-footer a:hover { color: var(--gold-light); }

        /* MAIN */
        .main-content { margin-left: var(--sidebar-w); flex: 1; display: flex; flex-direction: column; }
        .topbar {
            background: #fff; border-bottom: 1px solid #e8e4dc;
            padding: 0.85rem 1.75rem;
            display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 50;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        }
        .topbar-title { font-weight: 600; color: var(--black); font-size: 1rem; }
        .topbar-user { display: flex; align-items: center; gap: 0.75rem; font-size: 0.85rem; color: var(--text-light); }
        .user-avatar {
            width: 32px; height: 32px;
            background: linear-gradient(135deg, var(--black), var(--black-light));
            border-radius: 50%; border: 1px solid var(--gold);
            display: flex; align-items: center; justify-content: center;
            color: var(--gold); font-size: 0.8rem;
        }
        .page-content { padding: 2rem; flex: 1; }

        /* CARDS */
        .card { background: #fff; border-radius: 12px; border: 1px solid #e8e4dc; box-shadow: 0 2px 10px rgba(0,0,0,0.04); overflow: hidden; }
        .card-header { padding: 1.25rem 1.5rem; border-bottom: 1px solid #e8e4dc; display: flex; align-items: center; justify-content: space-between; }
        .card-title { font-weight: 600; color: var(--black); font-size: 0.95rem; }
        .card-body { padding: 1.5rem; }

        /* STATS */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px,1fr)); gap: 1.25rem; margin-bottom: 2rem; }
        .stat-card { background: #fff; border-radius: 12px; padding: 1.25rem; border: 1px solid #e8e4dc; display: flex; align-items: center; gap: 1rem; }
        .stat-icon { width: 48px; height: 48px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0; }
        .stat-icon.black { background: rgba(10,10,10,0.08); color: var(--black); }
        .stat-icon.gold { background: rgba(122,140,42,0.1); color: var(--gold); }
        .stat-icon.green { background: rgba(22,163,74,0.1); color: var(--success); }
        .stat-icon.blue { background: rgba(37,99,235,0.1); color: var(--info); }
        .stat-number { font-size: 1.6rem; font-weight: 700; color: var(--black); line-height: 1; }
        .stat-label { font-size: 0.78rem; color: var(--text-light); margin-top: 0.2rem; }

        /* TABLE */
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: 0.875rem; }
        th { background: var(--bg); color: var(--text-light); font-size: 0.7rem; letter-spacing: 0.1em; text-transform: uppercase; padding: 0.75rem 1rem; text-align: left; font-weight: 600; }
        td { padding: 0.875rem 1rem; border-bottom: 1px solid #f0ece4; vertical-align: middle; }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: #faf8f5; }
        .product-thumb { width: 44px; height: 44px; border-radius: 6px; object-fit: cover; }
        .product-thumb-placeholder { width:44px;height:44px;border-radius:6px;background:#f0ece4;display:flex;align-items:center;justify-content:center;color:var(--gold); }

        /* BADGES */
        .badge { display:inline-block;padding:0.25rem 0.6rem;border-radius:50px;font-size:0.7rem;font-weight:600;letter-spacing:0.04em; }
        .badge-success { background:#dcfce7;color:#15803d; }
        .badge-danger { background:#fee2e2;color:#b91c1c; }
        .badge-gold { background:rgba(122,140,42,0.12);color:#5a6e10; }

        /* FORMS */
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; }
        .form-group.full { grid-column: 1/-1; }
        label { display:block;font-size:0.82rem;font-weight:600;color:var(--black);margin-bottom:0.4rem; }
        input[type=text], input[type=number], input[type=email], input[type=password], input[type=url], select, textarea {
            width:100%;padding:0.65rem 0.9rem;border:1.5px solid #e0dbd0;border-radius:8px;
            font-size:0.9rem;font-family:inherit;color:var(--text);outline:none;transition:border-color 0.2s;background: #fff;
        }
        input:focus, select:focus, textarea:focus { border-color:var(--gold); }
        textarea { min-height:100px;resize:vertical; }
        .form-hint { font-size:0.75rem;color:var(--text-light);margin-top:0.25rem; }
        .form-check { display:flex;align-items:center;gap:0.5rem;font-size:0.88rem;cursor:pointer; }
        input[type=checkbox] { width:auto;cursor:pointer; }

        /* BUTTONS */
        .btn { display:inline-flex;align-items:center;gap:0.4rem;padding:0.55rem 1.1rem;border-radius:8px;font-size:0.85rem;font-weight:500;font-family:inherit;cursor:pointer;border:none;text-decoration:none;transition:all 0.2s; }
        .btn-primary { background:var(--black);color:#fff; }
        .btn-primary:hover { background:var(--black-light);color:var(--gold-light); }
        .btn-gold { background:var(--gold);color:var(--black);font-weight:600; }
        .btn-gold:hover { background:var(--gold-light); }
        .btn-danger { background:var(--danger);color:#fff; }
        .btn-danger:hover { opacity:0.85; }
        .btn-outline { background:#fff;color:var(--black);border:1.5px solid #e0dbd0; }
        .btn-outline:hover { border-color:var(--gold);color:var(--gold); }
        .btn-sm { padding:0.35rem 0.7rem;font-size:0.78rem; }
        .btn-icon { width:32px;height:32px;padding:0;justify-content:center; }

        /* ALERTS */
        .alert { padding:0.85rem 1rem;border-radius:8px;font-size:0.875rem;margin-bottom:1.25rem;display:flex;align-items:center;gap:0.6rem; }
        .alert-success { background:#dcfce7;border:1px solid #bbf7d0;color:#15803d; }
        .alert-danger { background:#fee2e2;border:1px solid #fecaca;color:#b91c1c; }
        .alert-info { background:#dbeafe;border:1px solid #bfdbfe;color:#1d4ed8; }

        /* IMAGE */
        .img-preview { width:80px;height:80px;border-radius:8px;object-fit:cover;border:2px solid #e0dbd0;margin-top:0.5rem; }
        .img-thumb-row { display:flex;gap:0.5rem;flex-wrap:wrap;margin-top:0.75rem; }
        .img-thumb-item { position:relative; }
        .img-thumb-item img { width:70px;height:70px;border-radius:6px;object-fit:cover;border:2px solid #e0dbd0; }
        .img-thumb-del { position:absolute;top:-6px;right:-6px;background:var(--danger);color:#fff;border:none;border-radius:50%;width:18px;height:18px;font-size:0.6rem;cursor:pointer;display:flex;align-items:center;justify-content:center; }

        /* PAGINATION */
        .pagination { display:flex;gap:0.4rem;justify-content:center;margin-top:1.5rem; }
        .page-link { padding:0.4rem 0.75rem;border-radius:6px;text-decoration:none;font-size:0.82rem;color:var(--text-light);border:1px solid #e0dbd0; }
        .page-link.active, .page-link:hover { background:var(--black);color:var(--gold);border-color:var(--black); }

        /* AUTO DISMISS */
        .alert { transition: opacity 0.4s, transform 0.4s; }

        /* RESPONSIVE */
        @media (max-width:768px) {
            .sidebar { width:0;overflow:hidden; }
            .main-content { margin-left:0; }
            .form-grid { grid-template-columns:1fr; }
            .stats-grid { grid-template-columns:1fr 1fr; }
        }
    </style>
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar">
    <div class="sidebar-brand">
        <h2><?= sanitize($siteName) ?></h2>
        <p>Panel Admin</p>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section-title">Utama</div>
        <a href="<?= SITE_URL ?>/admin/dashboard.php" class="<?= $currentFile==='dashboard.php'?'active':'' ?>">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>

        <div class="nav-section-title">Konten</div>
        <a href="<?= SITE_URL ?>/admin/pages/banners.php" class="<?= $currentFile==='banners.php'?'active':'' ?>">
            <i class="fas fa-images"></i> Banner Hero
        </a>
        <a href="<?= SITE_URL ?>/admin/pages/popup-banners.php" class="<?= $currentFile==='popup-banners.php'?'active':'' ?>">
            <i class="fas fa-ad"></i> Banner Pop-up
        </a>
        <a href="<?= SITE_URL ?>/admin/pages/categories.php" class="<?= $currentFile==='categories.php'?'active':'' ?>">
            <i class="fas fa-tags"></i> Kategori
        </a>
        <a href="<?= SITE_URL ?>/admin/pages/products.php" class="<?= in_array($currentFile,['products.php','product-form.php'])?'active':'' ?>">
            <i class="fas fa-tshirt"></i> Produk
        </a>

        <div class="nav-section-title">Pengaturan</div>
        <a href="<?= SITE_URL ?>/admin/pages/settings.php" class="<?= $currentFile==='settings.php'?'active':'' ?>">
            <i class="fas fa-cog"></i> Profil & Info Toko
        </a>
        <a href="<?= SITE_URL ?>/admin/pages/change-password.php" class="<?= $currentFile==='change-password.php'?'active':'' ?>">
            <i class="fas fa-key"></i> Ganti Password
        </a>

        <div class="nav-section-title">Lihat</div>
        <a href="<?= SITE_URL ?>/index.php" target="_blank">
            <i class="fas fa-external-link-alt"></i> Lihat Website
        </a>
    </nav>

    <div class="sidebar-footer">
        <a href="<?= SITE_URL ?>/admin/logout.php">
            <i class="fas fa-sign-out-alt"></i> Logout (<?= sanitize($_SESSION['admin_username'] ?? '') ?>)
        </a>
    </div>
</aside>

<!-- MAIN CONTENT -->
<div class="main-content">
    <div class="topbar">
        <span class="topbar-title"><?= $pageTitle ?? 'Dashboard' ?></span>
        <div class="topbar-user">
            <span><?= sanitize($_SESSION['admin_username'] ?? '') ?></span>
            <div class="user-avatar"><i class="fas fa-user"></i></div>
        </div>
    </div>
    <div class="page-content">