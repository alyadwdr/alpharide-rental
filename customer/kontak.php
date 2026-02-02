<?php
require_once '../config/database.php';
$page_title = 'Kontak - Alpharide Rental';
include 'includes/header.php';
include 'includes/navbar.php';
?>

<div style="max-width: 1200px; margin: 3rem auto; padding: 0 2rem;">
    <h1 style="text-align: center; color: var(--dark-color); margin-bottom: 3rem;">HUBUNGI KAMI</h1>
    
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 3rem;">
        <div style="background: white; padding: 3rem; border-radius: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.1);">
            <h2 style="color: var(--dark-color); margin-bottom: 2rem;">Informasi Kontak</h2>
            
            <div style="margin-bottom: 2rem;">
                <h3 style="color: var(--primary-color); margin-bottom: 0.5rem;">📍 Alamat</h3>
                <p style="color: #666; line-height: 1.6;">Jl. Veteran No. 45, Purwakarta, Jawa Barat 41115</p>
            </div>
            
            <div style="margin-bottom: 2rem;">
                <h3 style="color: var(--primary-color); margin-bottom: 0.5rem;">📞 Telepon & WhatsApp</h3>
                <p style="color: #666; line-height: 1.6;">+62 812 3456 7890</p>
                <p style="color: #666; line-height: 1.6;">+62 823 4567 8901</p>
            </div>
            
            <div style="margin-bottom: 2rem;">
                <h3 style="color: var(--primary-color); margin-bottom: 0.5rem;">✉️ Email</h3>
                <p style="color: #666; line-height: 1.6;">info@alpharide.com</p>
                <p style="color: #666; line-height: 1.6;">cs@alpharide.com</p>
            </div>
            
            <div>
                <h3 style="color: var(--primary-color); margin-bottom: 0.5rem;">⏰ Jam Operasional</h3>
                <p style="color: #666; line-height: 1.6;">Senin - Sabtu: 08:00 - 20:00</p>
                <p style="color: #666; line-height: 1.6;">Minggu: Tutup</p>
            </div>
        </div>
        
        <div style="background: var(--dark-color); padding: 3rem; border-radius: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.1);">
            <h2 style="color: white; margin-bottom: 2rem;">Kirim Pesan</h2>
            
            <form>
                <div class="form-group">
                    <input type="text" placeholder="Nama Lengkap" style="background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: white;">
                </div>
                <div class="form-group">
                    <input type="email" placeholder="Email" style="background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: white;">
                </div>
                <div class="form-group">
                    <input type="tel" placeholder="No. Telepon" style="background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: white;">
                </div>
                <div class="form-group">
                    <textarea rows="5" placeholder="Pesan Anda" style="width: 100%; padding: 12px 15px; border: 2px solid rgba(255,255,255,0.2); border-radius: 8px; background: rgba(255,255,255,0.1); color: white; resize: vertical;"></textarea>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 15px;">Kirim Pesan</button>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>