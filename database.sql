CREATE TABLE `admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `admin` (`id`, `username`, `password`) VALUES
(1, 'admin', '$2y$10$I0I.n2.a.iQe3g.s8Q3h6uJ5f.VA/v9uU8xP.l/jF9q5iP.q3a.Oq');

CREATE TABLE `produk` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_produk` varchar(255) NOT NULL,
  `harga` int(11) NOT NULL,
  `stok` int(11) NOT NULL,
  `deskripsi` text NOT NULL,
  `gambar` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `produk` (`id`, `nama_produk`, `harga`, `stok`, `deskripsi`, `gambar`) VALUES
(1, 'Gantungan HP Kucing Lucu', 45000, 25, 'Gantungan HP akrilik berbentuk kucing maneki-neko yang lucu. Tali kuat dan warna cerah. Bawa keberuntungan ke mana pun Anda pergi!', 'https://placehold.co/800x800/FACC15/1F2937?text=Gantungan+Kucing'),
(2, 'Strap Manik-Manik Bintang', 55000, 15, 'Strap gantungan HP handmade dengan manik-manik berkualitas, liontin bintang, dan mutiara imitasi. Tampil elegan dan kekinian.', 'https://placehold.co/800x800/FACC15/1F2937?text=Strap+Manik'),
(3, 'Gantungan HP Astronot 3D', 60000, 20, 'Gantungan HP 3D dengan figur astronot lucu sedang duduk di bulan. Bahan silikon berkualitas, awet, dan tidak mudah kotor.', 'https://placehold.co/800x800/FACC15/1F2937?text=Astronot+Bulan'),
(4, 'Gantungan HP Mutiara Elegan', 75000, 30, 'Strap gantungan HP mewah dengan mutiara air tawar asli. Desain klasik dan elegan untuk melengkapi gaya Anda.', 'https://placehold.co/800x800/FACC15/1F2937?text=Mutiara+Elegan');

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
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode_pesanan` (`kode_pesanan`),
  KEY `produk_id` (`produk_id`),
  CONSTRAINT `pesanan_ibfk_1` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;