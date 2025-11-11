<?php
require 'includes/session_check.php';
require '../config/database.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    $sql_get = "SELECT gambar FROM produk WHERE id = ?";
    if ($stmt_get = mysqli_prepare($koneksi, $sql_get)) {
        mysqli_stmt_bind_param($stmt_get, "i", $id);
        mysqli_stmt_execute($stmt_get);
        mysqli_stmt_bind_result($stmt_get, $gambar);
        if (mysqli_stmt_fetch($stmt_get)) {
            $target_file = "../assets/images/products/" . $gambar;
            if (filter_var($gambar, FILTER_VALIDATE_URL) === FALSE && file_exists($target_file) && !empty($gambar)) {
                unlink($target_file);
            }
        }
        mysqli_stmt_close($stmt_get);
    }
    
    $sql_delete = "DELETE FROM produk WHERE id = ?";
    if ($stmt_del = mysqli_prepare($koneksi, $sql_delete)) {
        mysqli_stmt_bind_param($stmt_del, "i", $id);
        if (mysqli_stmt_execute($stmt_del)) {
            header("Location: products.php?status=deleted");
            exit;
        }
        mysqli_stmt_close($stmt_del);
    }
}

header("Location: products.php?status=error");
exit;
?>