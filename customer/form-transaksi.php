<?php
require_once '../config/database.php';

if (!is_logged_in()) {
    redirect('login.php');
}

if (!isset($_GET['id'])) {
    redirect('beranda.php#mobil');
}

$id_mobil = clean_input($_GET['id']);
$query = "SELECT * FROM tbl_mobil WHERE id_mobil = '$id_mobil'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    redirect('beranda.php#mobil');
}

$car = mysqli_fetch_assoc($result);

// Get available plat nomor
$query_plat = "SELECT * FROM tbl_plat_nomor WHERE id_mobil = '$id_mobil' ORDER BY nomor_plat";
$result_plat = mysqli_query($conn, $query_plat);
$plat_list = [];
while ($plat = mysqli_fetch_assoc($result_plat)) {
    $plat_list[] = $plat;
}

// Get user data
$id_user = $_SESSION['user_id'];
$query_user = "SELECT * FROM tbl_user WHERE id_user = '$id_user'";
$result_user = mysqli_query($conn, $query_user);
$user = mysqli_fetch_assoc($result_user);

$page_title = 'Form Transaksi - ' . $car['merek'] . ' ' . $car['model'];

// Logic Warna Indikator
$warna_db = strtolower($car['warna']);
$hex_warna = '#CCCCCC'; 
if (strpos($warna_db, 'putih') !== false) $hex_warna = '#FFFFFF';
elseif (strpos($warna_db, 'hitam') !== false) $hex_warna = '#111111';
elseif (strpos($warna_db, 'merah') !== false) $hex_warna = '#8B0000';
elseif (strpos($warna_db, 'biru') !== false) $hex_warna = '#000080';
elseif (strpos($warna_db, 'silver') !== false) $hex_warna = '#C0C0C0';
elseif (strpos($warna_db, 'abu') !== false) $hex_warna = '#696969';
elseif (strpos($warna_db, 'coklat') !== false) $hex_warna = '#5D4037';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/customer.css">
    <link rel="stylesheet" href="../assets/css/customer-transaction.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="customer transaction-body">

    <div class="split-screen-container">
        
        <div class="left-panel">
            <div class="form-content-wrapper">
                <h1 class="main-title">FORM TRANSAKSI</h1>
                
                <form id="transactionForm" method="POST" action="process-booking.php">
                    <input type="hidden" name="id_mobil" value="<?php echo $car['id_mobil']; ?>">
                    <input type="hidden" name="total_harga" id="totalHargaHidden">

                    <div class="compact-section">
                        <h3 class="section-label">Data Pelanggan</h3>
                        
                        <div class="input-group">
                            <label>Nama</label>
                            <input type="text" value="<?php echo $user['nama_user']; ?>" readonly class="compact-input filled">
                        </div>

                        <div class="input-group">
                            <label>Email</label>
                            <input type="email" value="<?php echo $user['email']; ?>" readonly class="compact-input filled">
                        </div>

                        <div class="input-group">
                            <label>No. HP</label>
                            <input type="tel" name="no_hp" value="<?php echo $user['no_hp'] ?? ''; ?>" placeholder="08xxx" required class="compact-input">
                        </div>
                    </div>

                    <div class="compact-section">
                        <h3 class="section-label">Durasi Sewa</h3>
                        
                        <div class="input-group">
                            <label>Tanggal Mulai</label>
                            <div class="date-wrapper">
                                <input type="date" name="tanggal_sewa" id="tanggalSewa" min="<?php echo date('Y-m-d'); ?>" required class="compact-input date-field" onchange="calculateTotal()">
                                <i class="fas fa-calendar-alt calendar-icon"></i>
                            </div>
                        </div>

                        <div class="input-group">
                            <label>Tanggal Selesai</label>
                            <div class="date-wrapper">
                                <input type="date" name="tanggal_kembali" id="tanggalKembali" required class="compact-input date-field" onchange="calculateTotal()">
                                <i class="fas fa-calendar-alt calendar-icon"></i>
                            </div>
                        </div>
                    </div>

                    <div class="total-section">
                        <label>Total Harga</label>
                        <div class="price-display" id="totalHargaDisplay">Rp 0</div>
                        <small>*Otomatis dihitung berdasarkan durasi</small>
                    </div>
                </form>
            </div>
        </div>

        <div class="right-panel">
            <div class="decoration-card">
                <a href="beranda.php#mobil" class="close-btn">&times;</a>

                <div class="panel-top">
                    <div class="car-visual-container">
                        <img src="../assets/images/cars/<?php echo $car['foto_mobil']; ?>" alt="Mobil" class="car-hero-image">
                        <div class="car-shadow"></div>
                    </div>

                    <div class="car-info-stack">
                        <div class="brand-row">
                            <h2 class="car-brand"><?php echo strtoupper($car['merek'] . ' ' . $car['model']); ?></h2>
                            <div class="color-dot" style="background-color: <?php echo $hex_warna; ?>;"></div>
                        </div>
                        
                        <div class="capacity-info">
                            <i class="fas fa-user-group"></i> <?php echo $car['kapasitas']; ?> - <?php echo $car['kapasitas'] + 1; ?> Orang
                        </div>

                        <div class="plat-selector">
                            <select name="id_plat" form="transactionForm" class="plat-dropdown" required>
                                <option value="">Pilih Plat</option>
                                <?php foreach ($plat_list as $plat): ?>
                                    <?php $disabled = ($plat['status'] != 'tersedia') ? 'disabled' : ''; ?>
                                    <option value="<?php echo $plat['id_plat']; ?>" <?php echo $disabled; ?>>
                                        <?php echo $plat['nomor_plat']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <span class="status-badge">Tersedia</span>
                        </div>

                        <div class="price-per-day">
                            Rp. <?php echo number_format($car['harga_sewa_per_hari'], 0, ',', '.'); ?>/hari
                        </div>
                    </div>
                </div>

                <div class="panel-bottom">
                    <button type="submit" form="transactionForm" class="btn-confirm">Konfirmasi Pesanan</button>
                </div>
            </div>
        </div>

    </div>

    <?php include 'includes/footer.php'; ?>

    <script>
    const hargaPerHari = <?php echo $car['harga_sewa_per_hari']; ?>;

    function calculateTotal() {
        const tanggalSewa = document.getElementById('tanggalSewa').value;
        const tanggalKembali = document.getElementById('tanggalKembali').value;
        
        if (tanggalSewa && tanggalKembali) {
            const startDate = new Date(tanggalSewa);
            const endDate = new Date(tanggalKembali);
            startDate.setHours(0,0,0,0);
            endDate.setHours(0,0,0,0);

            if (endDate >= startDate) {
                const diffTime = Math.abs(endDate - startDate);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1; 
                const total = diffDays * hargaPerHari;
                document.getElementById('totalHargaDisplay').textContent = 'Rp. ' + total.toLocaleString('id-ID');
                document.getElementById('totalHargaHidden').value = total;
            } else {
                document.getElementById('totalHargaDisplay').textContent = 'Rp 0';
                document.getElementById('totalHargaHidden').value = 0;
                alert("Tanggal kembali tidak boleh sebelum tanggal mulai!");
                document.getElementById('tanggalKembali').value = '';
            }
        }
    }

    document.getElementById('tanggalSewa').addEventListener('change', function() {
        document.getElementById('tanggalKembali').min = this.value;
        calculateTotal();
    });

    document.getElementById('transactionForm').addEventListener('submit', function(e) {
        const totalHarga = document.getElementById('totalHargaHidden').value;
        const plat = document.querySelector('select[name="id_plat"]').value;
        if (!plat) { e.preventDefault(); alert('Mohon pilih plat nomor terlebih dahulu.'); return false; }
        if (!totalHarga || totalHarga == 0) { e.preventDefault(); alert('Mohon lengkapi durasi sewa.'); return false; }
    });
    </script>
</body>
</html>