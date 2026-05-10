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
            <div style="border-radius:14px;overflow:hidden;border:1px solid var(--border-gold);box-shadow:0 8px 30px rgba(0,0,0,0.12);">
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3953.8868329685765!2d109.99815120000001!3d-7.695292600000001!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7aebeb6e5742c3%3A0xb31736b1a63696b9!2sOutlet%20Banina!5e0!3m2!1sid!2sid!4v1778405942350!5m2!1sid!2sid"
                    width="100%" height="380" style="border:0;display:block;" 
                    allowfullscreen="" loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
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