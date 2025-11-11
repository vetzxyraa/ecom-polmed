-- Hapus tabel 'pesanan' jika sudah ada
DROP TABLE IF EXISTS `pesanan`;
-- Hapus tabel 'produk' jika sudah ada
DROP TABLE IF EXISTS `produk`;
-- Hapus tabel 'admin' jika sudah ada
DROP TABLE IF EXISTS `admin`;


CREATE TABLE `admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Password untuk 'admin' adalah 'admin123'
-- Password untuk 'polmed' adalah 'polmed123'
INSERT INTO `admin` (`id`, `username`, `password`) VALUES
(1, 'admin', '$2y$10$I0I.n2.a.iQe3g.s8Q3h6uJ5f.VA/v9uU8xP.l/jF9q5iP.q3a.Oq'),
(2, 'polmed', '$2y$10$E.hJP..TR6EAML.Qx7s1FuR3a4nU.JjR.Ngesa76iARj0uDjp3wua');

CREATE TABLE `produk` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_produk` varchar(255) NOT NULL,
  `harga` int(11) NOT NULL,
  `stok` int(11) NOT NULL,
  `deskripsi` text NOT NULL,
  `gambar` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- PERUBAHAN: Data dummy produk yang lebih rapi dan generik
INSERT INTO `produk` (`id`, `nama_produk`, `harga`, `stok`, `deskripsi`, `gambar`) VALUES
(1, 'Produk 1', 50000, 25, 'Deskripsi singkat untuk Produk 1. Ini adalah produk berkualitas tinggi.', 'https://placehold.co/800x800/E5E7EB/374151?text=Produk+1'),
(2, 'Produk 2', 55000, 15, 'Deskripsi singkat untuk Produk 2. Bahan terbaik dan desain modern.', 'https://placehold.co/800x800/E5E7EB/374151?text=Produk+2'),
(3, 'Produk 3', 60000, 20, 'Deskripsi singkat untuk Produk 3. Cocok untuk hadiah atau koleksi pribadi.', 'https://placehold.co/800x800/E5E7EB/374151?text=Produk+3'),
(4, 'Produk 4', 75000, 30, 'Deskripsi singkat untuk Produk 4. Tampil beda dengan produk eksklusif ini.', 'https://placehold.co/800x800/E5E7EB/374151?text=Produk+4');


CREATE TABLE `pesanan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `produk_id` int(11) NOT NULL,
  `kode_pesanan` varchar(50) NOT NULL,
  `nama_pembeli` varchar(100) NOT NULL,
  `no_hp` varchar(20) NOT NULL,
  `alamat` text NOT NULL,
  `jumlah` int(11) NOT NULL,
  `total_harga` int(11) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'menunggu',
  `tgl_pesan` timestamp NOT NULL DEFAULT current_timestamp(),
  `pesan_admin` TEXT DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode_pesanan` (`kode_pesanan`),
  KEY `produk_id` (`produk_id`),
  CONSTRAINT `pesanan_ibfk_1` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;