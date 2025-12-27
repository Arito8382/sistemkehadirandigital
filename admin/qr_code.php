<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit();
}

// URL form kehadiran
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$base_path = dirname(dirname($_SERVER['PHP_SELF']));
$form_url = $protocol . "://" . $host . $base_path . "/form_kehadiran.php";

include 'includes/header.php';
?>

<div class="dashboard-content">
    <h2>QR Code Scanner</h2>

    <div class="rekap-grid">
        <div class="rekap-card">
            <h3>
                <i class="fas fa-qrcode" style="background: rgba(59, 130, 246, 0.1); color: var(--primary);"></i>
                QR Code Form Kehadiran
            </h3>
            <div style="text-align: center; padding: 2rem;">
                <div id="qrcode" style="display: inline-block;"></div>
                <p style="margin-top: 1rem; color: var(--gray);">Scan QR Code untuk akses form kehadiran</p>
                <p style="font-size: 0.875rem; margin-top: 0.5rem; word-break: break-all;">
                    <strong>URL:</strong> <?php echo $form_url; ?>
                </p>
                <button onclick="downloadQR()" class="btn-primary" style="margin-top: 1rem; width: 100%;">
                    <i class="fas fa-download"></i> Download QR Code
                </button>
                <button onclick="printQR()" class="btn-secondary" style="margin-top: 0.5rem; width: 100%;">
                    <i class="fas fa-print"></i> Print QR Code
                </button>
            </div>
        </div>

        <div class="rekap-card">
            <h3>
                <i class="fas fa-info-circle" style="background: rgba(16, 185, 129, 0.1); color: var(--success);"></i>
                Cara Penggunaan
            </h3>
            <div style="padding: 1rem;">
                <ol style="padding-left: 1.5rem; line-height: 2;">
                    <li>Download atau print QR Code</li>
                    <li>Tempel di lokasi strategis (pintu masuk, resepsionis)</li>
                    <li>Pengunjung scan QR Code dengan HP</li>
                    <li>Otomatis diarahkan ke form kehadiran</li>
                    <li>Data tersimpan ke sistem</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="table-card" id="printArea">
        <div style="padding: 3rem; text-align: center;">
            <h2 style="margin-bottom: 1rem;">SISTEM KEHADIRAN DIGITAL</h2>
            <p style="font-size: 1.25rem; margin-bottom: 2rem;">Scan QR Code untuk Mengisi Form Kehadiran</p>
            <div id="qrcodePrint" style="display: inline-block;"></div>
            <p style="margin-top: 2rem; font-size: 1.125rem;">Gedung Perkantoran Utama</p>
            <p style="color: var(--gray); margin-top: 0.5rem;">Gunakan kamera HP Anda untuk scan QR Code</p>
        </div>
    </div>
</div>

<!-- QR Code Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<script>
// Generate QR Code
const formUrl = '<?php echo $form_url; ?>';

// QR Code untuk preview
new QRCode(document.getElementById("qrcode"), {
    text: formUrl,
    width: 256,
    height: 256,
    colorDark: "#000000",
    colorLight: "#ffffff",
    correctLevel: QRCode.CorrectLevel.H
});

// QR Code untuk print
new QRCode(document.getElementById("qrcodePrint"), {
    text: formUrl,
    width: 350,
    height: 350,
    colorDark: "#000000",
    colorLight: "#ffffff",
    correctLevel: QRCode.CorrectLevel.H
});

// Download QR Code
function downloadQR() {
    const canvas = document.querySelector('#qrcode canvas');
    const link = document.createElement('a');
    link.download = 'QR_Code_Kehadiran.png';
    link.href = canvas.toDataURL();
    link.click();
}

// Print QR Code
function printQR() {
    const printContent = document.getElementById('printArea').innerHTML;
    const originalContent = document.body.innerHTML;
    
    document.body.innerHTML = printContent;
    window.print();
    document.body.innerHTML = originalContent;
    location.reload();
}
</script>

<style>
@media print {
    .sidebar, .topbar, h2:first-of-type, .rekap-grid {
        display: none !important;
    }
    .main-content {
        margin-left: 0 !important;
    }
    #printArea {
        box-shadow: none;
        border: none;
    }
}

ol {
    color: var(--dark);
}

body.dark-mode ol {
    color: #e5e7eb;
}
</style>

<?php include 'includes/footer.php'; ?>