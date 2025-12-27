<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit();
}
require_once '../config/koneksi.php';

include 'includes/header.php';
?>

<div class="dashboard-content">
    <h2>Pengaturan Sistem</h2>

    <div class="rekap-grid">
        <div class="rekap-card">
            <h3>
                <i class="fas fa-moon" style="background: rgba(79, 70, 229, 0.1); color: #4f46e5;"></i>
                Mode Tampilan
            </h3>
            <div class="setting-item">
                <div class="setting-info">
                    <strong>Dark Mode</strong>
                    <p>Aktifkan mode gelap untuk kenyamanan mata</p>
                </div>
                <label class="switch">
                    <input type="checkbox" id="darkModeToggle">
                    <span class="slider"></span>
                </label>
            </div>
        </div>

        <div class="rekap-card">
            <h3>
                <i class="fas fa-bell" style="background: rgba(245, 158, 11, 0.1); color: var(--warning);"></i>
                Notifikasi
            </h3>
            <div class="setting-item">
                <div class="setting-info">
                    <strong>Sound Notifikasi</strong>
                    <p>Aktifkan suara untuk notifikasi baru</p>
                </div>
                <label class="switch">
                    <input type="checkbox" id="soundToggle" checked>
                    <span class="slider"></span>
                </label>
            </div>
        </div>

        <div class="rekap-card">
            <h3>
                <i class="fas fa-table" style="background: rgba(16, 185, 129, 0.1); color: var(--success);"></i>
                Data Per Halaman
            </h3>
            <form class="rekap-form" id="paginationForm">
                <select id="rowsPerPage" class="form-select">
                    <option value="10">10 baris</option>
                    <option value="25">25 baris</option>
                    <option value="50">50 baris</option>
                    <option value="100">100 baris</option>
                </select>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save"></i> Simpan
                </button>
            </form>
        </div>

        <div class="rekap-card">
            <h3>
                <i class="fas fa-clock" style="background: rgba(239, 68, 68, 0.1); color: var(--danger);"></i>
                Auto Refresh
            </h3>
            <div class="setting-item">
                <div class="setting-info">
                    <strong>Refresh Dashboard</strong>
                    <p>Refresh otomatis setiap 60 detik</p>
                </div>
                <label class="switch">
                    <input type="checkbox" id="autoRefreshToggle">
                    <span class="slider"></span>
                </label>
            </div>
        </div>
    </div>

    <div class="table-card" style="margin-top: 2rem;">
        <div style="padding: 2rem;">
            <h3 style="margin-bottom: 1.5rem;">Info Sistem</h3>
            <div class="detail-grid">
                <div class="detail-item">
                    <div class="detail-label">Versi Sistem</div>
                    <div class="detail-value">1.0.0</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">PHP Version</div>
                    <div class="detail-value"><?php echo phpversion(); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Database</div>
                    <div class="detail-value">MySQL</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Server</div>
                    <div class="detail-value"><?php echo $_SERVER['SERVER_SOFTWARE']; ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.setting-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    background: #f9fafb;
    border-radius: 10px;
}

.setting-info strong {
    display: block;
    margin-bottom: 0.25rem;
    color: var(--dark);
}

.setting-info p {
    color: var(--gray);
    font-size: 0.875rem;
    margin: 0;
}

.switch {
    position: relative;
    display: inline-block;
    width: 60px;
    height: 34px;
}

.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .4s;
    border-radius: 34px;
}

.slider:before {
    position: absolute;
    content: "";
    height: 26px;
    width: 26px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
}

input:checked + .slider {
    background-color: var(--primary);
}

input:checked + .slider:before {
    transform: translateX(26px);
}

.form-select {
    width: 100%;
    padding: 0.875rem 1rem;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    font-size: 1rem;
}
</style>

<script>
// Dark Mode Toggle
const darkModeToggle = document.getElementById('darkModeToggle');
const body = document.body;

// Load saved preference
if (localStorage.getItem('darkMode') === 'enabled') {
    body.classList.add('dark-mode');
    darkModeToggle.checked = true;
}

darkModeToggle.addEventListener('change', function() {
    if (this.checked) {
        body.classList.add('dark-mode');
        localStorage.setItem('darkMode', 'enabled');
    } else {
        body.classList.remove('dark-mode');
        localStorage.setItem('darkMode', 'disabled');
    }
});

// Sound Toggle
const soundToggle = document.getElementById('soundToggle');
if (localStorage.getItem('soundEnabled') === 'false') {
    soundToggle.checked = false;
}

soundToggle.addEventListener('change', function() {
    localStorage.setItem('soundEnabled', this.checked);
});

// Auto Refresh
const autoRefreshToggle = document.getElementById('autoRefreshToggle');
if (localStorage.getItem('autoRefresh') === 'enabled') {
    autoRefreshToggle.checked = true;
}

autoRefreshToggle.addEventListener('change', function() {
    if (this.checked) {
        localStorage.setItem('autoRefresh', 'enabled');
        alert('Auto refresh diaktifkan. Dashboard akan refresh setiap 60 detik.');
    } else {
        localStorage.setItem('autoRefresh', 'disabled');
    }
});

// Rows Per Page
const rowsPerPage = document.getElementById('rowsPerPage');
if (localStorage.getItem('rowsPerPage')) {
    rowsPerPage.value = localStorage.getItem('rowsPerPage');
}

document.getElementById('paginationForm').addEventListener('submit', function(e) {
    e.preventDefault();
    localStorage.setItem('rowsPerPage', rowsPerPage.value);
    alert('Pengaturan disimpan! Akan diterapkan di halaman data kehadiran.');
});
</script>

<?php include 'includes/footer.php'; ?>