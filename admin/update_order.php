<?php
// Inisialisasi Session dan Database
include 'includes/session_check.php';
require '../config/database.php';

// Status yang Diizinkan
$allowed_statuses = ['menunggu', 'berhasil', 'gagal'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil Data POST
    $pesanan_id = isset($_POST['pesanan_id']) ? (int)$_POST['pesanan_id'] : 0;
    $status = isset($_POST['status']) ? $_POST['status'] : '';
    $pesan_admin = !empty($_POST['pesan_admin']) ? mysqli_real_escape_string($koneksi, $_POST['pesan_admin']) : NULL;
    
    // Validasi dan Update
    if ($pesanan_id > 0 && in_array($status, $allowed_statuses)) {
        
        $sql = "UPDATE pesanan SET status = ?, pesan_admin = ? WHERE id = ?";

        if ($stmt = mysqli_prepare($koneksi, $sql)) {
            mysqli_stmt_bind_param($stmt, "ssi", $status, $pesan_admin, $pesanan_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            
            header("Location: dashboard.php?status=updated");
            exit;
        }
    }
}

// Redirect Jika Gagal
header("Location: dashboard.php?status=error");
exit;
?>