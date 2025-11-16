<?php
// Inisialisasi Database
require 'config/database.php';
include 'includes/header.php';

$kode_pesanan = isset($_GET['kode_pesanan']) ? mysqli_real_escape_string($koneksi, $_GET['kode_pesanan']) : '';
$pesanan = null;
$message = '';
$message_type = 'error'; 

// Proses Pencarian Kode Pesanan
if ($_SERVER["REQUEST_METHOD"] == "GET" && !empty($kode_pesanan)) {
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
        }
        mysqli_stmt_close($stmt);
    } else {
        $message = "Terjadi kesalahan pada database.";
    }
} elseif ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['kode_pesanan'])) {
    $message = "Silakan masukkan kode pesanan Anda.";
}
?>

<a href="index.php" class="back-button">
    <i data-feather="arrow-left"></i> Kembali
</a>

<h1 class="page-title">Cek Status Pesanan</h1>

<div class="form-container" style="margin-bottom: 30px;">
    
    <?php if ($message && !$pesanan): ?>
        <div class="message-box <?php echo $message_type; ?>"><?php echo $message; ?></div>
    <?php endif; ?>
    
    <form action="status.php" method="GET">
        <div class="form-group">
            <label for="kode_pesanan">Masukkan Kode Pesanan Anda</label>
            <input type="text" id="kode_pesanan" name="kode_pesanan" class="form-control" value="<?php echo htmlspecialchars($kode_pesanan); ?>" placeholder="Contoh: GHS-123456789" required>
        </div>
        <button type="submit" class="btn">Cari Pesanan</button>
    </form>
</div>

<?php if ($pesanan): ?>
<div class="status-box">
    <h2 style="margin-bottom: 20px;">Detail Pesanan Anda</h2>
    
    <div class="status-info-kode-wrapper">
        <strong style="min-width: 120px;">Kode Pesanan:</strong> 
        <span id="kode-pesanan-teks" class="status-info-kode"><?php echo htmlspecialchars($pesanan['kode_pesanan']); ?></span>
        <button class="btn btn-secondary btn-copy btn-icon" onclick="salinKode()">
            <i data-feather="copy"></i> <span id="salin-text">Salin</span>
        </button>
    </div>
    
    <?php
        // Logika Badge Status
        $status = $pesanan['status'];
        $status_class = 'status-menunggu';
        if ($status == 'berhasil') {
            $status_class = 'status-selesai';
        } elseif ($status == 'gagal') {
            $status_class = 'status-dibatalkan';
        }
    ?>
    
    <p class="status-info">
        <strong>Status:</strong> 
        <span class="status-badge <?php echo $status_class; ?>">
            <?php echo htmlspecialchars(ucfirst($status)); ?>
        </span>
    </p>
    <p class="status-info"><strong>Produk:</strong> <?php echo htmlspecialchars($pesanan['nama_produk']); ?></p>
    <p class="status-info"><strong>Jumlah:</strong> <?php echo $pesanan['jumlah']; ?> pcs</p>
    <p class="status-info"><strong>Total:</strong> Rp <?php echo number_format($pesanan['total_harga'], 0, ',', '.'); ?></p>
    <p class="status-info"><strong>Atas Nama:</strong> <?php echo htmlspecialchars($pesanan['nama_pembeli']); ?></p>
    <p class="status-info"><strong>Alamat:</strong> <?php echo htmlspecialchars($pesanan['alamat']); ?></p>
    
    <?php if ($pesanan['status'] == 'menunggu'): ?>
    <div class="message-box" style="background-color: var(--bg-light); margin-top: 20px; box-shadow: none;">
        <h3 style="margin-bottom: 15px; color: var(--primary-dark);">Menunggu Diproses Admin</h3>
        <p style="font-size: 0.9rem; color: var(--text-light); font-weight: 500;">
            Pesanan Anda telah kami terima dan akan segera diperiksa oleh admin.
            <br><br>
            Silakan cek status pesanan Anda secara berkala. Admin mungkin akan menghubungi Anda melalui WhatsApp jika diperlukan.
        </p>
    </div>
    <?php elseif ($pesanan['status'] == 'berhasil'): ?>
     <div class="message-box success">
        <h3 style="margin-bottom: 15px; color: #065F46;">Pesanan Selesai</h3>
        <p style="font-size: 0.9rem; font-weight: 500;">
            Pesanan Anda telah selesai. Terima kasih telah berbelanja!
        </p>
    </div>
    <?php elseif ($pesanan['status'] == 'gagal'): ?>
    <div class="message-box error" style="margin-top: 20px;">
        <h3 style="margin-bottom: 10px; color: #991B1B;">Pesanan Dibatalkan</h3>
        <?php if (!empty($pesanan['pesan_admin'])): ?>
            <p style="font-weight: 500;"><strong>Alasan:</strong> <?php echo nl2br(htmlspecialchars($pesanan['pesan_admin'])); ?></p>
        <?php else: ?>
             <p style="font-weight: 500;">Pesanan Anda telah dibatalkan.</p>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <?php if ($pesanan['status'] != 'gagal' && !empty($pesanan['pesan_admin'])): ?>
    <div class="message-box" style="margin-top: 20px; background-color: #f3f4f6; border: 1px solid var(--border-color); color: var(--secondary);">
        <h3 style="margin-bottom: 10px; color: var(--secondary);">Catatan dari Admin:</h3>
        <p style="font-weight: 500; font-size: 0.9rem;"><?php echo nl2br(htmlspecialchars($pesanan['pesan_admin'])); ?></p>
    </div>
    <?php endif; ?>
</div>

<script>
function salinKode() {
    var kodePesanan = document.getElementById("kode-pesanan-teks").innerText;
    var salinButtonText = document.getElementById("salin-text");
    
    // Coba Salin Modern
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(kodePesanan).then(function() {
            salinButtonText.innerText = "Tersalin!";
            setTimeout(function() {
                salinButtonText.innerText = "Salin";
            }, 2000);
        }).catch(function(err) {
            fallbackSalin(); 
        });
    } else {
        fallbackSalin(); 
    }

    // Fallback untuk Browser Lama
    function fallbackSalin() {
        var tempTextarea = document.createElement("textarea");
        tempTextarea.value = kodePesanan;
        tempTextarea.style.position = "fixed"; 
        tempTextarea.style.left = "-9999px";
        document.body.appendChild(tempTextarea);
        
        tempTextarea.select();
        tempTextarea.setSelectionRange(0, 99999); 
        
        try {
            document.execCommand("copy");
            salinButtonText.innerText = "Tersalin!";
            setTimeout(function() {
                salinButtonText.innerText = "Salin";
            }, 2000);
        } catch (err) {
            alert("Gagal menyalin kode. Silakan salin secara manual.");
        }
        
        document.body.removeChild(tempTextarea);
    }
}
</script>

<?php endif; ?>

<?php
include 'includes/footer.php';
?>