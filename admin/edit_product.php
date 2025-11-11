<?php
require 'includes/session_check.php';
require '../config/database.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$produk = [
    'id' => $id,
    'nama_produk' => '',
    'harga' => '',
    'stok' => '',
    'deskripsi' => '',
    'gambar' => ''
];
$page_title = "Tambah Produk Baru";
$message = '';
$message_type = '';

if ($id > 0) {
    $page_title = "Edit Produk";
    $sql = "SELECT * FROM produk WHERE id = $id";
    $result = mysqli_query($koneksi, $sql);
    $produk = mysqli_fetch_assoc($result);
    if (!$produk) {
        header("Location: products.php?status=error");
        exit;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = (int)$_POST['id'];
    $nama_produk = mysqli_real_escape_string($koneksi, $_POST['nama_produk']);
    $harga = (int)$_POST['harga'];
    $stok = (int)$_POST['stok'];
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $gambar_lama = mysqli_real_escape_string($koneksi, $_POST['gambar_lama']);
    $gambar_baru_nama = $gambar_lama;

    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $target_dir = "../assets/images/products/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }
        $file_extension = pathinfo($_FILES["gambar"]["name"], PATHINFO_EXTENSION);
        $gambar_baru_nama = "product_" . time() . "." . $file_extension;
        $target_file = $target_dir . $gambar_baru_nama;
        
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array(strtolower($file_extension), $allowed_types) && $_FILES["gambar"]["size"] <= 5000000) {
            if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
                if (!empty($gambar_lama) && filter_var($gambar_lama, FILTER_VALIDATE_URL) === FALSE && file_exists($target_dir . $gambar_lama) && $gambar_lama != $gambar_baru_nama) {
                    unlink($target_dir . $gambar_lama);
                }
            } else {
                $message = "Gagal mengupload gambar baru.";
                $message_type = 'error';
                $gambar_baru_nama = $gambar_lama;
            }
        } else {
            $message = "File gambar tidak valid. Hanya (JPG, JPEG, PNG, GIF) & max 5MB.";
            $message_type = 'error';
            $gambar_baru_nama = $gambar_lama;
        }
    }

    if (empty($nama_produk) || $harga <= 0 || $stok < 0) {
        $message = "Nama, harga, dan stok wajib diisi dengan benar.";
        $message_type = 'error';
    }

    if ($message_type != 'error') {
        if ($id > 0) {
            $sql = "UPDATE produk SET nama_produk = ?, harga = ?, stok = ?, deskripsi = ?, gambar = ? WHERE id = ?";
            if ($stmt = mysqli_prepare($koneksi, $sql)) {
                mysqli_stmt_bind_param($stmt, "siissi", $nama_produk, $harga, $stok, $deskripsi, $gambar_baru_nama, $id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
        } else {
            $sql = "INSERT INTO produk (nama_produk, harga, stok, deskripsi, gambar) VALUES (?, ?, ?, ?, ?)";
            if ($stmt = mysqli_prepare($koneksi, $sql)) {
                mysqli_stmt_bind_param($stmt, "siiss", $nama_produk, $harga, $stok, $deskripsi, $gambar_baru_nama);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
        }
        header("Location: products.php?status=success");
        exit;
    }
    
    $produk['nama_produk'] = $nama_produk;
    $produk['harga'] = $harga;
    $produk['stok'] = $stok;
    $produk['deskripsi'] = $deskripsi;
    $produk['gambar'] = $gambar_baru_nama;
}

include 'includes/header.php';
?>

<h1 class="page-title"><?php echo $page_title; ?></h1>

<div class="form-container">

    <?php if ($message): ?>
        <div class="message-box <?php echo $message_type; ?>"><?php echo $message; ?></div>
    <?php endif; ?>

    <form action="edit-product.php<?php echo ($id > 0) ? '?id='.$id : ''; ?>" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $produk['id']; ?>">
        <input type="hidden" name="gambar_lama" value="<?php echo htmlspecialchars($produk['gambar']); ?>">
        
        <div class="form-group">
            <label for="nama_produk">Nama Produk</label>
            <input type="text" id="nama_produk" name="nama_produk" class="form-control" value="<?php echo htmlspecialchars($produk['nama_produk']); ?>" required>
        </div>
        <div class="form-group">
            <label for="harga">Harga (Rp)</label>
            <input type="number" id="harga" name="harga" class="form-control" value="<?php echo htmlspecialchars($produk['harga']); ?>" min="0" required>
        </div>
        <div class="form-group">
            <label for="stok">Stok</label>
            <input type="number" id="stok" name="stok" class="form-control" value="<?php echo htmlspecialchars($produk['stok']); ?>" min="0" required>
        </div>
        <div class="form-group">
            <label for="deskripsi">Deskripsi</label>
            <textarea id="deskripsi" name="deskripsi" class="form-control" required><?php echo htmlspecialchars($produk['deskripsi']); ?></textarea>
        </div>
        <div class="form-group">
            <label for="gambar">Gambar Produk</label>
            <?php if (!empty($produk['gambar'])): 
                $gambar_url = $produk['gambar'];
                if (filter_var($gambar_url, FILTER_VALIDATE_URL) === FALSE) {
                    $gambar_url = '../assets/images/products/' . htmlspecialchars($gambar_url);
                }
            ?>
                <img src="<?php echo $gambar_url; ?>" style="max-width: 150px; height: auto; display: block; margin-bottom: 10px; border-radius: 4px;">
            <?php endif; ?>
            <input type="file" id="gambar" name="gambar" class="form-control form-control-file" accept="image/*">
            <small>Kosongkan jika tidak ingin mengubah gambar.</small>
        </div>
        
        <button type="submit" class="btn">Simpan Produk</button>
        <a href="products.php" class="btn btn-secondary" style="margin-left: 10px;">Batal</a>
    </form>
</div>

<?php
include 'includes/footer.php';
?>