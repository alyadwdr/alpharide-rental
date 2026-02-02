<div class="sidebar">
    <div class="sidebar-header">
        <img src="../assets/images/logo-navbar.png" alt="Alpharide">
    </div>
    
    <div class="sidebar-menu">
        <a href="index.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
            <span class="menu-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="7" height="7"></rect>
                    <rect x="14" y="3" width="7" height="7"></rect>
                    <rect x="14" y="14" width="7" height="7"></rect>
                    <rect x="3" y="14" width="7" height="7"></rect>
                </svg>
            </span>
            <span>Dashboard</span>
        </a>
        
        <a href="data-kendaraan.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'data-kendaraan.php' ? 'active' : ''; ?>">
            <span class="menu-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="1" y="3" width="15" height="13" rx="2" ry="2"></rect>
                    <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                    <circle cx="5.5" cy="18.5" r="2.5"></circle>
                    <circle cx="18.5" cy="18.5" r="2.5"></circle>
                </svg>
            </span>
            <span>Data Kendaraan</span>
        </a>
        
        <a href="data-pelanggan.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'data-pelanggan.php' ? 'active' : ''; ?>">
            <span class="menu-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
            </span>
            <span>Data Pelanggan</span>
        </a>
        
        <a href="data-transaksi.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'data-transaksi.php' ? 'active' : ''; ?>">
            <span class="menu-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="2" y="5" width="20" height="14" rx="2"></rect>
                    <line x1="2" y1="10" x2="22" y2="10"></line>
                </svg>
            </span>
            <span>Data Transaksi</span>
        </a>
    </div>
    
    <div class="sidebar-footer">
        <a href="logout.php" class="menu-item">
            <span class="menu-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                    <polyline points="16 17 21 12 16 7"></polyline>
                    <line x1="21" y1="12" x2="9" y2="12"></line>
                </svg>
            </span>
            <span>Logout</span>
        </a>
    </div>
</div>