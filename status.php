<?php
require 'config/database.php';
include 'includes/header.php';

$kode_pesanan = isset($_GET['kode_pesanan']) ? mysqli_real_escape_string($koneksi, $_GET['kode_pesanan']) : '';
$pesanan = null;
$produk_pesanan = null;
$message = '';
$message_type = '';

if (!empty($kode_pesanan)) {
    // MODIFIKASI: JOIN ke produk DAN ambil pesan_admin
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
    
    <!-- PERUBAHAN: Wrapper untuk Kode Pesanan dan Tombol Salin -->
    <div class="status-info-kode-wrapper">
        <strong style="min-width: 120px;">Kode Pesanan:</strong> 
        <span class-></span>
        <span id="kode-pesanan-teks" class="status-info-kode"><?php echo htmlspecialchars($pesanan['kode_pesanan']); ?></span>
        <button class="btn-copy" onclick="salinKode()">
            <i data-feather="copy"></i> <span id="salin-text">Salin</span>
        </button>
    </div>
    
    <?php
        $status = $pesanan['status'];
        $status_class = '';
        // PERUBAHAN: Status warna
        if (in_array($status, ['menunggu', 'diproses', 'menunggu konfirmasi'])) {
            $status_class = 'status-menunggu'; // Kuning
        } elseif (in_array($status, ['selesai', 'dikirim'])) {
            $status_class = 'status-berhasil'; // Hijau
        } elseif ($status == 'dibatalkan') {
            $status_class = 'status-gagal'; // Merah
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
    <!-- PERUBAHAN: Pesan pembayaran diganti -->
    <div class="message-box" style="background-color: var(--bg-light); margin-top: 20px; box-shadow: none;">
        <h3 style="margin-bottom: 15px; color: var(--primary-dark);">Menunggu Diproses Admin</h3>
        <p style="font-size: 0.9rem; color: var(--text-light); font-weight: 500;">
            Pesanan Anda telah kami terima dan akan segera diperiksa oleh admin.
            <br><br>
            Silakan cek status pesanan Anda secara berkala. Admin mungkin akan menghubungi Anda melalui WhatsApp jika diperlukan.
        </p>
    </div>
    <?php endif; ?>

    <!-- PERUBAHAN: Menampilkan pesan admin jika status 'dibatalkan' dan ada pesan -->
    <?php if ($pesanan['status'] == 'dibatalkan' && !empty($pesanan['pesan_admin'])): ?>
    <div class="message-box error admin-message">
        <h3 style="margin-bottom: 10px; color: #991B1B;">Pesan dari Admin:</h3>
        <p><?php echo nl2br(htmlspecialchars($pesanan['pesan_admin'])); ?></p>
    </div>
    <?php endif; ?>
</div>

<script>
// Fungsi untuk menyalin kode pesanan
function salinKode() {
    // Ambil teks kode pesanan
    var kodePesanan = document.getElementById("kode-pesanan-teks").innerText;
    
    // Buat elemen textarea sementara
    var tempTextarea = document.createElement("textarea");
    tempTextarea.value = kodePesanan;
    document.body.appendChild(tempTextarea);
    
    // Pilih dan salin teks
    tempTextarea.select();
    tempTextarea.setSelectionRange(0, 99999); // Untuk mobile
    
    try {
        document.execCommand("copy"); // Salin ke clipboard
        
        // Ubah teks tombol
        var salinButtonText = document.getElementById("salin-text");
        salinButtonText.innerText = "Tersalin!";
        
        // Kembalikan teks tombol setelah 2 detik
        setTimeout(function() {
            salinButtonText.innerText = "Salin";
        }, 2000);
        
    } catch (err) {
        console.error("Gagal menyalin kode: ", err);
    }
    
    // Hapus elemen textarea sementara
    document.body.removeChild(tempTextarea);
}
</script>

<?php endif; ?>

<?php
include 'includes/footer.php';
?>