<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit();
}
require_once '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ids'])) {
    $ids = $_POST['ids'];
    $deleted = 0;
    
    foreach ($ids as $id) {
        $id = (int)$id;
        
        // Get foto untuk dihapus
        $sql_get = "SELECT foto FROM kehadiran WHERE id = $id";
        $result = mysqli_query($conn, $sql_get);
        
        if (mysqli_num_rows($result) > 0) {
            $data = mysqli_fetch_assoc($result);
            
            // Hapus foto jika ada
            if (!empty($data['foto']) && file_exists('../uploads/' . $data['foto'])) {
                unlink('../uploads/' . $data['foto']);
            }
            
            // Hapus dari database
            $sql_delete = "DELETE FROM kehadiran WHERE id = $id";
            if (mysqli_query($conn, $sql_delete)) {
                $deleted++;
            }
        }
    }
    
    $_SESSION['success'] = "$deleted data berhasil dihapus!";
} else {
    $_SESSION['error'] = 'Tidak ada data yang dipilih!';
}

header('Location: data_kehadiran.php');
exit();
?>