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

include 'includes/header.php'; 
?>

<div class="login-container">
    <h1 class="page-title">Admin Login</h1>
    
    <?php if ($error): ?>
        <div class="message-box error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form action="index.php" method="POST">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn">Login</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>