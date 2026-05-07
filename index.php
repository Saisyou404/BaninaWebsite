<?php
require_once __DIR__ . '/includes/config.php';
$db = getDB();

$banners = $db->query("SELECT * FROM banners WHERE is_active=1 ORDER BY sort_order ASC")->fetchAll();
$categories = $db->query("
    SELECT c.*, 
        (SELECT COUNT(*) FROM products p WHERE p.category_id = c.id AND p.is_active = 1) as product_count
    FROM categories c
    WHERE c.is_active = 1
    ORDER BY c.sort_order ASC
")->fetchAll();
$featured = $db->query("
    SELECT p.*, c.name as cat_name,
        (SELECT image FROM product_images WHERE product_id=p.id AND is_primary=1 LIMIT 1) as primary_image,
        (SELECT image FROM product_images WHERE product_id=p.id ORDER BY sort_order ASC LIMIT 1) as first_image
    FROM products p
    LEFT JOIN categories c ON c.id = p.category_id
    WHERE p.is_featured = 1 AND p.is_active = 1
    ORDER BY p.sort_order ASC LIMIT 8
")->fetchAll();

$whatsapp = getSetting('whatsapp_number');
$heroTitle = getSetting('hero_title');
$heroSubtitle = getSetting('hero_subtitle');

include __DIR__ . '/includes/header.php';
?>

<style>
/* ===== HERO ELEGANT SLIDER ===== */
.hero-elegant {
    position: relative;
    height: 100vh;
    min-height: 600px;
    overflow: hidden;
    background: #0a0a0a;
}

.hero-slide-elegant {
    position: absolute;
    inset: 0;
    opacity: 0;
    transition: opacity 1.2s ease;
    z-index: 1;
}
.hero-slide-elegant.active {
    opacity: 1;
    z-index: 2;
}

.hero-slide-elegant img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transform: scale(1.08);
    transition: transform 7s ease;
}
.hero-slide-elegant.active img {
    transform: scale(1);
}

/* Multi-layer overlay */
.hero-overlay-elegant {
    position: absolute;
    inset: 0;
    background:
        linear-gradient(to right, rgba(0,0,0,0.82) 0%, rgba(0,0,0,0.45) 55%, rgba(0,0,0,0.15) 100%),
        linear-gradient(to top, rgba(0,0,0,0.6) 0%, transparent 50%);
    z-index: 3;
}

/* No banner fallback */
.hero-no-img {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 50%, #111800 100%);
    z-index: 1;
}
.hero-no-img::before {
    content: '';
    position: absolute;
    inset: 0;
    background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%237a8c2a' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}

/* Content */
.hero-content-elegant {
    position: absolute;
    inset: 0;
    z-index: 4;
    display: flex;
    align-items: center;
}
.hero-content-elegant .container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 3rem;
}

.hero-eyebrow {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1.5rem;
    opacity: 0;
    transform: translateY(20px);
    transition: opacity 0.8s ease 0.3s, transform 0.8s ease 0.3s;
}
.hero-slide-elegant.active .hero-eyebrow { opacity: 1; transform: translateY(0); }

.hero-eyebrow-line {
    width: 40px;
    height: 1px;
    background: var(--gold);
}
.hero-eyebrow-text {
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.3em;
    text-transform: uppercase;
    color: var(--gold);
}

.hero-title-elegant {
    font-family: 'Playfair Display', serif;
    font-size: clamp(2.8rem, 6vw, 5rem);
    font-weight: 700;
    color: #fff;
    line-height: 1.1;
    margin-bottom: 1.25rem;
    opacity: 0;
    transform: translateY(30px);
    transition: opacity 0.9s ease 0.5s, transform 0.9s ease 0.5s;
}
.hero-slide-elegant.active .hero-title-elegant { opacity: 1; transform: translateY(0); }
.hero-title-elegant em {
    font-style: italic;
    color: var(--gold-light);
    display: block;
}

.hero-subtitle-elegant {
    font-family: 'Cormorant Garamond', serif;
    font-size: 1.2rem;
    color: rgba(255,255,255,0.7);
    margin-bottom: 2.5rem;
    max-width: 480px;
    line-height: 1.7;
    opacity: 0;
    transform: translateY(20px);
    transition: opacity 0.8s ease 0.7s, transform 0.8s ease 0.7s;
}
.hero-slide-elegant.active .hero-subtitle-elegant { opacity: 1; transform: translateY(0); }

.hero-actions-elegant {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
    opacity: 0;
    transform: translateY(20px);
    transition: opacity 0.8s ease 0.9s, transform 0.8s ease 0.9s;
}
.hero-slide-elegant.active .hero-actions-elegant { opacity: 1; transform: translateY(0); }

.btn-hero-primary {
    background: var(--gold);
    color: #0a0a0a;
    padding: 0.9rem 2.2rem;
    border-radius: 2px;
    text-decoration: none;
    font-weight: 700;
    font-size: 0.88rem;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
    border: 2px solid var(--gold);
}
.btn-hero-primary:hover {
    background: transparent;
    color: var(--gold);
}

.btn-hero-outline {
    border: 2px solid rgba(255,255,255,0.4);
    color: #fff;
    padding: 0.9rem 2.2rem;
    border-radius: 2px;
    text-decoration: none;
    font-weight: 500;
    font-size: 0.88rem;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
}
.btn-hero-outline:hover {
    border-color: var(--gold);
    color: var(--gold);
}

/* Progress bar navigation */
.hero-nav {
    position: absolute;
    bottom: 3rem;
    left: 3rem;
    z-index: 5;
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.hero-nav-item {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    cursor: pointer;
    opacity: 0.4;
    transition: opacity 0.3s;
    background: none;
    border: none;
    padding: 0;
}
.hero-nav-item.active { opacity: 1; }
.hero-nav-item:hover { opacity: 0.8; }

.hero-nav-number {
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.15em;
    color: #fff;
}
.hero-nav-bar {
    width: 40px;
    height: 2px;
    background: rgba(255,255,255,0.3);
    border-radius: 2px;
    overflow: hidden;
    position: relative;
}
.hero-nav-progress {
    position: absolute;
    inset: 0;
    background: var(--gold);
    transform: scaleX(0);
    transform-origin: left;
    border-radius: 2px;
}
.hero-nav-item.active .hero-nav-progress {
    animation: progress-bar 5s linear forwards;
}
@keyframes progress-bar {
    from { transform: scaleX(0); }
    to { transform: scaleX(1); }
}

/* Slide counter */
.hero-counter {
    position: absolute;
    bottom: 3rem;
    right: 3rem;
    z-index: 5;
    color: rgba(255,255,255,0.5);
    font-size: 0.78rem;
    letter-spacing: 0.1em;
}
.hero-counter span { color: #fff; font-weight: 600; }

/* Scroll indicator */
.hero-scroll {
    position: absolute;
    bottom: 2rem;
    left: 50%;
    transform: translateX(-50%);
    z-index: 5;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    color: rgba(255,255,255,0.4);
    font-size: 0.65rem;
    letter-spacing: 0.2em;
    text-transform: uppercase;
    animation: bounce 2s infinite;
}
.hero-scroll-line {
    width: 1px;
    height: 40px;
    background: linear-gradient(to bottom, rgba(255,255,255,0.4), transparent);
}
@keyframes bounce {
    0%, 100% { transform: translateX(-50%) translateY(0); }
    50% { transform: translateX(-50%) translateY(6px); }
}

@media (max-width: 768px) {
    .hero-elegant { height: 85vh; }
    .hero-content-elegant .container { padding: 0 1.5rem; }
    .hero-title-elegant { font-size: 2.2rem; }
    .hero-nav { bottom: 1.5rem; left: 1.5rem; }
    .hero-counter { bottom: 1.5rem; right: 1.5rem; }
    .hero-scroll { display: none; }
}
</style>

<!-- ===== HERO ELEGANT ===== -->
<section class="hero-elegant">

    <?php if (!empty($banners)): ?>
        <!-- Hanya gambar yang slide -->
        <?php foreach ($banners as $i => $banner): ?>
        <div class="hero-slide-elegant <?= $i === 0 ? 'active' : '' ?>" data-index="<?= $i ?>">
            <img src="<?= UPLOAD_URL . sanitize($banner['image']) ?>" alt="<?= sanitize($banner['title']) ?>">
        </div>
        <?php endforeach; ?>

        <!-- Overlay tetap diam -->
        <div class="hero-overlay-elegant"></div>

        <!-- Teks & tombol tetap diam, tidak ikut slide -->
        <div class="hero-content-elegant">
            <div class="container">
                <div class="hero-eyebrow" style="opacity:1;transform:none">
                    <div class="hero-eyebrow-line"></div>
                    <span class="hero-eyebrow-text">Men Wear Since 2019</span>
                </div>
                <h1 class="hero-title-elegant" style="opacity:1;transform:none">
                    <?= sanitize($heroTitle) ?>
                    <em>BANINA</em>
                </h1>
                <p class="hero-subtitle-elegant" style="opacity:1;transform:none"><?= sanitize($heroSubtitle) ?></p>
                <div class="hero-actions-elegant" style="opacity:1;transform:none">
                    <a href="<?= SITE_URL ?>/pages/catalog.php" class="btn-hero-primary">
                        <i class="fas fa-th-large"></i> Lihat Koleksi
                    </a>
                    <a href="https://wa.me/<?= sanitize($whatsapp) ?>" class="btn-hero-outline" target="_blank">
                        <i class="fab fa-whatsapp"></i> Tanya via WA
                    </a>
                </div>
            </div>
        </div>

        <?php if (count($banners) > 1): ?>
        <div class="hero-nav">
            <?php foreach ($banners as $i => $b): ?>
            <button class="hero-nav-item <?= $i === 0 ? 'active' : '' ?>" data-slide="<?= $i ?>">
                <span class="hero-nav-number">0<?= $i + 1 ?></span>
                <div class="hero-nav-bar"><div class="hero-nav-progress"></div></div>
            </button>
            <?php endforeach; ?>
        </div>
        <div class="hero-counter">
            <span id="currentSlide">01</span> / <?= str_pad(count($banners), 2, '0', STR_PAD_LEFT) ?>
        </div>
        <?php endif; ?>

    <?php else: ?>
        <div class="hero-no-img"></div>
        <div class="hero-overlay-elegant" style="z-index:2"></div>
        <div class="hero-content-elegant">
            <div class="container">
                <div class="hero-eyebrow" style="opacity:1;transform:none">
                    <div class="hero-eyebrow-line"></div>
                    <span class="hero-eyebrow-text">Men Wear Since 2019</span>
                </div>
                <h1 class="hero-title-elegant" style="opacity:1;transform:none">
                    <?= sanitize($heroTitle) ?>
                    <em>BANINA</em>
                </h1>
                <p class="hero-subtitle-elegant" style="opacity:1;transform:none"><?= sanitize($heroSubtitle) ?></p>
                <div class="hero-actions-elegant" style="opacity:1;transform:none">
                    <a href="<?= SITE_URL ?>/pages/catalog.php" class="btn-hero-primary">
                        <i class="fas fa-th-large"></i> Lihat Koleksi
                    </a>
                    <a href="https://wa.me/<?= sanitize($whatsapp) ?>" class="btn-hero-outline" target="_blank">
                        <i class="fab fa-whatsapp"></i> Tanya via WA
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="hero-scroll">
        <div class="hero-scroll-line"></div>
        <span>Scroll</span>
    </div>
</section>

<script>
(function(){
    const slides = document.querySelectorAll('.hero-slide-elegant');
    const navItems = document.querySelectorAll('.hero-nav-item');
    const counter = document.getElementById('currentSlide');
    if (!slides.length || slides.length < 2) return;

    let current = 0;
    let timer;

    function goTo(n) {
        slides[current].classList.remove('active');
        navItems[current]?.classList.remove('active');
        current = (n + slides.length) % slides.length;
        slides[current].classList.add('active');
        if (navItems[current]) navItems[current].classList.add('active');
        if (counter) counter.textContent = String(current + 1).padStart(2, '0');
    }

    function next() { goTo(current + 1); }

    function startTimer() {
        clearInterval(timer);
        timer = setInterval(next, 5000);
    }

    navItems.forEach((btn, i) => {
        btn.addEventListener('click', () => { goTo(i); startTimer(); });
    });

    startTimer();
})();
</script>

<!-- ===== FEATURED PRODUCTS ===== -->
<?php if (!empty($featured)): ?>
<section class="section section-bg-dark">
    <div class="container">
        <div class="section-header fade-in">
            <span class="section-label">Pilihan Terbaik</span>
            <h2 class="section-title">Produk Unggulan</h2>
            <div class="divider"><span class="divider-icon">✦</span></div>
            <p class="section-subtitle">Koleksi terpilih yang paling diminati pelanggan kami</p>
        </div>
        <div class="products-grid">
            <?php foreach ($featured as $prod):
                $img = $prod['primary_image'] ?: $prod['first_image'];
                $waMsg = urlencode($prod['whatsapp_message'] ?: 'Halo BANINA, saya tertarik dengan produk ' . $prod['name']);
            ?>
            <div class="product-card fade-in">
                <a href="<?= SITE_URL ?>/pages/product.php?slug=<?= $prod['slug'] ?>" style="text-decoration:none;color:inherit;display:contents">
                    <div class="product-img-wrap">
                        <?php if ($img): ?>
                        <img src="<?= UPLOAD_URL . sanitize($img) ?>" alt="<?= sanitize($prod['name']) ?>" loading="lazy">
                        <?php else: ?>
                        <div class="product-placeholder"><i class="fas fa-tshirt"></i></div>
                        <?php endif; ?>
                        <span class="product-badge">Unggulan</span>
                    </div>
                    <div class="product-info">
                        <div class="product-cat"><?= sanitize($prod['cat_name'] ?? '') ?></div>
                        <div class="product-name"><?= sanitize($prod['name']) ?></div>
                        <div class="product-price">
                            <?= formatPrice($prod['price_min']) ?>
                            <?php if ($prod['price_max'] > $prod['price_min']): ?>
                            <span>– <?= formatPrice($prod['price_max']) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </a>
                <div style="padding:0 1.25rem 1.25rem">
                    <a href="https://id.shp.ee/y9timn2w" class="product-wa-btn shopee-btn" target="_blank" rel="noopener">
                        <i class="fas fa-shopping-bag"></i> Beli di Shopee
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div style="text-align:center;margin-top:3rem">
            <a href="<?= SITE_URL ?>/pages/catalog.php" class="btn-primary">
                Lihat Semua Produk <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ===== CATEGORIES CIRCLE ===== -->
<?php if (!empty($categories)): ?>
<section class="section">
    <div class="container">
        <div class="section-header fade-in">
            <span class="section-label">Koleksi BANINA</span>
            <h2 class="section-title">Jelajahi Kategori</h2>
            <div class="divider"><span class="divider-icon">✦</span></div>
            <p class="section-subtitle">Pilih kategori untuk menemukan koleksi busana muslim pria pilihan</p>
        </div>

        <!-- Tombol Tampilkan Semua -->
        <div style="text-align:center;margin-bottom:2rem" class="fade-in">
            <a href="<?= SITE_URL ?>/pages/catalog.php" class="btn-cat-all active-cat" id="btnTampilSemua">
                Tampilkan Semua
            </a>
        </div>

        <!-- Circle Category Row -->
        <div class="cat-circle-row fade-in">
            <?php
            $icons = ['songkok'=>'fa-hat-cowboy','kemeja'=>'fa-shirt','sarung'=>'fa-scroll','celana'=>'fa-person','sajadah'=>'fa-mosque'];
            foreach ($categories as $cat):
                $icon = $icons[$cat['slug']] ?? 'fa-tshirt';
            ?>
            <a href="<?= SITE_URL ?>/pages/catalog.php?category=<?= $cat['slug'] ?>" class="cat-circle-item">
                <div class="cat-circle-img">
                    <?php if (!empty($cat['image'])): ?>
                    <img src="<?= UPLOAD_URL . sanitize($cat['image']) ?>" alt="<?= sanitize($cat['name']) ?>">
                    <?php else: ?>
                    <div class="cat-circle-placeholder">
                        <i class="fas <?= $icon ?>"></i>
                    </div>
                    <?php endif; ?>
                </div>
                <span class="cat-circle-label"><?= strtoupper(sanitize($cat['name'])) ?></span>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ===== WHY BANINA ===== -->
<section class="section">
    <div class="container">
        <div class="section-header fade-in">
            <span class="section-label">Keunggulan Kami</span>
            <h2 class="section-title">Mengapa Memilih BANINA?</h2>
            <div class="divider"><span class="divider-icon">✦</span></div>
        </div>
        <div class="values-grid">
            <div class="value-card fade-in">
                <div class="value-icon"><i class="fas fa-award"></i></div>
                <h3>Kualitas Premium</h3>
                <p>Bahan pilihan berkualitas tinggi untuk kenyamanan dan ketahanan produk terbaik</p>
            </div>
            <div class="value-card fade-in">
                <div class="value-icon"><i class="fas fa-mosque"></i></div>
                <h3>Sesuai Syariat</h3>
                <p>Desain elegan yang tetap memenuhi ketentuan busana muslim yang baik</p>
            </div>
            <div class="value-card fade-in">
                <div class="value-icon"><i class="fas fa-palette"></i></div>
                <h3>Desain Modern</h3>
                <p>Koleksi up-to-date mengikuti tren fashion muslim pria masa kini</p>
            </div>
            <div class="value-card fade-in">
                <div class="value-icon"><i class="fas fa-shipping-fast"></i></div>
                <h3>Layanan Cepat</h3>
                <p>Pemesanan mudah via WhatsApp dan pengiriman ke seluruh Indonesia</p>
            </div>
        </div>
    </div>
</section>

<!-- ===== CTA SECTION ===== -->
<section style="background:linear-gradient(135deg,var(--black) 0%,#111800 100%);padding:5rem 0;text-align:center;position:relative;overflow:hidden;border-top:1px solid rgba(122,140,42,0.2)">
    <div style="position:absolute;inset:0;background-image:url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%237a8c2a\' fill-opacity=\'0.06\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')"></div>
    <div class="container fade-in" style="position:relative;z-index:1">
        <span class="section-label">Butuh Bantuan?</span>
        <h2 style="font-family:'Playfair Display',serif;font-size:2.5rem;color:#fff;margin:0.75rem 0 1rem">Konsultasi Gratis via WhatsApp</h2>
        <p style="color:rgba(255,255,255,0.6);font-family:'Cormorant Garamond',serif;font-size:1.1rem;margin-bottom:2rem">Tim BANINA siap membantu Anda memilih busana muslim yang tepat</p>
        <a href="https://wa.me/<?= sanitize($whatsapp) ?>?text=<?= urlencode(getSetting('whatsapp_greeting')) ?>"
           class="btn-primary" target="_blank" style="font-size:1.1rem;padding:1rem 2.5rem">
            <i class="fab fa-whatsapp"></i> Chat Sekarang
        </a>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>