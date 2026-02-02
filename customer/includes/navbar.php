<?php
// Detect halaman saat ini
$current_page = basename($_SERVER['PHP_SELF']);
$is_onepage = ($current_page === 'beranda.php');
?>

<?php if ($is_onepage): ?>
<!-- NAVBAR UNTUK ONE-PAGE (beranda.php) -->
<nav class="navbar-floating">
    <div class="navbar-capsule">
        <!-- Logo Only -->
        <a href="#beranda" class="navbar-logo-link">
            <img src="../assets/images/logo-navbar.png" alt="Alpharide" class="navbar-logo-only">
        </a>
        
        <!-- Navigation Menu dengan Smooth Scroll -->
        <ul class="nav-menu-floating">
            <li><a href="#beranda" class="nav-link active">Beranda</a></li>
            <li><a href="#tentang-kami" class="nav-link">Tentang Kami</a></li>
            <li><a href="#mobil" class="nav-link">Mobil</a></li>
            <li><a href="kontak.php" class="nav-link nav-link-kontak">Kontak</a></li>
        </ul>
        
        <!-- Auth Buttons -->
        <div class="nav-auth-floating">
            <?php if (is_logged_in()): ?>
                <a href="transaksi.php" class="btn-nav btn-nav-secondary">Riwayat</a>
                <a href="profil.php" class="btn-nav btn-nav-primary">Profil</a>
            <?php else: ?>
                <a href="register.php" class="btn-nav btn-nav-secondary">Daftar</a>
                <a href="login.php" class="btn-nav btn-nav-primary">Login</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<script>
// Smooth scroll untuk anchor links (hanya untuk one-page)
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            const offsetTop = target.offsetTop - 100;
            window.scrollTo({
                top: offsetTop,
                behavior: 'smooth'
            });
        }
    });
});

// Active state berubah saat scroll
window.addEventListener('scroll', () => {
    const sections = ['beranda', 'tentang-kami', 'mobil'];
    const navLinks = document.querySelectorAll('.nav-link');
    
    let current = '';
    sections.forEach(sectionId => {
        const section = document.getElementById(sectionId);
        if (section) {
            const sectionTop = section.offsetTop;
            if (scrollY >= (sectionTop - 200)) {
                current = sectionId;
            }
        }
    });
    
    navLinks.forEach(link => {
        link.classList.remove('active');
        const href = link.getAttribute('href');
        if (href === '#' + current) {
            link.classList.add('active');
        }
    });
});

// Kontak link active state
document.querySelector('.nav-link-kontak')?.addEventListener('click', function() {
    document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
    this.classList.add('active');
});
</script>

<?php else: ?>
<!-- NAVBAR BIASA UNTUK HALAMAN LAIN (kontak, profil, transaksi, dll) -->
<nav class="navbar-floating">
    <div class="navbar-capsule">
        <!-- Logo -->
        <a href="beranda.php" class="navbar-logo-link">
            <img src="../assets/images/logo-navbar.png" alt="Alpharide" class="navbar-logo-only">
        </a>
        
        <!-- Navigation Menu dengan Regular Links -->
        <ul class="nav-menu-floating">
            <li><a href="beranda.php" class="nav-link <?php echo $current_page === 'beranda.php' ? 'active' : ''; ?>">Beranda</a></li>
            <li><a href="beranda.php#tentang-kami" class="nav-link">Tentang Kami</a></li>
            <li><a href="beranda.php#mobil" class="nav-link">Mobil</a></li>
            <li><a href="kontak.php" class="nav-link <?php echo $current_page === 'kontak.php' ? 'active' : ''; ?>">Kontak</a></li>
        </ul>
        
        <!-- Auth Buttons -->
        <div class="nav-auth-floating">
            <?php if (is_logged_in()): ?>
                <a href="transaksi.php" class="btn-nav btn-nav-secondary">Riwayat</a>
                <a href="profil.php" class="btn-nav btn-nav-primary">Profil</a>
            <?php else: ?>
                <a href="register.php" class="btn-nav btn-nav-secondary">Daftar</a>
                <a href="login.php" class="btn-nav btn-nav-primary">Login</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
<?php endif; ?>