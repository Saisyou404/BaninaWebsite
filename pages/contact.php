<?php
require_once __DIR__ . '/../includes/config.php';
include __DIR__ . '/../includes/header.php';
$whatsapp = getSetting('whatsapp_number');
$address = getSetting('address');
$email = getSetting('email');
$instagram = getSetting('instagram');
$waGreeting = getSetting('whatsapp_greeting');
?>

<div class="page-header">
    <div class="container">
        <h1>Kontak Kami</h1>
        <p>Kami siap membantu Anda</p>
    </div>
</div>

<div class="container">
    <div class="contact-grid">
        <div class="contact-info fade-in">
            <span class="section-label">Hubungi Kami</span>
            <h2>Ada yang bisa kami bantu?</h2>
            <p>Jangan ragu untuk menghubungi kami. Tim BANINA siap melayani pertanyaan dan pesanan Anda.</p>

            <?php if ($whatsapp): ?>
            <div class="contact-item">
                <div class="contact-icon"><i class="fab fa-whatsapp"></i></div>
                <div>
                    <h4>WhatsApp</h4>
                    <p><?= sanitize($whatsapp) ?></p>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($address): ?>
            <div class="contact-item">
                <div class="contact-icon"><i class="fas fa-map-marker-alt"></i></div>
                <div>
                    <h4>Alamat</h4>
                    <p><?= sanitize($address) ?></p>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($email): ?>
            <div class="contact-item">
                <div class="contact-icon"><i class="fas fa-envelope"></i></div>
                <div>
                    <h4>Email</h4>
                    <p><?= sanitize($email) ?></p>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($instagram): ?>
            <div class="contact-item">
                <div class="contact-icon"><i class="fab fa-instagram"></i></div>
                <div>
                    <h4>Instagram</h4>
                    <p><?= sanitize($instagram) ?></p>
                </div>
            </div>
            <?php endif; ?>

            <div style="margin-top:2rem">
                <a href="https://wa.me/<?= sanitize($whatsapp) ?>?text=<?= urlencode($waGreeting) ?>"
                   class="btn-primary" target="_blank" style="display:inline-flex">
                    <i class="fab fa-whatsapp"></i> Chat via WhatsApp
                </a>
            </div>
        </div>

        <div class="fade-in">
            <div class="map-placeholder">
                <i class="fas fa-map-marker-alt"></i>
                <p><?= sanitize($address ?: 'Yogyakarta, Indonesia') ?></p>
            </div>

            <div style="margin-top:2rem;background:var(--cream);border-radius:14px;padding:2rem;border:1px solid #e8e0d0">
                <h3 style="font-family:'Playfair Display',serif;font-size:1.2rem;margin-bottom:0.5rem;color:var(--black)">Jam Operasional</h3>
                <table style="width:100%;font-size:0.88rem;color:var(--text-mid)">
                    <tr><td style="padding:0.4rem 0">Senin – Sabtu</td><td style="text-align:right;font-weight:600;color:var(--black)">08.00 – 21.00 WIB</td></tr>
                    <tr><td style="padding:0.4rem 0">Minggu</td><td style="text-align:right;font-weight:600;color:var(--black)">09.00 – 18.00 WIB</td></tr>
                    <tr><td style="padding:0.4rem 0;color:var(--gold)">WhatsApp 24 Jam</td><td style="text-align:right;font-weight:600;color:var(--gold)">Selalu aktif</td></tr>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>