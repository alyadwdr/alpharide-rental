<?php
require_once '../config/database.php';

if (!is_logged_in()) {
    redirect('login.php');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_mobil = clean_input($_POST['id_mobil']);
    $id_plat = clean_input($_POST['id_plat']);
    $id_user = $_SESSION['user_id'];
    $no_hp = clean_input($_POST['no_hp']);
    $tanggal_sewa = clean_input($_POST['tanggal_sewa']);
    $tanggal_kembali = clean_input($_POST['tanggal_kembali']);
    $total_harga = clean_input($_POST['total_harga']);
    
    // Validate
    if (empty($id_mobil) || empty($id_plat) || empty($tanggal_sewa) || empty($tanggal_kembali) || empty($total_harga)) {
        $_SESSION['error'] = 'Data tidak lengkap';
        redirect('form-transaksi.php?id=' . $id_mobil);
    }
    
    // Check if plat is available
    $check_plat = mysqli_query($conn, "SELECT status FROM tbl_plat_nomor WHERE id_plat = '$id_plat'");
    $plat_data = mysqli_fetch_assoc($check_plat);
    
    if ($plat_data['status'] != 'tersedia') {
        $_SESSION['error'] = 'Nomor plat yang dipilih tidak tersedia';
        redirect('form-transaksi.php?id=' . $id_mobil);
    }
    
    // Update user phone if provided
    if (!empty($no_hp)) {
        mysqli_query($conn, "UPDATE tbl_user SET no_hp = '$no_hp' WHERE id_user = '$id_user'");
    }
    
    // Start transaction
    mysqli_begin_transaction($conn);
    
    try {
        // Insert transaksi
        $query = "INSERT INTO tbl_transaksi (id_mobil, id_plat, id_user, tanggal_sewa, tanggal_kembali, total_harga, status_transaksi) 
                  VALUES ('$id_mobil', '$id_plat', '$id_user', '$tanggal_sewa', '$tanggal_kembali', '$total_harga', 'pending')";
        
        if (!mysqli_query($conn, $query)) {
            throw new Exception('Gagal membuat transaksi');
        }
        
        // Update plat status to disewa
        $update_plat = "UPDATE tbl_plat_nomor SET status = 'disewa' WHERE id_plat = '$id_plat'";
        if (!mysqli_query($conn, $update_plat)) {
            throw new Exception('Gagal update status plat');
        }
        
        // Check if all plats for this car are not available, update car status
        $check_all_plats = mysqli_query($conn, 
            "SELECT COUNT(*) as total, 
                    SUM(CASE WHEN status = 'tersedia' THEN 1 ELSE 0 END) as tersedia 
             FROM tbl_plat_nomor WHERE id_mobil = '$id_mobil'");
        $plat_stats = mysqli_fetch_assoc($check_all_plats);
        
        if ($plat_stats['tersedia'] == 0) {
            // All plats are not available, mark car as disewa
            mysqli_query($conn, "UPDATE tbl_mobil SET status = 'disewa' WHERE id_mobil = '$id_mobil'");
        }
        
        // Commit transaction
        mysqli_commit($conn);
        
        $_SESSION['success'] = 'Pemesanan berhasil! Mohon tunggu chat/WhatsApp dari kami untuk konfirmasi dan melakukan pembayaran. Tim kami akan segera menghubungi Anda.';
        redirect('transaksi.php');
        
    } catch (Exception $e) {
        // Rollback on error
        mysqli_rollback($conn);
        $_SESSION['error'] = 'Pemesanan gagal: ' . $e->getMessage();
        redirect('form-transaksi.php?id=' . $id_mobil);
    }
} else {
    redirect('beranda.php#mobil');
}
?>