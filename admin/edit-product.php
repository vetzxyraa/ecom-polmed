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
$message_type = 'error'; // Default ke error, ubah ke success jika berhasil

if ($id > 0) {
    $page_title = "Edit Produk";
    $sql_select = "SELECT * FROM produk WHERE id = ?";
    if ($stmt_select = mysqli_prepare($koneksi, $sql_select)) {
        mysqli_stmt_bind_param($stmt_select, "i", $id);
        mysqli_stmt_execute($stmt_select);
        $result = mysqli_stmt_get_result($stmt_select);
        $produk = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt_select);
        
        if (!$produk) {
            header("Location: products.php?status=error");
            exit;
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = (int)$_POST['id'];
    $nama_produk = mysqli_real_escape_string($koneksi, $_POST['nama_produk']);
    $harga = (int)$_POST['harga'];
    $stok = (int)$_POST['stok'];
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $gambar_lama = mysqli_real_escape_string($koneksi, $_POST['gambar_lama']);
    $gambar_baru_nama = $gambar_lama; // Default ke gambar lama

    // Validasi input dasar
    if (empty($nama_produk) || $harga <= 0 || $stok < 0) {
        $message = "Nama produk, harga (harus > 0), dan stok (harus >= 0) wajib diisi.";
    } else {
        // Logika upload gambar baru
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == UPLOAD_ERR_OK) {
            $target_dir = "../assets/images/products/";
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0755, true);
            }
            
            $file_info = pathinfo($_FILES["gambar"]["name"]);
            $file_extension = strtolower($file_info['extension']);
            $gambar_baru_nama = "product_" . time() . "." . $file_extension;
            $target_file = $target_dir . $gambar_baru_nama;
            
            $allowed_types = ['jpg', 'jpeg', 'png'];
            $max_file_size = 2 * 1024 * 1024; // 2MB

            if (!in_array($file_extension, $allowed_types)) {
                $message = "Gagal upload: Format file tidak didukung. Hanya .jpg, .jpeg, atau .png.";
            } elseif ($_FILES["gambar"]["size"] > $max_file_size) {
                $message = "Gagal upload: Ukuran file terlalu besar (Maks 2MB).";
            } elseif (!getimagesize($_FILES["gambar"]["tmp_name"])) {
                 $message = "Gagal upload: File yang diupload bukan gambar.";
            } else {
                if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
                    // Hapus gambar lama jika ada, bukan URL, dan beda nama
                    if (!empty($gambar_lama) && filter_var($gambar_lama, FILTER_VALIDATE_URL) === FALSE && file_exists($target_dir . $gambar_lama)) {
                        unlink($target_dir . $gambar_lama);
                    }
                    $message = "Gambar baru berhasil diupload."; // Pesan sementara, akan ditimpa sukses
                    $message_type = 'success';
                } else {
                    $message = "Terjadi kesalahan saat memindahkan file gambar.";
                    $gambar_baru_nama = $gambar_lama; // Kembalikan ke gambar lama jika gagal
                }
            }
        } elseif (isset($_FILES['gambar']) && $_FILES['gambar']['error'] != UPLOAD_ERR_NO_FILE) {
            // Error upload selain "tidak ada file"
            $message = "Terjadi error saat upload gambar: Kode " . $_FILES['gambar']['error'];
        }

        // Jika tidak ada error dari validasi gambar ATAU tidak ada gambar baru diupload
        if (empty($message) || $message_type == 'success') {
            
            // Logika Placeholder: Jika ini produk BARU (id=0) dan tidak ada gambar yg diupload
            if ($id == 0 && empty($gambar_baru_nama)) {
                 $gambar_baru_nama = "https://placehold.co/800x800/E5E7EB/374151?text=" . urlencode($nama_produk);
            }
            
            // Proses ke Database
            if ($id > 0) {
                // Update
                $sql = "UPDATE produk SET nama_produk = ?, harga = ?, stok = ?, deskripsi = ?, gambar = ? WHERE id = ?";
                if ($stmt = mysqli_prepare($koneksi, $sql)) {
                    mysqli_stmt_bind_param($stmt, "siissi", $nama_produk, $harga, $stok, $deskripsi, $gambar_baru_nama, $id);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                    header("Location: products.php?status=success");
                    exit;
                }
            } else {
                // Insert
                $sql = "INSERT INTO produk (nama_produk, harga, stok, deskripsi, gambar) VALUES (?, ?, ?, ?, ?)";
                if ($stmt = mysqli_prepare($koneksi, $sql)) {
                    mysqli_stmt_bind_param($stmt, "siiss", $nama_produk, $harga, $stok, $deskripsi, $gambar_baru_nama);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                    header("Location: products.php?status=success");
                    exit;
                }
            }
            // Jika query gagal (jarang terjadi jika koneksi berhasil)
            $message = "Gagal menyimpan data ke database.";
            $message_type = 'error';
        }
    }
    
    // Set ulang nilai $produk untuk ditampilkan di form jika terjadi error
    $produk['nama_produk'] = $nama_produk;
    $produk['harga'] = $harga;
    $produk['stok'] = $stok;
    $produk['deskripsi'] = $deskripsi;
    $produk['gambar'] = $gambar_baru_nama; // Tampilkan gambar baru (jika gagal) atau lama
}

include 'includes/header.php';
?>

<!-- Tombol kembali -->
<a href="products.php" class="back-button">
    <i data-feather="arrow-left"></i> Kembali
</a>

<h1 class="page-title"><?php echo $page_title; ?></h1>

<div class_alias="form-container">

    <?php if ($message): ?>
        <div class="message-box <?php echo $message_type; ?>"><?php echo htmlspecialchars($message); ?></div>
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
            <input type="number" id="harga" name="harga" class="form-control" value="<?php echo htmlspecialchars($produk['harga']); ?>" min="1" required>
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
                // Cek jika gambar adalah URL dari placehold.co atau file lokal
                if (filter_var($gambar_url, FILTER_VALIDATE_URL) === FALSE) {
                    $gambar_url = '../assets/images/products/' . htmlspecialchars($gambar_url);
                }
            ?>
                <img src="<?php echo $gambar_url; ?>" alt="Gambar Produk Saat Ini" style="max-width: 150px; height: auto; display: block; margin-bottom: 10px; border-radius: 4px; border: 1px solid var(--border-color);">
            <?php endif; ?>
            
            <input type="file" id="gambar" name="gambar" class="form-control form-control-file" accept="image/jpeg,image/png">
            <small>Hanya file .jpg atau .png. Maks 2MB. Kosongkan jika tidak ingin mengubah gambar.</small>
        </div>
        
        <button type="submit" class="btn">Simpan Produk</button>
        <a href="products.php" class="btn btn-secondary" style="margin-left: 10px;">Batal</a>
    </form>
</div>

<?php
include 'includes/footer.php';
?>