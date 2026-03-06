# 👘 BANINA - Website Toko Busana Muslim Pria

Website katalog busana muslim pria dinamis dengan panel admin lengkap.
Tema: **Hitam + Emas (Premium)** 🖤✨ | Men Wear Since 2019

---

## 📁 Struktur Folder

```
banina-website/
├── index.php                  ← Halaman beranda
├── database.sql               ← File setup database
├── includes/
│   ├── config.php             ← Konfigurasi database & fungsi
│   ├── header.php             ← Header/navbar publik
│   └── footer.php             ← Footer publik
├── pages/
│   ├── catalog.php            ← Halaman katalog + filter
│   ├── product.php            ← Detail produk
│   ├── about.php              ← Tentang kami
│   └── contact.php            ← Halaman kontak
├── admin/
│   ├── login.php              ← Login admin
│   ├── logout.php             ← Logout
│   ├── dashboard.php          ← Dashboard admin
│   ├── includes/
│   │   ├── header.php         ← Header admin panel
│   │   └── footer.php         ← Footer admin panel
│   └── pages/
│       ├── products.php       ← Kelola produk
│       ├── product-form.php   ← Form tambah/edit produk
│       ├── categories.php     ← Kelola kategori
│       ├── banners.php        ← Kelola banner
│       ├── settings.php       ← Pengaturan toko
│       └── change-password.php← Ganti password
└── assets/
    ├── css/style.css          ← Stylesheet utama (tema hitam + emas)
    ├── js/main.js             ← JavaScript
    └── images/uploads/        ← Folder upload gambar
```

---

## ⚙️ Cara Instalasi

### 1. Persiapan Server
- PHP 7.4+ (atau PHP 8.x)
- MySQL 5.7+ / MariaDB
- Web server: Apache/Nginx (XAMPP/Laragon/cPanel)

### 2. Setup Database

Buka **phpMyAdmin** atau MySQL CLI, lalu jalankan:
```sql
SOURCE /path/to/banina-website/database.sql;
```
Atau: phpMyAdmin → **Import** → pilih `database.sql` → klik Go.

### 3. Konfigurasi

Edit file `includes/config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'banina_store');
define('SITE_URL', 'http://localhost/banina-website');
```

### 4. Permission Folder Upload
```bash
chmod -R 775 assets/images/uploads/
```

### 5. Akses Website

| Halaman      | URL |
|-------------|-----|
| Beranda      | `http://localhost/banina-website/` |
| Katalog      | `http://localhost/banina-website/pages/catalog.php` |
| Admin Login  | `http://localhost/banina-website/admin/login.php` |

---

## 🔐 Login Admin Default

| | |
|---|---|
| **Username** | `admin` |
| **Password** | `admin123` |

> ⚠️ **Segera ganti password** setelah login pertama kali!

---

## 🗂️ Kategori Default

| Kategori  | Slug       | Keterangan |
|-----------|------------|------------|
| Songkok   | `songkok`  | Songkok premium berbagai model |
| Kemeja    | `kemeja`   | Kemeja muslim pria |
| Sarung    | `sarung`   | Sarung premium |
| Celana    | `celana`   | Celana muslim pria |
| Sajadah   | `sajadah`  | Sajadah lembut tebal |

Kategori bisa ditambah/edit/hapus dari panel admin.

---

## ✨ Fitur Website

### Halaman Publik
- 🏠 **Beranda** — Banner slider, kategori, produk unggulan, CTA WhatsApp
- 📋 **Katalog** — Semua produk dengan filter kategori & pencarian
- 🔍 **Detail Produk** — Gambar produk, deskripsi, harga, tombol WA
- 📖 **Tentang Kami** — Info toko, nilai, dan statistik brand
- 📞 **Kontak** — Info kontak lengkap + jam operasional
- 💬 **Tombol WA Float** — Tombol WhatsApp mengambang di semua halaman

### Panel Admin
- 📊 **Dashboard** — Statistik & produk terbaru
- 🖼️ **Kelola Banner** — Upload, edit, aktif/nonaktif banner
- 🏷️ **Kelola Kategori** — Tambah/edit/hapus kategori + gambar
- 👔 **Kelola Produk** — CRUD produk + multi-upload gambar + tandai unggulan
- ⚙️ **Pengaturan** — Edit nama toko, WA, alamat, media sosial, Shopee, teks hero
- 🔑 **Ganti Password** — Ubah password admin

---

## 🎨 Tema & Kustomisasi

Tema: **Hitam + Emas (Premium)**

Edit variabel CSS di `assets/css/style.css`:
```css
:root {
    --black:     #0a0a0a;    /* Hitam utama */
    --gold:      #c9972a;    /* Emas aksen */
    --gold-light:#e8b84b;    /* Emas terang */
}
```

---

## 🛠️ Tech Stack

- **Backend**: PHP 7.4+ (Native, tanpa framework)
- **Database**: MySQL dengan PDO
- **Frontend**: HTML5, CSS3, Vanilla JS
- **Icons**: Font Awesome 6
- **Fonts**: Playfair Display, Cormorant Garamond, DM Sans

---

> Dibuat untuk **BANINA Men Wear** 🖤 — Men Wear Since 2019
