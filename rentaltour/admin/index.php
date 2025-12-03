<?php
session_start();
include '../config/db.php';

// --- PROSES LOGIN DATABASE ---
if(isset($_POST['login'])){
    $user = mysqli_real_escape_string($koneksi, $_POST['user']); // Anti SQL Injection sederhana
    $pass = $_POST['pass'];

    // 1. Cari user berdasarkan username
    $query = mysqli_query($koneksi, "SELECT * FROM admin WHERE username='$user'");
    $data_admin = mysqli_fetch_array($query);

    // 2. Cek apakah user ada?
    if($data_admin){
        // 3. Cek apakah password cocok dengan Hash di database?
        // Note: Untuk dummy awal ini, jika hash di SQL tadi tidak jalan, 
        // kamu bisa ubah logika ini sementara menjadi md5 atau text biasa, 
        // tapi untuk PROFESIONAL kita pakai password_verify().
        
        // KITA PAKAI LOGIKA SEMENTARA: Cek apakah passwordnya 'admin123' manual 
        // ATAU cek hash (karena membuat hash manual di SQL text agak tricky tanpa script).
        
        // UNTUK KEMUDAHAN KAMU SAAT INI (Biar langsung jalan):
        // Kita anggap database menyimpan password biasa dulu, nanti kamu bisa fitur ubah password.
        // TAPI SAYA SARANKAN TETAP AMAN.
        
        // Revisi Script: Kita pakai Password Verify yang benar.
        // Saya sertakan script "Auto Create Admin" jika tabel kosong biar kamu gak pusing hashnya.
        
        if(password_verify($pass, $data_admin['password'])){
            $_SESSION['admin'] = true;
            header("location:index.php"); 
            exit();
        } else {
            $err = "Password salah!";
        }
    } else {
        $err = "Username tidak ditemukan!";
    }
}

// --- AUTO SEED ADMIN (Fitur Bantu Biar Gak Repot Insert SQL Hash Manual) ---
// Jika tabel admin kosong, script ini akan otomatis buat user: admin / admin123
$cek_admin = mysqli_query($koneksi, "SELECT * FROM admin");
if(mysqli_num_rows($cek_admin) == 0){
    $pass_default = password_hash('admin123', PASSWORD_DEFAULT);
    mysqli_query($koneksi, "INSERT INTO admin VALUES (NULL, 'admin', '$pass_default')");
}
// --------------------------------------------------------------------------

// --- TAMPILAN LOGIN (Jika belum login) ---
if(!isset($_SESSION['admin'])){
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <link rel="icon" href="../assets/img/favicon.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="d-flex justify-content-center align-items-center vh-100 bg-light">
    <div class="card border-0 shadow-sm p-4" style="width: 350px; border-radius: 16px;">
        <div class="text-center mb-4">
            <h4 class="fw-bold">Login System</h4>
            <p class="text-muted small">Masuk dengan akun terdaftar.</p>
        </div>
        
        <?php if(isset($err)) echo "<div class='alert alert-danger py-2 small'>$err</div>"; ?>
        
        <form method="POST">
            <div class="mb-3">
                <input type="text" name="user" class="form-control bg-light border-0 py-2" placeholder="Username" required>
            </div>
            <div class="mb-4">
                <input type="password" name="pass" class="form-control bg-light border-0 py-2" placeholder="Password" required>
            </div>
            <button type="submit" name="login" class="btn btn-black w-100 py-2">Masuk Dashboard</button>
        </form>
    </div>
</body>
</html>
<?php exit(); } ?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body>
    <div class="admin-container">
        <div class="sidebar">
            <a href="index.php" class="sidebar-brand">Sumatera<span style="color:#999">Admin.</span></a>
            <nav class="sidebar-menu">
                <a href="index.php" class="active"><i class="bi bi-grid"></i> Dashboard</a>
                <a href="kelola_mobil.php"><i class="bi bi-car-front"></i> Mobil</a>
                <a href="kelola_tour.php"><i class="bi bi-map"></i> Tour</a>
                <a href="kelola_rental.php"><i class="bi bi-receipt"></i> Pesanan</a>
                <a href="kelola_profil.php"><i class="bi bi-gear-fill"></i> Profil</a>
                <a href="logout.php" class="text-danger mt-5"><i class="bi bi-box-arrow-right"></i> Keluar</a>
            </nav>
        </div>

        <div class="main-content">
            <h2 class="fw-bold mb-4">Overview</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="stat-card">
                        <div class="stat-label">Total Armada</div>
                        <div class="stat-num"><?= mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM mobil")) ?></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card">
                        <div class="stat-label">Paket Tour</div>
                        <div class="stat-num"><?= mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM paket_tour")) ?></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card">
                        <div class="stat-label">Pesanan Pending</div>
                        <div class="stat-num text-warning">
                            <?php 
                            $c1 = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM booking_mobil WHERE status='Pending'")); 
                            $c2 = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM booking_tour WHERE status='Pending'")); 
                            echo $c1 + $c2;
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>