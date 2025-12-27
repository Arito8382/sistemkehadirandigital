<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit();
}
require_once '../config/koneksi.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get data untuk hapus foto
$sql_get = "SELECT foto FROM kehadiran WHERE id = $id";
$result = mysqli_query($conn, $sql_get);

if (mysqli_num_rows($result) > 0) {
    $data = mysqli_fetch_assoc($result);
    
    // Hapus foto jika ada
    if (!empty($data['foto']) && file_exists('../uploads/' . $data['foto'])) {
        unlink('../uploads/' . $data['foto']);
    }
    
    // Hapus data dari database
    $sql_delete = "DELETE FROM kehadiran WHERE id = $id";
    
    if (mysqli_query($conn, $sql_delete)) {
        $_SESSION['success'] = 'Data berhasil dihapus!';
    } else {
        $_SESSION['error'] = 'Gagal menghapus data!';
    }
} else {
    $_SESSION['error'] = 'Data tidak ditemukan!';
}

header('Location: data_kehadiran.php');
exit();
?>