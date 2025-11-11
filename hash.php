<?php
/*
  FILE SEMENTARA UNTUK MEMBUAT HASH PASSWORD
  
  Cara Pakai:
  1. Upload file ini ke folder project kamu (sejajar dengan 'index.php').
  2. Buka file ini di browser (misal: localhost/ecommerce-polmed/buat_hash.php).
  3. Copy teks hash yang muncul di layar (misal: $2y$10$Abcde...).
  4. Paste hash tersebut ke kolom 'password' di tabel 'admin' (via phpMyAdmin).
  5. Login pakai user 'polmed' dan password 'polmed123'.
  6. HAPUS file ini dari server kamu.
*/

echo password_hash("polmed123", PASSWORD_DEFAULT);
?>