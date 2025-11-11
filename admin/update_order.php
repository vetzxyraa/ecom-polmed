<?php
include 'includes/session_check.php';
require '../config/database.php';

// Daftar status yang diizinkan
$allowed_statuses = ['menunggu', 'menunggu konfirmasi', 'diproses', 'dikirim', 'selesai', 'dibatalkan'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pesanan_id = isset($_POST['pesanan_id']) ? (int)$_POST['pesan_id'] : 0;
    $status = isset($_POST['status']) ? $_POST['status'] : '';
    
    // Ambil pesan admin HANYA jika statusnya 'dibatalkan', selain itu NULL-kan
    $pesan_admin = NULL;
    if ($status == 'dibatalkan') {
        $pesan_admin = !empty($_POST['pesan_admin']) ? mysqli_real_escape_string($koneksi, $_POST['pesan_admin']) : NULL;
    }
    
    // Validasi
    if ($pesanan_id > 0 && in_array($status, $allowed_statuses)) {
        
        // Update status DAN pesan_admin (pesan_admin akan NULL jika status bukan 'dibatalkan')
        $sql = "UPDATE pesanan SET status = ?, pesan_admin = ? WHERE id = ?";

        if ($stmt = mysqli_prepare($koneksi, $sql)) {
            mysqli_stmt_bind_param($stmt, "ssi", $status, $pesan_admin, $pesanan_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            
            // Redirect dengan status sukses
            header("Location: dashboard.php?status=updated");
            exit;
        }
    }
}

// Jika ada error atau akses langsung, kembali ke dashboard
header("Location: dashboard.php?status=error");
exit;
?>