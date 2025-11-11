<?php
// Halaman baru untuk mengedit pengaturan toko
include 'includes/session_check.php';
require '../config/database.php';

$message = '';
$message_type = 'success';

// Ambil Data Awal DULU (dibutuhkan untuk cek file lama)
$settings = [];
$sql_get_init = "SELECT setting_key, setting_value FROM settings";
$result_get_init = mysqli_query($koneksi, $sql_get_init);
if ($result_get_init) {
    while ($row = mysqli_fetch_assoc($result_get_init)) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
}
function get_setting($key, $default = '') {
    global $settings;
    return isset($settings[$key]) ? htmlspecialchars($settings[$key]) : $default;
}

// Proses Simpan Data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil semua data teks dari POST
    $settings_data = $_POST;
    
    // --- START LOGIKA FOTO ABOUT ---
    $current_foto_path = isset($settings['foto_about']) ? $settings['foto_about'] : '';
    $pasted_url = mysqli_real_escape_string($koneksi, $settings_data['foto_about_url']);
    
    // Default: Ganti dengan URL yang di-paste, atau jika URL kosong, pakai path lama
    $new_foto_path = !empty($pasted_url) ? $pasted_url : $current_foto_path;

    // PRIORITAS 1: Cek jika ada file baru di-upload
    if (isset($_FILES['foto_about_file']) && $_FILES['foto_about_file']['error'] == UPLOAD_ERR_OK) {
        
        $target_dir = "../assets/images/uploads/"; // Folder baru untuk upload (lebih rapi)
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true); // Buat folder jika belum ada
        }
        
        $file_info = pathinfo($_FILES["foto_about_file"]["name"]);
        $file_extension = strtolower($file_info['extension']);
        $new_file_name = "about_" . time() . "." . $file_extension;
        $target_file = $target_dir . $new_file_name;
        
        $allowed_types = ['jpg', 'jpeg', 'png'];
        $max_file_size = 2 * 1024 * 1024; // 2MB

        // Validasi file
        if (!in_array($file_extension, $allowed_types)) {
            $message = "Gagal upload: Format file foto 'About' tidak didukung (hanya .jpg, .jpeg, .png).";
            $message_type = 'error';
            $new_foto_path = $current_foto_path; // Batalkan, pakai foto lama
        } elseif ($_FILES["foto_about_file"]["size"] > $max_file_size) {
            $message = "Gagal upload: Ukuran file foto 'About' terlalu besar (Maks 2MB).";
            $message_type = 'error';
            $new_foto_path = $current_foto_path; // Batalkan, pakai foto lama
        } else {
            // Jika valid, pindahkan file
            if (move_uploaded_file($_FILES["foto_about_file"]["tmp_name"], $target_file)) {
                // Berhasil! Set path baru (relative dari root)
                $new_foto_path = "assets/images/uploads/" . $new_file_name;
                
                // Hapus file lama JIKA file lama itu BUKAN URL
                if (!empty($current_foto_path) && filter_var($current_foto_path, FILTER_VALIDATE_URL) === FALSE && file_exists("../" . $current_foto_path)) {
                    unlink("../" . $current_foto_path);
                }
            } else {
                $message = "Terjadi kesalahan saat memindahkan file foto 'About'.";
                $message_type = 'error';
                $new_foto_path = $current_foto_path; // Batalkan, pakai foto lama
            }
        }
    } 
    // PRIORITAS 2: Tidak ada file upload, cek jika URL diganti (dan file lama harus dihapus)
    elseif ($current_foto_path != $pasted_url) {
        // Hapus file lama JIKA file lama itu BUKAN URL (karena diganti URL baru)
        if (!empty($current_foto_path) && filter_var($current_foto_path, FILTER_VALIDATE_URL) === FALSE && file_exists("../" . $current_foto_path)) {
            unlink("../" . $current_foto_path);
        }
        // $new_foto_path sudah di-set ke $pasted_url di awal
    }
    
    // Hapus key `foto_about_url` agar tidak disimpan ke DB
    unset($settings_data['foto_about_url']);
    
    // Inject path foto yang sudah final ke dalam data untuk disimpan
    $settings_data['foto_about'] = $new_foto_path;
    // --- END LOGIKA FOTO ABOUT ---


    // Mulai transaksi
    mysqli_begin_transaction($koneksi);
    
    try {
        // Loop melalui setiap data yang dikirim (termasuk 'foto_about' yang sudah di-update)
        foreach ($settings_data as $key => $value) {
            $key_clean = mysqli_real_escape_string($koneksi, $key);
            $value_clean = mysqli_real_escape_string($koneksi, $value);

            $sql = "INSERT INTO settings (setting_key, setting_value) 
                    VALUES (?, ?) 
                    ON DUPLICATE KEY UPDATE setting_value = ?";
            
            if ($stmt = mysqli_prepare($koneksi, $sql)) {
                mysqli_stmt_bind_param($stmt, "sss", $key_clean, $value_clean, $value_clean);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            } else {
                throw new Exception("Gagal mempersiapkan statement untuk: " . $key_clean);
            }
        }
        
        mysqli_commit($koneksi);
        if (empty($message)) { // Hanya tampilkan pesan sukses jika tidak ada error foto
             $message = "Pengaturan berhasil disimpan.";
             $message_type = 'success';
        }
        
        // Muat ulang data settings setelah disimpan
        $sql_get_new = "SELECT setting_key, setting_value FROM settings";
        $result_get_new = mysqli_query($koneksi, $sql_get_new);
        if ($result_get_new) {
            $settings = []; // Kosongkan array lama
            while ($row = mysqli_fetch_assoc($result_get_new)) {
                $settings[$row['setting_key']] = $row['setting_value'];
            }
        }
        
    } catch (Exception $e) {
        mysqli_rollback($koneksi);
        $message = "Gagal menyimpan pengaturan: " . $e->getMessage();
        $message_type = 'error';
    }
}

include 'includes/header.php';
?>

<h1 class="page-title">Pengaturan Toko</h1>

<div class="form-container" style="max-width: 900px;">

    <?php if ($message): ?>
        <div class="message-box <?php echo $message_type; ?>"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <!-- FIX: Tambahkan enctype untuk upload file -->
    <form action="settings.php" method="POST" enctype="multipart/form-data">
        
        <h3 class="form-section-title">Info Dasar</h3>
        <div class="form-group">
            <label for="nama_toko">Nama Toko</label>
            <input type="text" id="nama_toko" name="nama_toko" class="form-control" value="<?php echo get_setting('nama_toko'); ?>">
        </div>
        <div class="form-group">
            <label for="nama_pemilik">Nama Pemilik (untuk Halaman 'About Me')</label>
            <input type="text" id="nama_pemilik" name="nama_pemilik" class="form-control" value="<?php echo get_setting('nama_pemilik'); ?>">
        </div>
        
        <hr style="border: 0; border-top: 1px solid var(--border-color); margin: 30px 0;">
        
        <h3 class="form-section-title">Info Kontak</h3>
        <div class="form-group">
            <label for="nomor_wa">Nomor WhatsApp</label>
            <input type="text" id="nomor_wa" name="nomor_wa" class="form-control" value="<?php echo get_setting('nomor_wa'); ?>" placeholder="Format 628... (tanpa + atau 0 di depan)">
            <small>Digunakan untuk tombol "Chat via WhatsApp" di halaman kontak dan detail produk.</small>
        </div>
        <div class="form-group">
            <label for="email_kontak">Email Kontak</label>
            <input type="email" id="email_kontak" name="email_kontak" class="form-control" value="<?php echo get_setting('email_kontak'); ?>" placeholder="admin@tokoanda.com">
        </div>
        <div class="form-group">
            <label for="alamat_toko">Alamat Toko (Workshop)</label>
            <textarea id="alamat_toko" name="alamat_toko" class="form-control" style="min-height: 80px;"><?php echo get_setting('alamat_toko'); ?></textarea>
        </div>

        <hr style="border: 0; border-top: 1px solid var(--border-color); margin: 30px 0;">

        <h3 class="form-section-title">Halaman 'About Me'</h3>
        
        <!-- FIX: Form Gambar Hibrid -->
        <div class="form-group">
            <label>Foto 'About Me'</label>
            
            <?php
            // Tentukan URL gambar saat ini untuk ditampilkan
            $current_foto_display = get_setting('foto_about');
            if (empty($current_foto_display)) {
                $current_foto_display = "https://placehold.co/300x300/E5E7EB/374151?text=No+Image";
            } elseif (filter_var($current_foto_display, FILTER_VALIDATE_URL) === FALSE) {
                // Jika ini file lokal, tambahkan ../ agar path-nya benar dari folder admin
                $current_foto_display = "../" . $current_foto_display;
            }
            ?>
            
            <!-- Wadah Preview (Live) -->
            <div class="image-preview-container" id="about-preview-container" style="width: 200px; height: 200px; min-height: 200px; max-width: 200px;">
                <img id="image-preview-about" src="<?php echo $current_foto_display; ?>" alt="Preview" style="display: block; width: 100%; height: 100%; object-fit: cover;">
            </div>

            <label for="foto_about_file" style="margin-top: 15px; font-weight: 600;">Upload Gambar Baru (Prioritas)</label>
            <input type="file" id="foto_about_file" name="foto_about_file" class="form-control form-control-file" accept="image/jpeg,image/png">
            <small>Upload file baru (Maks 2MB) akan mengabaikan link di bawah.</small>
            
            <label for="foto_about_url" style="margin-top: 15px; font-weight: 600;">Atau Tempel URL Gambar</label>
            <input type="text" id="foto_about_url" name="foto_about_url" class="form-control" 
                   value="<?php echo (filter_var(get_setting('foto_about'), FILTER_VALIDATE_URL) ? get_setting('foto_about') : ''); ?>" 
                   placeholder="https://...">
            <small>Gunakan ini jika Anda tidak ingin meng-upload file baru.</small>
        </div>
        
        <div class="form-group" style="margin-top: 20px;">
            <label for="teks_about">Teks 'About Me'</label>
            <textarea id="teks_about" name="teks_about" class="form-control" style="min-height: 200px;"><?php echo get_setting('teks_about'); ?></textarea>
            <small>Anda bisa menggunakan enter untuk membuat paragraf baru.</small>
        </div>

        <button type="submit" class="btn">Simpan Pengaturan</button>
    </form>
</div>

<style>
    .form-section-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--secondary);
        border-bottom: 2px solid var(--primary);
        padding-bottom: 5px;
        margin-bottom: 20px;
        margin-top: 10px;
    }
</style>

<?php
include 'includes/footer.php';
?>