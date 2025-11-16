<?php
// Inisialisasi Session dan Database
include 'includes/session_check.php';
require '../config/database.php';

$message = '';
$message_type = 'success';

// Ambil Pengaturan Awal
$settings = [];
$sql_get_init = "SELECT setting_key, setting_value FROM settings";
$result_get_init = mysqli_query($koneksi, $sql_get_init);
if ($result_get_init) {
    while ($row = mysqli_fetch_assoc($result_get_init)) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
}
// Fungsi Helper Ambil Pengaturan
function get_setting($key, $default = '') {
    global $settings;
    return isset($settings[$key]) ? htmlspecialchars($settings[$key]) : $default;
}

// Proses Simpan Form (POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $settings_data = $_POST;
    
    // Logika Upload/Update Foto 'About'
    $current_foto_path = isset($settings['foto_about']) ? $settings['foto_about'] : '';
    $pasted_url = mysqli_real_escape_string($koneksi, $settings_data['foto_about_url']);
    
    $new_foto_path = !empty($pasted_url) ? $pasted_url : $current_foto_path;

    // Cek Jika Ada File Diupload
    if (isset($_FILES['foto_about_file']) && $_FILES['foto_about_file']['error'] == UPLOAD_ERR_OK) {
        
        $target_dir = "../assets/images/uploads/"; 
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true); 
        }
        
        // Buat Nama File Unik
        $file_info = pathinfo($_FILES["foto_about_file"]["name"]);
        $file_extension = strtolower($file_info['extension']);
        $new_file_name = "about_" . time() . "." . $file_extension;
        $target_file = $target_dir . $new_file_name;
        
        $allowed_types = ['jpg', 'jpeg', 'png'];
        $max_file_size = 2 * 1024 * 1024; // 2MB

        // Validasi File Upload
        if (!in_array($file_extension, $allowed_types)) {
            $message = "Gagal upload: Format file foto 'About' tidak didukung (hanya .jpg, .jpeg, .png).";
            $message_type = 'error';
            $new_foto_path = $current_foto_path; 
        } elseif ($_FILES["foto_about_file"]["size"] > $max_file_size) {
            $message = "Gagal upload: Ukuran file foto 'About' terlalu besar (Maks 2MB).";
            $message_type = 'error';
            $new_foto_path = $current_foto_path; 
        } else {
            // Pindahkan File
            if (move_uploaded_file($_FILES["foto_about_file"]["tmp_name"], $target_file)) {
                $new_foto_path = "assets/images/uploads/" . $new_file_name;
                
                // Hapus File Lama Jika Ada
                if (!empty($current_foto_path) && filter_var($current_foto_path, FILTER_VALIDATE_URL) === FALSE && file_exists("../" . $current_foto_path)) {
                    unlink("../" . $current_foto_path);
                }
            } else {
                $message = "Terjadi kesalahan saat memindahkan file foto 'About'.";
                $message_type = 'error';
                $new_foto_path = $current_foto_path; 
            }
        }
    // Hapus File Lama Jika Ganti ke URL
    } 
    elseif ($current_foto_path != $pasted_url) {
        if (!empty($current_foto_path) && filter_var($current_foto_path, FILTER_VALIDATE_URL) === FALSE && file_exists("../" . $current_foto_path)) {
            unlink("../" . $current_foto_path);
        }
    }
    
    unset($settings_data['foto_about_url']);
    $settings_data['foto_about'] = $new_foto_path;

    // Simpan Semua Pengaturan ke Database
    mysqli_begin_transaction($koneksi);
    
    try {
        foreach ($settings_data as $key => $value) {
            $key_clean = mysqli_real_escape_string($koneksi, $key);
            $value_clean = mysqli_real_escape_string($koneksi, $value);

            // Query INSERT ... ON DUPLICATE KEY UPDATE
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
        if (empty($message)) { 
             $message = "Pengaturan berhasil disimpan.";
             $message_type = 'success';
        }
        
        // Muat Ulang Pengaturan Setelah Simpan
        $sql_get_new = "SELECT setting_key, setting_value FROM settings";
        $result_get_new = mysqli_query($koneksi, $sql_get_new);
        if ($result_get_new) {
            $settings = []; 
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

    <form action="settings.php" method="POST" enctype="multipart/form-data">
        
        <h3 class="form-section-title">Info Dasar</h3>
        <div class="form-group">
            <label for="nama_toko">Nama Toko</label>
            <input type="text" id="nama_toko" name="nama_toko" class="form-control" value="<?php echo get_setting('nama_toko'); ?>">
        </div>
        <div class="form-group">
            <label for="nama_pemilik">Nama Pemilik</label>
            <input type="text" id="nama_pemilik" name="nama_pemilik" class="form-control" value="<?php echo get_setting('nama_pemilik'); ?>">
        </div>
        
        <hr style="border: 0; border-top: 1px solid var(--border-color); margin: 30px 0;">
        
        <h3 class="form-section-title">Info Kontak</h3>
        <div class="form-group">
            <label for="nomor_wa">Nomor WhatsApp</label>
            <input type="text" id="nomor_wa" name="nomor_wa" class="form-control" value="<?php echo get_setting('nomor_wa'); ?>" placeholder="Format 628... (tanpa + atau 0 di depan)">
            <small>Digunakan untuk tombol "Chat via WhatsApp"</small>
        </div>
        <div class="form-group">
            <label for="email_kontak">Email Kontak</label>
            <input type="email" id="email_kontak" name="email_kontak" class="form-control" value="<?php echo get_setting('email_kontak'); ?>" placeholder="admin@tokoanda.com">
        </div>
        <div class="form-group">
            <label for="alamat_toko">Alamat Toko</label>
            <textarea id="alamat_toko" name="alamat_toko" class="form-control" style="min-height: 80px;"><?php echo get_setting('alamat_toko'); ?></textarea>
        </div>

        <hr style="border: 0; border-top: 1px solid var(--border-color); margin: 30px 0;">

        <h3 class="form-section-title">Halaman "Tentang Saya"</h3>
        
        <div class="form-group">
            <label>Foto</label>
            
            <?php
            // Tampilkan Preview Foto 'About'
            $current_foto_display = get_setting('foto_about');
            if (empty($current_foto_display)) {
                $current_foto_display = "https://placehold.co/300x300/E5E7EB/374151?text=No+Image";
            } elseif (filter_var($current_foto_display, FILTER_VALIDATE_URL) === FALSE) {
                $current_foto_display = "../" . $current_foto_display;
            }
            ?>
            
            <div class="image-preview-container" id="about-preview-container" style="width: 200px; height: 200px; min-height: 200px; max-width: 200px;">
                <img id="image-preview-about" src="<?php echo $current_foto_display; ?>" alt="Preview" style="display: block; width: 100%; height: 100%; object-fit: cover;">
            </div>

            <label for="foto_about_file" style="margin-top: 15px; font-weight: 600;">Upload Gambar</label>
            <input type="file" id="foto_about_file" name="foto_about_file" class="form-control form-control-file" accept="image/jpeg,image/png">
            <small>Upload file baru (Maks 2MB)</small>
            
            <label for="foto_about_url" style="margin-top: 15px; font-weight: 600;">Atau Tempel URL Gambar</label>
            <input type="text" id="foto_about_url" name="foto_about_url" class="form-control" 
                   value="<?php echo (filter_var(get_setting('foto_about'), FILTER_VALIDATE_URL) ? get_setting('foto_about') : ''); ?>" 
                   placeholder="https://...">
        </div>
        
        <div class="form-group" style="margin-top: 20px;">
            <label for="teks_about">Deskripsi</label>
            <textarea id="teks_about" name="teks_about" class="form-control" style="min-height: 200px;"><?php echo get_setting('teks_about'); ?></textarea>
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