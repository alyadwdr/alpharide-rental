-- Database: alpharide
-- Password admin TIDAK DI-HASH untuk kemudahan testing

-- ========================================
-- Tabel Admin
-- ========================================
CREATE TABLE `tbl_admin` (
  `id_admin` INT(11) NOT NULL AUTO_INCREMENT,
  `nama_admin` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_admin`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Admin dengan password PLAINTEXT (tidak di-hash)
-- Email: admin@alpharide.com
-- Password: admin123
INSERT INTO `tbl_admin` (`nama_admin`, `email`, `password`) VALUES
('Admin Alpharide', 'admin@alpharide.com', 'admin123');

-- ========================================
-- Tabel User/Customer
-- ========================================
CREATE TABLE `tbl_user` (
  `id_user` INT(11) NOT NULL AUTO_INCREMENT,
  `nama_user` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `no_hp` VARCHAR(15) NOT NULL,
  `alamat` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- TIDAK ADA DATA DUMMY USER - User daftar sendiri dari website

-- ========================================
-- Tabel Mobil
-- (Struktur sudah disesuaikan: pakai kode_mobil, tanpa nomor_plat)
-- ========================================
CREATE TABLE `tbl_mobil` (
  `id_mobil` INT(11) NOT NULL AUTO_INCREMENT,
  `merek` VARCHAR(50) NOT NULL,
  `model` VARCHAR(50) NOT NULL,
  `kode_mobil` VARCHAR(20) NOT NULL,
  `warna` VARCHAR(30) NOT NULL,
  `kapasitas` INT(11) NOT NULL,
  `harga_sewa_per_hari` DECIMAL(10,2) NOT NULL,
  `foto_mobil` VARCHAR(255) DEFAULT 'default-car.jpg',
  `status` ENUM('tersedia','disewa','maintenance') DEFAULT 'tersedia',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_mobil`),
  UNIQUE KEY `kode_mobil` (`kode_mobil`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tbl_mobil` (`id_mobil`, `merek`, `model`, `kode_mobil`, `warna`, `kapasitas`, `harga_sewa_per_hari`, `foto_mobil`, `status`) VALUES
(1, 'Toyota', 'Avanza', 'TYT-AVZ-001', 'Silver', 7, 350000.00, 'avanza.png', 'tersedia'),
(2, 'Toyota', 'Rush', 'TYT-RSH-001', 'Coklat', 7, 450000.00, 'rush.png', 'tersedia'),
(3, 'Honda', 'Jazz', 'HND-JZZ-001', 'Merah', 5, 400000.00, 'jazz.png', 'tersedia'),
(4, 'Mitsubishi', 'Xpander', 'MIT-XPD-001', 'Hitam', 7, 420000.00, 'xpander.png', 'tersedia'),
(5, 'Daihatsu', 'Terios', 'DHT-TRS-001', 'Biru', 7, 380000.00, 'terios.png', 'tersedia');

-- ========================================
-- Tabel Plat Nomor (Sistem Stok)
-- ========================================
CREATE TABLE `tbl_plat_nomor` (
  `id_plat` INT(11) NOT NULL AUTO_INCREMENT,
  `id_mobil` INT(11) NOT NULL,
  `nomor_plat` VARCHAR(15) NOT NULL,
  `status` ENUM('tersedia','disewa','maintenance') DEFAULT 'tersedia',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_plat`),
  UNIQUE KEY `nomor_plat` (`nomor_plat`),
  CONSTRAINT `fk_plat_mobil` FOREIGN KEY (`id_mobil`) REFERENCES `tbl_mobil` (`id_mobil`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert data plat nomor untuk setiap mobil (multiple plat per mobil)
INSERT INTO `tbl_plat_nomor` (`id_mobil`, `nomor_plat`, `status`) VALUES
-- Toyota Avanza (3 unit)
(1, 'T 1234 AA', 'tersedia'),
(1, 'T 1235 AA', 'tersedia'),
(1, 'T 1236 AA', 'tersedia'),
-- Toyota Rush (2 unit)
(2, 'T 5678 CD', 'tersedia'),
(2, 'T 5679 CD', 'tersedia'),
-- Honda Jazz (2 unit)
(3, 'T 9012 EF', 'tersedia'),
(3, 'T 9013 EF', 'tersedia'),
-- Mitsubishi Xpander (3 unit)
(4, 'T 3456 GH', 'tersedia'),
(4, 'T 3457 GH', 'tersedia'),
(4, 'T 3458 GH', 'tersedia'),
-- Daihatsu Terios (2 unit)
(5, 'T 7890 IJ', 'tersedia'),
(5, 'T 7891 IJ', 'tersedia');

-- ========================================
-- Tabel Transaksi
-- (Sudah update: tambah id_plat & update enum status)
-- ========================================
CREATE TABLE `tbl_transaksi` (
  `id_sewa` INT(11) NOT NULL AUTO_INCREMENT,
  `id_mobil` INT(11) NOT NULL,
  `id_plat` INT(11) NULL, -- Kolom baru
  `id_user` INT(11) NOT NULL,
  `tanggal_sewa` DATE NOT NULL,
  `tanggal_kembali` DATE NOT NULL,
  `total_harga` DECIMAL(10,2) NOT NULL,
  `status_transaksi` ENUM('pending','diterima','ditolak','disewa','selesai') DEFAULT 'pending', -- Enum baru
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_sewa`),
  CONSTRAINT `fk_mobil` FOREIGN KEY (`id_mobil`) REFERENCES `tbl_mobil` (`id_mobil`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_user` FOREIGN KEY (`id_user`) REFERENCES `tbl_user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_transaksi_plat` FOREIGN KEY (`id_plat`) REFERENCES `tbl_plat_nomor` (`id_plat`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ========================================
-- View Laporan (Sudah termasuk plat nomor)
-- ========================================
CREATE VIEW `view_transaksi_lengkap` AS
SELECT 
    t.id_sewa,
    t.tanggal_sewa,
    t.tanggal_kembali,
    t.total_harga,
    t.status_transaksi,
    t.created_at,
    u.id_user,
    u.nama_user,
    u.email AS email_user,
    u.no_hp,
    m.id_mobil,
    m.merek,
    m.model,
    m.kode_mobil,
    m.warna,
    m.foto_mobil,
    p.id_plat,
    p.nomor_plat
FROM tbl_transaksi t
JOIN tbl_user u ON t.id_user = u.id_user
JOIN tbl_mobil m ON t.id_mobil = m.id_mobil
LEFT JOIN tbl_plat_nomor p ON t.id_plat = p.id_plat;