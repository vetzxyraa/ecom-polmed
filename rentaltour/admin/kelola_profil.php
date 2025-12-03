<?php
session_start();
include '../config/db.php';
if(!isset($_SESSION['admin'])) header("location:index.php");

// AMBIL DATA SAAT INI (ID 1)
$q = mysqli_query($koneksi, "SELECT * FROM pengaturan WHERE id=1");
$d = mysqli_fetch_array($q);

// PROSES UPDATE
if(isset($_POST['simpan'])){
    $email = $_POST['email'];
    $telepon = $_POST['telepon']; // Pastikan format 628xxx
    $alamat = $_POST['alamat'];
    $deskripsi = $_POST['deskripsi'];
    
    // Logic Gambar
    $path = $d['foto_kantor']; // Default gambar lama
    if(!empty($_FILES['foto_kantor']['name'])){
        $new = date('dmYHis').$_FILES['foto_kantor']['name'];
        move_uploaded_file($_FILES['foto_kantor']['tmp_name'], "../assets/img/".$new);
        $path = "assets/img/".$new;
    }
    
    mysqli_query($koneksi, "UPDATE pengaturan SET email='$email', telepon='$telepon', alamat='$alamat', deskripsi='$deskripsi', foto_kantor='$path' WHERE id=1");
    
    echo "<script>alert('Profil Diupdate!'); window.location='kelola_profil.php'</script>";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Kelola Profil</title>
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
                <a href="kelola_rental.php"><i class="bi bi-receipt"></i> Pesanan</a>
                <a href="kelola_profil.php" class="active"><i class="bi bi-gear-fill"></i> Setting Profil</a>
                
                <a href="logout.php" class="text-danger mt-5">Logout</a>
            </nav>
        </div>

        <div class="main-content">
            <h4 class="fw-bold mb-4">Pengaturan Profil Web</h4>
            
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <form method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-muted">Email Kontak</label>
                            <input type="email" name="email" class="form-control" value="<?= $d['email'] ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-muted">No. WhatsApp (Format: 628xxx)</label>
                            <input type="text" name="telepon" class="form-control" value="<?= $d['telepon'] ?>" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label small fw-bold text-muted">Alamat Kantor</label>
                            <input type="text" name="alamat" class="form-control" value="<?= $d['alamat'] ?>" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label small fw-bold text-muted">Deskripsi Singkat (Tentang Kami)</label>
                            <textarea name="deskripsi" class="form-control" rows="4"><?= $d['deskripsi'] ?></textarea>
                        </div>
                        
                        <div class="col-md-12 mb-4">
                            <label class="form-label small fw-bold text-muted">Foto Kantor / Banner Profile</label>
                            <?php if(!empty($d['foto_kantor'])): ?>
                                <div class="mb-2">
                                    <img src="../<?= $d['foto_kantor'] ?>" style="height: 100px; border-radius: 10px;">
                                </div>
                            <?php endif; ?>
                            <input type="file" name="foto_kantor" class="form-control">
                            <div class="form-text">Biarkan kosong jika tidak ingin mengubah foto.</div>
                        </div>
                    </div>
                    
                    <button name="simpan" class="btn btn-black px-5">Simpan Perubahan</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>