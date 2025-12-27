<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit();
}
require_once '../config/koneksi.php';

$tanggal = isset($_GET['tanggal']) ? clean_input($_GET['tanggal']) : date('Y-m-d');
$type = isset($_GET['type']) ? $_GET['type'] : 'view';

$sql = "SELECT * FROM kehadiran WHERE tanggal = '$tanggal' ORDER BY jam ASC";
$result = mysqli_query($conn, $sql);
$total = mysqli_num_rows($result);

// Jika view, tampilkan HTML biasa
if ($type == 'view') {
    include 'includes/header.php';
?>
<div class="dashboard-content">
    <a href="rekap.php" class="back-link">
        <i class="fas fa-arrow-left"></i> Kembali ke Rekap
    </a>

    <div class="detail-card">
        <div class="detail-header">
            <h3><i class="fas fa-calendar-day"></i> Rekap Harian - <?php echo tanggal_indonesia($tanggal); ?></h3>
            <div class="action-buttons">
                <button onclick="window.print()" class="btn-primary">
                    <i class="fas fa-print"></i> Cetak
                </button>
                <a href="export_pdf.php?tanggal=<?php echo $tanggal; ?>&type=pdf" class="btn-success" target="_blank">
                    <i class="fas fa-file-pdf"></i> Download PDF
                </a>
            </div>
        </div>

        <div style="margin-bottom: 2rem; padding: 1rem; background: #f9fafb; border-radius: 10px;">
            <strong>Total Pengunjung:</strong> <?php echo $total; ?> orang
        </div>

        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>NIK</th>
                        <th>Asal</th>
                        <th>Tujuan</th>
                        <th>No HP</th>
                        <th>Jam</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if ($total > 0):
                        $no = 1;
                        while ($row = mysqli_fetch_assoc($result)): 
                    ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><strong><?php echo $row['nama']; ?></strong></td>
                        <td><?php echo $row['nik']; ?></td>
                        <td><?php echo $row['asal']; ?></td>
                        <td><?php echo $row['tujuan']; ?></td>
                        <td><?php echo $row['no_hp']; ?></td>
                        <td><?php echo date('H:i', strtotime($row['jam'])); ?> WIB</td>
                    </tr>
                    <?php 
                        endwhile;
                    else: 
                    ?>
                    <tr>
                        <td colspan="7" class="text-center">Tidak ada pengunjung pada tanggal ini</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
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
}
</style>

<?php
    include 'includes/footer.php';
    exit();
}

// Jika PDF, buat format HTML untuk PDF
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="Rekap_Harian_' . $tanggal . '.pdf"');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Rekap Harian</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        h2 { text-align: center; color: #1f2937; border-bottom: 3px solid #3b82f6; padding-bottom: 10px; }
        .info { background: #f3f4f6; padding: 15px; border-radius: 8px; margin: 20px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background: #3b82f6; color: white; font-weight: bold; }
        tr:nth-child(even) { background: #f9fafb; }
        .footer { margin-top: 30px; text-align: center; color: #6b7280; font-size: 12px; }
    </style>
</head>
<body>
    <h2>REKAP KEHADIRAN HARIAN</h2>
    
    <div class="info">
        <strong>Tanggal:</strong> <?php echo tanggal_indonesia($tanggal); ?><br>
        <strong>Total Pengunjung:</strong> <?php echo $total; ?> orang<br>
        <strong>Dicetak:</strong> <?php echo date('d-m-Y H:i:s'); ?> WIB
    </div>
    
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>NIK</th>
                <th>Asal</th>
                <th>Tujuan</th>
                <th>No HP</th>
                <th>Jam</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            mysqli_data_seek($result, 0); // Reset pointer
            if ($total > 0):
                $no = 1;
                while ($row = mysqli_fetch_assoc($result)): 
            ?>
            <tr>
                <td><?php echo $no++; ?></td>
                <td><?php echo $row['nama']; ?></td>
                <td><?php echo $row['nik']; ?></td>
                <td><?php echo $row['asal']; ?></td>
                <td><?php echo $row['tujuan']; ?></td>
                <td><?php echo $row['no_hp']; ?></td>
                <td><?php echo date('H:i', strtotime($row['jam'])); ?> WIB</td>
            </tr>
            <?php 
                endwhile;
            else: 
            ?>
            <tr>
                <td colspan="7" style="text-align: center;">Tidak ada pengunjung</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
    
    <div class="footer">
        <p>Sistem Kehadiran Digital - Gedung Perkantoran Utama</p>
        <p>Dokumen ini dicetak secara otomatis</p>
    </div>
</body>
</html>
<?php
mysqli_close($conn);
?>