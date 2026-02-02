<?php
$page_title = 'Data Pelanggan - Admin';
include 'includes/header.php';

// Handle delete
if (isset($_GET['delete'])) {
    $id = clean_input($_GET['delete']);
    mysqli_query($conn, "DELETE FROM tbl_transaksi WHERE id_user = '$id'");
    mysqli_query($conn, "DELETE FROM tbl_user WHERE id_user = '$id'");
    redirect('data-pelanggan.php');
}

// Get all customers
$query = "SELECT * FROM tbl_user ORDER BY id_user ASC";
$result = mysqli_query($conn, $query);
?>

<div class="admin-layout">
    <?php include 'includes/sidebar.php'; ?>

    <div class="main-content">

        <!-- Page Title Card -->
        <div class="page-title-card">
            <h1>Data Pelanggan</h1>
        </div>

        <!-- Table Card -->
        <div class="table-card">

            <!-- Search Bar -->
            <div class="search-bar-wrap">
                <div class="search-bar">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input type="text" id="searchInput" placeholder="Cari nama, email, atau nomor HP…" oninput="filterTable()">
                </div>
            </div>

            <table class="data-table" id="pelangganTable">
                <thead>
                    <tr>
                        <th class="col-no">No</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Password</th>
                        <th>No Hp</th>
                        <th class="col-aksi">Aksi</th>
                    </tr>
                </thead>
                <tbody id="pelangganBody">
                    <?php
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($result)):
                    ?>
                    <tr>
                        <td class="col-no"><?php echo $no++; ?></td>
                        <td class="col-nama"><?php echo htmlspecialchars($row['nama_user']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td class="col-password">••••••••</td>
                        <td><?php echo htmlspecialchars($row['no_hp']); ?></td>
                        <td class="col-aksi">
                            <button class="btn-hapus"
                                    onclick="openDeleteModal(<?php echo $row['id_user']; ?>, '<?php echo addslashes(htmlspecialchars($row['nama_user'])); ?>')">
                                Hapus
                            </button>
                        </td>
                    </tr>
                    <?php endwhile; ?>

                    <?php if ($no === 1): ?>
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg><br>
                                Belum ada pelanggan yang terdaftar
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<!-- Modal Konfirmasi Hapus Pelanggan -->
<div class="modal-delete-overlay" id="deleteModal">
    <div class="modal-delete-sheet">
        <div class="modal-delete-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <polyline points="3 6 5 6 21 6"></polyline>
                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                <line x1="10" y1="11" x2="10" y2="17"></line>
                <line x1="14" y1="11" x2="14" y2="17"></line>
            </svg>
        </div>
        <div class="modal-delete-body">
            <h3>Hapus Pelanggan</h3>
            <p>Anda akan menghapus pelanggan <strong id="deleteNama" style="color: rgba(255,255,255,0.85);"></strong>.<br>
               Semua transaksi terkait juga akan dihapus dan tidak dapat dikembalikan.</p>
        </div>
        <div class="modal-delete-footer">
            <button class="btn-modal-hapus" id="btnKonfirmHapus" onclick="konfirmHapus()">Hapus</button>
            <button class="btn-modal-batal" onclick="closeDeleteModal()">Batal</button>
        </div>
    </div>
</div>

<script>
// ── Search / Filter ──────────────────────────────────────────
function filterTable() {
    var input  = document.getElementById('searchInput');
    var filter = input.value.toLowerCase();
    var tbody  = document.getElementById('pelangganBody');
    var rows   = tbody.getElementsByTagName('tr');

    for (var i = 0; i < rows.length; i++) {
        var cells = rows[i].getElementsByTagName('td');
        var found = false;

        // Cari di kolom: Nama (1), Email (2), No Hp (4)
        var searchCols = [1, 2, 4];
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

// ── Modal Hapus ──────────────────────────────────────────────
let deleteUserId = null;

function openDeleteModal(id, nama) {
    deleteUserId = id;
    document.getElementById('deleteNama').textContent = nama;
    document.getElementById('deleteModal').classList.add('show');
}

function closeDeleteModal() {
    deleteUserId = null;
    document.getElementById('deleteModal').classList.remove('show');
}

function konfirmHapus() {
    if (deleteUserId !== null) {
        window.location.href = 'data-pelanggan.php?delete=' + deleteUserId;
    }
}

// Tutup modal kalau klik di luar sheet
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) closeDeleteModal();
});
</script>

<?php include 'includes/footer.php'; ?>