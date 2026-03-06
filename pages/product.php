<?php
require_once __DIR__ . '/../includes/config.php';
$db = getDB();

$slug = $_GET['slug'] ?? '';
if (!$slug) redirect(SITE_URL . '/pages/catalog.php');

$product = $db->prepare("
    SELECT p.*, c.name as cat_name, c.slug as cat_slug
    FROM products p
    LEFT JOIN categories c ON c.id = p.category_id
    WHERE p.slug = ? AND p.is_active = 1
");
$product->execute([$slug]);
$product = $product->fetch();
if (!$product) redirect(SITE_URL . '/pages/catalog.php');

$images = $db->prepare("SELECT * FROM product_images WHERE product_id = ? ORDER BY is_primary DESC, sort_order ASC");
$images->execute([$product['id']]);
$images = $images->fetchAll();

$related = $db->prepare("
    SELECT p.*,
        (SELECT image FROM product_images WHERE product_id=p.id AND is_primary=1 LIMIT 1) as primary_image,
        (SELECT image FROM product_images WHERE product_id=p.id ORDER BY sort_order ASC LIMIT 1) as first_image
    FROM products p
    WHERE p.category_id = ? AND p.id != ? AND p.is_active = 1
    ORDER BY RAND() LIMIT 4
");
$related->execute([$product['category_id'], $product['id']]);
$related = $related->fetchAll();

$whatsapp = getSetting('whatsapp_number');
$waMsg = urlencode($product['whatsapp_message'] ?: 'Halo BANINA, saya tertarik dengan produk ' . $product['name']);
$mainImg = $images ? $images[0]['image'] : null;

include __DIR__ . '/../includes/header.php';
?>

<div class="product-detail">
    <div class="container">
        <div class="detail-grid">
            <!-- Images -->
            <div class="detail-images">
                <div class="main-image">
                    <?php if ($mainImg): ?>
                    <img src="<?= UPLOAD_URL . sanitize($mainImg) ?>" alt="<?= sanitize($product['name']) ?>" id="mainImage">
                    <?php else: ?>
                    <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#f0ece4,#e8e0d0);font-size:5rem;color:var(--gold)">
                        <i class="fas fa-tshirt"></i>
                    </div>
                    <?php endif; ?>
                </div>
                <?php if (count($images) > 1): ?>
                <div class="thumb-row">
                    <?php foreach ($images as $i => $img): ?>
                    <div class="thumb <?= $i === 0 ? 'active' : '' ?>" data-src="<?= UPLOAD_URL . sanitize($img['image']) ?>">
                        <img src="<?= UPLOAD_URL . sanitize($img['image']) ?>" alt="">
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Info -->
            <div class="detail-info">
                <div class="breadcrumb">
                    <a href="<?= SITE_URL ?>/index.php">Beranda</a>
                    <i class="fas fa-chevron-right" style="font-size:0.65rem"></i>
                    <a href="<?= SITE_URL ?>/pages/catalog.php">Produk</a>
                    <?php if ($product['cat_name']): ?>
                    <i class="fas fa-chevron-right" style="font-size:0.65rem"></i>
                    <a href="<?= SITE_URL ?>/pages/catalog.php?category=<?= $product['cat_slug'] ?>"><?= sanitize($product['cat_name']) ?></a>
                    <?php endif; ?>
                </div>

                <?php if ($product['cat_name']): ?>
                <span class="detail-cat-badge"><?= sanitize($product['cat_name']) ?></span>
                <?php endif; ?>
                <?php if ($product['is_featured']): ?>
                <span class="detail-cat-badge" style="background:var(--gold);color:var(--black);border-color:var(--gold);margin-left:0.5rem">⭐ Unggulan</span>
                <?php endif; ?>

                <h1><?= sanitize($product['name']) ?></h1>

                <div class="detail-price">
                    <?= formatPrice($product['price_min']) ?>
                    <?php if ($product['price_max'] > $product['price_min']): ?>
                    <span style="font-size:1.1rem;color:var(--text-light);font-weight:400"> – <?= formatPrice($product['price_max']) ?></span>
                    <?php endif; ?>
                </div>

                <div class="detail-divider"></div>

                <?php if ($product['description']): ?>
                <div class="detail-desc"><?= nl2br(sanitize($product['description'])) ?></div>
                <?php endif; ?>

                <a href="https://wa.me/<?= sanitize($whatsapp) ?>?text=<?= $waMsg ?>"
                   class="detail-wa-btn" target="_blank">
                    <i class="fab fa-whatsapp"></i> Pesan via WhatsApp
                </a>

                <p style="font-size:0.78rem;color:var(--text-light);margin-top:1rem;text-align:center">
                    <i class="fas fa-shield-alt"></i> Klik tombol di atas untuk menghubungi kami langsung
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Related Products -->
<?php if (!empty($related)): ?>
<section class="section section-bg-dark">
    <div class="container">
        <div class="section-header fade-in">
            <span class="section-label">Kategori Sama</span>
            <h2 class="section-title">Produk Lainnya</h2>
            <div class="divider"><span class="divider-icon">✦</span></div>
        </div>
        <div class="products-grid">
            <?php foreach ($related as $prod):
                $rImg = $prod['primary_image'] ?: $prod['first_image'];
                $rWaMsg = urlencode('Halo BANINA, saya tertarik dengan produk ' . $prod['name']);
            ?>
            <div class="product-card fade-in">
                <a href="<?= SITE_URL ?>/pages/product.php?slug=<?= $prod['slug'] ?>" style="text-decoration:none;color:inherit;display:contents">
                    <div class="product-img-wrap">
                        <?php if ($rImg): ?>
                        <img src="<?= UPLOAD_URL . sanitize($rImg) ?>" alt="<?= sanitize($prod['name']) ?>" loading="lazy">
                        <?php else: ?>
                        <div class="product-placeholder"><i class="fas fa-tshirt"></i></div>
                        <?php endif; ?>
                    </div>
                    <div class="product-info">
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
                    <a href="https://wa.me/<?= sanitize($whatsapp) ?>?text=<?= $rWaMsg ?>" class="product-wa-btn" target="_blank">
                        <i class="fab fa-whatsapp"></i> Tanya via WhatsApp
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
