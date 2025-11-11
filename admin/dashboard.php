<?php
include 'includes/session_check.php';
require '../config/database.php';
include 'includes/header.php';

$sql = "SELECT pesanan.*, produk.nama_produk 
        FROM pesanan 
        JOIN produk ON pesanan.produk_id = produk.id 
        ORDER BY pesanan.tgl_pesan DESC";
$result = mysqli_query($koneksi, $sql);
$pesanan_list = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<div class="admin-header">
    <h1 class="page-title">Manajemen Pesanan</h1>
</div>

<?php if (isset($_GET['status']) && $_GET['status'] == 'updated'): ?>
    <div class="message-box success">Status pesanan berhasil diperbarui.</div>
<?php endif; ?>

<div class="admin-table-wrapper">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Kode Pesanan</th>
                <th>Produk</th>
                <th>Pembeli</th>
                <th>Total</th>
                <th>Status & Pesan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($pesanan_list)): ?>
                <tr>
                    <td colspan="6" style="text-align: center;">Belum ada pesanan.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($pesanan_list as $pesanan): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($pesanan['kode_pesanan']); ?></strong></td>
                    <td><?php echo htmlspecialchars($pesanan['nama_produk']); ?> (x<?php echo $pesanan['jumlah']; ?>)</td>
                    <td>
                        <?php echo htmlspecialchars($pesanan['nama_pembeli']); ?><br>
                        <small><?php echo htmlspecialchars($pesanan['no_hp']); ?></small><br>
                        <small>Alamat: <?php echo htmlspecialchars($pesanan['alamat']); ?></small>
                    </td>
                    <td>Rp <?php echo number_format($pesanan['total_harga'], 0, ',', '.'); ?></td>
                    <td>
                        <?php
                            $status = $pesanan['status'];
                            $status_class = '';
                            if ($status == 'menunggu') {
                                $status_class = 'status-menunggu';
                            } elseif ($status == 'berhasil') {
                                $status_class = 'status-berhasil';
                            } elseif ($status == 'gagal') {
                                $status_class = 'status-gagal';
                            }
                        ?>
                        <span class="status-badge <?php echo $status_class; ?>" style="margin-bottom: 5px;">
                            <?php echo htmlspecialchars($status); ?>
                        </span>
                        
                        <?php if (!empty($pesanan['pesan_admin'])): ?>
                            <div class="admin-message-bubble">
                                <?php echo htmlspecialchars($pesanan['pesan_admin']); ?>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td>
                         <form action="update_order.php" method="POST" style="display: flex; flex-direction: column; gap: 10px;">
                            <input type="hidden" name="pesanan_id" value="<?php echo $pesanan['id']; ?>">
                            
                            <select name="status" class="form-control" style="padding: 5px 8px; font-size: 0.9rem;" onchange="resetPesan(this)">
                                <option value="menunggu" <?php echo ($pesanan['status'] == 'menunggu') ? 'selected' : ''; ?>>Menunggu</option>
                                <option value="berhasil" <?php echo ($pesanan['status'] == 'berhasil') ? 'selected' : ''; ?>>Berhasil</option>
                                <option value="gagal" <?php echo ($pesanan['status'] == 'gagal') ? 'selected' : ''; ?>>Gagal</option>
                            </select>
                            
                            <textarea name="pesan_admin" class="form-control" style="font-size: 0.9rem;" placeholder="Pesan admin (opsional)..."><?php echo htmlspecialchars($pesanan['pesan_admin']); ?></textarea>
                            
                            <button type="submit" class="btn" style="padding: 8px 10px; font-size: 0.8rem;">Update</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
// Fungsi untuk reset pesan admin saat status diubah
function resetPesan(selectElement) {
    // Cari form terdekat
    var form = selectElement.closest('form');
    if (form) {
        // Cari textarea di dalam form itu
        var textarea = form.querySelector('textarea[name="pesan_admin"]');
        if (textarea) {
            // Reset isinya
            textarea.value = '';
        }
    }
}
</script>

<?php
include 'includes/footer.php';
?>