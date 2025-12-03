<?php include '../config/db.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Daftar Mobil</title>
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
            <span class="fw-bold mx-auto">DAFTAR ARMADA</span>
            <div style="width: 150px;"></div> </div>
    </nav>

    <div class="container py-5">
        <div class="row g-4">
            <?php
            $q = mysqli_query($koneksi, "SELECT * FROM mobil ORDER BY id ASC");
            while($r = mysqli_fetch_array($q)){
                $img = !empty($r['gambar']) && !str_contains($r['gambar'],'http') ? "../".$r['gambar'] : "https://via.placeholder.com/400x250?text=Mobil";
                
                // Generic WA Message
                $wa_text = "Halo, info " . $r['nama'];
                $wa_link = "https://wa.me/6281234567890?text=".urlencode($wa_text);
            ?>
            <div class="col-md-4 col-sm-6">
                <div class="card h-100 border rounded-4 overflow-hidden shadow-sm">
                    <div style="height:220px; background:#f8f9fa;">
                        <img src="<?= $img ?>" style="width:100%; height:100%; object-fit:cover;">
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0"><?= $r['nama'] ?></h5>
                            <span class="badge bg-dark rounded-pill">Rp <?= number_format($r['harga']/1000) ?>k</span>
                        </div>
                        
                        <div class="row text-center g-2 mb-4 small text-muted">
                            <div class="col-4 border-end">
                                <i class="bi bi-car-front d-block mb-1"></i> <?= $r['merk'] ?>
                            </div>
                            <div class="col-4 border-end">
                                <i class="bi bi-gear d-block mb-1"></i> <?= $r['transmisi'] ?>
                            </div>
                            <div class="col-4">
                                <i class="bi bi-people d-block mb-1"></i> <?= $r['kursi'] ?> Org
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <a href="<?= $wa_link ?>" target="_blank" class="btn btn-success btn-sm"><i class="bi bi-whatsapp"></i> Chat Admin</a>
                            <a href="detail_mobil.php?id=<?= $r['id'] ?>" class="btn btn-outline-dark btn-sm">Lihat Detail & Booking</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
</body>
</html>