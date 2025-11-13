<?php 
include 'includes/header.php'; 
?>

<h1 class="page-title">Tentang Saya</h1>

<div class="status-box" style="text-align: left; max-width: 800px; margin: 0 auto;">
    
    <div style="display: flex; gap: 30px; flex-wrap: wrap; align-items: center;">
        
        <img src="<?php echo get_global_setting('foto_about', 'https://placehold.co/300x300/E5E7EB/374151?text=Foto+Anda'); ?>" 
             alt="Foto Pemilik" 
             style="border-radius: 8px; width: 100%; max-width: 300px; height: 300px; object-fit: cover; border: 1px solid var(--border-color);">
        
        <div style="flex: 1; min-width: 300px;">
            <h2 style="font-weight: 600; margin-bottom: 15px; font-size: 1.75rem; color: var(--secondary);">Halo, Selamat Datang!</h2>
            
            <p style="margin-bottom: 15px; font-size: 1.1rem; color: var(--text-light);">
                Halo! Saya <strong><?php echo get_global_setting('nama_pemilik', '[Nama Anda]'); ?></strong>, pendiri dan pengelola <?php echo get_global_setting('nama_toko', 'toko ini'); ?>.
            </p>
            
            <div style="font-size: 1.1rem; color: var(--text-light); white-space: pre-wrap;">
                <?php 
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