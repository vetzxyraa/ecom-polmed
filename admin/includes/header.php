<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - GantunganHP Store</title>
    
    <!-- GANTI DENGAN ICON KAMU -->
    <link rel="icon" href="/assets/images/icons/favicon.ico" sizes="any">
    <link rel="icon" href="/assets/images/icons/icon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="../assets/images/icons/apple-touch-icon.png">
    <link rel="manifest" href="../assets/images/icons/manifest.webmanifest">
    <!-- AKHIR DARI KODE ICON -->

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<header class="main-header">
    <nav class="navbar container">
        <a href="dashboard.php" class="logo">
            <i data-feather="package" class="logo-icon"></i>
            Admin Panel
        </a>
        <?php if (isset($_SESSION['admin_logged_in'])): ?>
        <ul class="nav-links">
            <li><a href="dashboard.php">Pesanan</a></li>
            <li><a href="products.php">Produk</a></li>
            <li><a href="logout.php" class="btn btn-secondary">Logout</a></li>
        </ul>
        <?php endif; ?>
    </nav>
</header>

<main>
<div class="container page-content">