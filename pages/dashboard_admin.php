<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
$custom_log_file_dashboard = __DIR__ . '/../logs/app_debug.log';
function custom_error_log_dashboard($message, $log_file) {
    $timestamp = date("Y-m-d H:i:s");
    error_log("[" . $timestamp . "] DASHBOARD_ADMIN: " . $message . PHP_EOL, 3, $log_file);
}
require_once __DIR__ . '/../auth/config/session.php';
require_once __DIR__ . '/../auth/config/database.php';
require_once __DIR__ . '/../auth/middleware/auth.php';
require_once __DIR__ . '/../auth/middleware/role.php';
require_once __DIR__ . '/../app/Services/AdminDashboardService.php';

Session::start();
AuthMiddleware::requireLogin();
RoleMiddleware::requireRole(['dosen', 'admin']);

$nidn_login = Session::get('nidn');
$nama_lengkap_login = Session::get('nama_lengkap');
$role_login = Session::get('role');
$user_id_session = Session::get('user_id');

if (empty($nidn_login) && $role_login === 'dosen') {
    error_log("FATAL: NIDN Dosen WAJIB tidak ditemukan. User ID: " . ($user_id_session ?? 'N/A'));
    die("Error Kritis: NIDN Dosen tidak ada di sesi. Cek log server.");
}

$db_instance = new Database();
$pdo_connection = $db_instance->connect();

if (!$pdo_connection) {
    error_log("ERROR FATAL di dashboard_admin.php: Gagal melakukan koneksi ke database.");
    die("Tidak dapat terhubung ke database. Silakan hubungi administrator sistem atau coba lagi nanti.");
}

$adminService = new \App\Services\AdminDashboardService($pdo_connection);
$dashboardData = $adminService->prepareDashboardData($nidn_login, $nama_lengkap_login);

$nama_dosen_header = $dashboardData['nama_dosen_header'] ?? 'Nama Dosen';
$tanggal_hari_ini_display = $dashboardData['tanggal_hari_ini_display'] ?? date('d F Y');
$jadwal_dosen_hari_ini = $dashboardData['jadwal_dosen_hari_ini'] ?? [];
custom_error_log_dashboard("Jadwal Dosen Hari Ini (Final di dashboard_admin.php): " . json_encode($jadwal_dosen_hari_ini), $custom_log_file_dashboard);

$kalender_mingguan = $dashboardData['kalender_mingguan'] ?? [];

// Optional: Jika Anda perlu mapping hari dalam bahasa Indonesia
$hari_map_indo = [
    'Monday'    => 'Senin',
    'Tuesday'   => 'Selasa',
    'Wednesday' => 'Rabu',
    'Thursday'  => 'Kamis',
    'Friday'    => 'Jumat',
    'Saturday'  => 'Sabtu',
    'Sunday'    => 'Minggu'
];

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Presensi Mahasiswa</title>
    <link rel="stylesheet" href="../assets/css/main_admin.css">
    <link rel="stylesheet" href="../assets/css/kelas_admin.css">
    <link rel="stylesheet" href="../assets/css/jadwal_admin.css">
    <link rel="stylesheet" href="../assets/css/header_admin.css">
    <link rel="stylesheet" href="../assets/css/absen_admin.css">
</head>
<body data-nidn-dosen="<?php echo htmlspecialchars($nidn_login ?? ''); ?>">
    <div class="header-container">
      <header class="header">
          <div class="header-left">
            <img src="../assets/images/knowledge.png" alt="Logo" class="logo">
            Presensi Mahasiswa
          </div>
        
          <div class="header-center">
            <div class="menu-item">
              <img style="filter: invert();" src="../assets/images/home.png" alt="Beranda" class="menu-icon">
              <span style="text-decoration: none;">Beranda</span>
            </div>
            <div class="menu-item">
              <img style="filter: invert();" src="../assets/images/logout.png" alt="Keluar" class="menu-icon">
              <a style="color: white;" href="../auth/handlers/logout.php">
              <span style="text-decoration: none;">Keluar</span>
              </a>
            </div>
          </div>
          <div class="header-right"> 
            <span class="user-name"><?php echo htmlspecialchars($nama_dosen_header); ?></span> <img style="filter: invert();" src="../assets/images/user.png" alt="Foto Profil" class="user-photo"> 
          </div>
        </header>   
    </div>
    <div class="content-container">
        <div class="left-container">
            <div class="jadwal-container">
                <div class="jadwal-header">
                    <h2>Jadwal Minggu Ini</h2>
                    <span class="tanggal-hari">DD - MM - YYYY</span>
                  </div>
                  <div class="hari-container">
                    <?php foreach ($kalender_mingguan as $hari_item): ?>
                        <div id="day-<?php echo strtolower(htmlspecialchars($hari_item['nama_pendek'])); ?>" class="day-item <?php echo $hari_item['is_hari_ini'] ? 'active-day' : ''; ?>" data-hari="<?php echo htmlspecialchars($hari_item['nama_panjang_indo']); ?>" data-tanggal-iso="<?php echo htmlspecialchars($hari_item['full_date_iso']); ?>">
                            <span class="hari"><?php echo htmlspecialchars($hari_item['nama_pendek']); ?></span>

                            <div class="hari-text-line" style="<?php echo ($hari_item['is_hari_ini']) ? 'display: block;' : 'display: none;'; ?>"></div>

                            <span class="tanggal"><?php echo htmlspecialchars($hari_item['tanggal_angka']); ?></span>
                        </div>
                    <?php endforeach; ?>
                  </div>
                  <hr>               
                  <div class="info-kelas">
                                <p class="info-title">Informasi Kelas Hari Ini :</p>
                                 <?php if (!empty($jadwal_dosen_hari_ini)):
                                     foreach ($jadwal_dosen_hari_ini as $jadwal):
                                 ?>
                                     <div class="info-grid">
                                         <div class="info-item">
                                             <img src="../assets/images/teachings.png" alt="icon" class="info-icon">
                                             <span><?= htmlspecialchars($jadwal['nama_matkul']) ?></span>
                                         </div>
                                         <div class="info-item">
                                             <img src="../assets/images/clock.png" alt="icon" class="info-icon">
                                             <span><?= htmlspecialchars(substr($jadwal['jam_mulai'], 0, 5)) ?> - <?= htmlspecialchars(substr($jadwal['jam_selesai'], 0, 5)) ?></span>
                                         </div>
                                         <div class="info-item">
                                             <img src="../assets/images/classroom.png" alt="icon" class="info-icon">
                                             <span><?= htmlspecialchars($jadwal['ruangan']) ?></span>
                                         </div>
                                         <div class="info-item">
                                             <img src="../assets/images/conference.png" alt="icon" class="info-icon">
                                             <span><?= htmlspecialchars($jadwal['nama_kelas']) ?></span>
                                         </div>
                                     </div>
                                 <?php
                                     endforeach;
                                 else:
                                 ?>
                                     <p>Tidak ada kelas hari ini.</p>
                                 <?php endif; ?>
                            </div>                                    
            </div>
            <div class="absen-container">
                <div class="absen-header">
                    <h2>Absen Mahasiswa</h2>
                    <span class="absen-subtitle">
                                <?php if (!empty($jadwal_dosen_hari_ini)):
                                    echo htmlspecialchars($jadwal_dosen_hari_ini[0]['nama_matkul']);
                                else:
                                    echo 'Tidak ada kelas';
                                endif; ?>
                            </span>
                </div>
                <div class="absen-text-line"></div>
                <div class="absen-status">
                    <div class="absen-icon">
                        <?php
                            // Ambil ID jadwal pertama dari jadwal hari ini, jika ada
                            $id_jadwal_hari_ini = !empty($jadwal_dosen_hari_ini) ? $jadwal_dosen_hari_ini[0]['id_jadwal'] : null;
                            // Buat link hanya jika ada id_jadwal
                            $link_absensi = $id_jadwal_hari_ini ? "absensi_admin.php?id_jadwal=" . htmlspecialchars($id_jadwal_hari_ini) : "#";
                        ?>
                        <a href="<?php echo $link_absensi; ?>"
                        class="<?php echo !$id_jadwal_hari_ini ? 'disabled-link' : ''; ?>"> <img src="../assets/images/lesson.png" alt="Status Icon">
                        </a>
                    </div>
                </div>
            </div>
        </div>  
        <div class="kelas-container-main">
            <div class="container-isi">
                <h3>Informasi Kelas</h3>
                <div class="divider"></div>
                <span class="nama">Nama Kelas</span>
                <br>
                <div class="search-container">
                    <input type="text" class="search-bar" placeholder="Cari berdasarkan nama mata kuliah">
                    <img src="../assets/images/search-interface-symbol.png" alt="Search Icon" class="search-icon">
                </div>
            </div>
            <div class="kelas-container">
                <?php if (!empty($dashboardData['semua_jadwal_dosen'])): ?> 
                     <?php foreach ($dashboardData['semua_jadwal_dosen'] as $jadwal_item): ?> 
                         <div class="kelas-card"> 
                             <div class="kelas-header"><?php echo htmlspecialchars($jadwal_item['nama_matkul']); ?></div> 
                             <div class="kelas-subheader"><?php echo htmlspecialchars($jadwal_item['nama_prodi']); ?></div> 
                             <div class="kelas-divider"></div> 
                             <div class="kelas-info"> 
                                 <div class="kelas-waktu"> 
                                     <img src="../assets/images/clock.png" class="kelas-icon" /> 
                                     <span> 
                                         <?php 
                                         // Asumsi $jadwal_item['hari'] sudah dalam Bahasa Indonesia dari JadwalModel::getAllJadwalDosen 
                                         echo htmlspecialchars($jadwal_item['hari']); 
                                         ?>, 
                                         <?php echo htmlspecialchars(substr($jadwal_item['jam_mulai'], 0, 5)); ?> - <?php echo htmlspecialchars(substr($jadwal_item['jam_selesai'], 0, 5)); ?> 
                                     </span> 
                                 </div> 
                                 <div class="kelas-dosen"> <img src="../assets/images/conference.png" class="kelas-icon" /> 
                                     <span><?php echo htmlspecialchars($jadwal_item['nama_kelas']); ?></span> 
                                 </div> 
                                 <div class="kelas-ruang"> 
                                     <img src="../assets/images/classroom.png" class="kelas-icon" /> 
                                     <span><?php echo htmlspecialchars($jadwal_item['ruangan']); ?></span> 
                                 </div> 
                             </div> 
                         </div> 
                     <?php endforeach; ?> 
                 <?php else: ?> 
                     <p style="text-align: center; grid-column: 1 / -1;">Tidak ada data jadwal mengajar yang ditemukan untuk dosen ini.</p> 
                 <?php endif; ?>
                 <p id="search-no-results" class="search-no-results-message" style="text-align: center; grid-column: 1 / -1; padding-top:20px; display: none;">
                     Tidak ada jadwal yang cocok dengan pencarian Anda.
                 </p>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchBar = document.querySelector('.search-bar');
            const kelasCards = document.querySelectorAll('.kelas-card');
            const noResultsMessage = document.getElementById('search-no-results');

            searchBar.addEventListener('keyup', function() {
                const searchTerm = searchBar.value.toLowerCase();
                let visibleCardsCount = 0;

                kelasCards.forEach(card => {
                    const namaMatkul = card.querySelector('.kelas-header').textContent.toLowerCase();
                    const namaKelas = card.querySelector('.kelas-dosen span').textContent.toLowerCase();
                    const ruangan = card.querySelector('.kelas-ruang span').textContent.toLowerCase();

                    if (namaMatkul.includes(searchTerm) || namaKelas.includes(searchTerm) || ruangan.includes(searchTerm)) {
                        card.style.display = 'block'; // Show the card
                        visibleCardsCount++;
                    } else {
                        card.style.display = 'none'; // Hide the card
                    }
                });

                if (visibleCardsCount === 0 && searchTerm !== '') {
                    noResultsMessage.style.display = 'block';
                } else {
                    noResultsMessage.style.display = 'none';
                }
            });
        });
    </script>
    <script src="../assets/js/dashboard_admin_calendar.js"></script>
</body>
</html>