<?php
require_once __DIR__ . '/../includes/config.php';
include __DIR__ . '/../includes/header.php';
$siteName = getSetting('site_name');
$whatsapp = getSetting('whatsapp_number');
$aboutText = getSetting('about_text');
?>

<div class="page-header">
    <div class="container">
        <h1>Tentang BANINA</h1>
        <p>Mengenal kami lebih dekat</p>
    </div>
</div>

<div class="container">
    <div class="about-grid fade-in">
        <div class="about-text">
            <span class="section-label">Kisah Kami</span>
            <h2>Busana Muslim Pria <em style="font-style:italic;color:var(--gold)">Premium</em> Sejak 2019</h2>
            <p><?= nl2br(sanitize($aboutText)) ?></p>
            <p>Kami berkomitmen untuk menghadirkan pilihan busana muslim pria berkualitas tinggi yang memadukan keindahan estetika dengan nilai-nilai islami, sehingga setiap pria bisa tampil percaya diri dan elegan.</p>
            <a href="https://wa.me/<?= sanitize($whatsapp) ?>" class="btn-primary" target="_blank" style="display:inline-flex;margin-top:1.5rem">
                <i class="fab fa-whatsapp"></i> Hubungi Kami
            </a>
        </div>
        <div class="about-img">
            <div style="background:linear-gradient(135deg,var(--black),var(--black-light));border-radius:14px;height:420px;display:flex;align-items:center;justify-content:center;color:var(--gold);font-size:5rem;border:1px solid rgba(122,140,42,0.3)">
                <i class="fas fa-store"></i>
            </div>
        </div>
    </div>
</div>

<!-- Values -->
<div style="background:var(--cream);padding:5rem 0;border-top:1px solid #e8e0d0;border-bottom:1px solid #e8e0d0">
    <div class="container">
        <div class="section-header fade-in">
            <span class="section-label">Nilai Kami</span>
            <h2 class="section-title">Komitmen BANINA</h2>
            <div class="divider"><span class="divider-icon">✦</span></div>
        </div>
        <div class="values-grid">
            <div class="value-card fade-in">
                <div class="value-icon"><i class="fas fa-award"></i></div>
                <h3>Kualitas Terjamin</h3>
                <p>Setiap produk melalui seleksi ketat untuk memastikan kualitas terbaik sampai ke tangan Anda</p>
            </div>
            <div class="value-card fade-in">
                <div class="value-icon"><i class="fas fa-mosque"></i></div>
                <h3>Islami & Elegan</h3>
                <p>Desain yang memadukan nilai kesopanan islami dengan tampilan modern dan stylish</p>
            </div>
            <div class="value-card fade-in">
                <div class="value-icon"><i class="fas fa-heart"></i></div>
                <h3>Kepuasan Pelanggan</h3>
                <p>Kepuasan Anda adalah prioritas utama kami dalam setiap pelayanan</p>
            </div>
            <div class="value-card fade-in">
                <div class="value-icon"><i class="fas fa-handshake"></i></div>
                <h3>Terpercaya</h3>
                <p>Telah melayani ribuan pelanggan setia sejak 2019 dengan reputasi terbaik</p>
            </div>
        </div>
    </div>
</div>

<!-- Stats -->
<div style="background:var(--black);padding:4rem 0;border-bottom:1px solid rgba(122,140,42,0.2)">
    <div class="container">
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:2rem;text-align:center">
            <div class="fade-in">
                <div style="font-family:'Playfair Display',serif;font-size:3rem;font-weight:700;color:var(--gold)">2019</div>
                <div style="color:rgba(255,255,255,0.6);font-size:0.88rem;margin-top:0.4rem;letter-spacing:0.1em;text-transform:uppercase">Berdiri Sejak</div>
            </div>
            <div class="fade-in">
                <div style="font-family:'Playfair Display',serif;font-size:3rem;font-weight:700;color:var(--gold)">5+</div>
                <div style="color:rgba(255,255,255,0.6);font-size:0.88rem;margin-top:0.4rem;letter-spacing:0.1em;text-transform:uppercase">Kategori Produk</div>
            </div>
            <div class="fade-in">
                <div style="font-family:'Playfair Display',serif;font-size:3rem;font-weight:700;color:var(--gold)">1000+</div>
                <div style="color:rgba(255,255,255,0.6);font-size:0.88rem;margin-top:0.4rem;letter-spacing:0.1em;text-transform:uppercase">Pelanggan Puas</div>
            </div>
            <div class="fade-in">
                <div style="font-family:'Playfair Display',serif;font-size:3rem;font-weight:700;color:var(--gold)">⭐ 4.9</div>
                <div style="color:rgba(255,255,255,0.6);font-size:0.88rem;margin-top:0.4rem;letter-spacing:0.1em;text-transform:uppercase">Rating Toko</div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>