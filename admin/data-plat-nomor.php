<?php
$page_title = 'Data Plat Nomor - Admin';
include 'includes/header.php';

// Handle delete
if (isset($_GET['delete'])) {
    $id = clean_input($_GET['delete']);
    mysqli_query($conn, "DELETE FROM tbl_plat_nomor WHERE id_plat = '$id'");
    redirect('data-plat-nomor.php');
}

// Handle add/edit
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_plat = isset($_POST['id_plat']) ? clean_input($_POST['id_plat']) : '';
    $id_mobil = clean_input($_POST['id_mobil']);
    $nomor_plat = clean_input($_POST['nomor_plat']);
    $status = clean_input($_POST['status']);
    
    if ($id_plat) {
        // Update
        $query = "UPDATE tbl_plat_nomor SET id_mobil='$id_mobil', nomor_plat='$nomor_plat', status='$status' WHERE id_plat='$id_plat'";
        if (mysqli_query($conn, $query)) {
            $success = 'Data plat nomor berhasil diupdate';
        } else {
            $error = 'Gagal update: ' . mysqli_error($conn);
        }
    } else {
        // Insert
        $query = "INSERT INTO tbl_plat_nomor (id_mobil, nomor_plat, status) VALUES ('$id_mobil', '$nomor_plat', '$status')";
        if (mysqli_query($conn, $query)) {
            $success = 'Data plat nomor berhasil ditambahkan';
        } else {
            $error = 'Gagal tambah: ' . mysqli_error($conn);
        }
    }
}

// Get all plat nomor with car info
$query = "SELECT p.*, m.merek, m.model FROM tbl_plat_nomor p 
          JOIN tbl_mobil m ON p.id_mobil = m.id_mobil 
          ORDER BY m.merek, m.model, p.nomor_plat";
$result = mysqli_query($conn, $query);

// Get all cars for dropdown
$cars_query = "SELECT * FROM tbl_mobil ORDER BY merek, model";
$cars_result = mysqli_query($conn, $cars_query);

// Get plat for edit
$edit_plat = null;
if (isset($_GET['edit'])) {
    $id = clean_input($_GET['edit']);
    $edit_plat = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tbl_plat_nomor WHERE id_plat = '$id'"));
}
?>

<div class="admin-layout">
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <div class="top-bar">
            <h1>Data Plat Nomor</h1>
            <button onclick="openModal()" class="btn-add">+ Tambah Plat Nomor</button>
        </div>
        
        <?php if ($success): ?>
            <div class="alert alert-success" style="margin-bottom: 2rem;"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error" style="margin-bottom: 2rem; background: #f8d7da; color: #721c24;"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="table-card">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kendaraan</th>
                        <th>Nomor Plat</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($result)): 
                    ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo $row['merek'] . ' ' . $row['model']; ?></td>
                        <td style="font-weight: 600;"><?php echo $row['nomor_plat']; ?></td>
                        <td>
                            <?php if ($row['status'] == 'tersedia'): ?>
                                <span class="badge badge-success">Tersedia</span>
                            <?php elseif ($row['status'] == 'disewa'): ?>
                                <span class="badge badge-warning">Disewa</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Maintenance</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="?edit=<?php echo $row['id_plat']; ?>" class="btn-edit">Edit</a>
                                <a href="?delete=<?php echo $row['id_plat']; ?>" class="btn-delete" onclick="return confirm('Yakin ingin menghapus plat nomor ini?')">Hapus</a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="platModal" class="modal <?php echo $edit_plat ? 'show' : ''; ?>">
    <div class="modal-content">
        <div class="modal-header">
            <h3><?php echo $edit_plat ? 'Edit' : 'Tambah'; ?> Plat Nomor</h3>
            <button onclick="closeModal()" class="close-modal">×</button>
        </div>
        <form method="POST">
            <div class="modal-body">
                <?php if ($edit_plat): ?>
                    <input type="hidden" name="id_plat" value="<?php echo $edit_plat['id_plat']; ?>">
                <?php endif; ?>
                
                <div class="form-group">
                    <label>Kendaraan</label>
                    <select name="id_mobil" required>
                        <option value="">-- Pilih Kendaraan --</option>
                        <?php 
                        mysqli_data_seek($cars_result, 0);
                        while ($car = mysqli_fetch_assoc($cars_result)): 
                        ?>
                            <option value="<?php echo $car['id_mobil']; ?>" 
                                    <?php echo ($edit_plat && $edit_plat['id_mobil'] == $car['id_mobil']) ? 'selected' : ''; ?>>
                                <?php echo $car['merek'] . ' ' . $car['model']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Nomor Plat</label>
                    <input type="text" name="nomor_plat" value="<?php echo $edit_plat['nomor_plat'] ?? ''; ?>" 
                           placeholder="Contoh: T 1234 AB" required>
                </div>
                
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" required>
                        <option value="tersedia" <?php echo ($edit_plat && $edit_plat['status'] == 'tersedia') ? 'selected' : ''; ?>>Tersedia</option>
                        <option value="disewa" <?php echo ($edit_plat && $edit_plat['status'] == 'disewa') ? 'selected' : ''; ?>>Disewa</option>
                        <option value="maintenance" <?php echo ($edit_plat && $edit_plat['status'] == 'maintenance') ? 'selected' : ''; ?>>Maintenance</option>
                    </select>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" onclick="closeModal()" class="btn-secondary">Batal</button>
                <button type="submit" class="btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal() {
    document.getElementById('platModal').classList.add('show');
}

function closeModal() {
    window.location.href = 'data-plat-nomor.php';
}
</script>

<?php include 'includes/footer.php'; ?>