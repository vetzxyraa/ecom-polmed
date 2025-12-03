<?php
include '../config/db.php';
$id = $_GET['id'];
$tour = mysqli_fetch_array(mysqli_query($koneksi, "SELECT * FROM paket_tour WHERE id='$id'"));

if(isset($_POST['book_tour'])){
    $nama = htmlspecialchars($_POST['nama']);
    $hp = htmlspecialchars($_POST['hp']);
    $tgl = $_POST['tgl'];
    $peserta = $_POST['peserta'];
    $total = $tour['harga'] * $peserta;

    $insert = mysqli_query($koneksi, "INSERT INTO booking_tour (nama_pemesan, kontak, tour_id, tgl_tour, jml_peserta, total_harga, status) VALUES ('$nama', '$hp', '$id', '$tgl', '$peserta', '$total', 'Pending')");

    if($insert){
        echo "<script>alert('Booking Tour Berhasil! Kami akan menghubungi Anda.'); window.location='tour.php';</script>";
    }
}

// --- LOGIC PATH GAMBAR ---
$gbr = $tour['gambar'];
if(empty($gbr)){ $gbr = "https://via.placeholder.com/600x400?text=No+Image"; }
elseif (!str_contains($gbr, 'http')) { $gbr = "../" . $gbr; }
// -------------------------
?>
<!DOCTYPE html>
<html>
<head>
    <title>Booking Tour</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-9">
                <div class="card shadow border-0 rounded-4">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                         <a href="tour.php" class="text-decoration-none text-muted">&larr; Kembali ke Daftar Tour</a>
                    </div>
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <img src="<?= $gbr ?>" class="img-fluid rounded mb-3 shadow-sm" style="max-height: 400px; width:100%; object-fit: cover;">
                            <h2 class="fw-bold mt-3"><?= $tour['judul'] ?></h2>
                            <p class="text-muted w-75 mx-auto"><?= $tour['deskripsi'] ?></p>
                            <span class="badge bg-success fs-5 px-4 py-2 rounded-pill">Rp <?= number_format($tour['harga']) ?> <small>/ pax</small></span>
                        </div>
                        
                        <div class="bg-white border rounded p-4 mt-4">
                            <h5 class="fw-bold mb-3 border-bottom pb-2">Form Pemesanan Tour</h5>
                            <form method="POST">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label small fw-bold text-muted">NAMA PEMESAN</label>
                                        <input type="text" name="nama" class="form-control bg-light" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label small fw-bold text-muted">WHATSAPP</label>
                                        <input type="text" name="hp" class="form-control bg-light" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label small fw-bold text-muted">TANGGAL TOUR</label>
                                        <input type="date" name="tgl" class="form-control bg-light" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label small fw-bold text-muted">JUMLAH PESERTA</label>
                                        <input type="number" name="peserta" class="form-control bg-light" min="1" required>
                                    </div>
                                </div>
                                <button type="submit" name="book_tour" class="btn btn-gold w-100 btn-lg mt-3">BOOKING SEKARANG</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>