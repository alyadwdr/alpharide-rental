<?php
require_once '../config/database.php';
$page_title = 'Alpharide Rental - Sewa Kendaraan Terpercaya';
include 'includes/header.php';
include 'includes/navbar.php';

// Get all cars untuk carousel
$query_cars = "SELECT * FROM tbl_mobil ORDER BY id_mobil ASC";
$result_cars = mysqli_query($conn, $query_cars);
$cars = [];
while ($car = mysqli_fetch_assoc($result_cars)) {
    $cars[] = $car;
}
?>

<!-- ════════════════════════════════════════
     SECTION 1: HERO / BERANDA
     ════════════════════════════════════════ -->
<section id="beranda" class="hero-section-new">
    <div class="hero-bg-cream"></div>
    <div class="hero-backdrop-circle"></div>
    
    <div class="hero-container-new">

        <!-- Left: Text Content — slide dari kiri -->
        <div class="hero-text-content" data-reveal="fade-left" data-delay="200">
            <h1 class="hero-title-caps">SEWA KENDARAAN<br>LEBIH PRAKTIS DAN<br>TERPERCAYA</h1>
            <div class="hero-divider"></div>
            <p class="hero-description">
                Kami adalah penyedia layanan rental kendaraan yang berkomitmen menghadirkan armada berkualitas untuk mendukung berbagai kebutuhan perjalanan. Dengan pilihan mobil dan motor yang terawat, kami siap menjadi partner perjalanan Anda dengan pelayanan yang dapat diandalkan.
            </p>
        </div>
        
        <!-- Center: Car Image — zoom in -->
        <div class="hero-car-wrapper" data-reveal="zoom-in" data-delay="400">
            <img src="../assets/images/cars/hero-car.png" alt="Alpharide Car" class="hero-car-image">
            <div class="hero-car-shadow"></div>
        </div>
        
        <!-- Right: Feature Arrows — stagger dari kanan, satu per satu -->
        <div class="hero-features-arrows" data-stagger>
            <div class="feature-arrow feature-arrow-1" data-reveal="fade-right">
                <div class="feature-arrow-line"></div>
                <div class="feature-arrow-point"></div>
                <div class="feature-text">
                    <span class="feature-icon">🚗</span>
                    <span>Armada Lengkap & Terawat</span>
                </div>
            </div>
            
            <div class="feature-arrow feature-arrow-2" data-reveal="fade-right">
                <div class="feature-arrow-line"></div>
                <div class="feature-arrow-point"></div>
                <div class="feature-text">
                    <span class="feature-icon">📅</span>
                    <span>Sewa Harian, Mingguan, Bulanan</span>
                </div>
            </div>
            
            <div class="feature-arrow feature-arrow-3" data-reveal="fade-right">
                <div class="feature-arrow-line"></div>
                <div class="feature-arrow-point"></div>
                <div class="feature-text">
                    <span class="feature-icon">📍</span>
                    <span>Siap Antar & Ambil Kendaraan</span>
                </div>
            </div>
            
            <div class="feature-arrow feature-arrow-4" data-reveal="fade-right">
                <div class="feature-arrow-line"></div>
                <div class="feature-arrow-point"></div>
                <div class="feature-text">
                    <span class="feature-icon">💬</span>
                    <span>Pelayanan Ramah & Profesional</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ════════════════════════════════════════
     SECTION 2: TENTANG KAMI
     ════════════════════════════════════════ -->
<section id="tentang-kami" class="tentang-section">

    <!-- Dark Background (1/4) -->
    <div class="tentang-bg-dark">
        <div class="tentang-header">
            <!-- Garis dekoratif — animasi scaleX (line) -->
            <div class="tentang-divider" data-reveal="line"></div>
            <!-- Judul — fade up -->
            <h2 class="tentang-title" data-reveal="fade-up" data-delay="150">TENTANG KAMI</h2>
        </div>
        <!-- Deskripsi — fade up, lebih lambat -->
        <p class="tentang-description" data-reveal="fade-up" data-delay="300" data-duration="slow">
            Alpharide Rental merupakan penyedia layanan rental kendaraan yang berkomitmen untuk memberikan pengalaman perjalanan yang aman, nyaman, dan menyenangkan. Kami menyediakan berbagai pilihan kendaraan, baik mobil maupun motor, yang dapat digunakan untuk berbagai kebutuhan perjalanan, mulai dari aktivitas harian hingga perjalanan jarak jauh. Kami selalu menjaga kualitas armada melalui perawatan rutin dan pengecekan berkala untuk memastikan setiap kendaraan berada dalam kondisi terbaik sebelum digunakan. Dengan dukungan tim yang profesional dan berpengalaman, Alpharide Rental hadir sebagai mitra perjalanan yang siap melayani pelanggan dengan sepenuh hati.
        </p>
    </div>
    
    <!-- Cream Background (3/4) -->
    <div class="tentang-bg-cream">
        <div class="mengapa-header">
            <!-- Garis dekoratif — line -->
            <div class="mengapa-divider" data-reveal="line"></div>
            <!-- Judul — fade up -->
            <h2 class="mengapa-title" data-reveal="fade-up" data-delay="100">MENGAPA MEMILIH ALPHARIDE RENTAL?</h2>
        </div>
        
        <!-- Reason Cards — stagger: muncul satu-satu dari bawah -->
        <div class="reason-cards" data-stagger>
            <!-- Card 1: Brown — fade up -->
            <div class="reason-card card-brown" data-reveal="fade-up">
                <h3>ARMADA TERAWAT</h3>
                <p>Setiap kendaraan di Alpharide Rental dirawat dan dicek secara berkala untuk memastikan kondisi mesin, interior, dan eksterior tetap prima. Kami menjaga kebersihan serta kenyamanan armada agar siap digunakan untuk berbagai kebutuhan perjalanan dengan aman dan nyaman.</p>
            </div>
            
            <!-- Card 2: Dark — fade up -->
            <div class="reason-card card-dark" data-reveal="fade-up">
                <h3>SYARAT JELAS<br>& TRANSPARAN</h3>
                <p>Kami menerapkan ketentuan penyewaan yang jelas dan mudah dipahami oleh pelanggan. Seluruh informasi terkait durasi sewa, penggunaan kendaraan, serta ketentuan lainnya disampaikan secara terbuka demi memberikan rasa aman dan kepercayaan.</p>
            </div>
            
            <!-- Card 3: Brown — fade up -->
            <div class="reason-card card-brown" data-reveal="fade-up">
                <h3>PELAYANAN<br>RESPONSIF & RAMAH</h3>
                <p>Tim Alpharide Rental siap memberikan pelayanan yang cepat, ramah, dan profesional. Kami selalu berusaha merespons setiap pertanyaan dan kebutuhan pelanggan dengan baik agar proses penyewaan berjalan dengan lancar.</p>
            </div>
        </div>
    </div>
</section>

<!-- ════════════════════════════════════════
     SECTION 3: MOBIL (CAROUSEL)
     ════════════════════════════════════════ -->
<section id="mobil" class="mobil-section">
    <div class="mobil-bg-cream">

        <!-- Logo -->
        <div class="mobil-logo-wrapper">
            <img src="../assets/images/logo.png" alt="Alpharide" class="mobil-logo">
        </div>
        
        <!-- Carousel Container -->
        <div class="car-carousel-container">
            <!-- Previous Button -->
            <button class="carousel-btn carousel-prev" onclick="prevCar()">
                <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="15 18 9 12 15 6"></polyline>
                </svg>
            </button>
            
            <!-- Carousel Track -->
            <div class="car-carousel-track">
                <?php foreach ($cars as $index => $car): ?>
                <div class="car-carousel-item <?php echo $index === 0 ? 'active' : ''; ?>" 
                     data-index="<?php echo $index; ?>"
                     data-name="<?php echo $car['merek'] . ' ' . $car['model']; ?>"
                     data-price="<?php echo number_format($car['harga_sewa_per_hari'], 0, ',', '.'); ?>"
                     data-id="<?php echo $car['id_mobil']; ?>">
                    <img src="../assets/images/cars/<?php echo $car['foto_mobil']; ?>" 
                         alt="<?php echo $car['merek']; ?>" 
                         class="car-carousel-image">
                </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Next Button -->
            <button class="carousel-btn carousel-next" onclick="nextCar()">
                <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
            </button>
        </div>
        
        <!-- Car Info (Fixed Position) -->
        <div class="car-info-fixed">
            <h2 class="car-name" id="carName"><?php echo $cars[0]['merek'] . ' ' . $cars[0]['model']; ?></h2>
            <p class="car-price" id="carPrice">Rp. <?php echo number_format($cars[0]['harga_sewa_per_hari'], 0, ',', '.'); ?>/hari</p>
            <a href="form-transaksi.php?id=<?php echo $cars[0]['id_mobil']; ?>" 
               class="btn-lihat-detail" 
               id="btnDetail">Lihat Detail</a>
        </div>
    </div>
</section>

<!-- FOOTER -->
<?php include 'includes/footer.php'; ?>

<!-- ════════════════════════════════════════
     SCROLL REVEAL ENGINE — load sebelum </body>
     ════════════════════════════════════════ -->
<script src="../assets/js/scroll-reveal.js"></script>

<script>
// ── Carousel JavaScript ──────────────────────────────────────
const cars = <?php echo json_encode($cars); ?>;
const totalCars = cars.length;
let currentIndex = 0;

const track = document.querySelector('.car-carousel-track');
const items = document.querySelectorAll('.car-carousel-item');
const carNameEl = document.getElementById('carName');
const carPriceEl = document.getElementById('carPrice');
const btnDetailEl = document.getElementById('btnDetail');

function updateCarousel() {
    const containerCenter = document.querySelector('.car-carousel-container').offsetWidth / 2;
    const itemWidth = items[0].offsetWidth; 
    const position = containerCenter - (currentIndex * itemWidth) - (itemWidth / 2);
    
    track.style.transform = `translateX(${position}px)`;

    items.forEach((item, index) => {
        if (index === currentIndex) {
            item.classList.add('active');
        } else {
            item.classList.remove('active');
        }
    });

    const currentCar = cars[currentIndex];
    carNameEl.textContent = currentCar.merek + ' ' + currentCar.model;
    const priceFormatted = new Intl.NumberFormat('id-ID').format(currentCar.harga_sewa_per_hari);
    carPriceEl.textContent = 'Rp. ' + priceFormatted + '/hari';
    btnDetailEl.href = 'form-transaksi.php?id=' + currentCar.id_mobil;
}

function nextCar() {
    if (currentIndex < totalCars - 1) {
        currentIndex++;
    } else {
        currentIndex = 0;
    }
    updateCarousel();
}

function prevCar() {
    if (currentIndex > 0) {
        currentIndex--;
    } else {
        currentIndex = totalCars - 1;
    }
    updateCarousel();
}

let touchStartX = 0;
let touchEndX = 0;

document.querySelector('.car-carousel-track').addEventListener('touchstart', (e) => {
    touchStartX = e.changedTouches[0].screenX;
});

document.querySelector('.car-carousel-track').addEventListener('touchend', (e) => {
    touchEndX = e.changedTouches[0].screenX;
    handleSwipe();
});

function handleSwipe() {
    if (touchEndX < touchStartX - 50) nextCar();
    if (touchEndX > touchStartX + 50) prevCar();
}

window.addEventListener('load', () => {
    updateCarousel();
    window.addEventListener('resize', updateCarousel);
});

window.addEventListener('scroll', () => {
    const sections = ['beranda', 'tentang-kami', 'mobil'];
    const navLinks = document.querySelectorAll('.nav-link');
    
    let current = '';
    sections.forEach(sectionId => {
        const section = document.getElementById(sectionId);
        if (section) {
            const sectionTop = section.offsetTop;
            const sectionHeight = section.clientHeight;
            if (scrollY >= (sectionTop - 200)) {
                current = sectionId;
            }
        }
    });
    
    navLinks.forEach(link => {
        link.classList.remove('active');
        if (link.getAttribute('href') === '#' + current) {
            link.classList.add('active');
        }
    });
});
</script>

</body>
</html>