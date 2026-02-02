<?php
require_once '../config/database.php';

if (!is_logged_in()) {
    redirect('login.php');
}

$page_title = 'Profil - Alpharide Rental';
include 'includes/header.php';
include 'includes/navbar.php';

$id_user = $_SESSION['user_id'];
// FIXED: gunakan nama tabel yang benar
$query = "SELECT * FROM tbl_user WHERE id_user = '$id_user'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);
?>

<div class="profile-container">
    <div class="profile-card">
        <div class="profile-avatar">
            <?php echo strtoupper(substr($user['nama_user'], 0, 1)); ?>
        </div>
        
        <div class="profile-info">
            <div class="form-group">
                <label>Nama</label>
                <input type="text" value="<?php echo $user['nama_user']; ?>" readonly>
            </div>
            
            <div class="form-group">
                <label>Email</label>
                <input type="email" value="<?php echo $user['email']; ?>" readonly>
            </div>
            
            <div class="form-group">
                <label>No. HP</label>
                <input type="tel" value="<?php echo $user['no_hp']; ?>" readonly>
            </div>
        </div>
        
        <a href="logout.php" class="btn-logout">LOGOUT</a>
    </div>
</div>

<?php include 'includes/footer.php'; ?>