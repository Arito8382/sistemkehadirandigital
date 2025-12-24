<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Kehadiran Digital</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="hero-section">
        <div class="container">
            <div class="hero-content">
                <div class="logo-circle">
                    <i class="fas fa-users"></i>
                </div>
                <h1 class="hero-title">Sistem Kehadiran Digital</h1>
                <p class="hero-subtitle">Sumedang Creative Center</p>
                
                <div class="info-cards">
                    <div class="info-item">
                        <i class="fas fa-clock"></i>
                        <span>Senin - Jumat: 08:00 - 17:00</span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-calendar"></i>
                        <span>Sabtu: 08:00 - 12:00</span>
                    </div>
                </div>
            </div>

            <div class="features">
                <div class="feature-card">
                    <i class="fas fa-user-check"></i>
                    <h3>Cepat & Mudah</h3>
                    <p>Isi form kehadiran hanya dalam 2 menit</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-file-alt"></i>
                    <h3>Data Aman</h3>
                    <p>Data tersimpan dengan sistem terenkripsi</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-chart-bar"></i>
                    <h3>Terintegrasi</h3>
                    <p>Sistem pelaporan otomatis real-time</p>
                </div>
            </div>

            <div class="cta-section">
                <a href="form_kehadiran.php" class="btn-primary">
                    <i class="fas fa-edit"></i> Isi Kehadiran Sekarang
                </a>
                <div class="admin-link">
                    <a href="login.php">Login Admin</a>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <p>&copy; 2024 Sistem Kehadiran Digital By Riko Fuasra Pramudika. All rights reserved.</p>
    </footer>
</body>
</html>