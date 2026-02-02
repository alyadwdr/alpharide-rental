<?php
require_once '../config/database.php';

if (is_admin_logged_in()) {
    redirect('index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = clean_input($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        $error = 'Email dan password harus diisi';
    } else {
        $query = "SELECT * FROM tbl_admin WHERE email = '$email'";
        $result = mysqli_query($conn, $query);
        
        if (mysqli_num_rows($result) > 0) {
            $admin = mysqli_fetch_assoc($result);
            if ($password === $admin['password']) {
                $_SESSION['admin_id'] = $admin['id_admin'];
                $_SESSION['admin_nama'] = $admin['nama_admin'];
                $_SESSION['admin_email'] = $admin['email'];
                redirect('index.php');
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
    <title>Login Admin - Alpharide Rental</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&family=Montserrat:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin-auth.css">
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
                <h1 class="title">LOGIN ADMIN</h1>
                <p class="subtitle">Masuk ke sistem manajemen ALPHARIDE RENTAL</p>
                
                <!-- Error Message -->
                <?php if ($error): ?>
                    <div class="alert-error"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <!-- Login Form -->
                <form method="POST" action="" class="login-form">
                    <div class="form-group">
                        <input type="email" name="email" placeholder="Username /ID Admin" required class="form-input">
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
                
                <!-- Footer Text -->
                <div class="footer-text">
                    <small>Akses terbatas hanya untuk administrator</small>
                </div>
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