<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit();
}
require_once '../config/koneksi.php';

// Set header untuk download Excel
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="Data_Kehadiran_' . date('Y-m-d_H-i-s') . '.xls"');

// Get filter
$search = isset($_GET['search']) ? clean_input($_GET['search']) : '';
$filter_date = isset($_GET['filter_date']) ? clean_input($_GET['filter_date']) : '';
$bulan = isset($_GET['bulan']) ? clean_input($_GET['bulan']) : '';

$where = "WHERE 1=1";
if (!empty($search)) {
    $where .= " AND (nama LIKE '%$search%' OR asal LIKE '%$search%' OR tujuan LIKE '%$search%')";
}
if (!empty($filter_date)) {
    $where .= " AND tanggal = '$filter_date'";
}
if (!empty($bulan)) {
    $where .= " AND DATE_FORMAT(tanggal, '%Y-%m') = '$bulan'";
}

$sql = "SELECT * FROM kehadiran $where ORDER BY tanggal DESC, jam DESC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Data Kehadiran</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #4472C4;
            color: white;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h2>DATA KEHADIRAN PENGUNJUNG</h2>
    <p>Tanggal Export: <?php echo date('d-m-Y H:i:s'); ?></p>
    
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
            if (mysqli_num_rows($result) > 0):
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
    
    <p>Total Data: <?php echo mysqli_num_rows($result); ?></p>
</body>
</html>
<?php
mysqli_close($conn);
?>