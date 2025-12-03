<?php
session_start();
include '../config/db.php';
if(!isset($_SESSION['admin'])) header("location:index.php");

// --- LOGIC TAMBAH & UPDATE ---
if(isset($_POST['simpan'])){
    $nama = $_POST['nama']; $merk = $_POST['merk']; 
    $trans = $_POST['transmisi']; $kursi = $_POST['kursi']; $harga = $_POST['harga'];
    
    // Logic Gambar
    $path = $_POST['gambar_lama']; // Default pakai gambar lama
    if(!empty($_FILES['gambar']['name'])){
        $new = date('dmYHis').$_FILES['gambar']['name'];
        move_uploaded_file($_FILES['gambar']['tmp_name'], "../assets/img/".$new);
        $path = "assets/img/".$new;
    }

    if(isset($_POST['id_edit']) && !empty($_POST['id_edit'])){
        // UPDATE DATA
        $id = $_POST['id_edit'];
        mysqli_query($koneksi, "UPDATE mobil SET nama='$nama', merk='$merk', transmisi='$trans', kursi='$kursi', harga='$harga', gambar='$path' WHERE id='$id'");
    } else {
        // INSERT DATA BARU
        mysqli_query($koneksi, "INSERT INTO mobil VALUES (NULL, '$nama', '$merk', '$trans', '$kursi', '$harga', '$path')");
    }
    header("location:kelola_mobil.php");
}

// --- LOGIC HAPUS ---
if(isset($_GET['hapus'])){
    mysqli_query($koneksi, "DELETE FROM mobil WHERE id='$_GET[hapus]'");
    header("location:kelola_mobil.php");
}

// --- AMBIL DATA UNTUK EDIT ---
$edit_data = null;
if(isset($_GET['edit'])){
    $id_edit = $_GET['edit'];
    $edit_data = mysqli_fetch_array(mysqli_query($koneksi, "SELECT * FROM mobil WHERE id='$id_edit'"));
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Kelola Mobil</title>
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
                <a href="kelola_mobil.php" class="active"><i class="bi bi-car-front"></i> Mobil</a>
                <a href="kelola_tour.php"><i class="bi bi-map"></i> Tour</a>
                <a href="kelola_rental.php"><i class="bi bi-receipt"></i> Pesanan</a>
                <a href="kelola_profil.php"><i class="bi bi-gear-fill"></i> Setting</a>
                <a href="logout.php" class="text-danger mt-5">Logout</a>
            </nav>
        </div>

        <div class="main-content">
            <h4 class="fw-bold mb-4">Armada Mobil</h4>
            <div class="row">
                <div class="col-md-4">
                    <div class="card p-4 border-0 shadow-sm rounded-4">
                        <h6 class="fw-bold mb-3"><?= $edit_data ? 'Edit Data' : 'Tambah Baru' ?></h6>
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="id_edit" value="<?= $edit_data['id'] ?? '' ?>">
                            <input type="hidden" name="gambar_lama" value="<?= $edit_data['gambar'] ?? '' ?>">

                            <div class="mb-3"><input type="text" name="nama" class="form-control" placeholder="Nama Mobil" value="<?= $edit_data['nama'] ?? '' ?>" required></div>
                            <div class="mb-3"><input type="text" name="merk" class="form-control" placeholder="Merk" value="<?= $edit_data['merk'] ?? '' ?>" required></div>
                            <div class="row mb-3">
                                <div class="col">
                                    <select name="transmisi" class="form-select">
                                        <option <?= ($edit_data['transmisi']??'')=='Manual'?'selected':'' ?>>Manual</option>
                                        <option <?= ($edit_data['transmisi']??'')=='Matic'?'selected':'' ?>>Matic</option>
                                    </select>
                                </div>
                                <div class="col"><input type="number" name="kursi" class="form-control" placeholder="Seat" value="<?= $edit_data['kursi'] ?? '' ?>" required></div>
                            </div>
                            <div class="mb-3"><input type="number" name="harga" class="form-control" placeholder="Harga / Hari" value="<?= $edit_data['harga'] ?? '' ?>" required></div>
                            <div class="mb-3">
                                <label class="small text-muted">Upload Gambar (Kosongkan jika tidak ganti)</label>
                                <input type="file" name="gambar" class="form-control">
                            </div>
                            
                            <button name="simpan" class="btn btn-black w-100 mb-2"><?= $edit_data ? 'Update Data' : 'Simpan Data' ?></button>
                            <?php if($edit_data): ?>
                                <a href="kelola_mobil.php" class="btn btn-outline w-100">Batal Edit</a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="bg-light"><tr><th class="ps-4">Unit</th><th>Specs</th><th>Harga</th><th>Aksi</th></tr></thead>
                            <tbody>
                                <?php $q=mysqli_query($koneksi,"SELECT * FROM mobil ORDER BY id DESC"); while($r=mysqli_fetch_array($q)){ ?>
                                <tr>
                                    <td class="ps-4 fw-bold"><?= $r['nama'] ?></td>
                                    <td><span class="badge bg-light text-dark border"><?= $r['transmisi'] ?></span> <span class="badge bg-light text-dark border"><?= $r['kursi'] ?> Seat</span></td>
                                    <td>Rp <?= number_format($r['harga']) ?></td>
                                    <td>
                                        <a href="?edit=<?=$r['id']?>" class="btn btn-sm btn-light border me-1"><i class="bi bi-pencil-square"></i></a>
                                        <a href="?hapus=<?=$r['id']?>" class="btn btn-sm btn-light border text-danger" onclick="return confirm('Hapus?')"><i class="bi bi-trash-fill"></i></a>
                                    </td>
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