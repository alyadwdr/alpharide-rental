<?php
require_once '../config/database.php';

if (!is_logged_in()) {
    redirect('login.php');
}

$page_title = 'Transaksi - Alpharide Rental';
include 'includes/header.php';
include 'includes/navbar.php';

// Get success/error messages
$success = $_SESSION['success'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);

// Get user transactions
$id_user = $_SESSION['user_id'];
$query = "SELECT t.*, m.merek, m.model, m.foto_mobil, p.nomor_plat
          FROM tbl_transaksi t 
          JOIN tbl_mobil m ON t.id_mobil = m.id_mobil 
          LEFT JOIN tbl_plat_nomor p ON t.id_plat = p.id_plat
          WHERE t.id_user = '$id_user' 
          ORDER BY t.created_at DESC";
$result = mysqli_query($conn, $query);
?>

<div style="max-width: 1200px; margin: 3rem auto; padding: 0 2rem;">
    <?php if ($success): ?>
        <div style="background: #d4edda; color: #155724; padding: 15px 20px; border-radius: 10px; margin-bottom: 2rem; border: 2px solid #c3e6cb; font-size: 0.95rem; line-height: 1.6;">
            ✓ <?php echo $success; ?>
        </div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div style="background: #f8d7da; color: #721c24; padding: 15px 20px; border-radius: 10px; margin-bottom: 2rem; border: 2px solid #f5c6cb; font-size: 0.95rem;">
            ✗ <?php echo $error; ?>
        </div>
    <?php endif; ?>
    
    <h1 style="text-align: center; color: var(--dark-color); margin-bottom: 3rem;">RIWAYAT TRANSAKSI</h1>
    
    <div class="table-card">
        <table class="data-table">
            <thead>
                <tr>
                    <th>No. Booking</th>
                    <th>Kendaraan</th>
                    <th>Plat</th>
                    <th>Tanggal Sewa</th>
                    <th>Tanggal Kembali</th>
                    <th>Total Harga</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo str_pad($row['id_sewa'], 4, '0', STR_PAD_LEFT); ?></td>
                        <td><?php echo $row['merek'] . ' ' . $row['model']; ?></td>
                        <td><?php echo $row['nomor_plat'] ?? '-'; ?></td>
                        <td><?php echo date('d M Y', strtotime($row['tanggal_sewa'])); ?></td>
                        <td><?php echo date('d M Y', strtotime($row['tanggal_kembali'])); ?></td>
                        <td>Rp <?php echo number_format($row['total_harga'], 0, ',', '.'); ?></td>
                        <td>
                            <?php if ($row['status_transaksi'] == 'pending'): ?>
                                <span class="badge badge-warning">Proses</span>
                            <?php elseif ($row['status_transaksi'] == 'diterima'): ?>
                                <span class="badge badge-success">Diterima</span>
                            <?php elseif ($row['status_transaksi'] == 'ditolak'): ?>
                                <span class="badge badge-danger">Ditolak</span>
                            <?php elseif ($row['status_transaksi'] == 'disewa'): ?>
                                <span class="badge badge-info">Disewa</span>
                            <?php else: ?>
                                <span class="badge" style="background: #6c757d;">Selesai</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 2rem;">Belum ada transaksi</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>