<?php
require_once '../config/database.php';

if (is_logged_in() && !isset($_GET['from_index'])) {
    redirect('beranda.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = clean_input($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        $error = 'Email dan password harus diisi';
    } else {
        $query = "SELECT * FROM tbl_user WHERE email = '$email'";
        $result = mysqli_query($conn, $query);
        
        if (mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id_user'];
                $_SESSION['user_nama'] = $user['nama_user'];
                $_SESSION['user_email'] = $user['email'];
                redirect('beranda.php');
            } else {
                $error = 'Password salah';
            }
        } else {
            $error = 'Email tidak terdaftar';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Alpharide Rental</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&family=Montserrat:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/customer-auth.css">
</head>
<body>
    <div class="auth-container">
        <!-- Diagonal Dark Background -->
        <div class="diagonal-bg"></div>
        
        <!-- Car Image -->
        <img src="../assets/images/cars/xpander.png" alt="Mitsubishi Xpander" class="car-image">
        
        <!-- Login Form -->
        <div class="auth-form-wrapper">
            <div class="auth-content">
                <!-- Logo -->
                <img src="../assets/images/logo.png" alt="Alpharide Rental" class="logo">
                
                <!-- Title -->
                <h1 class="title">LOGIN</h1>
                <p class="subtitle">Silahkan masuk untuk melakukan penyewaan kendaraan</p>
                
                <!-- Error Message -->
                <?php if ($error): ?>
                    <div class="alert-error"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <!-- Login Form -->
                <form method="POST" action="" class="login-form">
                    <div class="form-group">
                        <input type="email" name="email" placeholder="Email" required class="form-input">
                    </div>
                    
                    <div class="form-group password-group">
                        <input type="password" name="password" id="password" placeholder="Password" required class="form-input">
                        <button type="button" class="toggle-password" onclick="togglePassword()">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                    </div>
                    
                    <button type="submit" class="btn-login">LOGIN</button>
                </form>
                
                <!-- Footer Link -->
                <p class="footer-text">Tidak memiliki akun? <a href="register.php" class="link-daftar">Daftar</a></p>
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