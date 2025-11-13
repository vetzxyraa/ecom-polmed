<?php
session_start();
require '../config/database.php';

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: dashboard.php");
    exit;
}

$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT id, username, password FROM admin WHERE username = ?";
    
    if ($stmt = mysqli_prepare($koneksi, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $username);
        
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_store_result($stmt);
            
            if (mysqli_stmt_num_rows($stmt) == 1) {
                mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password);
                if (mysqli_stmt_fetch($stmt)) {
                    if (password_verify($password, $hashed_password)) {
                        session_start();
                        $_SESSION['admin_logged_in'] = true;
                        $_SESSION['admin_id'] = $id;
                        $_SESSION['admin_username'] = $username;
                        
                        header("Location: dashboard.php");
                        exit;
                    } else {
                        $error = "Username atau password salah.";
                    }
                }
            } else {
                $error = "Username atau password salah.";
            }
        } else {
            $error = "Terjadi kesalahan. Silakan coba lagi.";
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_close($koneksi);
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - GantunganHP Store</title>
    
    <link rel="icon" href="../assets/images/icons/favicon.ico" sizes="any">
    <link rel="icon" href="../assets/images/icons/icon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="../assets/images/icons/apple-touch-icon.png">
    <link rel="manifest" href="../assets/images/icons/manifest.webmanifest">
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="login-page-wrapper">
    <div class="login-container">
        <h1 class="login-title">
            <i data-feather="package" class="logo-icon"></i>
            Admin Panel
        </h1>
        
        <?php if ($error): ?>
            <div class="message-box error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="index.php" method="POST" class="login-form">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-login">Login</button>
        </form>
        
        <div class="login-footer-links">
            <a href="../index.php"><i data-feather="home"></i> Ke Home</a>
            <a href="../status.php"><i data-feather="check-square"></i> Cek Status</a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
<script>
    feather.replace();
</script>

</body>
</html>