<?php 
error_reporting(E_ALL); 
ini_set('display_errors', 0); // Jangan tampilkan error PHP langsung di HTML production, tapi OK untuk debug jika perlu 
ini_set('log_errors', 1);    // Pastikan error dicatat 

// Definisikan path ke file log kustom 
$custom_log_file_user_dash = __DIR__ . '/../logs/app_debug.log'; 

// Fungsi helper untuk logging kustom 
function custom_log_user_dashboard($message, $log_file) { 
    $timestamp = date("Y-m-d H:i:s"); 
    error_log("[" . $timestamp . "] USER_DASHBOARD_PAGE: " . $message . PHP_EOL, 3, $log_file); 
} 

custom_log_user_dashboard("--- (Sesi DU-2.2a) Eksekusi dashboard_user.php Dimulai ---", $custom_log_file_user_dash); 

// 1. Sertakan file-file yang diperlukan 
custom_log_user_dashboard("Tahap 1: Melakukan require_once...", $custom_log_file_user_dash); 
require_once __DIR__ . '/../auth/config/session.php'; 
require_once __DIR__ . '/../auth/config/database.php'; 
require_once __DIR__ . '/../auth/middleware/auth.php'; 
require_once __DIR__ . '/../auth/middleware/role.php'; 
require_once __DIR__ . '/../App/Services/UserDashboardService.php'; // Service baru 
// JadwalModel.php akan di-require_once di dalam UserDashboardService.php 
custom_log_user_dashboard("Semua require_once awal berhasil.", $custom_log_file_user_dash); 

// 2. Mulai Sesi & Terapkan Middleware 
custom_log_user_dashboard("Tahap 2: Memulai Sesi & Middleware...", $custom_log_file_user_dash); 
Session::start(); 
AuthMiddleware::requireLogin(); 
RoleMiddleware::requireRole('mahasiswa'); // Pastikan hanya mahasiswa 
custom_log_user_dashboard("Sesi dimulai dan Middleware lolos.", $custom_log_file_user_dash); 

// 3. Ambil Informasi Pengguna (Mahasiswa) dari Sesi 
custom_log_user_dashboard("Tahap 3: Mengambil data sesi mahasiswa...", $custom_log_file_user_dash); 
$nim_login = Session::get('nim'); 
$nama_lengkap_login = Session::get('nama_lengkap'); 
$user_id_session = Session::get('user_id'); // Untuk logging tambahan jika perlu 

if (empty($nim_login)) { 
    $log_msg = "FATAL: NIM Mahasiswa tidak ditemukan di sesi. User ID: " . ($user_id_session ?? 'N/A') . ". Mengarahkan ke logout."; 
    custom_log_user_dashboard($log_msg, $custom_log_file_user_dash); 
    // Redirect atau tampilkan error jika NIM krusial dan tidak ada 
    header('Location: ../auth/handlers/logout.php?error=nim_missing_for_mahasiswa_dashboard'); 
    exit(); 
} 
custom_log_user_dashboard("Data Sesi Berhasil Diambil: NIM='{$nim_login}', Nama='{$nama_lengkap_login}'", $custom_log_file_user_dash); 

// 4. Inisialisasi Koneksi Database 
custom_log_user_dashboard("Tahap 4: Inisialisasi Koneksi Database...", $custom_log_file_user_dash); 
$db_instance = new Database(); 
$pdo_connection = $db_instance->connect(); 

if (!$pdo_connection) { 
    custom_log_user_dashboard("FATAL: Gagal koneksi ke database.", $custom_log_file_user_dash); 
    // die() akan menghentikan eksekusi dan mungkin tidak ideal jika file ini di-include. 
    // Pertimbangkan mekanisme error handling yang lebih baik untuk produksi. 
    // Untuk sekarang, ini akan mencatat error dan menghentikan. 
    throw new \RuntimeException("Koneksi ke database gagal untuk dashboard pengguna."); 
} 
custom_log_user_dashboard("Koneksi DB berhasil.", $custom_log_file_user_dash); 

// 5. Inisialisasi Service dan Panggil Metode untuk Mendapatkan Data Dasbor 
$dashboardData = []; // Inisialisasi default 
try { 
    custom_log_user_dashboard("Tahap 5: Inisialisasi UserDashboardService...", $custom_log_file_user_dash); 
    $userDashboardService = new \App\Services\UserDashboardService($pdo_connection); 
    custom_log_user_dashboard("UserDashboardService diinstansiasi. Memanggil prepareUserDashboardData...", $custom_log_file_user_dash); 
    
    $dashboardData = $userDashboardService->prepareUserDashboardData($nim_login, $nama_lengkap_login); 
    custom_log_user_dashboard("prepareUserDashboardData() berhasil dipanggil.", $custom_log_file_user_dash); 

} catch (Throwable $e) { // Throwable menangkap Error dan Exception 
    custom_log_user_dashboard("PHP Throwable saat inisialisasi/penggunaan UserDashboardService (DU-2.2a): " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine() . "\nTrace: " . $e->getTraceAsString(), $custom_log_file_user_dash); 
    // Untuk debugging, tampilkan error jika display_errors On, jika tidak, catat dan berikan pesan umum 
    if (ini_get('display_errors')) { 
        echo "Error saat memuat data dasbor: " . htmlspecialchars($e->getMessage()); 
    } else { 
        echo "Terjadi kesalahan pada server. Silakan coba lagi nanti."; 
    } 
    // Kita tetap ingin menginisialisasi variabel agar HTML di bawah tidak error 
    // dan mungkin bisa keluar dengan die() setelah ini jika error dianggap fatal untuk tampilan. 
} 

// 6. Ekstrak Variabel untuk View (dengan fallback jika $dashboardData tidak terisi karena error) 
custom_log_user_dashboard("Tahap 6: Mengekstrak variabel untuk view...", $custom_log_file_user_dash); 
$nama_mahasiswa_header = htmlspecialchars($dashboardData['nama_mahasiswa_header'] ?? ($nama_lengkap_login ?: 'Mahasiswa')); 
$tanggal_hari_ini_display = htmlspecialchars($dashboardData['tanggal_hari_ini_display'] ?? date('d F Y')); 
$kalender_mingguan = $dashboardData['kalender_mingguan'] ?? []; 
$jadwal_mahasiswa_hari_ini = $dashboardData['jadwal_mahasiswa_hari_ini'] ?? []; 
$semua_jadwal_mahasiswa = $dashboardData['semua_jadwal_mahasiswa'] ?? []; 

// Logging nilai akhir variabel yang akan digunakan oleh view 
custom_log_user_dashboard("Nama Header Final: '{$nama_mahasiswa_header}'", $custom_log_file_user_dash); 
custom_log_user_dashboard("Tanggal Display Final: '{$tanggal_hari_ini_display}'", $custom_log_file_user_dash); 
custom_log_user_dashboard("Data Kalender Mingguan Final (jumlah item): " . count($kalender_mingguan), $custom_log_file_user_dash); 
if (!empty($kalender_mingguan)) { 
    custom_log_user_dashboard("Detail Kalender Mingguan (sampel item pertama): " . json_encode($kalender_mingguan[0]), $custom_log_file_user_dash); 
} 
custom_log_user_dashboard("Jadwal Hari Ini Final (jumlah item): " . count($jadwal_mahasiswa_hari_ini), $custom_log_file_user_dash); 
custom_log_user_dashboard("Semua Jadwal Mahasiswa Final (jumlah item): " . count($semua_jadwal_mahasiswa), $custom_log_file_user_dash); 

custom_log_user_dashboard("--- (Sesi DU-2.2a) Eksekusi dashboard_user.php Selesai ---", $custom_log_file_user_dash); 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Presensi Mahasiswa</title>
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/kelas.css">
    <link rel="stylesheet" href="../assets/css/jadwal.css">
    <link rel="stylesheet" href="../assets/css/header_admin.css">
    <link rel="stylesheet" href="../assets/css/absen.css">
</head>
<body data-nim-mahasiswa="<?php echo htmlspecialchars($nim_login ?? ''); ?>">
    <div class="header-container">
        <header id="main-header" class="header">
            <div id="header-left" class="header-left">
              <img id="logo" src="../assets/images/knowledge.png" alt="Logo" class="logo">
              <span id="app-title">Presensi Mahasiswa</span>
            </div>
          
            <div id="main-menu" class="header-center">
                <div id="home-menu" class="menu-item">
                    <img style="filter: invert();" src="../assets/images/home.png" alt="Beranda" class="menu-icon">
                    <a style="color: white; text-decoration: none;" href="dashboard_user.php">
                    <span>Beranda</span>
                    </a>
                </div>
                <div id="logout-menu" class="menu-item">
                    <img style="filter: invert();" src="../assets/images/logout.png" alt="Keluar" class="menu-icon">
                    <a style="color: white; text-decoration: none;" href="../auth/handlers/logout.php">
                    <span style="text-decoration: none;">Keluar</span>
                    </a>
                </div>
            </div>
            <div class="header-right">
              <span class="user-name"><?php echo htmlspecialchars($nama_mahasiswa_header); ?></span>
              <img style="filter: invert();" src="../assets/images/user.png" alt="Foto Profil" class="user-photo">
            </div>
          </header>   
    </div>
    <div class="content-container">
        <div class="left-container">
            <div class="jadwal-container">
                <div class="jadwal-header">
                    <h2>Jadwal Minggu Ini</h2>
                    <span class="tanggal-hari"><?php echo htmlspecialchars($tanggal_hari_ini_display); ?></span>
                </div>
                    <div class="hari-container">
                        <?php if (!empty($kalender_mingguan)): ?>
                            <?php foreach ($kalender_mingguan as $hari_item): ?>
                                <div id="day-<?php echo strtolower($hari_item['nama_pendek']); ?>" class="day-item <?php echo $hari_item['is_hari_ini'] ? 'active-day' : ''; ?>" data-tanggal="<?php echo $hari_item['full_date_iso']; ?>" data-hari="<?php echo htmlspecialchars($hari_item['nama_panjang_indo']); ?>">
                                    <span class="hari"><?php echo htmlspecialchars($hari_item['nama_pendek']); ?></span>
                                    <div class="hari-text-line"></div>
                                    <span class="tanggal"><?php echo htmlspecialchars($hari_item['tanggal_angka']); ?></span>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p style="text-align: center; padding-top: 20px;">Tidak ada data kalender mingguan.</p>
                        <?php endif; ?>
                    </div>
                  <hr>
                  <div class="info-kelas" id="info-kelas-container">
                        <?php if (!empty($jadwal_mahasiswa_hari_ini)): ?>
                            <p class="info-title">Informasi Kelas Hari Ini :</p>
                            <?php foreach ($jadwal_mahasiswa_hari_ini as $index => $jadwal_item): ?>
                                <div class="info-grid" data-id-jadwal="<?php echo htmlspecialchars($jadwal_item['id_jadwal']); ?>">
                                    <div class="info-item">
                                        <img src="../assets/images/academic.png" alt="Mata Kuliah" class="info-icon">
                                        <span><?php echo htmlspecialchars($jadwal_item['nama_matkul']); ?></span>
                                    </div>
                                    <div class="info-item">
                                        <img src="../assets/images/clock.png" alt="Jam" class="info-icon">
                                        <span><?php echo htmlspecialchars(substr($jadwal_item['jam_mulai'], 0, 5)); ?> - <?php echo htmlspecialchars(substr($jadwal_item['jam_selesai'], 0, 5)); ?></span>
                                    </div>
                                    <div class="info-item">
                                        <img src="../assets/images/classroom.png" alt="Ruangan" class="info-icon">
                                        <span><?php echo htmlspecialchars($jadwal_item['ruangan']); ?></span>
                                    </div>
                                    <div class="info-item">
                                        <img src="../assets/images/conference.png" alt="Dosen" class="info-icon">
                                        <span><?php echo htmlspecialchars($jadwal_item['nama_dosen']); ?></span>
                                    </div>
                                </div>
                                <?php if ($index < count($jadwal_mahasiswa_hari_ini) - 1): ?><hr><?php endif; ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p style="text-align: center; margin-top: 20px;">Tidak ada jadwal kuliah hari ini.</p>
                        <?php endif; ?>
                    </div>
            </div>
            <div class="container">
                <div class="absen-container">
                    <div class="absen-header">
                        <h2>Status Absensi</h2>
                        <span class="absen-subtitle">
                            <?php
                                if (!empty($jadwal_mahasiswa_hari_ini)) {
                                    echo htmlspecialchars($jadwal_mahasiswa_hari_ini[0]['nama_matkul']);
                                } else {
                                    echo "Pilih jadwal untuk melihat status absensi";
                                }
                            ?>
                        </span>
                    </div>
                    
                    <div class="absen-status">
                        <div class="absen-icon">
                            <img src="../assets/images/presentation.png" alt="Status Icon" id="statusIcon">
                        </div>
                        <p class="absen-text" id="statusText">Kamu Belum Absen</p>
                        <div class="absen-text-line"></div>
                    </div>
                    <div id="beforeAttendance">
                        <div class="absen-action-label">Ajukan Absensi :</div>
                        <div class="absen-button-container">
                            <button class="absen-button absen" onclick="submitUserAbsensi('Hadir')">Absen</button>
                            <button class="absen-button izin" onclick="submitUserAbsensi('Izin')">Izin</button>
                            <button class="absen-button sakit" onclick="submitUserAbsensi('Sakit')">Sakit</button>
                        </div>
                    </div>
                    
                    <!-- Attendance stats (after check-in) -->
                    <div id="afterAttendance" class="hidden">
                        <div class="absen-action-label">Kehadiran :</div>
                        <div class="kehadiran-stats">
                            <div class="kehadiran-item">
                                <div class="kehadiran-icon">
                                    <img class="image" src="../assets/images/absenteeism.png" alt="Absen">
                                </div>
                                <span class="kehadiran-label">Absen</span>
                                <span class="kehadiran-value" id="absenCount">0</span>
                            </div>
                            
                            <div class="kehadiran-item">
                                <div class="kehadiran-icon">
                                    <img class="image" src="../assets/images/absent.png" alt="Izin">
                                </div>
                                <span class="kehadiran-label">Izin</span>
                                <span class="kehadiran-value" id="izinCount">0</span>
                            </div>
                            
                            <div class="kehadiran-item">
                                <div class="kehadiran-icon">
                                    <img class="image" src="../assets/images/patient.png" alt="Sakit">
                                </div>
                                <span class="kehadiran-label">Sakit</span>
                                <span class="kehadiran-value" id="sakitCount">0</span>
                            </div>
                            
                            <div class="kehadiran-item">
                                <div class="kehadiran-icon">
                                    <img class="image" src="../assets/images/alpha.png" alt="Alpha">
                                </div>
                                <span class="kehadiran-label">Alpha</span>
                                <span class="kehadiran-value" id="alphaCount">0</span>
                            </div>
                        </div>
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
                    <input type="text" class="search-bar" placeholder="Cari berdasarkan nama mata kuliah atau dosen">
                    <img src="../assets/images/search-interface-symbol.png" alt="Search Icon" class="search-icon">
                </div>
            </div>
            <div class="kelas-container">
                <?php if (!empty($semua_jadwal_mahasiswa)): ?>
                    <?php foreach ($semua_jadwal_mahasiswa as $jadwal_item_mhs): ?>
                        <div class="kelas-card">
                            <div class="kelas-header"><?php echo htmlspecialchars($jadwal_item_mhs['nama_matkul']); ?></div>
                            <div class="kelas-subheader"><?php echo htmlspecialchars($jadwal_item_mhs['nama_prodi']); // Asumsi nama_prodi ada ?></div>
                            <div class="kelas-divider"></div>
                            <div class="kelas-info">
                                <div class="kelas-waktu">
                                    <img src="../assets/images/clock.png" alt="Jam" class="kelas-icon">
                                    <span>
                                        <?php echo htmlspecialchars($jadwal_item_mhs['hari']); ?>,
                                        <?php echo htmlspecialchars(substr($jadwal_item_mhs['jam_mulai'], 0, 5)); ?> - <?php echo htmlspecialchars(substr($jadwal_item_mhs['jam_selesai'], 0, 5)); ?>
                                    </span>
                                </div>
                                <div class="kelas-dosen">
                                    <img src="../assets/images/conference.png" alt="Dosen" class="kelas-icon">
                                    <span><?php echo htmlspecialchars($jadwal_item_mhs['nama_dosen']); ?></span>
                                </div>
                            </div>
                            <div class="kehadiran">Kehadiran: (Data Menyusul)</div>
                            <div class="progress-bar"><div class="progress" style="width: 0%;"></div></div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="text-align: center; grid-column: 1 / -1; padding-top:20px;">Anda belum terdaftar pada jadwal mata kuliah apapun.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script src="../assets/js/dashboard_user_calendar.js"></script>
    </body>
</html>