<?php
require 'includes/session_check.php';
require '../config/database.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    // 1. Ambil nama file gambar dari database
    $sql_get = "SELECT gambar FROM produk WHERE id = ?";
    if ($stmt_get = mysqli_prepare($koneksi, $sql_get)) {
        mysqli_stmt_bind_param($stmt_get, "i", $id);
        mysqli_stmt_execute($stmt_get);
        mysqli_stmt_bind_result($stmt_get, $gambar);
        
        // Simpan nama gambar sebelum menutup statement
        $gambar_to_delete = null;
        if (mysqli_stmt_fetch($stmt_get)) {
            $gambar_to_delete = $gambar;
        }
        mysqli_stmt_close($stmt_get);

        // 2. Hapus entri database
        $sql_delete = "DELETE FROM produk WHERE id = ?";
        if ($stmt_del = mysqli_prepare($koneksi, $sql_delete)) {
            mysqli_stmt_bind_param($stmt_del, "i", $id);
            
            if (mysqli_stmt_execute($stmt_del)) {
                // 3. Jika data di DB berhasil dihapus, hapus file gambarnya
                if (!empty($gambar_to_delete)) {
                    $target_file = "../assets/images/products/" . $gambar_to_delete;
                    // Pastikan itu bukan URL dan file-nya ada
                    if (filter_var($gambar_to_delete, FILTER_VALIDATE_URL) === FALSE && file_exists($target_file)) {
                        unlink($target_file);
                    }
                }
                header("Location: products.php?status=deleted");
                exit;
            }
            mysqli_stmt_close($stmt_del);
        }
    }
}

// Jika gagal di tahap mana pun
header("Location: products.php?status=error");
exit;
?>