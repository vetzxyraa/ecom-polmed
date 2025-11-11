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
        
        <?php if ($produk['stok'] > 0): ?>
            <a href="checkout.php?id=<?php echo $produk['id']; ?>" class="btn btn-icon" style="padding: 14px 28px; font-size: 1.1rem;">
                <i data-feather="shopping-cart"></i> Beli Sekarang
            </a>
        <?php else: ?>
            <p class="btn btn-secondary" style="cursor: not-allowed; background: #ccc;">Stok Habis</p>
        <?php endif; ?>
    </div>
</div>

<?php
include 'includes/footer.php';
?>