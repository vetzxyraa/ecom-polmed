<?php 
// Halaman "Contact"
include 'includes/header.php'; 

// FIX: Ambil data dari helper global
$nomor_wa = get_global_setting('nomor_wa', '6281234567890');
$nama_toko = get_global_setting('nama_toko', 'GantunganHP Store');
$email = get_global_setting('email_kontak', 'admin@contoh.com');
$alamat = get_global_setting('alamat_toko', 'Silakan isi alamat di panel admin.');

// Buat link WA dinamis
$pesan_wa = urlencode("Halo, saya ingin bertanya sesuatu tentang produk $nama_toko...");
$link_wa = "https://api.whatsapp.com/send?phone={$nomor_wa}&text={$pesan_wa}";
?>

<h1 class="page-title">Hubungi Kami</h1>
<p style="text-align: center; max-width: 600px; margin: -2rem auto 2rem auto; font-size: 1.1rem; color: var(--text-light);">
    Punya pertanyaan atau butuh bantuan? Jangan ragu untuk menghubungi kami melalui salah satu saluran di bawah ini.
</p>

<!-- Menggunakan style 'status-box' agar rapi -->
<div class="status-box" style="max-width: 700px; margin: 0 auto; padding: 2.5rem;">
    
    <!-- Kontak WhatsApp -->
    <div style="margin-bottom: 25px; border-bottom: 1px solid var(--border-color); padding-bottom: 25px;">
        <h3 style="display: flex; align-items: center; gap: 10px; font-size: 1.25rem; font-weight: 600; margin-bottom: 15px;">
            <i data-feather="message-circle" style="color: var(--whatsapp);"></i>
            WhatsApp (Respon Cepat)
        </h3>
        <!-- FIX: Link WA dinamis -->
        <a href="<?php echo $link_wa; ?>" target="_blank" class="btn btn-icon" style="background-color: var(--whatsapp); color: white;">
            <i data-feather="external-link"></i> Chat Sekarang di WhatsApp
        </a>
    </div>

    <!-- Kontak Email -->
    <div style="margin-bottom: 25px; border-bottom: 1px solid var(--border-color); padding-bottom: 25px;">
        <h3 style="display: flex; align-items: center; gap: 10px; font-size: 1.25rem; font-weight: 600; margin-bottom: 10px;">
            <i data-feather="mail" style="color: var(--primary);"></i>
            Email
        </h3>
        <!-- FIX: Email dinamis -->
        <p style="font-size: 1.1rem; color: var(--secondary); font-weight: 600;">
            <?php echo $email; ?>
        </p>
        <small>(Harap tunggu 1x24 jam untuk balasan via email)</small>
    </div>

    <!-- Lokasi -->
    <div>
        <h3 style="display: flex; align-items: center; gap: 10px; font-size: 1.25rem; font-weight: 600; margin-bottom: 10px;">
            <i data-feather="map-pin" style="color: var(--status-dibatalkan);"></i>
            Lokasi (Workshop)
        </h3>
        <!-- FIX: Alamat dinamis -->
        <p style="font-size: 1.1rem; color: var(--secondary); font-weight: 600;">
            <?php echo $alamat; ?>
        </p>
        <small>(Hanya workshop, belum melayani pembelian offline)</small>
    </div>

</div>

<?php 
include 'includes/footer.php'; 
?>