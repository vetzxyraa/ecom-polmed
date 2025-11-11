<?php 
// Halaman "About Me"
// Cukup panggil header, koneksi DB dan Settings sudah di-load oleh header.php
include 'includes/header.php'; 
?>

<h1 class="page-title">Tentang Saya</h1>

<!-- Menggunakan style 'status-box' agar rapi -->
<div class="status-box" style="text-align: left; max-width: 800px; margin: 0 auto;">
    
    <!-- Konten halaman "Tentang Saya" -->
    <div style="display: flex; gap: 30px; flex-wrap: wrap; align-items: center;">
        
        <!-- FIX: Gambar dinamis dari database -->
        <img src="<?php echo get_global_setting('foto_about', 'https://placehold.co/300x300/E5E7EB/374151?text=Foto+Anda'); ?>" 
             alt="Foto Pemilik" 
             style="border-radius: 8px; width: 100%; max-width: 300px; height: 300px; object-fit: cover; border: 1px solid var(--border-color);">
        
        <!-- Teks deskripsi -->
        <div style="flex: 1; min-width: 300px;">
            <h2 style="font-weight: 600; margin-bottom: 15px; font-size: 1.75rem; color: var(--secondary);">Halo, Selamat Datang!</h2>
            
            <!-- FIX: Nama pemilik dinamis -->
            <p style="margin-bottom: 15px; font-size: 1.1rem; color: var(--text-light);">
                Halo! Saya <strong><?php echo get_global_setting('nama_pemilik', '[Nama Anda]'); ?></strong>, pendiri dan pengelola <?php echo get_global_setting('nama_toko', 'toko ini'); ?>.
            </p>
            
            <!-- FIX: Teks about dinamis, nl2br untuk ganti baris -->
            <div style="font-size: 1.1rem; color: var(--text-light); white-space: pre-wrap;">
                <?php 
                    // Menggunakan nl2br(htmlspecialchars_decode(...)) agar ganti baris dari textarea berfungsi
                    // Kita decode dulu karena get_global_setting() otomatis memakai htmlspecialchars
                    $teks_about = htmlspecialchars_decode(get_global_setting('teks_about', 'Silakan isi teks "Tentang Saya" di panel admin.'));
                    echo nl2br($teks_about); 
                ?>
            </div>
        </div>
    </div>
</div>

<?php 
include 'includes/footer.php'; 
?>