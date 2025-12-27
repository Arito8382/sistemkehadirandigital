<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Sistem Kehadiran Digital</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="admin-layout">
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <i class="fas fa-users"></i>
                    <span class="logo-text">Admin Panel</span>
                </div>
            </div>

            <nav class="sidebar-nav">
                <a href="dashboard.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
                <a href="data_kehadiran.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'data_kehadiran.php' ? 'active' : ''; ?>">
                    <i class="fas fa-file-alt"></i>
                    <span>Data Kehadiran</span>
                </a>
                <a href="rekap.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'rekap.php' ? 'active' : ''; ?>">
                    <i class="fas fa-chart-bar"></i>
                    <span>Rekap & Export</span>
                </a>
                <a href="qr_code.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'qr_code.php' ? 'active' : ''; ?>">
                    <i class="fas fa-qrcode"></i>
                    <span>QR Code</span>
                </a>
                <a href="admin.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'admin.php' ? 'active' : ''; ?>">
                    <i class="fas fa-user-cog"></i>
                    <span>Manajemen Admin</span>
                </a>
                <a href="settings.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>">
                    <i class="fas fa-cog"></i>
                    <span>Pengaturan</span>
                </a>
                <a href="../logout.php" class="nav-item nav-logout">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </nav>

            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-chevron-left"></i>
            </button>
        </aside>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Topbar -->
            <header class="topbar">
                <div class="topbar-left">
                    <button class="mobile-toggle" id="mobileToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1>Sistem Kehadiran Digital</h1>
                </div>
                <div class="topbar-right">
                    <button class="btn-icon-topbar" id="darkModeBtn" title="Toggle Dark Mode">
                        <i class="fas fa-moon"></i>
                    </button>
                    <div class="admin-info">
                        <div class="admin-text">
                            <span class="admin-label">Admin</span>
                            <span class="admin-name"><?php echo $_SESSION['admin_nama']; ?></span>
                        </div>
                        <div class="admin-avatar">
                            <?php echo strtoupper(substr($_SESSION['admin_nama'], 0, 1)); ?>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content Area -->
            <main class="content-area">