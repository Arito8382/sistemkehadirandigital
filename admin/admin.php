<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit();
}
require_once '../config/koneksi.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'add') {
            $username = clean_input($_POST['username']);
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $nama_lengkap = clean_input($_POST['nama_lengkap']);
            
            // Cek username sudah ada
            $check_sql = "SELECT id FROM admin WHERE username = ?";
            $check_stmt = mysqli_prepare($conn, $check_sql);
            mysqli_stmt_bind_param($check_stmt, "s", $username);
            mysqli_stmt_execute($check_stmt);
            $check_result = mysqli_stmt_get_result($check_stmt);
            
            if (mysqli_num_rows($check_result) > 0) {
                $_SESSION['error'] = 'Username sudah digunakan!';
            } else {
                $sql = "INSERT INTO admin (username, password, nama_lengkap) VALUES (?, ?, ?)";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "sss", $username, $password, $nama_lengkap);
                
                if (mysqli_stmt_execute($stmt)) {
                    $_SESSION['success'] = 'Admin berhasil ditambahkan!';
                } else {
                    $_SESSION['error'] = 'Gagal menambahkan admin!';
                }
                mysqli_stmt_close($stmt);
            }
            mysqli_stmt_close($check_stmt);
            
        } elseif ($_POST['action'] == 'change_password') {
            $old_password = $_POST['old_password'];
            $new_password = $_POST['new_password'];
            $confirm_password = $_POST['confirm_password'];
            
            if ($new_password != $confirm_password) {
                $_SESSION['error'] = 'Password baru tidak cocok!';
            } elseif (strlen($new_password) < 6) {
                $_SESSION['error'] = 'Password minimal 6 karakter!';
            } else {
                $admin_id = $_SESSION['admin_id'];
                $sql = "SELECT password FROM admin WHERE id = ?";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "i", $admin_id);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $admin = mysqli_fetch_assoc($result);
                
                if (password_verify($old_password, $admin['password'])) {
                    $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                    $sql_update = "UPDATE admin SET password = ? WHERE id = ?";
                    $stmt_update = mysqli_prepare($conn, $sql_update);
                    mysqli_stmt_bind_param($stmt_update, "si", $new_password_hash, $admin_id);
                    
                    if (mysqli_stmt_execute($stmt_update)) {
                        $_SESSION['success'] = 'Password berhasil diubah!';
                    } else {
                        $_SESSION['error'] = 'Gagal mengubah password!';
                    }
                    mysqli_stmt_close($stmt_update);
                } else {
                    $_SESSION['error'] = 'Password lama salah!';
                }
                mysqli_stmt_close($stmt);
            }
            
        } elseif ($_POST['action'] == 'delete' && isset($_POST['admin_id'])) {
            $delete_id = (int)$_POST['admin_id'];
            
            // Tidak bisa hapus diri sendiri
            if ($delete_id == $_SESSION['admin_id']) {
                $_SESSION['error'] = 'Tidak dapat menghapus akun Anda sendiri!';
            } else {
                // Cek minimal harus ada 1 admin
                $count_sql = "SELECT COUNT(*) as total FROM admin";
                $count_result = mysqli_query($conn, $count_sql);
                $count = mysqli_fetch_assoc($count_result)['total'];
                
                if ($count <= 1) {
                    $_SESSION['error'] = 'Tidak dapat menghapus admin terakhir!';
                } else {
                    $sql = "DELETE FROM admin WHERE id = ?";
                    $stmt = mysqli_prepare($conn, $sql);
                    mysqli_stmt_bind_param($stmt, "i", $delete_id);
                    
                    if (mysqli_stmt_execute($stmt)) {
                        $_SESSION['success'] = 'Admin berhasil dihapus!';
                    } else {
                        $_SESSION['error'] = 'Gagal menghapus admin!';
                    }
                    mysqli_stmt_close($stmt);
                }
            }
        }
    }
    header('Location: admin.php');
    exit();
}

// Get all admins
$sql = "SELECT * FROM admin ORDER BY id ASC";
$result = mysqli_query($conn, $sql);

include 'includes/header.php';
?>

<div class="dashboard-content">
    <h2>Manajemen Admin</h2>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <div class="rekap-grid">
        <div class="rekap-card">
            <h3>
                <i class="fas fa-user-plus" style="background: rgba(59, 130, 246, 0.1); color: var(--primary);"></i>
                Tambah Admin Baru
            </h3>
            <form method="POST" class="rekap-form">
                <input type="hidden" name="action" value="add">
                <input type="text" name="username" placeholder="Username" required>
                <input type="text" name="nama_lengkap" placeholder="Nama Lengkap" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save"></i> Tambah Admin
                </button>
            </form>
        </div>

        <div class="rekap-card">
            <h3>
                <i class="fas fa-key" style="background: rgba(245, 158, 11, 0.1); color: var(--warning);"></i>
                Ubah Password
            </h3>
            <form method="POST" class="rekap-form">
                <input type="hidden" name="action" value="change_password">
                <input type="password" name="old_password" placeholder="Password Lama" required>
                <input type="password" name="new_password" placeholder="Password Baru" required>
                <input type="password" name="confirm_password" placeholder="Konfirmasi Password Baru" required>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save"></i> Ubah Password
                </button>
            </form>
        </div>
    </div>

    <div class="table-card" style="margin-top: 2rem;">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Username</th>
                        <th>Nama Lengkap</th>
                        <th>Tanggal Dibuat</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($result)): 
                    ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><strong><?php echo $row['username']; ?></strong></td>
                        <td><?php echo $row['nama_lengkap']; ?></td>
                        <td><?php echo date('d/m/Y', strtotime($row['created_at'])); ?></td>
                        <td>
                            <?php if ($row['id'] == $_SESSION['admin_id']): ?>
                                <span style="background: rgba(16, 185, 129, 0.1); color: var(--success); padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.875rem;">
                                    <i class="fas fa-check-circle"></i> Anda
                                </span>
                            <?php else: ?>
                                <span style="background: rgba(107, 114, 128, 0.1); color: var(--gray); padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.875rem;">
                                    <i class="fas fa-user"></i> Admin
                                </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($row['id'] != $_SESSION['admin_id']): ?>
                            <form method="POST" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus admin ini?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="admin_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" class="btn-icon btn-danger" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            <?php else: ?>
                                <span style="color: var(--gray); font-size: 0.875rem;">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
.alert-success {
    background: #d1fae5;
    color: #065f46;
    padding: 1rem;
    border-radius: 10px;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
</style>

<?php include 'includes/footer.php'; ?>