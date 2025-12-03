<?php 
include 'config/db.php'; 

// AMBIL DATA PROFIL DARI DB
$q_prof = mysqli_query($koneksi, "SELECT * FROM pengaturan WHERE id=1");
$prof = mysqli_fetch_array($q_prof);

// Validasi Gambar Kantor
$img_kantor = !empty($prof['foto_kantor']) ? $prof['foto_kantor'] : "https://via.placeholder.com/600x400?text=FOTO+KANTOR";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rental Mobil</title>
    <link rel="icon" href="assets/img/favicon.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body>

    <div style="background-color: #f0f8ff;" class="border-bottom py-2 small text-muted">
        <div class="container d-flex justify-content-between align-items-center">
            <div>
                <i class="bi bi-envelope-fill me-2 text-primary"></i> <?= $prof['email'] ?>
                <span class="mx-3">|</span>
                <i class="bi bi-geo-alt-fill me-2 text-primary"></i> <?= $prof['alamat'] ?>
            </div>
            <a href="https://wa.me/<?= $prof['telepon'] ?>" target="_blank" class="text-decoration-none fw-bold text-primary">
                <i class="bi bi-whatsapp me-1"></i> Hubungi Kami
            </a>
        </div>
    </div>

    <nav class="navbar navbar-expand-lg sticky-top bg-white border-bottom shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="index.php">Rental<span class="text-primary">Mobil.</span></a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto gap-2">
                    <li class="nav-item"><a class="nav-link" href="pages/mobil.php">List Mobil</a></li>
                    <li class="nav-item"><a class="nav-link" href="pages/tour.php">Paket Tour</a></li>
                    <li class="nav-item">
                        <a href="admin/index.php" class="btn btn-sm btn-primary px-4 rounded-pill">Login Admin</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="hero-minimal">
        <div class="container text-center">
            <h1 class="hero-title text-white">Partner Perjalanan<br>Terpercaya & Aman.</h1>
            <p class="hero-subtitle mx-auto text-white" style="max-width: 600px;">
                Solusi transportasi terbaik dengan harga transparan dan pelayanan profesional.
            </p>
            
            <div class="d-flex justify-content-center gap-3">
                <a href="pages/mobil.php" class="btn btn-black btn-lg text-white border-white">Lihat Mobil</a>
                <a href="pages/tour.php" class="btn btn-outline btn-lg text-white border-white">Paket Wisata</a>
            </div>
        </div>
    </section>

    <section class="bg-white py-5">
        <div class="container py-4">
            <div class="row g-5 align-items-center">
                <div class="col-md-6">
                    <div class="rounded-4 overflow-hidden shadow-sm border" style="height: 350px;">
                        <img src="<?= $img_kantor ?>" style="width:100%; height:100%; object-fit:cover;">
                    </div>
                </div>
                <div class="col-md-6">
                    <h6 class="text-uppercase fw-bold text-primary mb-2">Tentang Kami</h6>
                    <h2 class="fw-bold mb-4 text-dark">Profil Perusahaan</h2>
                    
                    <p class="text-muted mb-4" style="white-space: pre-line;">
                        <?= $prof['deskripsi'] ?>
                    </p>
                    
                    <ul class="list-unstyled">
                        <li class="mb-3 d-flex align-items-center">
                            <i class="bi bi-telephone-fill text-primary me-3 fs-5"></i>
                            <div><strong>Kontak Resmi</strong> <small class="text-muted d-block"><?= $prof['telepon'] ?></small></div>
                        </li>
                        <li class="mb-3 d-flex align-items-center">
                            <i class="bi bi-geo-alt-fill text-primary me-3 fs-5"></i>
                            <div><strong>Lokasi</strong> <small class="text-muted d-block"><?= $prof['alamat'] ?></small></div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <footer class="text-center">
        <div class="container">
            <h5 class="fw-bold text-dark m-0">Rental<span class="text-primary">Mobil.</span></h5>
            <small class="text-muted d-block mt-2"><?= $prof['alamat'] ?></small>
            <small class="text-muted">&copy; 2024. All Rights Reserved.</small>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>