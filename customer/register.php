<?php
require_once '../config/database.php';

if (is_logged_in() && !isset($_GET['from_index'])) {
    redirect('beranda.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = clean_input($_POST['nama']);
    $email = clean_input($_POST['email']);
    $password = $_POST['password'];
    $no_hp = clean_input($_POST['no_hp']);
    
    // Validasi
    if (empty($nama) || empty($email) || empty($password) || empty($no_hp)) {
        $error = 'Semua field harus diisi';
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid';
    } else {
        // Cek email sudah terdaftar
        $check_email = mysqli_query($conn, "SELECT * FROM tbl_user WHERE email = '$email'");
        if (mysqli_num_rows($check_email) > 0) {
            $error = 'Email sudah terdaftar';
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert ke database 
            $query = "INSERT INTO tbl_user (nama_user, email, password, no_hp) VALUES ('$nama', '$email', '$hashed_password', '$no_hp')";
            if (mysqli_query($conn, $query)) {
                $success = 'Registrasi berhasil! Silakan login.';
            } else {
                $error = 'Registrasi gagal: ' . mysqli_error($conn);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Alpharide Rental</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&family=Montserrat:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/customer-auth.css">
</head>
<body>
    <div class="auth-container reversed">
        <!-- Diagonal Dark Background -->
        <div class="diagonal-bg"></div>
        
        <!-- Car Image -->
        <img src="../assets/images/cars/xpander.png" alt="Mitsubishi Xpander" class="car-image">
        
        <!-- Register Form -->
        <div class="auth-form-wrapper">
            <div class="auth-content">
                <!-- Logo -->
                <img src="../assets/images/logo.png" alt="Alpharide Rental" class="logo">
                
                <!-- Title -->
                <h1 class="title">DAFTAR</h1>
                <p class="subtitle">Buat akun untuk mulai menyewa kendaraan</p>
                
                <!-- Error Message -->
                <?php if ($error): ?>
                    <div class="alert-error"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <!-- Success Message -->
                <?php if ($success): ?>
                    <div class="alert-error" style="background: #d4edda; color: #155724; border-color: #c3e6cb;">
                        <?php echo $success; ?>
                    </div>
                <?php endif; ?>
                
                <!-- Register Form -->
                <form method="POST" action="" class="login-form">
                    <div class="form-group">
                        <input type="text" name="nama" placeholder="Nama" required class="form-input">
                    </div>
                    
                    <div class="form-group">
                        <input type="email" name="email" placeholder="Email" required class="form-input">
                    </div>
                    
                    <div class="form-group password-group">
                        <input type="password" name="password" id="password" placeholder="Buat password" required class="form-input">
                        <button type="button" class="toggle-password" onclick="togglePassword()">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                    </div>
                    
                    <div class="form-group">
                        <input type="tel" name="no_hp" placeholder="No. HP" required class="form-input">
                    </div>
                    
                    <button type="submit" class="btn-login">DAFTAR</button>
                </form>
                
                <!-- Footer Link -->
                <p class="footer-text">Sudah memiliki akun? <a href="login.php" class="link-daftar">Login</a></p>
            </div>
        </div>
    </div>
    
    <script>
        function togglePassword() {
            const password = document.getElementById('password');
            password.type = password.type === 'password' ? 'text' : 'password';
        }
    </script>
</body>
</html>