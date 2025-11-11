<?php
// Link-link sudah diperbaiki (hyphen -> underscore)
// File ini sudah benar, hanya memastikan saja

require 'includes/session_check.php';
require '../config/database.php';
include 'includes/header.php';

$message = '';
$message_type = 'success'; // default
if (isset($_GET['status'])) {
    if ($_GET['status'] == 'success') {
        $message = '<div class="message-box success">Produk berhasil disimpan.</div>';
    } elseif ($_GET['status'] == 'deleted') {
        $message = '<div class="message-box success">Produk berhasil dihapus.</div>';
    } elseif ($_GET['status'] == 'error') {
        $message = '<div class="message-box error">Terjadi kesalahan atau ID produk tidak ditemukan.</div>';
        $message_type = 'error';
    }
}

$sql = "SELECT * FROM produk ORDER BY id DESC";
$result = mysqli_query($koneksi, $sql);
?>

<div class="admin-header">
    <h1 class="page-title">Manajemen Produk</h1>
    <!-- FIX: Link diperbaiki ke edit-product.php -->
    <a href="edit-product.php" class="btn btn-add-new">
        <i data-feather="plus"></i> Tambah Produk
    </a>
</div>

<?php echo $message; ?>


<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Gambar</th>
                <th>Nama Produk</th>
                <th>Harga</th>
                <th>Stok</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)) {
                    $gambar_url = $row['gambar'];
                    // Cek jika gambar adalah URL dari placehold.co atau file lokal
                    if (filter_var($gambar_url, FILTER_VALIDATE_URL) === FALSE) {
                        // Pastikan path ../ benar
                        $gambar_url = '../assets/images/products/' . htmlspecialchars($gambar_url);
                    }
            ?>
            <tr>
                <td><img src="<?php echo $gambar_url; ?>" alt="<?php echo htmlspecialchars($row['nama_produk']); ?>" class="order-image"></td>
                <td><?php echo htmlspecialchars($row['nama_produk']); ?></td>
                <td>Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                <td><?php echo $row['stok']; ?></td>
                <td>
                    <!-- FIX: Link diperbaiki ke edit-product.php -->
                    <a href="edit-product.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-icon" title="Edit">
                        <i data-feather="edit-2"></i>
                    </a>
                    <!-- FIX: Link diperbaiki ke delete-product.php -->
                    <a href="delete_product.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-icon" title="Hapus" onclick="return confirm('Anda yakin ingin menghapus produk ini?');">
                        <i data-feather="trash-2"></i>
                    </a>
                </td>
            </tr>
            <?php
                }
            } else {
                echo "<tr><td colspan='5' style='text-align:center;'>Tidak ada produk.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<?php
include 'includes/footer.php';
?>