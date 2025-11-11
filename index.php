<?php
require 'config/database.php';
include 'includes/header.php';

$sql = "SELECT * FROM produk ORDER BY id DESC";
$result = mysqli_query($koneksi, $sql);
$produks = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<h1 class="page-title">Katalog Produk</h1>

<div class="product-grid">
    <?php if (empty($produks)): ?>
        <p>Belum ada produk.</p>
    <?php else: ?>
        <?php foreach ($produks as $produk): ?>
            <div class="product-card">
                <div class="product-image-wrapper">
                    <?php
                    $gambar_url = $produk['gambar'];
                    if (!filter_var($gambar_url, FILTER_VALIDATE_URL)) {
                        $gambar_url = 'assets/images/products/' . htmlspecialchars($gambar_url);
                    }
                    ?>
                    <img src="<?php echo $gambar_url; ?>" alt="<?php echo htmlspecialchars($produk['nama_produk']); ?>">
                </div>
                <div class="product-info">
                    <h3><?php echo htmlspecialchars($produk['nama_produk']); ?></h3>
                    <p class="price">Rp <?php echo number_format($produk['harga'], 0, ',', '.'); ?></p>
                    <p class="stock">Stok: <?php echo $produk['stok']; ?></p>
                    
                    <div class="product-actions">
                        <a href="checkout.php?id=<?php echo $produk['id']; ?>" class="btn btn-icon">
                            <i data-feather="shopping-cart"></i> Pesan
                        </a>
                        <a href="product-detail.php?id=<?php echo $produk['id']; ?>" class="btn btn-secondary">
                            Detail
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php
include 'includes/footer.php';
?>