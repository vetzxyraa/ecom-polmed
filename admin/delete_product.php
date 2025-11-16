<?php
// Inisialisasi Session dan Database
require 'includes/session_check.php';
require '../config/database.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    // Ambil Nama File Gambar
    $sql_get = "SELECT gambar, gambar2 FROM produk WHERE id = ?";
    if ($stmt_get = mysqli_prepare($koneksi, $sql_get)) {
        mysqli_stmt_bind_param($stmt_get, "i", $id);
        mysqli_stmt_execute($stmt_get);
        mysqli_stmt_bind_result($stmt_get, $gambar, $gambar2);
        
        $gambar_to_delete = null;
        $gambar2_to_delete = null;
        if (mysqli_stmt_fetch($stmt_get)) {
            $gambar_to_delete = $gambar;
            $gambar2_to_delete = $gambar2;
        }
        mysqli_stmt_close($stmt_get);

        // Hapus Data Produk dari Database
        $sql_delete = "DELETE FROM produk WHERE id = ?";
        if ($stmt_del = mysqli_prepare($koneksi, $sql_delete)) {
            mysqli_stmt_bind_param($stmt_del, "i", $id);
            
            if (mysqli_stmt_execute($stmt_del)) {
                // Hapus File Gambar 1
                if (!empty($gambar_to_delete)) {
                    $target_file = "../assets/images/products/" . $gambar_to_delete;
                    if (filter_var($gambar_to_delete, FILTER_VALIDATE_URL) === FALSE && file_exists($target_file)) {
                        unlink($target_file);
                    }
                }
                // Hapus File Gambar 2
                if (!empty($gambar2_to_delete)) {
                    $target_file_2 = "../assets/images/products/" . $gambar2_to_delete;
                    if (filter_var($gambar2_to_delete, FILTER_VALIDATE_URL) === FALSE && file_exists($target_file_2)) {
                        unlink($target_file_2);
                    }
                }
                header("Location: products.php?status=deleted");
                exit;
            }
            mysqli_stmt_close($stmt_del);
        }
    }
}

// Redirect Jika Gagal
header("Location: products.php?status=error");
exit;
?>