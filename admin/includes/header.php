<?php
require_once '../config/database.php';

if (!is_admin_logged_in()) {
    redirect('login.php');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Admin - Alpharide Rental'; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Admin CSS Files -->
    <link rel="stylesheet" href="../assets/css/admin-layout.css">
    
    <?php
    // Load specific CSS based on current page
    $current_page = basename($_SERVER['PHP_SELF']);
    
    if ($current_page == 'index.php') {
        echo '<link rel="stylesheet" href="../assets/css/admin-dashboard.css">';
    } elseif ($current_page == 'data-kendaraan.php') {
        echo '<link rel="stylesheet" href="../assets/css/admin-data-kendaraan.css">';
    } elseif ($current_page == 'data-pelanggan.php') {
        echo '<link rel="stylesheet" href="../assets/css/admin-data-pelanggan.css">';
    } elseif ($current_page == 'data-transaksi.php') {
        echo '<link rel="stylesheet" href="../assets/css/admin-data-transaksi.css">';
    } elseif ($current_page == 'data-plat-nomor.php') {
        echo '<link rel="stylesheet" href="../assets/css/admin-data-plat.css">';
    }
    ?>
</head>
<body class="admin">