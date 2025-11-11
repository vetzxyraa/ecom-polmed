<?php
require 'config/database.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$produk = null;
$message = '';
$message_type = '';
$order_success = false;
$new_order_code = '';

if ($id > 0) {
    $sql = "SELECT * FROM produk WHERE id = $id AND stok > 0";
    $result = mysqli_query($koneksi, $sql);
    $produk = mysqli_fetch_assoc($result);
}

if (!$produk) {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_pembeli = mysqli_real_escape_string($koneksi, $_POST['nama_pembeli']);
    $no_hp = mysqli_real_escape_string($koneksi, $_POST['no_hp']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $jumlah = (int)$_POST['jumlah'];
    
    if ($jumlah > 0 && $jumlah <= $produk['stok']) {
        $total_harga = $jumlah * $produk['harga'];
        $kode_pesanan = "GHS-" . time() . rand(10, 99);
        
        // Perhatikan: kolom 'bukti_pembayaran' tidak lagi diisi di sini
        $sql_insert = "INSERT INTO pesanan (produk_id, kode_pesanan, nama_pembeli, no_hp, alamat, jumlah, total_harga, status) 
                       VALUES (?, ?, ?, ?, ?, ?, ?, 'menunggu')";
        
        if ($stmt = mysqli_prepare($koneksi, $sql_insert)) {
            mysqli_stmt_bind_param($stmt, "issssis", $produk['id'], $kode_pesanan, $nama_pembeli, $no_hp, $alamat, $jumlah, $total_harga);
            
            if (mysqli_stmt_execute($stmt)) {
                $sql_update_stok = "UPDATE produk SET stok = stok - ? WHERE id = ?";
                if ($stmt_stok = mysqli_prepare($koneksi, $sql_update_stok)) {
                    mysqli_stmt_bind_param($stmt_stok, "ii", $jumlah, $produk['id']);
                    mysqli_stmt_execute($stmt_stok);
                    mysqli_stmt_close($stmt_stok);
                }
                
                $order_success = true;
                $new_order_code = $kode_pesanan;
                $message = "Pesanan Anda berhasil dibuat! Kode Pesanan Anda: <strong>$kode_pesanan</strong>. Silakan cek status pesanan Anda.";
                $message_type = 'success';
            } else {
                $message = "Gagal membuat pesanan. Silakan coba lagi.";
                $message_type = 'error';
            }
            mysqli_stmt_close($stmt);
        }
    } else {
        $message = "Jumlah pesanan tidak valid atau stok tidak mencukupi.";
        $message_type = 'error';
    }
}

include 'includes/header.php';
?>

<a href="javascript:history.back()" class="back-button">
    <i data-feather="arrow-left"></i> Kembali ke Detail
</a>

<?php if (!$order_success): ?>
    <h1 class="page-title">Form Pemesanan</h1>
    <div class="form-container">
        
        <?php if ($message): ?>
            <div class="message-box <?php echo $message_type; ?>"><?php echo $message; ?></div>
        <?php endif; ?>

        <div style="margin-bottom: 25px; padding: 20px; background: #FFFBEB; border: 1px solid var(--primary); border-radius: 10px;">
            <h3 style="margin-bottom: 8px; font-size: 1.15rem; color: var(--primary-dark);">Produk yang Dipesan:</h3>
            <p style="font-weight: 600; font-size: 1.25rem; margin-bottom: 5px;"><?php echo htmlspecialchars($produk['nama_produk']); ?></p>
            <p style="font-size: 1.05rem; color: var(--text-light);">Harga Satuan: Rp <?php echo number_format($produk['harga'], 0, ',', '.'); ?></p>
        </div>
        
        <form action="checkout.php?id=<?php echo $produk['id']; ?>" method="POST">
            <div class="form-group">
                <label for="nama_pembeli">Nama Lengkap</label>
                <input type="text" id="nama_pembeli" name="nama_pembeli" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="no_hp">No. HP (WhatsApp)</label>
                <input type="tel" id="no_hp" name="no_hp" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="alamat">Alamat Lengkap</label>
                <textarea id="alamat" name="alamat" class="form-control" required></textarea>
            </div>
            <div class="form-group">
                <label for="jumlah">Jumlah</label>
                <input type="number" id="jumlah" name="jumlah" class="form-control" value="1" min="1" max="<?php echo $produk['stok']; ?>" required>
            </div>
            <button type="submit" class="btn">Buat Pesanan</button>
        </form>
    </div>
<?php else: ?>
    <div class="status-box">
        <h1 class="page-title" style="color: var(--success); margin-bottom: 1.5rem;">Pesanan Berhasil!</h1>
        <div class="message-box success"><?php echo $message; ?></div>
        <p style="margin: 20px 0; font-size: 1.05rem;">Silakan cek status pesanan Anda secara berkala di halaman "Cek Status" dengan menggunakan kode pesanan di atas.</p>
        <a href="status.php?kode_pesanan=<?php echo $new_order_code; ?>" class="btn">Cek Status Sekarang</a>
        <a href="index.php" class="btn btn-secondary" style="margin-top: 15px;">Kembali ke Home</a>
    </div>
<?php endif; ?>

<?php
include 'includes/footer.php';
?>