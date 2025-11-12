<?php
define('DB_SERVER', 'sql100.infinityfree.com');
define('DB_USERNAME', 'if0_40388356');
define('DB_PASSWORD', 'Andre301006');
define('DB_NAME', 'if0_40388356_ecommerce');

$koneksi = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if($koneksi === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
?>