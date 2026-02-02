<?php
$page_title = 'Data Kendaraan - Admin';
include 'includes/header.php';

// Handle delete
if (isset($_GET['delete'])) {
    $id = clean_input($_GET['delete']);
    mysqli_query($conn, "DELETE FROM tbl_mobil WHERE id_mobil = '$id'");
    // Also delete related plat nomor
    mysqli_query($conn, "DELETE FROM tbl_plat_nomor WHERE id_mobil = '$id'");
    redirect('data-kendaraan.php');
}

// Handle add/edit
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_mobil = isset($_POST['id_mobil']) ? clean_input($_POST['id_mobil']) : '';
    $merek = clean_input($_POST['merek']);
    $model = clean_input($_POST['model']);
    $kode_mobil = clean_input($_POST['kode_mobil']);
    $plat_nomor = clean_input($_POST['plat_nomor']);
    $warna = clean_input($_POST['warna']);
    $kapasitas = clean_input($_POST['kapasitas']);
    $harga_sewa_per_hari = clean_input($_POST['harga_sewa_per_hari']);
    
    $foto_mobil = '';
    if (isset($_FILES['foto_mobil']) && $_FILES['foto_mobil']['error'] == 0) {
        $foto_mobil = basename($_FILES['foto_mobil']['name']);
        move_uploaded_file($_FILES['foto_mobil']['tmp_name'], "../assets/images/cars/" . $foto_mobil);
    } else if (isset($_POST['foto_lama'])) {
        $foto_mobil = $_POST['foto_lama'];
    }
    
    if ($id_mobil) {
        // Update mobil
        $query = "UPDATE tbl_mobil SET merek='$merek', model='$model', kode_mobil='$kode_mobil', warna='$warna', 
                  kapasitas='$kapasitas', harga_sewa_per_hari='$harga_sewa_per_hari', foto_mobil='$foto_mobil' 
                  WHERE id_mobil='$id_mobil'";
        if (mysqli_query($conn, $query)) {
            // Update plat nomor if exists
            $check_plat = mysqli_query($conn, "SELECT * FROM tbl_plat_nomor WHERE id_mobil='$id_mobil' LIMIT 1");
            if (mysqli_num_rows($check_plat) > 0) {
                $plat_data = mysqli_fetch_assoc($check_plat);
                mysqli_query($conn, "UPDATE tbl_plat_nomor SET nomor_plat='$plat_nomor' WHERE id_plat='{$plat_data['id_plat']}'");
            }
            $success = 'Data kendaraan berhasil diupdate';
        }
    } else {
        // Insert mobil
        $query = "INSERT INTO tbl_mobil (merek, model, kode_mobil, warna, kapasitas, harga_sewa_per_hari, foto_mobil, status) 
                  VALUES ('$merek', '$model', '$kode_mobil', '$warna', '$kapasitas', '$harga_sewa_per_hari', '$foto_mobil', 'tersedia')";
        if (mysqli_query($conn, $query)) {
            $new_id = mysqli_insert_id($conn);
            // Insert plat nomor
            mysqli_query($conn, "INSERT INTO tbl_plat_nomor (id_mobil, nomor_plat, status) VALUES ('$new_id', '$plat_nomor', 'tersedia')");
            $success = 'Data kendaraan berhasil ditambahkan';
        }
    }
}

// Get all cars with plat
$query = "SELECT m.*, 
          (SELECT nomor_plat FROM tbl_plat_nomor WHERE id_mobil = m.id_mobil LIMIT 1) as plat_nomor
          FROM tbl_mobil m 
          ORDER BY m.id_mobil DESC";
$result = mysqli_query($conn, $query);

// Get car for edit
$edit_car = null;
if (isset($_GET['edit'])) {
    $id = clean_input($_GET['edit']);
    $edit_query = "SELECT m.*, 
                   (SELECT nomor_plat FROM tbl_plat_nomor WHERE id_mobil = m.id_mobil LIMIT 1) as plat_nomor
                   FROM tbl_mobil m 
                   WHERE m.id_mobil = '$id'";
    $edit_car = mysqli_fetch_assoc(mysqli_query($conn, $edit_query));
}
?>

<div class="admin-layout">
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        
        <div class="page-header">
            <h1>Data Kendaraan</h1>
        </div>
        
        <?php if ($success): ?>
            <div class="alert alert-success" style="margin-bottom: 1.5rem; padding: 1rem; background: #D4EDDA; color: #155724; border-radius: 8px;">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>
        
        <div class="toolbar-container">
            <button onclick="openModal()" class="btn-add-vehicle">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Tambah Kendaraan
            </button>
            
            <div class="search-filter-group">
                <div class="search-box">
                    <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.35-4.35"></path>
                    </svg>
                    <input type="text" class="search-input" id="searchInput" placeholder="Cari mobil..." onkeyup="searchVehicles()">
                </div>
                
                <div class="filter-group">
                    <select class="filter-select" id="filterMerek" onchange="filterVehicles()">
                        <option value="">Semua Merek</option>
                        <option value="Toyota">Toyota</option>
                        <option value="Honda">Honda</option>
                        <option value="Mitsubishi">Mitsubishi</option>
                        <option value="Daihatsu">Daihatsu</option>
                    </select>
                    
                    <select class="filter-select" id="filterStatus" onchange="filterVehicles()">
                        <option value="">Semua Status</option>
                        <option value="tersedia">Tersedia</option>
                        <option value="disewa">Disewa</option>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="vehicles-container">
            <div class="vehicles-grid" id="vehiclesGrid">
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="vehicle-card" 
                     data-merek="<?php echo $row['merek']; ?>" 
                     data-status="<?php echo $row['status']; ?>"
                     data-searchable="<?php echo strtolower($row['merek'] . ' ' . $row['model'] . ' ' . $row['plat_nomor'] . ' ' . $row['warna']); ?>">
                    <img src="../assets/images/cars/<?php echo $row['foto_mobil']; ?>" alt="<?php echo $row['merek']; ?>" class="vehicle-image">
                    
                    <div class="vehicle-info">
                        <div class="vehicle-brand">
                            <h3><?php echo $row['merek'] . ' ' . $row['model']; ?></h3>
                            <div class="color-indicator" style="background-color: <?php 
                                // Simple color mapping
                                $warna = strtolower($row['warna']);
                                if (strpos($warna, 'putih') !== false) echo '#FFFFFF';
                                elseif (strpos($warna, 'hitam') !== false) echo '#111111';
                                elseif (strpos($warna, 'merah') !== false) echo '#8B0000';
                                elseif (strpos($warna, 'biru') !== false) echo '#000080';
                                elseif (strpos($warna, 'silver') !== false) echo '#C0C0C0';
                                elseif (strpos($warna, 'abu') !== false) echo '#696969';
                                elseif (strpos($warna, 'coklat') !== false) echo '#5D4037';
                                else echo '#CCCCCC';
                            ?>;"></div>
                        </div>
                        
                        <div class="vehicle-plat"><?php echo $row['plat_nomor'] ?? $row['kode_mobil']; ?></div>
                        
                        <div class="vehicle-details">
                            <div class="vehicle-detail-item">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                </svg>
                                Max <?php echo $row['kapasitas']; ?> Orang
                            </div>
                        </div>
                        
                        <div class="vehicle-price">Rp <?php echo number_format($row['harga_sewa_per_hari'], 0, ',', '.'); ?>/hari</div>
                        
                        <div class="vehicle-actions">
                            <span class="status-badge status-<?php echo $row['status']; ?>">
                                <?php echo ucfirst($row['status']); ?>
                            </span>
                            
                            <div class="action-buttons">
                                <button onclick="editVehicle(<?php echo $row['id_mobil']; ?>)" class="btn-edit">Edit</button>
                                <button onclick="deleteVehicle(<?php echo $row['id_mobil']; ?>)" class="btn-delete">Hapus</button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</div>

<div class="modal-overlay" id="vehicleModal">
    <div class="modal-sheet">
        <div class="modal-header">
            <h2 class="modal-title" id="modalTitle"><?php echo $edit_car ? 'Edit' : 'Isi'; ?> Detail Kendaraan</h2>
            <button onclick="closeModal()" class="btn-close-modal">&times;</button>
        </div>
        
        <form method="POST" enctype="multipart/form-data" id="vehicleForm">
            <div class="modal-body">
                <div class="image-upload-area">
                    <?php if ($edit_car): ?>
                        <input type="hidden" name="id_mobil" value="<?php echo $edit_car['id_mobil']; ?>">
                        <input type="hidden" name="foto_lama" value="<?php echo $edit_car['foto_mobil']; ?>">
                    <?php endif; ?>
                    
                    <div class="upload-box" id="uploadBox">
                        <div class="upload-icon">+</div>
                        <img src="<?php echo $edit_car ? '../assets/images/cars/' . $edit_car['foto_mobil'] : ''; ?>" 
                             alt="Preview" class="preview-image" id="previewImage">
                        <input type="file" name="foto_mobil" accept="image/*" onchange="previewFile(this)">
                    </div>
                </div>
                
                <div class="form-fields">
                    <div class="form-group-modal">
                        <label class="form-label-modal">Merek Kendaraan</label>
                        <input type="text" name="merek" class="form-input-modal" 
                               value="<?php echo $edit_car['merek'] ?? ''; ?>" 
                               placeholder="Contoh: Toyota" required>
                    </div>
                    
                    <div class="form-group-modal">
                        <label class="form-label-modal">Model Kendaraan</label>
                        <input type="text" name="model" class="form-input-modal" 
                               value="<?php echo $edit_car['model'] ?? ''; ?>" 
                               placeholder="Contoh: Avanza" required>
                    </div>
                    
                    <div class="form-group-modal">
                        <label class="form-label-modal">Kode Mobil</label>
                        <input type="text" name="kode_mobil" class="form-input-modal" 
                               value="<?php echo $edit_car['kode_mobil'] ?? ''; ?>" 
                               placeholder="Contoh: TYT-AVZ-001" required>
                    </div>
                    
                    <div class="form-group-modal">
                        <label class="form-label-modal">Plat Nomor Kendaraan</label>
                        <input type="text" name="plat_nomor" class="form-input-modal" 
                               value="<?php echo $edit_car['plat_nomor'] ?? ''; ?>" 
                               placeholder="Contoh: T 1234 AB" required>
                    </div>
                    
                    <div class="form-group-modal">
                        <label class="form-label-modal">Kapasitas</label>
                        <input type="number" name="kapasitas" class="form-input-modal" 
                               value="<?php echo $edit_car['kapasitas'] ?? ''; ?>" 
                               placeholder="Jumlah penumpang" required>
                    </div>
                    
                    <div class="form-group-modal">
                        <label class="form-label-modal">Harga per Hari</label>
                        <input type="number" name="harga_sewa_per_hari" class="form-input-modal" 
                               value="<?php echo $edit_car['harga_sewa_per_hari'] ?? ''; ?>" 
                               placeholder="Contoh: 350000" required>
                    </div>
                    
                    <div class="form-group-modal">
                        <label class="form-label-modal">Warna</label>
                        <div class="color-picker-wrapper">
                            <input type="color" name="warna_picker" class="color-picker" 
                                   value="<?php 
                                        if ($edit_car) {
                                            $warna = strtolower($edit_car['warna']);
                                            if (strpos($warna, 'putih') !== false) echo '#FFFFFF';
                                            elseif (strpos($warna, 'hitam') !== false) echo '#111111';
                                            elseif (strpos($warna, 'merah') !== false) echo '#8B0000';
                                            elseif (strpos($warna, 'biru') !== false) echo '#000080';
                                            elseif (strpos($warna, 'silver') !== false) echo '#C0C0C0';
                                            elseif (strpos($warna, 'abu') !== false) echo '#696969';
                                            elseif (strpos($warna, 'coklat') !== false) echo '#5D4037';
                                            else echo '#000000';
                                        } else {
                                            echo '#000000';
                                        }
                                   ?>" 
                                   onchange="updateColorText(this.value)">
                            <input type="text" name="warna" class="form-input-modal color-text-input" 
                                   value="<?php echo $edit_car['warna'] ?? ''; ?>" 
                                   placeholder="Nama warna" id="colorTextInput" required>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" onclick="closeModal()" class="btn-cancel">Batal</button>
                <button type="submit" class="btn-submit">
                    <?php echo $edit_car ? 'Simpan' : 'Tambah Kendaraan'; ?>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Modal functions
function openModal() {
    document.getElementById('vehicleModal').classList.add('show');
}

function closeModal() {
    window.location.href = 'data-kendaraan.php';
}

function editVehicle(id) {
    window.location.href = 'data-kendaraan.php?edit=' + id;
}

function deleteVehicle(id) {
    if (confirm('Yakin ingin menghapus kendaraan ini?')) {
        window.location.href = 'data-kendaraan.php?delete=' + id;
    }
}

// Preview image
function previewFile(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const uploadBox = document.getElementById('uploadBox');
            const previewImage = document.getElementById('previewImage');
            previewImage.src = e.target.result;
            uploadBox.classList.add('has-image');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Color picker
function updateColorText(colorValue) {
    document.getElementById('colorTextInput').value = colorValue;
}

// Search vehicles
function searchVehicles() {
    const searchValue = document.getElementById('searchInput').value.toLowerCase();
    filterVehicles();
}

// Filter vehicles
function filterVehicles() {
    const searchValue = document.getElementById('searchInput').value.toLowerCase();
    const merekFilter = document.getElementById('filterMerek').value;
    const statusFilter = document.getElementById('filterStatus').value;
    const cards = document.querySelectorAll('.vehicle-card');
    
    cards.forEach(card => {
        const searchable = card.getAttribute('data-searchable');
        const merek = card.getAttribute('data-merek');
        const status = card.getAttribute('data-status');
        
        const matchSearch = searchValue === '' || searchable.includes(searchValue);
        const matchMerek = merekFilter === '' || merek === merekFilter;
        const matchStatus = statusFilter === '' || status === statusFilter;
        
        if (matchSearch && matchMerek && matchStatus) {
            card.style.display = 'flex';
        } else {
            card.style.display = 'none';
        }
    });
}

// Show modal if editing
<?php if ($edit_car): ?>
    document.addEventListener('DOMContentLoaded', function() {
        openModal();
        const uploadBox = document.getElementById('uploadBox');
        if (uploadBox.querySelector('.preview-image').src) {
            uploadBox.classList.add('has-image');
        }
    });
<?php endif; ?>
</script>

<?php include 'includes/footer.php'; ?>