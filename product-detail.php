<?php
require 'config/database.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$produk = null;

if ($id > 0) {
    $sql = "SELECT * FROM produk WHERE id = ?";
    if ($stmt = mysqli_prepare($koneksi, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $produk = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
    }
}

if (!$produk) {
    header("Location: index.php");
    exit;
}

include 'includes/header.php';

$gambar_list = [];
if (!empty($produk['gambar'])) {
    $gambar_url = $produk['gambar'];
    if (filter_var($gambar_url, FILTER_VALIDATE_URL) === FALSE) {
        $gambar_url = 'assets/images/products/' . htmlspecialchars($gambar_url);
    }
    $gambar_list[] = $gambar_url;
}
if (!empty($produk['gambar2'])) {
    $gambar_url_2 = $produk['gambar2'];
    if (filter_var($gambar_url_2, FILTER_VALIDATE_URL) === FALSE) {
        $gambar_url_2 = 'assets/images/products/' . htmlspecialchars($gambar_url_2);
    }
    $gambar_list[] = $gambar_url_2;
}

if (empty($gambar_list)) {
    $gambar_list[] = 'https://placehold.co/800x800/E5E7EB/374151?text=No+Image';
}
?>

<a href="index.php" class="back-button">
    <i data-feather="arrow-left"></i> Kembali
</a>

<div class="product-detail-container">
    <div class="product-detail-image">
        
        <div class="slider-container">
            <?php foreach ($gambar_list as $index => $gambar_src): ?>
                <div class="slider-slide" <?php echo ($index == 0) ? 'style="display:block;"' : ''; ?>>
                    <img src="<?php echo $gambar_src; ?>" alt="<?php echo htmlspecialchars($produk['nama_produk']); ?> - Gambar <?php echo $index + 1; ?>">
                </div>
            <?php endforeach; ?>
        </div>
        
        <?php if (count($gambar_list) > 1): ?>
        <div class="slider-controls">
            <button class="slider-btn" onclick="plusSlides(-1)"><i data-feather="chevron-left"></i></button>
            <button class="slider-btn" onclick="plusSlides(1)"><i data-feather="chevron-right"></i></button>
        </div>
        <?php endif; ?>
    </div>
    
    <div class="product-detail-info">
        <h1 class="product-name"><?php echo htmlspecialchars($produk['nama_produk']); ?></h1>
        <p class="product-price">Rp <?php echo number_format($produk['harga'], 0, ',', '.'); ?></p>
        <p class="product-stock">Stok Tersedia: <?php echo $produk['stok']; ?></p>
        <div class="product-description">
            <p><?php echo nl2br(htmlspecialchars($produk['deskripsi'])); ?></p>
        </div>
        
        <div class="product-detail-actions">
            <?php if ($produk['stok'] > 0): ?>
                <a href="checkout.php?id=<?php echo $produk['id']; ?>" class="btn btn-icon">
                    <i data-feather="shopping-cart"></i> Beli Sekarang
                </a>
            <?php else: ?>
                <span class="btn btn-secondary" style="cursor: not-allowed; background: #ccc; flex-grow: 1;">Stok Habis</span>
            <?php endif; ?>
            
            <?php
                $nomor_wa = get_global_setting('nomor_wa', '6281234567890');
                $nama_produk_url = urlencode($produk['nama_produk']);
                $pesan_wa = "Halo, saya tertarik dengan produk \"{$nama_produk_url}\". Apakah produk ini masih tersedia?";
                $link_wa = "https://api.whatsapp.com/send?phone={$nomor_wa}&text={$pesan_wa}";
            ?>
            <a href="<?php echo $link_wa; ?>" target="_blank" class="btn btn-icon btn-whatsapp">
                <i data-feather="message-circle"></i> Chat via WhatsApp
            </a>
        </div>
        
    </div>
</div>

<?php if (count($gambar_list) > 1): ?>
<script>
let slideIndex = 1;
showSlides(slideIndex);

function plusSlides(n) {
    showSlides(slideIndex += n);
}

function showSlides(n) {
    let i;
    let slides = document.getElementsByClassName("slider-slide");
    if (n > slides.length) {slideIndex = 1}
    if (n < 1) {slideIndex = slides.length}
    for (i = 0; i < slides.length; i++) {
        slides[i].style.display = "none";
    }
    slides[slideIndex-1].style.display = "block";
}
</script>
<?php endif; ?>

<?php
include 'includes/footer.php';
?>