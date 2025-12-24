<?php
session_start();
if (!isset($_SESSION['success'])) {
    header('Location: index.php');
    exit();
}
unset($_SESSION['success']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berhasil - Sistem Kehadiran Digital</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="success-page">
    <div class="success-container">
        <div class="success-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <h1>Terima Kasih!</h1>
        <p>Data kehadiran Anda telah tersimpan</p>
        <div class="loading-text">Mengalihkan ke halaman utama...</div>
    </div>

    <script>
        setTimeout(function() {
            window.location.href = 'index.php';
        }, 3000);
    </script>
</body>
</html>