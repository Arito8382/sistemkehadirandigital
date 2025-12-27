<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit();
}
require_once '../config/koneksi.php';

$start_date = isset($_GET['start_date']) ? clean_input($_GET['start_date']) : '';
$end_date = isset($_GET['end_date']) ? clean_input($_GET['end_date']) : '';
$format = isset($_GET['format']) ? $_GET['format'] : 'excel';

if (empty($start_date) || empty($end_date)) {
    header('Location: rekap.php');
    exit();
}

$sql = "SELECT * FROM kehadiran WHERE tanggal BETWEEN '$start_date' AND '$end_date' ORDER BY tanggal DESC, jam DESC";
$result = mysqli_query($conn, $sql);
$total = mysqli_num_rows($result);

if ($format == 'excel') {
    // Export ke Excel
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="Rekap_Custom_' . $start_date . '_to_' . $end_date . '.xls"');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Rekap Custom</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #4472C4; color: white; font-weight: bold; }
        tr:nth-child(even) { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2>REKAP KEHADIRAN CUSTOM</h2>
    <p><strong>Periode:</strong> <?php echo tanggal_indonesia($start_date); ?> s/d <?php echo tanggal_indonesia($end_date); ?></p>
    <p><strong>Total:</strong> <?php echo $total; ?> pengunjung</p>
    <p><strong>Dicetak:</strong> <?php echo date('d-m-Y H:i:s'); ?> WIB</p>
    
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>NIK</th>
                <th>Jenis Kelamin</th>
                <th>Asal</th>
                <th>Tujuan</th>
                <th>No HP</th>
                <th>Tanggal</th>
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
                <td><?php echo $row['nama']; ?></td>
                <td><?php echo $row['nik']; ?></td>
                <td><?php echo $row['jenis_kelamin']; ?></td>
                <td><?php echo $row['asal']; ?></td>
                <td><?php echo $row['tujuan']; ?></td>
                <td><?php echo $row['no_hp']; ?></td>
                <td><?php echo date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                <td><?php echo date('H:i', strtotime($row['jam'])); ?></td>
            </tr>
            <?php 
                endwhile;
            else: 
            ?>
            <tr>
                <td colspan="9" align="center">Tidak ada data</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
<?php
} else {
    // Export ke PDF
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="Rekap_Custom_' . $start_date . '_to_' . $end_date . '.pdf"');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Rekap Custom PDF</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        h2 { text-align: center; color: #1f2937; border-bottom: 3px solid #3b82f6; padding-bottom: 10px; }
        .info { background: #f3f4f6; padding: 15px; border-radius: 8px; margin: 20px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 11px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #3b82f6; color: white; font-weight: bold; }
        tr:nth-child(even) { background: #f9fafb; }
        .footer { margin-top: 30px; text-align: center; color: #6b7280; font-size: 12px; }
    </style>
</head>
<body>
    <h2>REKAP KEHADIRAN CUSTOM</h2>
    
    <div class="info">
        <strong>Periode:</strong> <?php echo tanggal_indonesia($start_date); ?> s/d <?php echo tanggal_indonesia($end_date); ?><br>
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
                <th>Tanggal</th>
                <th>Jam</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            mysqli_data_seek($result, 0);
            if ($total > 0):
                $no = 1;
                while ($row = mysqli_fetch_assoc($result)): 
            ?>
            <tr>
                <td><?php echo $no++; ?></td>
                <td><?php echo $row['nama']; ?></td>
                <td><?php echo $row['nik']; ?></td>
                <td><?php echo substr($row['asal'], 0, 30); ?></td>
                <td><?php echo substr($row['tujuan'], 0, 40); ?></td>
                <td><?php echo $row['no_hp']; ?></td>
                <td><?php echo date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                <td><?php echo date('H:i', strtotime($row['jam'])); ?></td>
            </tr>
            <?php 
                endwhile;
            else: 
            ?>
            <tr>
                <td colspan="8" style="text-align: center;">Tidak ada data</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
    
    <div class="footer">
        <p>Sistem Kehadiran Digital - Gedung Perkantoran Utama</p>
    </div>
</body>
</html>
<?php
}
mysqli_close($conn);
?>