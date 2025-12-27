<?php
session_start();
require_once '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil dan bersihkan data
    $nama = clean_input($_POST['nama']);
    $nik = clean_input($_POST['nik']);
    $jenis_kelamin = clean_input($_POST['jenis_kelamin']);
    $asal = clean_input($_POST['asal']);
    $tujuan = clean_input($_POST['tujuan']);
    $no_hp = clean_input($_POST['no_hp']);
    $tanda_tangan = isset($_POST['tanda_tangan']) ? $_POST['tanda_tangan'] : NULL;
    
    // Tanggal dan jam otomatis
    $tanggal = date('Y-m-d');
    $jam = date('H:i:s');
    
    // Upload foto jika ada
    $foto = NULL;
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png'];
        $filename = $_FILES['foto']['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);
        
        if (in_array(strtolower($filetype), $allowed)) {
            if ($_FILES['foto']['size'] <= 2097152) { // 2MB
                $new_filename = time() . '_' . uniqid() . '.' . $filetype;
                $upload_path = '../uploads/' . $new_filename;
                
                // Buat folder uploads jika belum ada
                if (!file_exists('../uploads/')) {
                    mkdir('../uploads/', 0777, true);
                }
                
                if (move_uploaded_file($_FILES['foto']['tmp_name'], $upload_path)) {
                    $foto = $new_filename;
                }
            }
        }
    }
    
    // Validasi NIK (harus 16 digit)
    if (strlen($nik) != 16 || !is_numeric($nik)) {
        $_SESSION['error'] = 'NIK harus 16 digit angka!';
        header('Location: ../form_kehadiran.php');
        exit();
    }
    
    // Validasi nomor HP
    if (!preg_match('/^[0-9]{10,13}$/', $no_hp)) {
        $_SESSION['error'] = 'Nomor HP tidak valid!';
        header('Location: ../form_kehadiran.php');
        exit();
    }
    
    // Insert ke database
    $sql = "INSERT INTO kehadiran (nama, nik, jenis_kelamin, asal, tujuan, no_hp, tanggal, jam, foto, tanda_tangan) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssssssssss", $nama, $nik, $jenis_kelamin, $asal, $tujuan, $no_hp, $tanggal, $jam, $foto, $tanda_tangan);
    
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success'] = true;
        header('Location: ../success.php');
    } else {
        $_SESSION['error'] = 'Gagal menyimpan data: ' . mysqli_error($conn);
        header('Location: ../form_kehadiran.php');
    }
    
    mysqli_stmt_close($stmt);
} else {
    header('Location: ../form_kehadiran.php');
}

mysqli_close($conn);
?>