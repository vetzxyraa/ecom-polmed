<?php
// Inisialisasi Session dan Database
require 'includes/session_check.php';
require '../config/database.php';

// Ambil ID Produk
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$produk = [
    'id' => $id,
    'nama_produk' => '',
    'harga' => '',
    'stok' => '',
    'deskripsi' => '',
    'gambar' => '',
    'gambar2' => ''
];
$page_title = "Tambah Produk Baru";
$message = '';
$message_type = 'error'; 

// Mode Edit: Ambil Data Produk
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

// Fungsi Proses Upload Gambar
function process_image_upload($file_input_name, $existing_image_name, $product_name_prefix) {
    global $message, $message_type;
    $new_image_name = $existing_image_name;
    
    // Cek Jika Ada File Diupload
    if (isset($_FILES[$file_input_name]) && $_FILES[$file_input_name]['error'] == UPLOAD_ERR_OK) {
        $target_dir = "../assets/images/products/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }
        
        // Buat Nama File Unik
        $file_info = pathinfo($_FILES[$file_input_name]["name"]);
        $file_extension = strtolower($file_info['extension']);
        $new_image_name = $product_name_prefix . time() . "." . $file_extension;
        $target_file = $target_dir . $new_image_name;
        
        $allowed_types = ['jpg', 'jpeg', 'png'];
        $max_file_size = 2 * 1024 * 1024; // 2MB

        // Validasi File
        if (!in_array($file_extension, $allowed_types)) {
            $message .= " Gagal upload $file_input_name: Format file tidak didukung. ";
            $message_type = 'error';
            return $existing_image_name;
        } elseif ($_FILES[$file_input_name]["size"] > $max_file_size) {
            $message .= " Gagal upload $file_input_name: Ukuran file terlalu besar (Maks 2MB). ";
            $message_type = 'error';
            return $existing_image_name;
        } elseif (!getimagesize($_FILES[$file_input_name]["tmp_name"])) {
             $message .= " Gagal upload $file_input_name: File bukan gambar. ";
             $message_type = 'error';
             return $existing_image_name;
        } else {
            // Pindahkan File
            if (move_uploaded_file($_FILES[$file_input_name]["tmp_name"], $target_file)) {
                // Hapus Gambar Lama Jika Ada
                if (!empty($existing_image_name) && filter_var($existing_image_name, FILTER_VALIDATE_URL) === FALSE && file_exists($target_dir . $existing_image_name)) {
                    unlink($target_dir . $existing_image_name);
                }
                return $new_image_name;
            } else {
                $message .= " Gagal memindahkan file $file_input_name. ";
                $message_type = 'error';
                return $existing_image_name;
            }
        }
    } elseif (isset($_FILES[$file_input_name]) && $_FILES[$file_input_name]['error'] != UPLOAD_ERR_NO_FILE) {
        $message .= " Error upload $file_input_name: Kode " . $_FILES[$file_input_name]['error'];
        $message_type = 'error';
    }
    
    return $new_image_name;
}

// Proses Simpan Form (POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = (int)$_POST['id'];
    $nama_produk = mysqli_real_escape_string($koneksi, $_POST['nama_produk']);
    $harga = (int)$_POST['harga'];
    $stok = (int)$_POST['stok'];
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    
    $gambar_lama = mysqli_real_escape_string($koneksi, $_POST['gambar_lama']);
    $gambar_lama_2 = mysqli_real_escape_string($koneksi, $_POST['gambar_lama_2']);

    // Validasi Input Dasar
    if (empty($nama_produk) || $harga <= 0 || $stok < 0) {
        $message = "Nama produk, harga (harus > 0), dan stok (harus >= 0) wajib diisi.";
    } else {
        // Proses Gambar
        $gambar_baru_nama = process_image_upload('gambar', $gambar_lama, "product1_");
        $gambar_baru_nama_2 = process_image_upload('gambar2', $gambar_lama_2, "product2_");

        // Gambar Default Jika Tambah Baru dan Tidak Upload
        if ($id == 0 && empty($gambar_baru_nama)) {
             $gambar_baru_nama = "https://placehold.co/800x800/E5E7EB/374151?text=" . urlencode($nama_produk);
        }
        
        if ($message_type !== 'error') {
            $message = "Produk berhasil disimpan.";
            $message_type = 'success';
        }

        // Update (Edit)
        if ($id > 0) {
            $sql = "UPDATE produk SET nama_produk = ?, harga = ?, stok = ?, deskripsi = ?, gambar = ?, gambar2 = ? WHERE id = ?";
            if ($stmt = mysqli_prepare($koneksi, $sql)) {
                mysqli_stmt_bind_param($stmt, "siisssi", $nama_produk, $harga, $stok, $deskripsi, $gambar_baru_nama, $gambar_baru_nama_2, $id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                header("Location: products.php?status=success");
                exit;
            }
        // Insert (Tambah Baru)
        } else {
            $sql = "INSERT INTO produk (nama_produk, harga, stok, deskripsi, gambar, gambar2) VALUES (?, ?, ?, ?, ?, ?)";
            if ($stmt = mysqli_prepare($koneksi, $sql)) {
                mysqli_stmt_bind_param($stmt, "siisss", $nama_produk, $harga, $stok, $deskripsi, $gambar_baru_nama, $gambar_baru_nama_2);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                header("Location: products.php?status=success");
                exit;
            }
        }
        $message = "Gagal menyimpan data ke database.";
        $message_type = 'error';
    }
    
    // Set Data ke Form Jika Gagal
    $produk['nama_produk'] = $nama_produk;
    $produk['harga'] = $harga;
    $produk['stok'] = $stok;
    $produk['deskripsi'] = $deskripsi;
    $produk['gambar'] = $gambar_baru_nama;
    $produk['gambar2'] = $gambar_baru_nama_2;
}

include 'includes/header.php';

// Fungsi Tampilkan Preview Gambar
function display_image_preview($gambar_url) {
    if (!empty($gambar_url)) {
        if (filter_var($gambar_url, FILTER_VALIDATE_URL) === FALSE) {
            $gambar_url = '../assets/images/products/' . htmlspecialchars($gambar_url);
        }
        echo '<img src="' . $gambar_url . '" alt="Gambar Produk" style="max-width: 150px; height: auto; display: block; margin-bottom: 10px; border-radius: 4px; border: 1px solid var(--border-color);">';
    }
}
?>

<a href="products.php" class="back-button">
    <i data-feather="arrow-left"></i> Kembali
</a>

<h1 class="page-title"><?php echo $page_title; ?></h1>

<div class="form-container">

    <?php if ($message): ?>
        <div class="message-box <?php echo $message_type; ?>"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <form action="edit-product.php<?php echo ($id > 0) ? '?id='.$id : ''; ?>" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $produk['id']; ?>">
        <input type="hidden" name="gambar_lama" value="<?php echo htmlspecialchars($produk['gambar']); ?>">
        <input type="hidden" name="gambar_lama_2" value="<?php echo htmlspecialchars($produk['gambar2']); ?>">
        
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
        
        <hr style="border: 0; border-top: 1px solid var(--border-color); margin: 30px 0;">

        <div class="form-group">
            <label for="gambar">Gambar Produk 1 (Utama)</label>
            <?php display_image_preview($produk['gambar']); ?>
            <input type="file" id="gambar" name="gambar" class="form-control form-control-file" accept="image/jpeg,image/png">
            <small>Kosongkan jika tidak ingin mengubah gambar 1.</small>
        </div>
        
        <div class="form-group">
            <label for="gambar2">Gambar Produk 2 (Opsional)</label>
            <?php display_image_preview($produk['gambar2']); ?>
            <input type="file" id="gambar2" name="gambar2" class="form-control form-control-file" accept="image/jpeg,image/png">
            <small>Kosongkan jika tidak ingin mengubah gambar 2.</small>
        </div>
        
        <hr style="border: 0; border-top: 1px solid var(--border-color); margin: 30px 0;">

        <button type="submit" class="btn">Simpan Produk</button>
        <a href="products.php" class.btn-secondary" style="margin-left: 10px;">Batal</a>
    </form>
</div>

<?php
include 'includes/footer.php';
?>