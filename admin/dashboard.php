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
                <th>Status</th>
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
                        <small><?php echo htmlspecialchars($pesanan['no_hp']); ?></small>
                    </td>
                    <td>Rp <?php echo number_format($pesanan['total_harga'], 0, ',', '.'); ?></td>
                    <td>
                        <?php
                            $status = $pesanan['status'];
                            $status_class = '';
                            if (in_array($status, ['menunggu', 'diproses', 'menunggu konfirmasi'])) {
                                $status_class = 'status-menunggu';
                            } elseif (in_array($status, ['selesai', 'dikirim'])) {
                                $status_class = 'status-berhasil';
                            } elseif ($status == 'dibatalkan') {
                                $status_class = 'status-gagal';
                            }
                        ?>
                        <span class="status-badge <?php echo $status_class; ?>" style="margin-bottom: 5px;">
                            <?php echo htmlspecialchars($status); ?>
                        </span>
                    </td>
                    <td>
                         <form action="update-order.php" method="POST" style="display: flex; gap: 5px;">
                            <input type="hidden" name="pesanan_id" value="<?php echo $pesanan['id']; ?>">
                            <select name="status" class="form-control" style="padding: 5px 8px; font-size: 0.9rem;">
                                <option value="menunggu" <?php echo ($pesanan['status'] == 'menunggu') ? 'selected' : ''; ?>>Menunggu</option>
                                <option value="menunggu konfirmasi" <?php echo ($pesanan['status'] == 'menunggu konfirmasi') ? 'selected' : ''; ?>>Menunggu Konfirmasi</option>
                                <option value="diproses" <?php echo ($pesanan['status'] == 'diproses') ? 'selected' : ''; ?>>Diproses</option>
                                <option value="dikirim" <?php echo ($pesanan['status'] == 'dikirim') ? 'selected' : ''; ?>>Dikirim</option>
                                <option value="selesai" <?php echo ($pesanan['status'] == 'selesai') ? 'selected' : ''; ?>>Selesai</t>
                                <option value="dibatalkan" <?php echo ($pesanan['status'] == 'dibatalkan') ? 'selected' : ''; ?>>Dibatalkan</option>
                            </select>
                            <button type="submit" class="btn" style="padding: 8px 10px; font-size: 0.8rem;">Update</button>
                        </form>
                        <small style="margin-top: 5px; display: block;">
                            Alamat: <?php echo htmlspecialchars($pesanan['alamat']); ?>
                        </small>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
include 'includes/footer.php';
?>