<?php
// Detect halaman saat ini
$current_page = basename($_SERVER['PHP_SELF']);

// Halaman yang pakai one-page CSS
$onepage_files = ['beranda.php'];

// Halaman yang pakai customer.css biasa
$customer_files = ['kontak.php', 'profil.php', 'transaksi.php', 'katalog.php', 'detail-mobil.php', 'tentang.php'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Alpharide Rental'; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <?php if (in_array($current_page, $onepage_files)): ?>
        <!-- One-page CSS untuk beranda -->
        <link rel="stylesheet" href="../assets/css/customer-onepage.css">
    <?php else: ?>
        <!-- Customer CSS biasa untuk halaman lain -->
        <link rel="stylesheet" href="../assets/css/style.css">
        <link rel="stylesheet" href="../assets/css/customer.css">
        <link rel="stylesheet" href="../assets/css/customer-navbar-floating.css">
    <?php endif; ?>
</head>
<body class="customer">