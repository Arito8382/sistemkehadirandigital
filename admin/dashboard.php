<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit();
}
require_once '../config/koneksi.php';

// Hitung statistik
$today = date('Y-m-d');
$this_month = date('Y-m');

// Total hari ini
$sql_today = "SELECT COUNT(*) as total FROM kehadiran WHERE tanggal = '$today'";
$result_today = mysqli_query($conn, $sql_today);
$total_today = mysqli_fetch_assoc($result_today)['total'];

// Total bulan ini
$sql_month = "SELECT COUNT(*) as total FROM kehadiran WHERE DATE_FORMAT(tanggal, '%Y-%m') = '$this_month'";
$result_month = mysqli_query($conn, $sql_month);
$total_month = mysqli_fetch_assoc($result_month)['total'];

// Jam tersibuk
$sql_peak = "SELECT HOUR(jam) as hour, COUNT(*) as total FROM kehadiran GROUP BY HOUR(jam) ORDER BY total DESC LIMIT 1";
$result_peak = mysqli_query($conn, $sql_peak);
$peak_hour = mysqli_fetch_assoc($result_peak);
$peak_time = isset($peak_hour['hour']) ? sprintf("%02d:00", $peak_hour['hour']) : "10:00";

// Rata-rata per hari (30 hari terakhir)
$sql_avg = "SELECT COUNT(*) / 30 as avg_daily FROM kehadiran WHERE tanggal >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
$result_avg = mysqli_query($conn, $sql_avg);
$avg_daily = round(mysqli_fetch_assoc($result_avg)['avg_daily']);

// Data grafik mingguan (7 hari terakhir)
$data_weekly = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $day_name = date('D', strtotime($date));
    $sql_day = "SELECT COUNT(*) as total FROM kehadiran WHERE tanggal = '$date'";
    $result_day = mysqli_query($conn, $sql_day);
    $total_day = mysqli_fetch_assoc($result_day)['total'];
    $data_weekly[] = ['day' => $day_name, 'total' => $total_day];
}

include 'includes/header.php';
?>

<div class="dashboard-content">
    <h2>Dashboard</h2>
    
    <div class="stats-cards">
        <div class="stat-card stat-blue">
            <div class="stat-content">
                <div class="stat-info">
                    <p class="stat-label">Hari Ini</p>
                    <p class="stat-value"><?php echo $total_today; ?></p>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <p class="stat-desc">Total pengunjung</p>
        </div>

        <div class="stat-card stat-green">
            <div class="stat-content">
                <div class="stat-info">
                    <p class="stat-label">Bulan Ini</p>
                    <p class="stat-value"><?php echo $total_month; ?></p>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-calendar"></i>
                </div>
            </div>
            <p class="stat-desc">Total kunjungan</p>
        </div>

        <div class="stat-card stat-purple">
            <div class="stat-content">
                <div class="stat-info">
                    <p class="stat-label">Jam Sibuk</p>
                    <p class="stat-value"><?php echo $peak_time; ?></p>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
            <p class="stat-desc">Peak time</p>
        </div>

        <div class="stat-card stat-orange">
            <div class="stat-content">
                <div class="stat-info">
                    <p class="stat-label">Rata-rata</p>
                    <p class="stat-value"><?php echo $avg_daily; ?></p>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
            <p class="stat-desc">Per hari</p>
        </div>
    </div>

    <div class="chart-card">
        <h3>Grafik Kehadiran Mingguan</h3>
        <div class="bar-chart">
            <?php foreach ($data_weekly as $data): ?>
            <div class="bar-item">
                <div class="bar" style="height: <?php echo min(($data['total'] / max(array_column($data_weekly, 'total'))) * 100, 100); ?>%">
                    <span class="bar-value"><?php echo $data['total']; ?></span>
                </div>
                <span class="bar-label"><?php echo $data['day']; ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>