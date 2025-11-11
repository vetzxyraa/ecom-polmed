<?php
include 'includes/session_check.php';
require '../config/database.php';
include 'includes/header.php';

// Daftar status yang konsisten
$list_status = ['menunggu', 'menunggu konfirmasi', 'diproses', 'dikirim', 'selesai', 'dibatalkan'];

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
                <th style="width: 15%;">Kode Pesanan</th>
                <th style="width: 15%;">Produk</th>
                <th style="width: 20%;">Pembeli</th>
                <th style="width: 10%;">Total</th>
                <th style="width: 15%;">Status</th>
                <th style="width: 25%;">Update Status & Pesan</th>
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
                            $status_class = 'status-' . str_replace(' ', '-', $status); // misal: status-menunggu-konfirmasi
                        ?>
                        <span class="status-badge <?php echo $status_class; ?>">
                            <?php echo htmlspecialchars($status); ?>
                        </span>
                        
                        <?php if (!empty($pesanan['pesan_admin'])): ?>
                            <div class="admin-message-bubble">
                                <?php echo htmlspecialchars($pesanan['pesan_admin']); ?>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td>
                         <!-- Form update status -->
                         <form action="update_order.php" method="POST" class="form-update-status">
                            <input type="hidden" name="pesanan_id" value="<?php echo $pesanan['id']; ?>">
                            
                            <div class="form-group" style="margin-bottom: 10px;">
                                <select name="status" class="form-control" onchange="togglePesanAdmin(this)">
                                    <?php foreach ($list_status as $status_option): ?>
                                        <option value="<?php echo $status_option; ?>" <?php echo ($pesanan['status'] == $status_option) ? 'selected' : ''; ?>>
                                            <?php echo ucfirst($status_option); // Huruf awal kapital ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <!-- Textarea untuk pesan admin, defaultnya tersembunyi -->
                            <div class="form-group pesan-admin-wrapper" <?php echo ($pesanan['status'] != 'dibatalkan') ? 'style="display: none;"' : ''; ?>>
                                <textarea name="pesan_admin" class="form-control" placeholder="Alasan pembatalan (jika dibatalkan)..."><?php echo htmlspecialchars($pesanan['pesan_admin']); ?></textarea>
                            </div>
                            
                            <button type="submit" class="btn">Update</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
// Fungsi untuk menampilkan/menyembunyikan field pesan admin
function togglePesanAdmin(selectElement) {
    // Cari form terdekat
    var form = selectElement.closest('.form-update-status');
    if (form) {
        // Cari wrapper textarea di dalam form itu
        var textareaWrapper = form.querySelector('.pesan-admin-wrapper');
        var textarea = textareaWrapper.querySelector('textarea');
        
        if (selectElement.value === 'dibatalkan') {
            textareaWrapper.style.display = 'block'; // Tampilkan
        } else {
            textareaWrapper.style.display = 'none'; // Sembunyikan
            textarea.value = ''; // Kosongkan nilainya
        }
    }
}
</script>

<?php
include 'includes/footer.php';
?>