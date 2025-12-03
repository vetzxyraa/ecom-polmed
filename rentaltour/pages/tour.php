<?php include '../config/db.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Paket Tour</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body>
    <nav class="navbar border-bottom sticky-top bg-white py-3">
        <div class="container">
            <a href="../index.php" class="btn btn-outline-dark rounded-pill px-4 btn-sm">
                <i class="bi bi-arrow-left me-2"></i> Kembali ke Beranda
            </a>
            <span class="fw-bold mx-auto">PAKET WISATA</span>
            <div style="width: 150px;"></div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row g-4">
            <?php
            $q = mysqli_query($koneksi, "SELECT * FROM paket_tour ORDER BY id ASC");
            while($r = mysqli_fetch_array($q)){
                $img = !empty($r['gambar']) && !str_contains($r['gambar'],'http') ? "../".$r['gambar'] : "https://via.placeholder.com/400x250?text=Tour";
            ?>
            <div class="col-md-4 col-sm-6">
                <div class="card h-100 border rounded-4 overflow-hidden shadow-sm">
                    <div style="height:220px; background:#f8f9fa;">
                        <img src="<?= $img ?>" style="width:100%; height:100%; object-fit:cover;">
                    </div>
                    <div class="card-body p-4 text-center">
                        <h5 class="fw-bold mb-2"><?= $r['nama'] ?></h5>
                        <span class="badge bg-light text-dark border mb-3"><?= $r['durasi'] ?></span>
                        <h4 class="text-success fw-bold mb-4">Rp <?= number_format($r['harga']) ?></h4>
                        
                        <div class="d-grid gap-2">
                            <a href="detail_tour.php?id=<?= $r['id'] ?>" class="btn btn-dark btn-sm">Pilih Paket Ini</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
</body>
</html>