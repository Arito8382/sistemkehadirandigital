<?php
session_start();
require_once '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = clean_input($_POST['username']);
    $password = $_POST['password'];
    
    // Debug mode - hapus setelah berhasil
    error_log("Login attempt - Username: $username");
    
    $sql = "SELECT * FROM admin WHERE username = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) == 1) {
        $admin = mysqli_fetch_assoc($result);
        
        // Debug
        error_log("User found - checking password");
        error_log("Hash from DB: " . substr($admin['password'], 0, 20));
        
        // Verifikasi password
        if (password_verify($password, $admin['password'])) {
            error_log("Password verified successfully");
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_nama'] = $admin['nama_lengkap'];
            header('Location: ../admin/dashboard.php');
            exit();
        } else {
            error_log("Password verification failed");
            $_SESSION['error'] = 'Username atau password salah! (Password tidak cocok)';
            header('Location: ../login.php');
            exit();
        }
    } else {
        error_log("User not found in database");
        $_SESSION['error'] = 'Username atau password salah! (User tidak ditemukan)';
        header('Location: ../login.php');
        exit();
    }
    
    mysqli_stmt_close($stmt);
} else {
    header('Location: ../login.php');
}

mysqli_close($conn);
?>