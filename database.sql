-- =============================================
-- DATABASE SETUP: banina_store
-- Jalankan file ini di phpMyAdmin atau MySQL CLI
-- =============================================

CREATE DATABASE IF NOT EXISTS banina_store CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE banina_store;

-- Tabel Settings
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    `key` VARCHAR(100) NOT NULL UNIQUE,
    value TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel Admin
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Kategori
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    image VARCHAR(255),
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Produk
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    name VARCHAR(200) NOT NULL,
    slug VARCHAR(200) NOT NULL UNIQUE,
    description TEXT,
    price_min DECIMAL(15,2) DEFAULT 0,
    price_max DECIMAL(15,2) DEFAULT 0,
    whatsapp_message TEXT,
    is_featured TINYINT(1) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Tabel Gambar Produk
CREATE TABLE IF NOT EXISTS product_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    image VARCHAR(255) NOT NULL,
    is_primary TINYINT(1) DEFAULT 0,
    sort_order INT DEFAULT 0,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Tabel Banner
CREATE TABLE IF NOT EXISTS banners (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200),
    subtitle VARCHAR(300),
    image VARCHAR(255) NOT NULL,
    link VARCHAR(255),
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Variant Produk (Ukuran)
CREATE TABLE IF NOT EXISTS product_variants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    size VARCHAR(50) NOT NULL,
    sku VARCHAR(100),
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_product_size (product_id, size)
);

-- =============================================
-- DATA AWAL (Seed Data)
-- =============================================

-- Admin default: username=admin, password=admin123
INSERT INTO admins (username, password) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Settings default BANINA
INSERT INTO settings (`key`, value) VALUES
('site_name', 'BANINA'),
('site_tagline', 'Men Wear Since 2019'),
('site_description', 'Koleksi busana muslim pria premium berkualitas tinggi. Songkok, kemeja, sarung, celana, dan sajadah pilihan terbaik untuk tampil elegan.'),
('whatsapp_number', '6281234567890'),
('whatsapp_greeting', 'Halo BANINA, saya ingin menanyakan produk Anda'),
('address', 'Yogyakarta, Indonesia'),
('email', 'info@banina.id'),
('instagram', '@banina.fact'),
('about_text', 'BANINA adalah brand busana muslim pria yang berdiri sejak 2019. Kami hadir untuk memenuhi kebutuhan pria muslim modern yang ingin tampil elegan, percaya diri, dan tetap sesuai syariat. Setiap produk kami dirancang dengan bahan pilihan berkualitas tinggi dan desain yang stylish namun tetap sopan.'),
('logo', ''),
('hero_title', 'Busana Muslim Pria Premium'),
('hero_subtitle', 'Tampil elegan & penuh percaya diri bersama BANINA'),
('facebook', ''),
('shopee', '');

-- Kategori BANINA
INSERT INTO categories (name, slug, description, sort_order) VALUES
('Songkok', 'songkok', 'Koleksi songkok premium berbagai model dan bahan pilihan', 1),
('Kemeja', 'kemeja', 'Kemeja muslim pria dengan desain modern dan bahan berkualitas', 2),
('Sarung', 'sarung', 'Sarung premium untuk sholat maupun kasual sehari-hari', 3),
('Celana', 'celana', 'Celana muslim pria yang nyaman dan stylish', 4),
('Sajadah', 'sajadah', 'Sajadah lembut dan tebal untuk kenyamanan ibadah', 5);

-- Produk contoh
INSERT INTO products (category_id, name, slug, description, price_min, price_max, whatsapp_message, is_featured, is_active) VALUES
(1, 'Songkok Premium Velvet Hitam', 'songkok-premium-velvet-hitam', 'Songkok berbahan velvet premium dengan jahitan rapi dan tahan lama. Tersedia dalam berbagai ukuran.', 85000, 120000, 'Halo BANINA, saya tertarik dengan Songkok Premium Velvet Hitam. Bisa info lebih lanjut?', 1, 1),
(1, 'Songkok Rajut Eksklusif', 'songkok-rajut-eksklusif', 'Songkok rajut tangan dengan desain eksklusif. Ringan dan nyaman dipakai seharian.', 95000, 135000, 'Halo BANINA, saya tertarik dengan Songkok Rajut Eksklusif. Bisa info lebih lanjut?', 0, 1),
(2, 'Kemeja Koko Lengan Panjang', 'kemeja-koko-lengan-panjang', 'Kemeja koko lengan panjang berbahan katun premium. Cocok untuk acara formal maupun kasual.', 185000, 250000, 'Halo BANINA, saya tertarik dengan Kemeja Koko Lengan Panjang. Bisa info lebih lanjut?', 1, 1),
(2, 'Kemeja Batik Muslim Modern', 'kemeja-batik-muslim-modern', 'Kemeja batik dengan motif modern dan elegan. Bahan katun adem dan tidak mudah kusut.', 220000, 295000, 'Halo BANINA, saya tertarik dengan Kemeja Batik Muslim Modern. Bisa info lebih lanjut?', 1, 1),
(3, 'Sarung Tenun Premium', 'sarung-tenun-premium', 'Sarung tenun berkualitas tinggi dengan motif klasik. Lembut, nyaman, dan tahan lama.', 150000, 200000, 'Halo BANINA, saya tertarik dengan Sarung Tenun Premium. Bisa info lebih lanjut?', 1, 1),
(3, 'Sarung Polosan Eksklusif', 'sarung-polosan-eksklusif', 'Sarung polosan bahan premium untuk penampilan bersih dan elegan.', 120000, 165000, 'Halo BANINA, saya tertarik dengan Sarung Polosan Eksklusif. Bisa info lebih lanjut?', 0, 1),
(4, 'Celana Bahan Pria Muslim', 'celana-bahan-pria-muslim', 'Celana bahan premium potongan slim fit yang nyaman dan stylish untuk pria muslim.', 175000, 230000, 'Halo BANINA, saya tertarik dengan Celana Bahan Pria Muslim. Bisa info lebih lanjut?', 1, 1),
(5, 'Sajadah Tebal Anti Slip', 'sajadah-tebal-anti-slip', 'Sajadah tebal berbahan lembut dengan alas anti slip. Nyaman untuk ibadah sehari-hari.', 65000, 95000, 'Halo BANINA, saya tertarik dengan Sajadah Tebal Anti Slip. Bisa info lebih lanjut?', 1, 1);

-- Variant Produk (Ukuran)
INSERT INTO product_variants (product_id, size, sort_order, is_active) VALUES
-- Songkok Premium Velvet Hitam (ID 1)
(1, 'S', 1, 1),
(1, 'M', 2, 1),
(1, 'L', 3, 1),
(1, 'XL', 4, 1),
-- Songkok Rajut Eksklusif (ID 2)
(2, 'S', 1, 1),
(2, 'M', 2, 1),
(2, 'L', 3, 1),
(2, 'XL', 4, 1),
-- Kemeja Koko Lengan Panjang (ID 3)
(3, 'S', 1, 1),
(3, 'M', 2, 1),
(3, 'L', 3, 1),
(3, 'XL', 4, 1),
(3, 'XXL', 5, 1),
-- Kemeja Batik Muslim Modern (ID 4)
(4, 'S', 1, 1),
(4, 'M', 2, 1),
(4, 'L', 3, 1),
(4, 'XL', 4, 1),
(4, 'XXL', 5, 1),
-- Sarung Tenun Premium (ID 5)
(5, 'STANDAR', 1, 1),
(5, 'PANJANG', 2, 1),
-- Sarung Polosan Eksklusif (ID 6)
(6, 'STANDAR', 1, 1),
(6, 'PANJANG', 2, 1),
-- Celana Bahan Pria Muslim (ID 7)
(7, '28', 1, 1),
(7, '29', 2, 1),
(7, '30', 3, 1),
(7, '31', 4, 1),
(7, '32', 5, 1),
(7, '33', 6, 1),
(7, '34', 7, 1),
-- Sajadah Tebal Anti Slip (ID 8)
(8, 'STANDAR', 1, 1);
