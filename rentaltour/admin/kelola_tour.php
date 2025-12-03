<?php
session_start();
include '../config/db.php';
if(!isset($_SESSION['admin'])) header("location:index.php");

if(isset($_POST['simpan'])){
    $nama = $_POST['nama']; $durasi = $_POST['durasi']; $harga = $_POST['harga'];
    
    $path = "";
    if(!empty($_FILES['gambar']['name'])){
        $new = date('dmYHis').$_FILES['gambar']['name'];
        move_uploaded_file($_FILES['gambar']['tmp_name'], "../assets/img/".$new);
        $path = "assets/img/".$new;
    }
    
    mysqli_query($koneksi, "INSERT INTO paket_tour VALUES (NULL, '$nama', '$durasi', '$harga', '$path')");
    echo "<script>window.location='kelola_tour.php'</script>";
}

if(isset($_GET['hapus'])){
    mysqli_query($koneksi, "DELETE FROM paket_tour WHERE id='$_GET[hapus]'");
    echo "<script>window.location='kelola_tour.php'</script>";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Kelola Tour</title>
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
                <a href="kelola_tour.php" class="active"><i class="bi bi-map"></i> Tour</a>
                <a href="kelola_rental.php"><i class="bi bi-receipt"></i> Pesanan</a>
                <a href="logout.php" class="text-danger mt-5">Logout</a>
            </nav>
        </div>

        <div class="main-content">
            <h4 class="fw-bold mb-4">Paket Tour</h4>
            <div class="row">
                <div class="col-md-4">
                    <div class="card p-4 border-0 shadow-sm rounded-4">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="mb-3"><input type="text" name="nama" class="form-control" placeholder="Nama Paket" required></div>
                            <div class="mb-3"><input type="text" name="durasi" class="form-control" placeholder="Durasi (ex: 3 Hari)" required></div>
                            <div class="mb-3"><input type="number" name="harga" class="form-control" placeholder="Harga / Pax" required></div>
                            <div class="mb-3"><input type="file" name="gambar" class="form-control"></div>
                            <button name="simpan" class="btn btn-black w-100">Tambah Data</button>
                        </form>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="bg-light"><tr><th class="ps-4">Paket</th><th>Durasi</th><th>Harga</th><th></th></tr></thead>
                            <tbody>
                                <?php $q=mysqli_query($koneksi,"SELECT * FROM paket_tour ORDER BY id DESC"); while($r=mysqli_fetch_array($q)){ ?>
                                <tr>
                                    <td class="ps-4 fw-bold"><?= $r['nama'] ?></td>
                                    <td><span class="badge bg-info bg-opacity-10 text-dark border"><?= $r['durasi'] ?></span></td>
                                    <td>Rp <?= number_format($r['harga']) ?></td>
                                    <td class="text-end pe-4"><a href="?hapus=<?=$r['id']?>" class="text-danger"><i class="bi bi-trash-fill"></i></a></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>