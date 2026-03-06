<?php
$siteName = getSetting('site_name');
$instagram = getSetting('instagram');
$facebook = getSetting('facebook');
$whatsapp = getSetting('whatsapp_number');
$address = getSetting('address');
$email = getSetting('email');
$shopee = getSetting('shopee');
$db = getDB();
$categories = $db->query("SELECT * FROM categories WHERE is_active=1 ORDER BY sort_order ASC")->fetchAll();
?>

<!-- WhatsApp Float Button -->
<a href="https://wa.me/<?= sanitize($whatsapp) ?>?text=<?= urlencode(getSetting('whatsapp_greeting')) ?>"
   class="wa-float" target="_blank" title="Chat WhatsApp">
    <i class="fab fa-whatsapp"></i>
</a>

<!-- Footer -->
<footer class="footer">
    <div class="footer-pattern"></div>
    <div class="container">
        <div class="footer-grid">
            <div class="footer-brand">
                <h3><?= sanitize($siteName) ?></h3>
                <p><?= sanitize(getSetting('site_description')) ?></p>
                <div class="social-links">
                    <?php if ($instagram): ?>
                    <a href="https://instagram.com/<?= ltrim(sanitize($instagram), '@') ?>" target="_blank" title="Instagram"><i class="fab fa-instagram"></i></a>
                    <?php endif; ?>
                    <?php if ($facebook): ?>
                    <a href="https://facebook.com/<?= sanitize($facebook) ?>" target="_blank" title="Facebook"><i class="fab fa-facebook"></i></a>
                    <?php endif; ?>
                    <?php if ($shopee): ?>
                    <a href="<?= sanitize($shopee) ?>" target="_blank" title="Shopee"><i class="fas fa-store"></i></a>
                    <?php endif; ?>
                    <a href="https://wa.me/<?= sanitize($whatsapp) ?>" target="_blank" title="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                </div>
            </div>

            <div class="footer-links">
                <h4>Kategori</h4>
                <ul>
                    <?php foreach ($categories as $cat): ?>
                    <li><a href="<?= SITE_URL ?>/pages/catalog.php?category=<?= $cat['slug'] ?>"><?= sanitize($cat['name']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="footer-links">
                <h4>Informasi</h4>
                <ul>
                    <li><a href="<?= SITE_URL ?>/index.php">Beranda</a></li>
                    <li><a href="<?= SITE_URL ?>/pages/catalog.php">Katalog</a></li>
                    <li><a href="<?= SITE_URL ?>/pages/about.php">Tentang Kami</a></li>
                    <li><a href="<?= SITE_URL ?>/pages/contact.php">Kontak</a></li>
                </ul>
            </div>

            <div class="footer-contact">
                <h4>Kontak</h4>
                <?php if ($address): ?><p><i class="fas fa-map-marker-alt"></i> <?= sanitize($address) ?></p><?php endif; ?>
                <?php if ($email): ?><p><i class="fas fa-envelope"></i> <?= sanitize($email) ?></p><?php endif; ?>
                <?php if ($whatsapp): ?><p><i class="fab fa-whatsapp"></i> <?= sanitize($whatsapp) ?></p><?php endif; ?>
                <?php if ($instagram): ?><p><i class="fab fa-instagram"></i> <?= sanitize($instagram) ?></p><?php endif; ?>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> <?= sanitize($siteName) ?>. All rights reserved. | Men Wear Since 2019</p>
        </div>
    </div>
</footer>

<script src="<?= SITE_URL ?>/assets/js/main.js"></script>
</body>
</html>
