<?php
require 'config/database.php';
include 'includes/header.php';

$kode_pesanan = isset($_GET['kode_pesanan']) ? mysqli_real_escape_string($koneksi, $_GET['kode_pesanan']) : '';
$pesanan = null;
$produk_pesanan = null;
$message = '';
$message_type = '';

if (!empty($kode_pesanan)) {
    $sql = "SELECT p.*, pr.nama_produk, pr.gambar 
            FROM pesanan p 
            JOIN produk pr ON p.produk_id = pr.id 
            WHERE p.kode_pesanan = ?";
            
    if ($stmt = mysqli_prepare($koneksi, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $kode_pesanan);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($result) == 1) {
            $pesanan = mysqli_fetch_assoc($result);
        } else {
            $message = "Kode pesanan tidak ditemukan.";
            $message_type = 'error';
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<a href="javascript:history.back()" class="back-button">
    <i data-feather="arrow-left"></i> Kembali
</a>

<h1 class="page-title">Cek Status Pesanan</h1>

<div class="form-container" style="margin-bottom: 30px;">
    <form action="status.php" method="GET">
        <div class="form-group">
            <label for="kode_pesanan">Masukkan Kode Pesanan</label>
            <input type="text" id="kode_pesanan" name="kode_pesanan" class="form-control" value="<?php echo htmlspecialchars($kode_pesanan); ?>" required>
        </div>
        <button type="submit" class="btn">Cari Pesanan</button>
    </form>
</div>

<?php if ($message && !$pesanan): ?>
    <div class="message-box <?php echo $message_type; ?>"><?php echo $message; ?></div>
<?php endif; ?>

<?php if ($pesanan): ?>
<div class="status-box">
    <h2 style="margin-bottom: 20px;">Detail Pesanan Anda</h2>
    
    <p class="status-info">
        <strong>Kode Pesanan:</strong> 
        <span class="status-info-kode"><?php echo htmlspecialchars($pesanan['kode_pesanan']); ?></span>
    </p>
    
    <?php
        $status = $pesanan['status'];
        $status_class = '';
        if (in_array($status, ['menunggu', 'diproses', 'menunggu konfirmasi'])) {
            $status_class = 'status-menunggu';
        } elseif (in_array($status, ['selesai', 'dikirim'])) {
            $status_class = 'status-berhasil';
        } elseif ($status == 'dibatalkan') {
            $status_class = 'status-gagal';
        }
    ?>
    
    <p class="status-info">
        <strong>Status:</strong> 
        <span class="status-badge <?php echo $status_class; ?>">
            <?php echo htmlspecialchars($pesanan['status']); ?>
        </span>
    </p>
    
    <p class="status-info"><strong>Produk:</strong> <?php echo htmlspecialchars($pesanan['nama_produk']); ?></p>
    <p class="status-info"><strong>Jumlah:</strong> <?php echo $pesanan['jumlah']; ?> pcs</p>
    <p class="status-info"><strong>Total:</strong> Rp <?php echo number_format($pesanan['total_harga'], 0, ',', '.'); ?></p>
    <p class="status-info"><strong>Atas Nama:</strong> <?php echo htmlspecialchars($pesanan['nama_pembeli']); ?></p>
    <p class="status-info"><strong>Alamat:</strong> <?php echo htmlspecialchars($pesanan['alamat']); ?></p>
    
    <?php if ($pesanan['status'] == 'menunggu'): ?>
    <div class="form-container" style="background-color: var(--bg-light); margin-top: 20px; box-shadow: none;">
        <h3 style="margin-bottom: 15px;">Menunggu Pembayaran</h3>
        <p style="font-size: 0.9rem;">
            Silakan lakukan pembayaran ke No. Rek <strong>BCA 123456789</strong> a/n GantunganHP Store.
            <br><br>
            Setelah itu, mohon konfirmasi pembayaran Anda (misalnya via WhatsApp) kepada admin agar pesanan dapat segera diproses.
        </p>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php
include 'includes/footer.php';
?>