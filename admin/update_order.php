<?php
include 'includes/session_check.php';
require '../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pesanan_id = (int)$_POST['pesanan_id'];
    $status = mysqli_real_escape_string($koneksi, $_POST['status']);
    
    // Ambil pesan admin dari POST, set ke NULL jika kosong
    $pesan_admin = !empty($_POST['pesan_admin']) ? mysqli_real_escape_string($koneksi, $_POST['pesan_admin']) : NULL;
    
    $allowed_statuses = ['menunggu', 'menunggu konfirmasi', 'diproses', 'dikirim', 'selesai', 'dibatalkan'];
    
    if ($pesanan_id > 0 && in_array($status, $allowed_statuses)) {
        
        // Jika status BUKAN 'dibatalkan', KOSONGKAN pesan admin
        if ($status != 'dibatalkan' && $pesan_admin === NULL) {
             $sql = "UPDATE pesanan SET status = ?, pesan_admin = NULL WHERE id = ?";
        } else {
            // Update status DAN pesan_admin
            $sql = "UPDATE pesanan SET status = ?, pesan_admin = ? WHERE id = ?";
        }

        if ($stmt = mysqli_prepare($koneksi, $sql)) {
            if ($status != 'dibatalkan' && $pesan_admin === NULL) {
                mysqli_stmt_bind_param($stmt, "si", $status, $pesanan_id);
            } else {
                mysqli_stmt_bind_param($stmt, "ssi", $status, $pesan_admin, $pesanan_id);
            }
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }
}

header("Location: dashboard.php?status=updated");
exit;
?>