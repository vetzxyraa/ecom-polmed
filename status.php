<?php
require 'config/database.php';
include 'includes/header.php';

$kode_pesanan = isset($_GET['kode_pesanan']) ? mysqli_real_escape_string($koneksi, $_GET['kode_pesanan']) : '';
$pesanan = null;
$message = '';
$message_type = 'error'; // Set default ke error

if ($_SERVER["REQUEST_METHOD"] == "GET" && !empty($kode_pesanan)) {
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
        }
        mysqli_stmt_close($stmt);
    } else {
        $message = "Terjadi kesalahan pada database.";
    }
} elseif ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['kode_pesanan'])) {
    // Jika parameter ada tapi kosong
    $message = "Silakan masukkan kode pesanan Anda.";
}
?>

<a href="index.php" class="back-button">
    <i data-feather="arrow-left"></i> Kembali ke Home
</a>

<h1 class="page-title">Cek Status Pesanan</h1>

<div class="form-container" style="margin-bottom: 30px;">
    
    <?php if ($message && !$pesanan): // Tampilkan error hanya jika pencarian gagal ?>
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
    
    <!-- Wrapper untuk Kode Pesanan dan Tombol Salin -->
    <div class="status-info-kode-wrapper">
        <strong style="min-width: 120px;">Kode Pesanan:</strong> 
        <span id="kode-pesanan-teks" class="status-info-kode"><?php echo htmlspecialchars($pesanan['kode_pesanan']); ?></span>
        <button class="btn btn-secondary btn-copy" onclick="salinKode()">
            <i data-feather="copy"></i> <span id="salin-text">Salin</span>
        </button>
    </div>
    
    <?php
        $status = $pesanan['status'];
        $status_class = 'status-' . str_replace(' ', '-', $status); // misal: status-menunggu-konfirmasi
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
    
    <?php if (in_array($pesanan['status'], ['menunggu', 'menunggu konfirmasi'])): ?>
    <div class="message-box" style="background-color: var(--bg-light); margin-top: 20px; box-shadow: none;">
        <h3 style="margin-bottom: 15px; color: var(--primary-dark);">Menunggu Diproses Admin</h3>
        <p style="font-size: 0.9rem; color: var(--text-light); font-weight: 500;">
            Pesanan Anda telah kami terima dan akan segera diperiksa oleh admin.
            <br><br>
            Silakan cek status pesanan Anda secara berkala. Admin mungkin akan menghubungi Anda melalui WhatsApp jika diperlukan.
        </p>
    </div>
    <?php elseif ($pesanan['status'] == 'diproses'): ?>
     <div class="message-box" style="background-color: #EBF8FF; margin-top: 20px; box-shadow: none; border: 1px solid #90CDF4; color: #2C5282;">
        <h3 style="margin-bottom: 15px; color: #2B6CB0;">Pesanan Diproses</h3>
        <p style="font-size: 0.9rem; font-weight: 500;">
            Pesanan Anda sedang dalam proses penyiapan dan pengemasan oleh tim kami.
        </p>
    </div>
    <?php elseif ($pesanan['status'] == 'dikirim'): ?>
     <div class="message-box" style="background-color: #E6FFFA; margin-top: 20px; box-shadow: none; border: 1px solid #A7F3D0; color: #234E52;">
        <h3 style="margin-bottom: 15px; color: #2C7A7B;">Pesanan Dikirim</h3>
        <p style="font-size: 0.9rem; font-weight: 500;">
            Pesanan Anda telah dikirim. Mohon ditunggu kedatangannya.
        </p>
    </div>
    <?php elseif ($pesanan['status'] == 'selesai'): ?>
     <div class="message-box success">
        <h3 style="margin-bottom: 15px; color: #065F46;">Pesanan Selesai</h3>
        <p style="font-size: 0.9rem; font-weight: 500;">
            Pesanan Anda telah selesai. Terima kasih telah berbelanja!
        </p>
    </div>
    <?php endif; ?>

    <!-- Menampilkan pesan admin jika status 'dibatalkan' dan ada pesan -->
    <?php if ($pesanan['status'] == 'dibatalkan' && !empty($pesanan['pesan_admin'])): ?>
    <div class="message-box error" style="margin-top: 20px;">
        <h3 style="margin-bottom: 10px; color: #991B1B;">Pesanan Dibatalkan</h3>
        <p style="font-weight: 500;"><strong>Alasan:</strong> <?php echo nl2br(htmlspecialchars($pesanan['pesan_admin'])); ?></p>
    </div>
    <?php elseif ($pesanan['status'] == 'dibatalkan'): ?>
     <div class="message-box error" style="margin-top: 20px;">
        <h3 style="margin-bottom: 10px; color: #991B1B;">Pesanan Dibatalkan</h3>
        <p style="font-weight: 500;">Pesanan Anda telah dibatalkan.</p>
    </div>
    <?php endif; ?>
</div>

<script>
// Fungsi untuk menyalin kode pesanan
function salinKode() {
    var kodePesanan = document.getElementById("kode-pesanan-teks").innerText;
    var salinButtonText = document.getElementById("salin-text");
    
    // Gunakan Clipboard API modern jika tersedia
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(kodePesanan).then(function() {
            salinButtonText.innerText = "Tersalin!";
            setTimeout(function() {
                salinButtonText.innerText = "Salin";
            }, 2000);
        }).catch(function(err) {
            console.error("Gagal menyalin dengan Clipboard API: ", err);
            fallbackSalin(); // Coba metode fallback
        });
    } else {
        fallbackSalin(); // Fallback untuk browser lama atau non-HTTPS
    }

    function fallbackSalin() {
        var tempTextarea = document.createElement("textarea");
        tempTextarea.value = kodePesanan;
        tempTextarea.style.position = "fixed"; // Hindari pergeseran layar
        tempTextarea.style.left = "-9999px";
        document.body.appendChild(tempTextarea);
        
        tempTextarea.select();
        tempTextarea.setSelectionRange(0, 99999); // Untuk mobile
        
        try {
            document.execCommand("copy");
            salinButtonText.innerText = "Tersalin!";
            setTimeout(function() {
                salinButtonText.innerText = "Salin";
            }, 2000);
        } catch (err) {
            console.error("Gagal menyalin dengan fallback: ", err);
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