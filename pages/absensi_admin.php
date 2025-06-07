<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 1. Mulai dengan require_once untuk semua dependensi
require_once __DIR__ . '/../auth/config/session.php';
require_once __DIR__ . '/../auth/config/database.php';
require_once __DIR__ . '/../auth/middleware/auth.php';
require_once __DIR__ . '/../auth/middleware/role.php'; // <-- PASTIKAN BARIS INI ADA DAN PATH-NYA BENAR
require_once __DIR__ . '/../App/Services/AbsensiAdminService.php';

// 2. Setelah semua require, baru mulai sesi
Session::start();

// 3. Setelah sesi dimulai, baru jalankan middleware
AuthMiddleware::requireLogin();
RoleMiddleware::requireRole(['dosen', 'admin']); // Baris ini sekarang seharusnya bekerja

$nama_lengkap_login = Session::get('nama_lengkap'); // Variabel ini yang akan kita gunakan

// Log file path
$logFile = __DIR__ . '/../logs/app_debug.log';

// Function to log messages
function log_message($message)
{
    global $logFile;
    file_put_contents($logFile, date('Y-m-d H:i:s') . ' - ' . $message . PHP_EOL, FILE_APPEND);
}

log_message("Memulai pemuatan halaman absensi_admin.php");

// Pastikan parameter id_jadwal ada di URL
if (!isset($_GET['id_jadwal'])) {
    log_message("Error: Parameter id_jadwal tidak ditemukan di URL.");
    // Redirect atau tampilkan pesan error
    echo "Error: ID Jadwal tidak ditemukan.";
    exit();
}

$idJadwal = $_GET['id_jadwal'];
log_message("Parameter id_jadwal diterima: " . $idJadwal);

// Inisialisasi service
$database = new Database();

$pdo = $database->connect();
$absensiAdminService = new App\Services\AbsensiAdminService($pdo);

// Ambil data yang diperlukan
$data = $absensiAdminService->prepareAbsensiPageData($idJadwal, date('Y-m-d'));

if (isset($data['error'])) {
    log_message("Error saat mengambil data absensi admin: " . $data['error']);
    echo "Error: " . $data['error'];
    exit();
}

// Ekstrak data ke variabel untuk kemudahan di view
// PERBAIKAN: Ganti 'jadwal_detail' menjadi 'detail_jadwal'
$detail_jadwal = $data['detail_jadwal'];
$daftar_mahasiswa = $data['daftar_mahasiswa'];
// $tanggal_hari_ini = $data['tanggal_hari_ini']; // Removed as it's not needed and caused warnings.

log_message("Data absensi admin berhasil diambil untuk id_jadwal: " . $idJadwal);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Presensi Mahasiswa</title>
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/header_admin.css">
    <link rel="stylesheet" href="../assets/css/absensi_admin.css">
    <link rel="stylesheet" href="../assets/css/absensi_admin.css">
</head>
<body>
    <div class="header-container">
        <header class="header">
            <div class="header-left">
              <img src="../assets/images/knowledge.png" alt="Logo" class="logo">
              Presensi Mahasiswa
            </div>
          
            <div id="main-navigation" class="header-center">
              <div id="home-menu-item" class="menu-item">
                <img style="filter: invert();" src="../assets/images/home.png" alt="Beranda" class="menu-icon">
                <a style="color: white; text-decoration: none;" href="dashboard_admin.php">
                <span>Beranda</span>
                </a>
              </div>
              <div class="menu-item">
                <img style="filter: invert();" src="../assets/images/logout.png" alt="Keluar" class="menu-icon">
                <a style="color: white; text-decoration: none;" href="../auth/handlers/logout.php">
                <span>Keluar</span>
                </a>
              </div>
            </div>
            <div class="header-right">
              <span class="user-name"><?php echo htmlspecialchars($nama_lengkap_login ?? 'Nama Pengguna'); ?></span>
              <img src="../assets/images/user.png" alt="Foto Profil" class="user-photo">
            </div>
          </header>   
    </div>
    <main class="main-content">
        <div class="container">
            <h2 class="page-title">Informasi Absensi Mahasiswa</h2>
            <div class="horizontal-line"></div>
            
            <div id="student-search-section" class="search-section">
                <label id="search-label" for="search" class="search-label">Nama mahasiswa</label>
                <div id="search-container" class="search-container">
                    <input type="text" id="search" class="search-input" placeholder="Cari Berdasarkan nama atau NIM Mahasiswa">
                    <button class="search-button">
                        <img src="../assets/images/search-interface-symbol.png" alt="" class="image-button">
                    </button>
                </div>
            </div>
            
            <div class="attendance-container">
            <h3 class="section-title"><?php echo htmlspecialchars($detail_jadwal['nama_matkul']); ?></h3>
            <div id="course-info-section" class="course-info">
                <div class="info-row">
                    <span class="info-label">Prodi</span>
                    <span class="info-value">: <?php echo htmlspecialchars($detail_jadwal['nama_prodi']); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Kelas</span>
                    <span class="info-value">: <?php echo htmlspecialchars($detail_jadwal['nama_kelas']); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tahun</span>
                    <span class="info-value">: <?php echo htmlspecialchars($detail_jadwal['tahun_ajaran']); ?></span>
                </div>
            </div>
                <form id="form-absensi" method="POST" action="../App/Api/submit_absensi_dosen.php">
                    <input type="hidden" name="id_jadwal" value="<?php echo htmlspecialchars($detail_jadwal['id_jadwal']); ?>">
                    <input type="hidden" name="tanggal_absensi" value="<?php echo date('Y-m-d'); ?>">

                    <table class="attendance-table">
                        <thead>
                            <tr>
                                <th>NIM</th>
                                <th>Nama Mahasiswa</th>
                                <th>Hadir</th>
                                <th class="izin">Izin</th>
                                <th>Sakit</th>
                                <th>Alpha</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($daftar_mahasiswa)): ?>
                                <?php foreach ($daftar_mahasiswa as $mahasiswa): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($mahasiswa['nim']); ?></td>
                                        <td><?php echo htmlspecialchars($mahasiswa['nama_lengkap']); ?></td>

                                        <?php
                                            $statuses = ['Hadir', 'Izin', 'Sakit', 'Alpha'];
                                            $images = ['absenteeism.png', 'absent.png', 'patient.png', 'alpha.png'];
                                        ?>

                                        <?php foreach ($statuses as $index => $status): ?>
                                            <td class="action-cell">
                                                <label class="action-button">
                                                    <input type="radio"
                                                           name="status[<?php echo htmlspecialchars($mahasiswa['nim']); ?>]"
                                                           value="<?php echo $status; ?>"
                                                           <?php echo (($mahasiswa['status_kehadiran'] ?? null) === $status) ? 'checked' : ''; ?>>
                                                    <img src="../assets/images/<?php echo $images[$index]; ?>" alt="<?php echo $status; ?>" class="image">
                                                    <span class="deskripsi"><?php echo $status; ?></span>
                                                </label>
                                            </td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" style="text-align: center;">Tidak ada mahasiswa terdaftar di kelas ini.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>

                    <div class="button-container">
                        <button type="button" class="btn cancel-btn">Batal</button>
                        <button type="submit" class="btn save-btn">Simpan Absensi</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
<script src="../assets/js/absen_functions.js"></script>
</body>
</html>