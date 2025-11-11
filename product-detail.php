<?php
require 'config/database.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$produk = null;

if ($id > 0) {
    $sql = "SELECT * FROM produk WHERE id = $id";
    $result = mysqli_query($koneksi, $sql);
    $produk = mysqli_fetch_assoc($result);
}

if (!$produk) {
    header("Location: index.php");
    exit;
}

include 'includes/header.php';
?>

<a href="javascript:history.back()" class="back-button">
    <i data-feather="arrow-left"></i> Kembali
</a>

<div class="product-detail-container">
    <div class="product-detail-image">
        <?php
        $gambar_url = $produk['gambar'];
        if (!filter_var($gambar_url, FILTER_VALIDATE_URL)) {
            $gambar_url = 'assets/images/products/' . htmlspecialchars($gambar_url);
        }
        ?>
        <img src="<?php echo $gambar_url; ?>" alt="<?php echo htmlspecialchars($produk['nama_produk']); ?>">
    </div>
    <div class="product-detail-info">
        <h1 class="product-name"><?php echo htmlspecialchars($produk['nama_produk']); ?></h1>
        <p class="product-price">Rp <?php echo number_format($produk['harga'], 0, ',', '.'); ?></p>
        <p class="product-stock">Stok Tersedia: <?php echo $produk['stok']; ?></p>
        <div class="product-description">
            <p><?php echo nl2br(htmlspecialchars($produk['deskripsi'])); ?></p>
        </div>
        
        <!-- Wrapper untuk tombol-tombol aksi -->
        <div class="product-detail-actions">
            <?php if ($produk['stok'] > 0): ?>
                <a href="checkout.php?id=<?php echo $produk['id']; ?>" class="btn btn-icon" style="padding: 14px 28px; font-size: 1.1rem;">
                    <i data-feather="shopping-cart"></i> Beli Sekarang
                </a>
            <?php else: ?>
                <p class="btn btn-secondary" style="cursor: not-allowed; background: #ccc; padding: 14px 28px; font-size: 1.1rem;">Stok Habis</p>
            <?php endif; ?>
            
            <?php
                // PERUBAHAN: Tombol WhatsApp
                // GANTI NOMOR DI BAWAH INI
                $nomor_wa = "6281234567890"; // Ganti dengan nomor WA admin (format 62)
                $nama_produk_url = urlencode($produk['nama_produk']);
                $pesan_wa = "Halo, saya tertarik dengan produk \"{$nama_produk_url}\". Apakah saya bisa melihat product lebih jelas?";
                $link_wa = "https://api.whatsapp.com/send?phone={$nomor_wa}&text={$pesan_wa}";
            ?>
            <a href="<?php echo $link_wa; ?>" target="_blank" class="btn btn-icon btn-whatsapp" style="padding: 14px 28px; font-size: 1.1rem;">
                <i data-feather="message-circle"></i> Chat via WhatsApp
            </a>
        </div>
        
    </div>
</div>

<?php
include 'includes/footer.php';
?>