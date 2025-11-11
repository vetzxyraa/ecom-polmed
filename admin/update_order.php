<?php
include 'includes/session_check.php';
require '../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pesanan_id = (int)$_POST['pesanan_id'];
    $status = mysqli_real_escape_string($koneksi, $_POST['status']);
    
    $allowed_statuses = ['menunggu', 'menunggu konfirmasi', 'diproses', 'dikirim', 'selesai', 'dibatalkan'];
    
    if ($pesanan_id > 0 && in_array($status, $allowed_statuses)) {
        $sql = "UPDATE pesanan SET status = ? WHERE id = ?";
        
        if ($stmt = mysqli_prepare($koneksi, $sql)) {
            mysqli_stmt_bind_param($stmt, "si", $status, $pesanan_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }
}

header("Location: dashboard.php?status=updated");
exit;
?>