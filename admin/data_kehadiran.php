<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit();
}
require_once '../config/koneksi.php';

// Pagination
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Search & Filter
$search = isset($_GET['search']) ? clean_input($_GET['search']) : '';
$filter_date = isset($_GET['filter_date']) ? clean_input($_GET['filter_date']) : '';

$where = "WHERE 1=1";
if (!empty($search)) {
    $where .= " AND (nama LIKE '%$search%' OR asal LIKE '%$search%' OR tujuan LIKE '%$search%')";
}
if (!empty($filter_date)) {
    $where .= " AND tanggal = '$filter_date'";
}

// Get total records
$sql_count = "SELECT COUNT(*) as total FROM kehadiran $where";
$result_count = mysqli_query($conn, $sql_count);
$total_records = mysqli_fetch_assoc($result_count)['total'];
$total_pages = ceil($total_records / $limit);

// Get data
$sql = "SELECT * FROM kehadiran $where ORDER BY tanggal DESC, jam DESC LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $sql);

include 'includes/header.php';
?>

<div class="dashboard-content">
    <div class="content-header">
        <h2>Data Kehadiran</h2>
        <div class="header-actions">
            <button class="btn-danger" id="bulkDeleteBtn" style="display: none;">
                <i class="fas fa-trash"></i> Hapus Terpilih (<span id="selectedCount">0</span>)
            </button>
            <button class="btn-success" onclick="exportExcel()">
                <i class="fas fa-file-excel"></i> Export Excel
            </button>
        </div>
    </div>

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

    <div class="filter-card">
        <form method="GET" class="filter-form">
            <div class="filter-group">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" placeholder="Cari nama, asal, atau tujuan..." value="<?php echo $search; ?>">
                </div>
                <input type="date" name="filter_date" value="<?php echo $filter_date; ?>" placeholder="Tanggal">
                <select name="filter_jk" class="form-select">
                    <option value="">Semua Jenis Kelamin</option>
                    <option value="Laki-laki" <?php echo (isset($_GET['filter_jk']) && $_GET['filter_jk'] == 'Laki-laki') ? 'selected' : ''; ?>>Laki-laki</option>
                    <option value="Perempuan" <?php echo (isset($_GET['filter_jk']) && $_GET['filter_jk'] == 'Perempuan') ? 'selected' : ''; ?>>Perempuan</option>
                </select>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-filter"></i> Filter
                </button>
                <a href="data_kehadiran.php" class="btn-secondary">
                    <i class="fas fa-redo"></i> Reset
                </a>
            </div>
        </form>
    </div>

    <div class="table-card">
        <div class="table-responsive">
            <form id="bulkForm" method="POST" action="bulk_delete.php">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 40px;">
                                <input type="checkbox" id="selectAll">
                            </th>
                            <th>No</th>
                            <th>Nama</th>
                            <th>NIK</th>
                            <th>JK</th>
                            <th>Asal</th>
                            <th>Tujuan</th>
                            <th>No HP</th>
                            <th>Tanggal</th>
                            <th>Jam</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php 
                    if (mysqli_num_rows($result) > 0):
                        $no = $offset + 1;
                        while ($row = mysqli_fetch_assoc($result)): 
                    ?>
                    <tr>
                        <td>
                            <input type="checkbox" class="row-checkbox" name="ids[]" value="<?php echo $row['id']; ?>">
                        </td>
                        <td><?php echo $no++; ?></td>
                        <td><strong><?php echo $row['nama']; ?></strong></td>
                        <td><?php echo $row['nik']; ?></td>
                        <td><?php echo $row['jenis_kelamin']; ?></td>
                        <td><?php echo $row['asal']; ?></td>
                        <td><?php echo substr($row['tujuan'], 0, 50) . '...'; ?></td>
                        <td><?php echo $row['no_hp']; ?></td>
                        <td><?php echo tanggal_indonesia($row['tanggal']); ?></td>
                        <td><?php echo date('H:i', strtotime($row['jam'])); ?> WIB</td>
                        <td>
                            <div class="action-buttons">
                                <a href="detail.php?id=<?php echo $row['id']; ?>" class="btn-icon btn-info" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn-icon btn-danger" title="Hapus" onclick="return confirm('Yakin ingin menghapus data ini?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php 
                        endwhile;
                    else: 
                    ?>
                    <tr>
                        <td colspan="11" class="text-center">Tidak ada data</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </form>
        </div>

        <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page-1; ?>&search=<?php echo $search; ?>&filter_date=<?php echo $filter_date; ?>" class="page-link">
                    <i class="fas fa-chevron-left"></i> Prev
                </a>
            <?php endif; ?>

            <?php 
            $start_page = max(1, $page - 2);
            $end_page = min($total_pages, $page + 2);
            
            for ($i = $start_page; $i <= $end_page; $i++): 
            ?>
                <a href="?page=<?php echo $i; ?>&search=<?php echo $search; ?>&filter_date=<?php echo $filter_date; ?>" 
                   class="page-link <?php echo $i == $page ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <a href="?page=<?php echo $page+1; ?>&search=<?php echo $search; ?>&filter_date=<?php echo $filter_date; ?>" class="page-link">
                    Next <i class="fas fa-chevron-right"></i>
                </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Bulk Delete Functionality
const selectAll = document.getElementById('selectAll');
const rowCheckboxes = document.querySelectorAll('.row-checkbox');
const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
const selectedCount = document.getElementById('selectedCount');
const bulkForm = document.getElementById('bulkForm');

function updateBulkDeleteBtn() {
    const checked = document.querySelectorAll('.row-checkbox:checked').length;
    selectedCount.textContent = checked;
    bulkDeleteBtn.style.display = checked > 0 ? 'inline-flex' : 'none';
}

selectAll.addEventListener('change', function() {
    rowCheckboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
    updateBulkDeleteBtn();
});

rowCheckboxes.forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const allChecked = Array.from(rowCheckboxes).every(cb => cb.checked);
        const someChecked = Array.from(rowCheckboxes).some(cb => cb.checked);
        selectAll.checked = allChecked;
        selectAll.indeterminate = someChecked && !allChecked;
        updateBulkDeleteBtn();
    });
});

bulkDeleteBtn.addEventListener('click', function() {
    const checked = document.querySelectorAll('.row-checkbox:checked').length;
    if (confirm(`Yakin ingin menghapus ${checked} data terpilih?`)) {
        bulkForm.submit();
    }
});

function exportExcel() {
    window.location.href = 'export_excel.php<?php echo !empty($search) || !empty($filter_date) ? "?search=$search&filter_date=$filter_date" : ""; ?>';
}
</script>

<style>
.header-actions {
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

.btn-danger {
    background: var(--danger);
    color: var(--white);
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    border: none;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s;
}

.btn-danger:hover {
    background: #dc2626;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(239, 68, 68, 0.3);
}

.form-select {
    padding: 0.75rem 1rem;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    font-size: 1rem;
    background: white;
}

body.dark-mode .form-select {
    background: #2d2d2d;
    border-color: #3d3d3d;
    color: #e5e7eb;
}

input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
}

@media (max-width: 768px) {
    .header-actions {
        flex-direction: column;
        width: 100%;
    }
    
    .header-actions button {
        width: 100%;
        justify-content: center;
    }
}
</style>

<?php include 'includes/footer.php'; ?>