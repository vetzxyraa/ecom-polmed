SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `admin` (`id`, `username`, `password`) VALUES
(2, 'polmed', '$2y$10$HIQasZaTh8ibMHu3HZ55CeA1/4HsjiXl0RzR96C4.aG5TtLacVWBq');

CREATE TABLE `pesanan` (
  `id` int(11) NOT NULL,
  `produk_id` int(11) NOT NULL,
  `kode_pesanan` varchar(50) NOT NULL,
  `nama_pembeli` varchar(100) NOT NULL,
  `no_hp` varchar(20) NOT NULL,
  `alamat` text NOT NULL,
  `jumlah` int(11) NOT NULL,
  `total_harga` int(11) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'menunggu',
  `tgl_pesan` timestamp NOT NULL DEFAULT current_timestamp(),
  `pesan_admin` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `pesanan` (`id`, `produk_id`, `kode_pesanan`, `nama_pembeli`, `no_hp`, `alamat`, `jumlah`, `total_harga`, `status`, `tgl_pesan`, `pesan_admin`) VALUES
(1, 2, 'GHS-176290362523', 'aa', '22222', 'ssss', 1, 55000, 'gagal', '2025-11-11 23:27:05', 'abis stock'),
(2, 2, 'GHS-176290410653', 'JG', '989', 'UYU', 1, 55000, 'menunggu', '2025-11-11 23:35:06', NULL),
(3, 2, 'GHS-176290771941', 'www', '2222', '22', 1, 55000, 'menunggu', '2025-11-12 00:35:19', NULL);

CREATE TABLE `produk` (
  `id` int(11) NOT NULL,
  `nama_produk` varchar(255) NOT NULL,
  `harga` int(11) NOT NULL,
  `stok` int(11) NOT NULL,
  `deskripsi` text NOT NULL,
  `gambar` varchar(255) NOT NULL,
  `gambar2` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `produk` (`id`, `nama_produk`, `harga`, `stok`, `deskripsi`, `gambar`, `gambar2`) VALUES
(1, 'Produk 1', 50000, 25, 'Deskripsi singkat untuk Produk 1. Ini adalah produk berkualitas tinggi.', 'https://placehold.co/800x800/E5E7EB/374151?text=Produk+1', 'https://placehold.co/800x800/F59E0B/374151?text=Produk+1+-+Img+2'),
(2, 'Produk 2', 55000, 12, 'Deskripsi singkat untuk Produk 2. Bahan terbaik dan desain modern.', 'https://placehold.co/800x800/E5E7EB/374151?text=Produk+2', NULL),
(3, 'Produk 3', 60000, 20, 'Deskripsi singkat untuk Produk 3. Cocok untuk hadiah atau koleksi pribadi.', 'https://placehold.co/800x800/E5E7EB/374151?text=Produk+3', 'https://placehold.co/800x800/F59E0B/374151?text=Produk+3+-+Img+2'),
(4, 'Produk 4', 75000, 30, 'Deskripsi singkat untuk Produk 4. Tampil beda dengan produk eksklusif ini.', 'https://placehold.co/800x800/E5E7EB/374151?text=Produk+4', NULL);

CREATE TABLE `settings` (
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
('alamat_toko', 'hahawww'),
('email_kontak', 'wkwksssswk@gmail.com'),
('foto_about', 'https://imgs.search.brave.com/qx5pQQhNoGKAMKQ5fHy3WrzqBTT7sde8Q_38MSnTJZQ/rs:fit:500:0:1:0/g:ce/aHR0cHM6Ly9pbWcu/ZnJlZXBpay5jb20v/Zm90by1wcmVtaXVt/L3NlYnVhaC1nYW1i/YXItc2VvcmFuZy1h/bmFrLWxha2ktbGFr/aS1kZW5nYW4tcGVk/YW5nLWRhbi1rYXRh/LWthdGEtYW5pbWUt/ZGktYXRhc255YV8x/MDIwNDk1LTczNDY4/NS5qcGc_c2VtdD1h/aXNfaHlicmlkJnc9/NzQwJnE9ODA'),
('nama_pemilik', 'jamal'),
('nama_toko', 'cepor'),
('nomor_wa', '1233322'),
('teks_about', 'ok');


ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

ALTER TABLE `pesanan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_pesanan` (`kode_pesanan`),
  ADD KEY `produk_id` (`produk_id`);

ALTER TABLE `produk`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `settings`
  ADD PRIMARY KEY (`setting_key`);


ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

ALTER TABLE `pesanan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

ALTER TABLE `produk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;


ALTER TABLE `pesanan`
  ADD CONSTRAINT `pesanan_ibfk_1` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;