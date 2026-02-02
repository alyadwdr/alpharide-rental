<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alpharide Rental - Pilih Role</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&family=Montserrat:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/index.css">
</head>
<body>
    <div class="role-selection-container">
        <!-- Logo Only -->
        <div class="logo-container">
            <img src="assets/images/logo.png" alt="Alpharide Rental Logo" class="logo">
        </div>
        
        <!-- Tagline -->
        <div class="tagline">
            <h2>Selamat Datang</h2>
            <p>Silahkan pilih role untuk melanjutkan</p>
        </div>

        <!-- Role Cards -->
        <div class="role-cards">
            <!-- Customer Card -->
            <div class="role-card customer-card">
                <div class="icon">
                    <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </div>
                <h3>Customer</h3>
                <p>Sewa kendaraan dengan mudah dan cepat</p>
                <div class="button-group">
                    <a href="customer/login.php" class="btn btn-primary">Login</a>
                    <a href="customer/register.php" class="btn btn-secondary">Daftar</a>
                </div>
            </div>

            <!-- Admin Card -->
            <div class="role-card admin-card">
                <div class="icon">
                    <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                        <path d="M2 17l10 5 10-5"></path>
                        <path d="M2 12l10 5 10-5"></path>
                    </svg>
                </div>
                <h3>Admin</h3>
                <p>Kelola sistem rental kendaraan</p>
                <div class="button-group">
                    <a href="admin/login.php" class="btn btn-primary">Login Admin</a>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer-info">
            <p>&copy; 2026 Alpharide Rental. All Rights Reserved.</p>
        </div>
    </div>
</body>
</html>