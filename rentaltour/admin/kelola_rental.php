<?php
session_start();
include '../config/db.php';
if(!isset($_SESSION['admin'])) header("location:index.php");

if(isset($_GET['ok'])){
    mysqli_query($koneksi, "UPDATE booking SET status='Confirmed' WHERE id='$_GET[ok]'");
    header("location:kelola_rental.php");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Pesanan Masuk</title>
    <link rel="icon" href="../assets/img/favicon.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body>
    <div class="admin-container">
        <div class="sidebar">
            <a href="index.php" class="sidebar-brand">AdminPanel</a>
            <nav class="sidebar-menu">
                <a href="index.php"><i class="bi bi-grid"></i> Dashboard</a>
                <a href="kelola_mobil.php"><i class="bi bi-car-front"></i> Mobil</a>
                <a href="kelola_tour.php"><i class="bi bi-map"></i> Tour</a>
                <a href="kelola_rental.php" class="active"><i class="bi bi-receipt"></i> Pesanan</a>
                <a href="logout.php" class="text-danger mt-5">Logout</a>
            </nav>
        </div>

        <div class="main-content">
            <h4 class="fw-bold mb-4">Daftar Booking Masuk</h4>
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Tipe / Item</th>
                            <th>Pemesan</th>
                            <th style="width:25%">Catatan User</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $sql = "SELECT booking.*, 
                                CASE WHEN booking.tipe = 'Mobil' THEN mobil.nama ELSE paket_tour.nama END as nama_item
                                FROM booking 
                                LEFT JOIN mobil ON booking.tipe='Mobil' AND booking.item_id = mobil.id
                                LEFT JOIN paket_tour ON booking.tipe='Tour' AND booking.item_id = paket_tour.id
                                ORDER BY booking.id DESC";
                        $q=mysqli_query($koneksi, $sql); 
                        while($r=mysqli_fetch_array($q)){ 
                        ?>
                        <tr>
                            <td class="ps-4">
                                <span class="badge bg-light text-secondary border mb-1"><?= $r['tipe'] ?></span><br>
                                <span class="fw-bold"><?= $r['nama_item'] ?></span><br>
                                <small class="text-muted"><?= $r['tanggal'] ?> (<?= $r['qty'] ?>x)</small>
                            </td>
                            <td>
                                <div class="fw-bold"><?= $r['nama_pemesan'] ?></div>
                                <div class="small text-muted"><?= $r['kontak'] ?></div>
                                <div class="fw-bold text-success small">Rp <?= number_format($r['total_harga']) ?></div>
                            </td>
                            <td>
                                <?php if(!empty($r['catatan'])): ?>
                                    <div class="p-2 bg-light border rounded small fst-italic text-muted">
                                        "<?= $r['catatan'] ?>"
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted small">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($r['status']=='Pending'){ ?>
                                    <span class="badge bg-warning text-dark border border-warning bg-opacity-25">Pending</span>
                                <?php } else { ?>
                                    <span class="badge bg-success text-success border border-success bg-opacity-25">Confirmed</span>
                                <?php } ?>
                            </td>
                            <td>
                                <?php if($r['status']=='Pending'){ ?>
                                    <a href="?ok=<?=$r['id']?>" class="btn-icon btn-icon-success" title="Terima"><i class="bi bi-check-lg"></i></a>
                                <?php } ?>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>