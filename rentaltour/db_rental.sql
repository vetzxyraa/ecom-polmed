SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+07:00";

-- --------------------------------------------------------

-- 1. TABEL ADMIN
CREATE TABLE `admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
);

-- Password default: admin123 (Sudah di-hash)
INSERT INTO `admin` (`username`, `password`) VALUES 
('admin', '$2y$10$Be4.Be4.Be4.Be4.Be4.Be4.Be4.Be4.Be4.Be4.Be4.Be4.Be4.Be4');

-- --------------------------------------------------------

-- 2. TABEL PENGATURAN (PROFIL WEBSITE)
CREATE TABLE `pengaturan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `telepon` varchar(20) NOT NULL,
  `alamat` text NOT NULL,
  `deskripsi` text NOT NULL,
  `foto_kantor` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
);

-- Data Default Profil (Bisa diedit di Admin Panel > Setting Profil)
INSERT INTO `pengaturan` (`id`, `email`, `telepon`, `alamat`, `deskripsi`, `foto_kantor`) VALUES 
(1, 'info@rentalmobil.com', '6281234567890', 'Jl. Protokol No. 10, Jakarta Pusat', 'Kami adalah penyedia layanan transportasi terpercaya dengan armada terbaru. Siap melayani kebutuhan bisnis maupun wisata Anda dengan harga kompetitif dan pelayanan profesional.', '');

-- --------------------------------------------------------

-- 3. TABEL MOBIL
CREATE TABLE `mobil` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `merk` varchar(50) NOT NULL,
  `transmisi` varchar(20) NOT NULL, -- Manual / Matic
  `kursi` int(11) NOT NULL,
  `harga` int(11) NOT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
);

-- Dummy Data Mobil (Generic)
INSERT INTO `mobil` (`nama`, `merk`, `transmisi`, `kursi`, `harga`, `gambar`) VALUES
('MPV Keluarga Standar', 'Toyota', 'Manual', 7, 350000, ''),
('City Car Compact', 'Honda', 'Matic', 4, 400000, ''),
('SUV Premium Sport', 'Mitsubishi', 'Matic', 7, 900000, ''),
('Minibus Wisata', 'Isuzu', 'Manual', 14, 1200000, '');

-- --------------------------------------------------------

-- 4. TABEL PAKET TOUR
CREATE TABLE `paket_tour` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `durasi` varchar(50) NOT NULL,
  `harga` int(11) NOT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
);

-- Dummy Data Tour (Generic)
INSERT INTO `paket_tour` (`nama`, `durasi`, `harga`, `gambar`) VALUES
('Paket Wisata Kota A', '1 Hari', 500000, ''),
('Paket Liburan Pulau B', '3 Hari 2 Malam', 1850000, '');

-- --------------------------------------------------------

-- 5. TABEL BOOKING (Unified: Mobil & Tour)
CREATE TABLE `booking` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tipe` enum('Mobil','Tour') NOT NULL,
  `item_id` int(11) NOT NULL,
  `nama_pemesan` varchar(100) NOT NULL,
  `kontak` varchar(50) NOT NULL, -- No WA
  `tanggal` date NOT NULL,
  `qty` int(11) NOT NULL, -- Jumlah Hari (Mobil) atau Peserta (Tour)
  `catatan` text DEFAULT NULL, -- Request user
  `total_harga` int(11) NOT NULL,
  `status` enum('Pending','Confirmed') NOT NULL DEFAULT 'Pending',
  PRIMARY KEY (`id`)
);

COMMIT;