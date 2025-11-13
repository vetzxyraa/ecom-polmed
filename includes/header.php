<?php
require_once __DIR__ . '/../config/database.php';

$SETTINGS = [];
$sql_settings = "SELECT setting_key, setting_value FROM settings";
$result_settings = mysqli_query($koneksi, $sql_settings);
if ($result_settings) {
    while ($row = mysqli_fetch_assoc($result_settings)) {
        $SETTINGS[$row['setting_key']] = $row['setting_value'];
    }
}

function get_global_setting($key, $default = '') {
    global $SETTINGS;
    return isset($SETTINGS[$key]) ? htmlspecialchars($SETTINGS[$key]) : $default;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo get_global_setting('nama_toko', 'GantunganHP Store'); ?></title>
    
    <link rel="icon" href="assets/images/icons/favicon.ico" sizes="any">
    <link rel="icon" href="assets/images/icons/icon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="assets/images/icons/apple-touch-icon.png">

    <script>
        const startTime = Date.now();
        const MIN_LOAD_TIME = 1500; 
    </script>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div id="preloader">
    <div class="spinner"></div>
</div>

<header class="main-header">
    <nav class="navbar container">
        <a href="index.php" class="logo">
            <i data-feather="package" class="logo-icon"></i>
            <?php echo get_global_setting('nama_toko', 'GantunganHP Store'); ?>
        </a>
        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="about.php">About</a></li>
            <li><a href="contact.php">Contact</a></li>
            <li><a href="status.php">Cek Status</a></li>
            <li><a href="admin/" class="btn-nav-admin">Admin</a></li>
        </ul>
    </nav>
</header>

<main>
<div class="container page-content">