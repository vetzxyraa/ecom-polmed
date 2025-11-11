<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toko Gantungan HP</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header class="header">
        <div class="container nav-container">
            <div class="nav-logo">
                <a href="index.php">GantunganHP</a>
            </div>
            <nav class="nav-menu">
                <a href="index.php" class="<?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">Home</a>
                <a href="status.php" class="<?php echo ($current_page == 'status.php') ? 'active' : ''; ?>">Cek Status</a>
                <a href="admin/" target="_blank">Admin</a>
            </nav>
        </div>
    </header>
    <main class="main-content">
        <div class="container">