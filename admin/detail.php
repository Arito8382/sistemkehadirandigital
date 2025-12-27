<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit();
}
require_once '../config/koneksi.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$sql = "SELECT * FROM kehadiran WHERE id = $id";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 0) {
    header('Location: data_kehadiran.php');
    exit();
}

$data = mysqli_fetch_assoc($result);

include 'includes/header.php';
?>

<div class="dashboard-content">
    <a href="data_kehadiran.php" class="back-link">
        <i class="fas fa-arrow-left"></i> Kembali ke Data Kehadiran
    </a>

    <div class="detail-card">
        <div class="detail-header">
            <h3><i class="fas fa-user-circle"></i> Detail Kehadiran</h3>
            <div class="action-buttons">
                <button onclick="window.print()" class="btn-primary">
                    <i class="fas fa-print"></i> Cetak
                </button>
                <a href="delete.php?id=<?php echo $data['id']; ?>" class="btn-secondary" onclick="return confirm('Yakin ingin menghapus data ini?')">
                    <i class="fas fa-trash"></i> Hapus
                </a>
            </div>
        </div>

        <div class="detail-grid">
            <div class="detail-item">
                <div class="detail-label">Nama Lengkap</div>
                <div class="detail-value"><?php echo $data['nama']; ?></div>
            </div>

            <div class="detail-item">
                <div class="detail-label">NIK / No Identitas</div>
                <div class="detail-value"><?php echo $data['nik']; ?></div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Jenis Kelamin</div>
                <div class="detail-value"><?php echo $data['jenis_kelamin']; ?></div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Asal Instansi / Alamat</div>
                <div class="detail-value"><?php echo $data['asal']; ?></div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Nomor HP</div>
                <div class="detail-value"><?php echo $data['no_hp']; ?></div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Tanggal Kunjungan</div>
                <div class="detail-value"><?php echo tanggal_indonesia($data['tanggal']); ?></div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Jam Kunjungan</div>
                <div class="detail-value"><?php echo date('H:i', strtotime($data['jam'])); ?> WIB</div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Waktu Input</div>
                <div class="detail-value"><?php echo date('d/m/Y H:i', strtotime($data['created_at'])); ?></div>
            </div>
        </div>

        <div style="margin-top: 2rem;">
            <div class="detail-item">
                <div class="detail-label">Tujuan Datang</div>
                <div class="detail-value"><?php echo nl2br($data['tujuan']); ?></div>
            </div>
        </div>

        <?php if (!empty($data['foto'])): ?>
        <div style="margin-top: 2rem;">
            <h4 style="margin-bottom: 1rem;">Foto Pengunjung</h4>
            <img src="../uploads/<?php echo $data['foto']; ?>" alt="Foto" class="photo-preview">
        </div>
        <?php endif; ?>

        <?php if (!empty($data['tanda_tangan'])): ?>
        <div style="margin-top: 2rem;">
            <h4 style="margin-bottom: 1rem;">Tanda Tangan Digital</h4>
            <img src="<?php echo $data['tanda_tangan']; ?>" alt="Tanda Tangan" class="signature-image">
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
@media print {
    .sidebar, .topbar, .back-link, .action-buttons {
        display: none !important;
    }
    .main-content {
        margin-left: 0 !important;
    }
    .detail-card {
        box-shadow: none;
    }
}
</style>

<?php include 'includes/footer.php'; ?>