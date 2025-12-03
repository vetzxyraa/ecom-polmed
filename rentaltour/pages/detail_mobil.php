<?php
include '../config/db.php';
$id = $_GET['id'];
$d = mysqli_fetch_array(mysqli_query($koneksi, "SELECT * FROM mobil WHERE id='$id'"));
$img = !empty($d['gambar']) && !str_contains($d['gambar'],'http') ? "../".$d['gambar'] : "https://via.placeholder.com/600x400?text=Detail+Mobil";

$success = false;
$booking_data = [];

if(isset($_POST['pesan'])){
    $nama = $_POST['nama']; $kontak = $_POST['kontak'];
    $tgl = $_POST['tgl']; $hari = $_POST['hari'];
    $catatan = $_POST['catatan']; 
    $total = $d['harga'] * $hari;
    
    mysqli_query($koneksi, "INSERT INTO booking VALUES (NULL, 'Mobil', '$id', '$nama', '$kontak', '$tgl', '$hari', '$catatan', '$total', 'Pending')");
    
    $success = true;
    $booking_data = [ 'nama' => $nama, 'mobil' => $d['nama'], 'total' => $total ];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Detail Mobil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body>
    <div class="container py-5">
        
        <?php if(!$success): ?>
            <a href="mobil.php" class="btn btn-outline-secondary rounded-pill px-4 mb-4">
                <i class="bi bi-arrow-left me-2"></i> Kembali ke List
            </a>
            
            <div class="row g-5">
                <div class="col-md-7">
                    <div class="rounded-4 overflow-hidden bg-light mb-4 shadow-sm" style="height:400px">
                        <img src="<?= $img ?>" style="width:100%; height:100%; object-fit:cover">
                    </div>
                    <h2 class="fw-bold mb-1"><?= $d['nama'] ?></h2>
                    <p class="text-muted"><?= $d['merk'] ?> - <?= $d['transmisi'] ?></p>
                    <h3 class="text-success fw-bold mb-4">Rp <?= number_format($d['harga']) ?> <span class="fs-6 text-muted text-dark fw-normal">/ hari</span></h3>
                    
                    <div class="p-3 bg-light rounded border">
                        <small class="text-uppercase fw-bold text-muted">Spesifikasi Singkat</small>
                        <div class="d-flex gap-4 mt-2">
                            <span><i class="bi bi-gear-fill me-1"></i> <?= $d['transmisi'] ?></span>
                            <span><i class="bi bi-people-fill me-1"></i> <?= $d['kursi'] ?> Penumpang</span>
                            <span><i class="bi bi-fuel-pump-fill me-1"></i> Bensin/Solar</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="card p-4 border rounded-4 shadow-sm">
                        <h4 class="fw-bold mb-3">Form Pemesanan</h4>
                        <form method="POST">
                            <div class="mb-3"><label class="form-label small text-muted">Nama Lengkap</label><input type="text" name="nama" class="form-control" required></div>
                            <div class="mb-3"><label class="form-label small text-muted">No. WhatsApp</label><input type="text" name="kontak" class="form-control" required></div>
                            <div class="row mb-3">
                                <div class="col"><label class="form-label small text-muted">Tanggal</label><input type="date" name="tgl" class="form-control" required></div>
                                <div class="col"><label class="form-label small text-muted">Durasi (Hari)</label><input type="number" name="hari" class="form-control" min="1" required></div>
                            </div>
                            <div class="mb-4"><label class="form-label small text-muted">Catatan (Opsional)</label><textarea name="catatan" class="form-control" rows="2"></textarea></div>
                            
                            <button name="pesan" class="btn btn-dark w-100 py-3 rounded-3">Kirim Booking</button>
                        </form>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <div class="row justify-content-center text-center mt-5">
                <div class="col-md-6">
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
                    <h2 class="fw-bold mt-3">Pesanan Diterima</h2>
                    <p class="text-muted">Terima kasih, data pesanan Anda sudah masuk ke sistem kami.</p>
                    
                    <div class="card p-4 my-4 bg-light border-0">
                        <div class="d-flex justify-content-between mb-2"><span>Unit</span><strong><?= $booking_data['mobil'] ?></strong></div>
                        <div class="d-flex justify-content-between border-top pt-2"><span>Total</span><strong class="text-success">Rp <?= number_format($booking_data['total']) ?></strong></div>
                    </div>
                    
                    <div class="d-grid gap-2">
                         <?php $wa_confirm = "https://wa.me/6281234567890?text=".urlencode("Halo, saya konfirmasi booking " . $booking_data['mobil']); ?>
                        <a href="<?= $wa_confirm ?>" target="_blank" class="btn btn-success py-3"><i class="bi bi-whatsapp"></i> Konfirmasi ke WA</a>
                        <a href="mobil.php" class="btn btn-outline-dark py-2">Kembali ke List</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    </div>
</body>
</html>