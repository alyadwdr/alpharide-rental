<?php
$page_title = 'Dashboard Admin - Alpharide Rental';
include 'includes/header.php';

// =====================
// AUTO-STATUS UPDATE
// =====================
$today = date('Y-m-d');

// 1) Diterima → Disewa (tanggal sewa sudah tiba)
$auto_disewa = mysqli_query($conn,
    "SELECT id_sewa, id_mobil FROM tbl_transaksi 
     WHERE status_transaksi = 'diterima' AND tanggal_sewa <= '$today'");

if ($auto_disewa) {
    while ($row = mysqli_fetch_assoc($auto_disewa)) {
        mysqli_query($conn, "UPDATE tbl_transaksi SET status_transaksi = 'disewa' WHERE id_sewa = '{$row['id_sewa']}'");
        mysqli_query($conn, "UPDATE tbl_mobil SET status = 'disewa' WHERE id_mobil = '{$row['id_mobil']}'");
    }
}

// 2) Disewa → Selesai (tanggal kembali sudah lewat)
$auto_selesai = mysqli_query($conn,
    "SELECT id_sewa, id_mobil FROM tbl_transaksi 
     WHERE status_transaksi = 'disewa' AND tanggal_kembali < '$today'");

if ($auto_selesai) {
    while ($row = mysqli_fetch_assoc($auto_selesai)) {
        // REVISI: id_transaksi -> id_sewa
        mysqli_query($conn, "UPDATE tbl_transaksi SET status_transaksi = 'selesai' WHERE id_sewa = '{$row['id_sewa']}'");
        mysqli_query($conn, "UPDATE tbl_mobil SET status = 'tersedia' WHERE id_mobil = '{$row['id_mobil']}'");
    }
}

// ================
// GET STATISTICS
// ================
$total_kendaraan    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM tbl_mobil"))['total'];
$kendaraan_tersedia = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM tbl_mobil WHERE status='tersedia'"))['total'];
$kendaraan_disewa   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM tbl_mobil WHERE status='disewa'"))['total'];

// Get monthly customer rental data
$query_monthly_customers = "SELECT 
    MONTH(tanggal_sewa) as bulan,
    COUNT(DISTINCT id_user) as total_pelanggan
    FROM tbl_transaksi 
    WHERE YEAR(tanggal_sewa) = YEAR(CURDATE())
    GROUP BY MONTH(tanggal_sewa)
    ORDER BY bulan";
$result_monthly_customers = mysqli_query($conn, $query_monthly_customers);

$customer_data = array_fill(1, 12, 0);
while ($row = mysqli_fetch_assoc($result_monthly_customers)) {
    $customer_data[$row['bulan']] = $row['total_pelanggan'];
}

// Get monthly transaction data
$query_monthly_transactions = "SELECT 
    MONTH(tanggal_sewa) as bulan,
    COUNT(*) as total_transaksi
    FROM tbl_transaksi 
    WHERE YEAR(tanggal_sewa) = YEAR(CURDATE())
    GROUP BY MONTH(tanggal_sewa)
    ORDER BY bulan";
$result_monthly_transactions = mysqli_query($conn, $query_monthly_transactions);

$transaction_data = array_fill(1, 12, 0);
while ($row = mysqli_fetch_assoc($result_monthly_transactions)) {
    $transaction_data[$row['bulan']] = $row['total_transaksi'];
}

// Get recent transactions (8 latest)
$query_transaksi = "SELECT t.*, u.nama_user, m.merek, m.model, p.nomor_plat 
                    FROM tbl_transaksi t 
                    JOIN tbl_user u ON t.id_user = u.id_user 
                    JOIN tbl_mobil m ON t.id_mobil = m.id_mobil 
                    LEFT JOIN tbl_plat_nomor p ON t.id_plat = p.id_plat
                    ORDER BY t.created_at DESC LIMIT 8";
$result_transaksi = mysqli_query($conn, $query_transaksi);
?>

<div class="admin-layout">
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <div class="page-title-card">
            <h1>Dashboard</h1>
        </div>
        
        <div class="dashboard-grid-top">
            <div class="dashboard-card">
                <div class="card-header">
                    <h3 class="card-title">Data Kendaraan</h3>
                </div>
                <div class="vehicle-summary">
                    <div class="summary-item">
                        <span class="summary-label">Total Mobil</span>
                        <span class="summary-value"><?php echo $total_kendaraan; ?></span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Tersedia</span>
                        <span class="summary-value"><?php echo $kendaraan_tersedia; ?></span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Disewa</span>
                        <span class="summary-value"><?php echo $kendaraan_disewa; ?></span>
                    </div>
                </div>
            </div>
            
            <div class="dashboard-card">
                <div class="card-header">
                    <div>
                        <h3 class="card-title">Data Pelanggan Bulanan</h3>
                        <p class="card-subtitle">Jumlah pelanggan yang menyewa per bulan (<?php echo date('Y'); ?>)</p>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="customerChart"></canvas>
                </div>
            </div>
        </div>
        
        <div class="dashboard-grid-bottom">
            <div class="dashboard-card">
                <div class="card-header">
                    <div>
                        <h3 class="card-title">Transaksi Bulanan</h3>
                        <p class="card-subtitle">Jumlah transaksi per bulan (<?php echo date('Y'); ?>)</p>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="transactionChart"></canvas>
                </div>
            </div>
            
            <div class="dashboard-card">
                <div class="card-header">
                    <h3 class="card-title">Transaksi Terakhir</h3>
                    <a href="data-transaksi.php" class="view-all-link">Lihat Semua →</a>
                </div>
                <div class="recent-transactions">
                    <table class="transaction-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Kendaraan</th>
                                <th>Plat</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            while ($row = mysqli_fetch_assoc($result_transaksi)): 
                            ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo htmlspecialchars($row['nama_user']); ?></td>
                                <td><?php echo htmlspecialchars($row['merek'] . ' ' . $row['model']); ?></td>
                                <td><?php echo $row['nomor_plat'] ? htmlspecialchars($row['nomor_plat']) : '-'; ?></td>
                                <td><?php echo date('d/m', strtotime($row['tanggal_sewa'])); ?></td>
                                <td>
                                    <?php 
                                    $status_class = '';
                                    $status_text = '';
                                    switch($row['status_transaksi']) {
                                        case 'pending':
                                            $status_class = 'badge-pending';
                                            $status_text = 'Pending';
                                            break;
                                        case 'diterima':
                                            $status_class = 'badge-diterima';
                                            $status_text = 'Diterima';
                                            break;
                                        case 'disewa':
                                            $status_class = 'badge-disewa';
                                            $status_text = 'Disewa';
                                            break;
                                        case 'selesai':
                                            $status_class = 'badge-selesai';
                                            $status_text = 'Selesai';
                                            break;
                                        case 'ditolak':
                                            $status_class = 'badge-ditolak';
                                            $status_text = 'Ditolak';
                                            break;
                                    }
                                    ?>
                                    <span class="badge <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Data from PHP
const customerData = <?php echo json_encode(array_values($customer_data)); ?>;
const transactionData = <?php echo json_encode(array_values($transaction_data)); ?>;
const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Oct', 'Nov', 'Dec'];

// Chart.js default settings
Chart.defaults.font.family = 'Montserrat';
Chart.defaults.color = '#2D2D2D';

// Customer Chart
const customerCtx = document.getElementById('customerChart').getContext('2d');
const customerChart = new Chart(customerCtx, {
    type: 'bar',
    data: {
        labels: months,
        datasets: [{
            label: 'Jumlah Pelanggan',
            data: customerData,
            backgroundColor: 'rgba(166, 135, 99, 0.8)',
            borderColor: '#A68763',
            borderWidth: 2,
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        animation: {
            duration: 1500,
            easing: 'easeOutQuart'
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1,
                    font: {
                        size: 11,
                        weight: '500'
                    }
                },
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)'
                }
            },
            x: {
                ticks: {
                    font: {
                        size: 11,
                        weight: '500'
                    }
                },
                grid: {
                    display: false
                }
            }
        },
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                backgroundColor: '#2D2D2D',
                titleFont: {
                    size: 13,
                    weight: '600'
                },
                bodyFont: {
                    size: 12
                },
                padding: 12,
                cornerRadius: 8
            }
        }
    }
});

// Transaction Chart
const transactionCtx = document.getElementById('transactionChart').getContext('2d');
const transactionChart = new Chart(transactionCtx, {
    type: 'bar',
    data: {
        labels: months,
        datasets: [{
            label: 'Jumlah Transaksi',
            data: transactionData,
            backgroundColor: 'rgba(76, 175, 80, 0.8)',
            borderColor: '#4CAF50',
            borderWidth: 2,
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        animation: {
            duration: 1500,
            easing: 'easeOutQuart'
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1,
                    font: {
                        size: 11,
                        weight: '500'
                    }
                },
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)'
                }
            },
            x: {
                ticks: {
                    font: {
                        size: 11,
                        weight: '500'
                    }
                },
                grid: {
                    display: false
                }
            }
        },
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                backgroundColor: '#2D2D2D',
                titleFont: {
                    size: 13,
                    weight: '600'
                },
                bodyFont: {
                    size: 12
                },
                padding: 12,
                cornerRadius: 8
            }
        }
    }
});
</script>

<?php include 'includes/footer.php'; ?>