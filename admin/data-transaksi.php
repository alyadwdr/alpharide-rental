<?php
$page_title = 'Data Transaksi - Admin';
include 'includes/header.php';

// ==========================
// AUTO-STATUS UPDATE LOGIC 
// ===========================
$today = date('Y-m-d');

// 1) Diterima → Disewa (saat tanggal sewa sudah tiba)
$auto_disewa = mysqli_query($conn,
    "SELECT id_sewa, id_mobil FROM tbl_transaksi 
     WHERE status_transaksi = 'diterima' 
       AND tanggal_sewa <= '$today'"
);
if ($auto_disewa) {
    while ($row = mysqli_fetch_assoc($auto_disewa)) {
        mysqli_query($conn,
            "UPDATE tbl_transaksi SET status_transaksi = 'disewa' WHERE id_sewa = '{$row['id_sewa']}'");
        mysqli_query($conn,
            "UPDATE tbl_mobil SET status = 'disewa' WHERE id_mobil = '{$row['id_mobil']}'");
    }
}

// 2) Disewa → Selesai (saat tanggal selesai sudah lewat)
$auto_selesai = mysqli_query($conn,
    "SELECT id_sewa, id_mobil FROM tbl_transaksi 
     WHERE status_transaksi = 'disewa' 
       AND tanggal_kembali < '$today'"
);
if ($auto_selesai) {
    while ($row = mysqli_fetch_assoc($auto_selesai)) {
        mysqli_query($conn,
            "UPDATE tbl_transaksi SET status_transaksi = 'selesai' WHERE id_sewa = '{$row['id_sewa']}'");
        mysqli_query($conn,
            "UPDATE tbl_mobil SET status = 'tersedia' WHERE id_mobil = '{$row['id_mobil']}'");
    }
}

// =============================================
// HANDLE ACTION (Terima / Selesaikan / Tolak)
// =============================================
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action   = clean_input($_GET['action']);
    $id_trans = clean_input($_GET['id']); // Variabel tetap $id_trans biar ga ubah banyak logika, tapi isinya id_sewa

    // Ambil data transaksi untuk update mobil
    $trans = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT id_mobil FROM tbl_transaksi WHERE id_sewa = '$id_trans'"));

    if ($trans) {
        switch ($action) {
            case 'terima':
                mysqli_query($conn,
                    "UPDATE tbl_transaksi SET status_transaksi = 'diterima' WHERE id_sewa = '$id_trans'");
                break;

            case 'selesai':
                mysqli_query($conn,
                    "UPDATE tbl_transaksi SET status_transaksi = 'selesai' WHERE id_sewa = '$id_trans'");
                mysqli_query($conn,
                    "UPDATE tbl_mobil SET status = 'tersedia' WHERE id_mobil = '{$trans['id_mobil']}'");
                break;

            case 'tolak':
                mysqli_query($conn,
                    "UPDATE tbl_transaksi SET status_transaksi = 'ditolak' WHERE id_sewa = '$id_trans'");
                mysqli_query($conn,
                    "UPDATE tbl_mobil SET status = 'tersedia' WHERE id_mobil = '{$trans['id_mobil']}'");
                break;
        }
    }
    redirect('data-transaksi.php');
}

// ======================
// FETCH ALL TRANSAKSI
// ======================
$query = "SELECT 
            t.id_sewa,
            u.nama_user,
            m.merek,
            m.model,
            p.nomor_plat,
            t.tanggal_sewa,
            t.tanggal_kembali,
            t.total_harga,
            t.status_transaksi,
            t.id_mobil
          FROM tbl_transaksi t
          JOIN tbl_user u        ON t.id_user  = u.id_user
          JOIN tbl_mobil m       ON t.id_mobil = m.id_mobil
          LEFT JOIN tbl_plat_nomor p ON t.id_plat = p.id_plat
          ORDER BY t.id_sewa ASC"; 

$result = mysqli_query($conn, $query);
?>

<div class="admin-layout">
    <?php include 'includes/sidebar.php'; ?>

    <div class="main-content">

        <div class="page-title-card">
            <h1>Data Transaksi</h1>
        </div>

        <div class="table-card">

            <div class="search-bar-wrap">
                <div class="search-bar">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input type="text" id="searchInput" placeholder="Cari ID, nama, kendaraan, plat…" oninput="filterTable()">
                </div>
            </div>

            <table class="data-table" id="transaksiTable">
                <thead>
                    <tr>
                        <th class="col-no">No</th>
                        <th>ID Booking</th>
                        <th>Nama</th>
                        <th>Kendaraan</th>
                        <th>Plat</th>
                        <th>Tanggal</th>
                        <th class="col-harga">Total Harga</th>
                        <th class="col-status">Status</th>
                        <th class="col-aksi">Aksi</th>
                    </tr>
                </thead>
                <tbody id="transaksiBody">
                    <?php
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($result)):
                        $status   = $row['status_transaksi'];
                        
                        $id_trans = $row['id_sewa']; 
                        
                        $nama     = htmlspecialchars($row['nama_user']);
                        $kendaraan= htmlspecialchars($row['merek'] . ' ' . $row['model']);
                        $plat     = $row['nomor_plat'] ? htmlspecialchars($row['nomor_plat']) : '-';
                        
                        $id_book  = str_pad($row['id_sewa'], 4, '0', STR_PAD_LEFT);

                        // Format tanggal
                        $tgl_sewa    = date('d M Y', strtotime($row['tanggal_sewa']));
                        $tgl_selesai = date('d M Y', strtotime($row['tanggal_kembali']));
                        
                        $tanggal_display = ($row['tanggal_sewa'] === $row['tanggal_kembali'])
                            ? $tgl_sewa
                            : $tgl_sewa . ' – <br>' . $tgl_selesai;

                        // Total harga format
                        $harga_fmt = 'Rp ' . number_format((float)$row['total_harga'], 0, ',', '.');

                        // Badge & label
                        $badge_class = '';
                        $badge_text  = '';
                        switch ($status) {
                            case 'pending':   $badge_class = 'badge-pending';   $badge_text = 'Pending';   break;
                            case 'diterima':  $badge_class = 'badge-diterima';  $badge_text = 'Diterima';  break;
                            case 'disewa':    $badge_class = 'badge-disewa';    $badge_text = 'Disewa';    break;
                            case 'selesai':   $badge_class = 'badge-selesai';   $badge_text = 'Selesai';   break;
                            case 'ditolak':   $badge_class = 'badge-ditolak';   $badge_text = 'Ditolak';   break;
                        }

                        // Tombol aksi berdasarkan status
                        $aksi_html = '';
                        switch ($status) {
                            case 'pending':
                                $aksi_html = '
                                <div class="btn-group">
                                    <button class="btn-aksi btn-terima"
                                            onclick="openModal(\'terima\', ' . $id_trans . ', \'' . addslashes($nama) . '\')">Terima</button>
                                    <button class="btn-aksi btn-tolak"
                                            onclick="openModal(\'tolak\', ' . $id_trans . ', \'' . addslashes($nama) . '\')">Tolak</button>
                                </div>';
                                break;

                            case 'diterima':
                                // Tampilkan info "Auto-aktif tanggal X"
                                $auto_info = 'Auto-aktif ' . date('d M', strtotime($row['tanggal_sewa']));
                                $aksi_html = '
                                <div class="btn-group">
                                    <button class="btn-aksi btn-selesai"
                                            onclick="openModal(\'selesai\', ' . $id_trans . ', \'' . addslashes($nama) . '\')">Selesaikan</button>
                                </div>
                                <span class="auto-label">' . $auto_info . '</span>';
                                break;

                            case 'disewa':
                                $aksi_html = '
                                <div class="btn-group">
                                    <button class="btn-aksi btn-selesai"
                                            onclick="openModal(\'selesai\', ' . $id_trans . ', \'' . addslashes($nama) . '\')">Selesaikan</button>
                                </div>';
                                break;

                            case 'selesai':
                            case 'ditolak':
                                $aksi_html = '<span style="color:#999; font-size:0.78rem; font-family:Montserrat,sans-serif;">—</span>';
                                break;
                        }
                    ?>
                    <tr>
                        <td class="col-no"><?php echo $no++; ?></td>
                        <td class="col-id"><?php echo $id_book; ?></td>
                        <td class="col-nama"><?php echo $nama; ?></td>
                        <td><?php echo $kendaraan; ?></td>
                        <td><?php echo $plat; ?></td>
                        <td><?php echo $tanggal_display; ?></td>
                        <td class="col-harga"><?php echo $harga_fmt; ?></td>
                        <td class="col-status">
                            <span class="badge <?php echo $badge_class; ?>"><?php echo $badge_text; ?></span>
                        </td>
                        <td class="col-aksi"><?php echo $aksi_html; ?></td>
                    </tr>
                    <?php endwhile; ?>

                    <?php if ($no === 1): ?>
                    <tr>
                        <td colspan="9">
                            <div class="empty-state">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <rect x="1" y="3" width="15" height="13" rx="2" ry="2"></rect>
                                    <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                                    <circle cx="5.5" cy="18.5" r="2.5"></circle>
                                    <circle cx="18.5" cy="18.5" r="2.5"></circle>
                                </svg><br>
                                Belum ada transaksi
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<div class="modal-delete-overlay" id="modalAksi">
    <div class="modal-delete-sheet">
        <div class="modal-delete-icon" id="modalIcon">
            </div>
        <div class="modal-delete-body">
            <h3 id="modalTitle"></h3>
            <p id="modalMsg"></p>
        </div>
        <div class="modal-delete-footer">
            <button class="btn-modal-konfirm" id="btnModalKonfirm" onclick="konfirmAksi()"></button>
            <button class="btn-modal-batal" onclick="closeModal()">Batal</button>
        </div>
    </div>
</div>

<script>
// ── Search / Filter ──────────────────────────────────────────
function filterTable() {
    var input   = document.getElementById('searchInput');
    var filter  = input.value.toLowerCase();
    var tbody   = document.getElementById('transaksiBody');
    var rows    = tbody.getElementsByTagName('tr');

    for (var i = 0; i < rows.length; i++) {
        var cells = rows[i].getElementsByTagName('td');
        var found = false;

        // Cari di kolom: ID Booking (1), Nama (2), Kendaraan (3), Plat (4)
        var searchCols = [1, 2, 3, 4];
        for (var c = 0; c < searchCols.length; c++) {
            if (cells[searchCols[c]]) {
                var txt = cells[searchCols[c]].textContent || cells[searchCols[c]].innerText;
                if (txt.toLowerCase().indexOf(filter) !== -1) {
                    found = true;
                    break;
                }
            }
        }

        rows[i].style.display = found ? '' : 'none';
    }
}

// ── Modal Aksi ───────────────────────────────────────────────
var pendingAction = null;
var pendingId     = null;

// Icon SVG templates
var svgCheck = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>';
var svgX     = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>';

function openModal(action, id, nama) {
    pendingAction = action;
    pendingId     = id;

    var title, msg, btnText, btnClass, iconClass, iconSvg;

    switch (action) {
        case 'terima':
            title     = 'Terima Pesanan';
            msg       = 'Anda akan menerima pesanan dari <strong style="color:rgba(255,255,255,0.85);">' + nama + '</strong>.<br>Kendaraan akan otomatis diaktifkan pada tanggal sewa.';
            btnText   = 'Terima';
            btnClass  = 'konfirm-green';
            iconClass = 'icon-green';
            iconSvg   = svgCheck;
            break;
        case 'selesai':
            title     = 'Selesaikan Transaksi';
            msg       = 'Anda akan menandai transaksi dari <strong style="color:rgba(255,255,255,0.85);">' + nama + '</strong> sebagai selesai.<br>Kendaraan akan dikembalikan ke status tersedia.';
            btnText   = 'Selesaikan';
            btnClass  = 'konfirm-green';
            iconClass = 'icon-green';
            iconSvg   = svgCheck;
            break;
        case 'tolak':
            title     = 'Tolak Pesanan';
            msg       = 'Anda akan menolak pesanan dari <strong style="color:rgba(255,255,255,0.85);">' + nama + '</strong>.<br>Tindakan ini tidak dapat dibatalkan.';
            btnText   = 'Tolak';
            btnClass  = 'konfirm-red';
            iconClass = 'icon-red';
            iconSvg   = svgX;
            break;
    }

    document.getElementById('modalIcon').className  = 'modal-delete-icon ' + iconClass;
    document.getElementById('modalIcon').innerHTML   = iconSvg;
    document.getElementById('modalTitle').textContent = title;
    document.getElementById('modalMsg').innerHTML     = msg;

    var btn = document.getElementById('btnModalKonfirm');
    btn.textContent = btnText;
    btn.className   = 'btn-modal-konfirm ' + btnClass;

    document.getElementById('modalAksi').classList.add('show');
}

function closeModal() {
    pendingAction = null;
    pendingId     = null;
    document.getElementById('modalAksi').classList.remove('show');
}

function konfirmAksi() {
    if (pendingAction && pendingId) {
        window.location.href = 'data-transaksi.php?action=' + pendingAction + '&id=' + pendingId;
    }
}

// Tutup modal kalau klik di luar sheet
document.getElementById('modalAksi').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});
</script>

<?php include 'includes/footer.php'; ?>