<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit();
}
require_once '../config/koneksi.php';

// Statistik Tujuan Kunjungan
$sql_tujuan = "SELECT tujuan, COUNT(*) as total FROM kehadiran GROUP BY tujuan ORDER BY total DESC LIMIT 5";
$result_tujuan = mysqli_query($conn, $sql_tujuan);
$data_tujuan = [];
$max_tujuan = 0;

while ($row = mysqli_fetch_assoc($result_tujuan)) {
    $data_tujuan[] = $row;
    if ($row['total'] > $max_tujuan) {
        $max_tujuan = $row['total'];
    }
}

include 'includes/header.php';
?>

<div class="dashboard-content">
    <h2>Rekap & Export</h2>

    <div class="rekap-grid">
        <div class="rekap-card">
            <h3>
                <i class="fas fa-calendar-day" style="background: rgba(59, 130, 246, 0.1); color: var(--primary);"></i>
                Rekap Harian
            </h3>
            <form class="rekap-form" method="GET" action="export_pdf.php" target="_blank">
                <input type="date" name="tanggal" value="<?php echo date('Y-m-d'); ?>" required>
                <button type="submit" name="type" value="view" class="btn-primary">
                    <i class="fas fa-eye"></i> Lihat Rekap
                </button>
                <button type="submit" name="type" value="pdf" class="btn-success">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </button>
            </form>
        </div>

        <div class="rekap-card">
            <h3>
                <i class="fas fa-calendar-alt" style="background: rgba(16, 185, 129, 0.1); color: var(--success);"></i>
                Rekap Bulanan
            </h3>
            <form class="rekap-form" method="GET" action="export_excel.php">
                <input type="month" name="bulan" value="<?php echo date('Y-m'); ?>" required>
                <button type="submit" name="type" value="view" class="btn-primary">
                    <i class="fas fa-eye"></i> Lihat Rekap
                </button>
                <button type="submit" name="type" value="excel" class="btn-success">
                    <i class="fas fa-file-excel"></i> Export Excel
                </button>
            </form>
        </div>
    </div>

    <div class="table-card">
        <div style="padding: 2rem;">
            <h3 style="margin-bottom: 1.5rem;">Statistik Tujuan Kunjungan</h3>
            <div class="stats-list">
                <?php foreach ($data_tujuan as $item): ?>
                <div class="stats-item">
                    <span class="stats-label"><?php echo substr($item['tujuan'], 0, 30); ?></span>
                    <div class="stats-bar-container">
                        <div class="stats-bar" style="width: <?php echo ($item['total'] / $max_tujuan * 100); ?>%">
                            <?php echo $item['total']; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="table-card" style="margin-top: 2rem;">
        <div style="padding: 2rem;">
            <h3 style="margin-bottom: 1.5rem;">Export Custom</h3>
            <form method="GET" action="export_custom.php" class="filter-form">
                <div class="filter-group">
                    <input type="date" name="start_date" placeholder="Tanggal Mulai" required>
                    <input type="date" name="end_date" placeholder="Tanggal Akhir" required>
                    <button type="submit" name="format" value="excel" class="btn-success">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </button>
                    <button type="submit" name="format" value="pdf" class="btn-danger">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>