<?php
// 1. Sertakan file-file yang diperlukan (Pastikan path relatif ini benar dari direktori 'pages/')
require_once __DIR__ . '/../auth/config/session.php';
require_once __DIR__ . '/../auth/config/database.php'; // Untuk koneksi PDO
require_once __DIR__ . '/../auth/middleware/auth.php';
require_once __DIR__ . '/../auth/middleware/role.php';
// Path ke AdminDashboardService.php yang akan dibuat/digunakan di sesi berikutnya
require_once __DIR__ . '/../app/Services/AdminDashboardService.php';

// 2. Mulai Sesi
Session::start();

// 3. Terapkan Middleware Autentikasi dan Otorisasi Peran
AuthMiddleware::requireLogin(); // Memastikan pengguna sudah login
// Mengizinkan peran 'dosen' atau 'admin' untuk mengakses dasbor ini
RoleMiddleware::requireRole(['dosen']);

// 4. Ambil Informasi Pengguna (Dosen/Admin) dari Sesi
// NIDN HARUS sudah disimpan ke sesi saat proses login dosen/admin (telah dikerjakan di Sesi 3.1)
$nidn_login = Session::get('nidn');
$nama_lengkap_login = Session::get('nama_lengkap');
$role_login = Session::get('role'); // Ambil juga peran untuk referensi atau logika tambahan jika perlu

// 5. Validasi Kritis: Pastikan NIDN ada di sesi untuk dosen/admin
// Jika NIDN kosong dan peran adalah dosen/admin, ini adalah kondisi error.
if (empty($nidn_login) && ($role_login === 'dosen')) {
    error_log("KESALAHAN KRITIS di dashboard_admin.php: NIDN tidak ditemukan di sesi untuk pengguna dengan peran '" . htmlspecialchars($role_login) . "'. User ID: " . htmlspecialchars(Session::get('user_id')));
    // Arahkan ke logout untuk membersihkan sesi dan paksa login ulang,
    // ini mengindikasikan masalah pada Sesi 3.1 (penyimpanan NIDN saat login) atau data pengguna.
    header('Location: ../auth/handlers/logout.php?error=nidn_tidak_tersedia_disesi');
    exit();
}

// 6. Inisialisasi Koneksi Database
$db_instance = new Database();
$pdo_connection = $db_instance->connect();

if (!$pdo_connection) {
    // Jika koneksi gagal, tampilkan pesan error yang layak atau log dan hentikan.
    error_log("ERROR FATAL di dashboard_admin.php: Gagal melakukan koneksi ke database.");
    // Sebaiknya tampilkan halaman error yang lebih ramah pengguna di produksi.
    // Untuk pengembangan, die() bisa digunakan, tapi pertimbangkan untuk production.
    die("Tidak dapat terhubung ke database. Silakan hubungi administrator sistem atau coba lagi nanti.");
}

// Variabel $pdo_connection sekarang siap digunakan oleh AdminDashboardService di sesi berikutnya.
// Variabel $nidn_login dan $nama_lengkap_login juga siap.

// Logging sementara untuk verifikasi (bisa dihapus setelah diverifikasi)
error_log("dashboard_admin.php: Inisialisasi berhasil. NIDN: " . ($nidn_login ?? 'KOSONG') . ", Nama: " . ($nama_lengkap_login ?? 'KOSONG') . ", Role: " . ($role_login ?? 'KOSONG'));
$custom_log_file = __DIR__ . '/../../logs/app_debug.log';

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
<body>
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
              <a style="color: white;" href="login.php">
              <span style="text-decoration: none;">Keluar</span>
              </a>
            </div>
          </div>
        
          <div class="header-right">
            <span class="user-name">Nama Dosen</span>
            <img style="filter: invert();" src="../assets/images/user.png" alt="Foto Profil" class="user-photo">
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
                    <div>
                      <span class="hari">Sen</span>
                      <div class="hari-text-line"></div>
                      <span class="tanggal">01</span>
                    </div>
                    <div>
                      <span class="hari">Sel</span>
                      <span class="tanggal">02</span>
                    </div>
                    <div>
                      <span class="hari">Rab</span>
                      <span class="tanggal">03</span>
                    </div>
                    <div>
                      <span class="hari">Kam</span>
                      <span class="tanggal">04</span>
                    </div>
                    <div>
                      <span class="hari">Jum</span>
                      <span class="tanggal">05</span>
                    </div>
                    <div>
                      <span class="hari">Sab</span>
                      <span class="tanggal">06</span>
                    </div>
                    <div>
                      <span class="hari">Min</span>
                      <span class="tanggal">07</span>
                    </div>
                  </div>
                  <hr>               
                  <div class="info-kelas">
                    <p class="info-title">Informasi Kelas Hari Ini :</p>
                  
                    <div class="info-grid">
                      <div class="info-item">
                        <img src="../assets/images/teachings.png" alt="icon" class="info-icon">
                        <span>Praktik Pemrograman<br>berbasis web</span>
                      </div>
                      <div class="info-item">
                        <img src="../assets/images/clock.png" alt="icon" class="info-icon">
                        <span>HH-MM - HH-MM</span>
                      </div>
                      <div class="info-item">
                        <img src="../assets/images/classroom.png" alt="icon" class="info-icon">
                        <span>Laboraturium</span>
                      </div>
                      <div class="info-item">
                        <img src="../assets/images/conference.png" alt="icon" class="info-icon">
                        <span>GKelas 2B</span>
                      </div>
                    </div>
                  </div>                                    
            </div>
            <div class="absen-container">
                <div class="absen-header">
                    <h2>Absen Mahasiswa</h2>
                    <span class="absen-subtitle">Praktik Pemrograman berbasis web</span>
                </div>
                <div class="absen-text-line"></div>

                <div class="absen-status">
                    <div class="absen-icon">
                        <a href="absensi_admin.php">
                        <img src="../assets/images/lesson.png" alt="Status Icon">
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
                    <input type="text" class="search-bar" placeholder="Cari Berdasarkan nama kelas atau dosen">
                    <img src="../assets/images/search-interface-symbol.png" alt="Search Icon" class="search-icon">
                </div>
            </div>
            <div class="kelas-container">
                <div class="kelas-card">
                    <div class="kelas-header">Mata Kuliah 1</div>
                    <div class="kelas-subheader">Prodi Mahasiswa</div>
                    <div class="kelas-divider"></div>
                    <div class="kelas-info">
                      <div class="kelas-waktu">
                        <img src="../assets/images/clock.png" class="kelas-icon" />
                        <span>DD, HH:MM - HH:MM</span>
                      </div>
                      <div class="kelas-dosen">
                        <img src="../assets/images/conference.png" class="kelas-icon" />
                        <span>Kelas 2B</span>
                      </div>
                      <div class="kelas-ruang">
                        <img src="../assets/images/classroom.png" class="kelas-icon" />
                        <span>Ruang Kelas</span>
                      </div>
                    </div>
                  </div>
                  <div class="kelas-card">
                    <div class="kelas-header">Mata Kuliah 2</div>
                    <div class="kelas-subheader">Prodi Mahasiswa</div>
                    <div class="kelas-divider"></div>
                    <div class="kelas-info">
                      <div class="kelas-waktu">
                        <img src="../assets/images/clock.png" class="kelas-icon" />
                        <span>DD, HH:MM - HH:MM</span>
                      </div>
                      <div class="kelas-dosen">
                        <img src="../assets/images/conference.png" class="kelas-icon" />
                        <span>Kelas 2B</span>
                      </div>
                      <div class="kelas-ruang">
                        <img src="../assets/images/classroom.png" class="kelas-icon" />
                        <span>Ruang Kelas</span>
                      </div>
                    </div>
                  </div> 
                  <div class="kelas-card">
                    <div class="kelas-header">Mata Kuliah 3</div>
                    <div class="kelas-subheader">Prodi Mahasiswa</div>
                    <div class="kelas-divider"></div>
                    <div class="kelas-info">
                      <div class="kelas-waktu">
                        <img src="../assets/images/clock.png" class="kelas-icon" />
                        <span>DD, HH:MM - HH:MM</span>
                      </div>
                      <div class="kelas-dosen">
                        <img src="../assets/images/conference.png" class="kelas-icon" />
                        <span>Kelas 2B</span>
                      </div>
                      <div class="kelas-ruang">
                        <img src="../assets/images/classroom.png" class="kelas-icon" />
                        <span>Ruang Kelas</span>
                      </div>
                    </div>
                  </div> 
                  <div class="kelas-card">
                    <div class="kelas-header">Mata Kuliah 4</div>
                    <div class="kelas-subheader">Prodi Mahasiswa</div>
                    <div class="kelas-divider"></div>
                    <div class="kelas-info">
                      <div class="kelas-waktu">
                        <img src="../assets/images/clock.png" class="kelas-icon" />
                        <span>DD, HH:MM - HH:MM</span>
                      </div>
                      <div class="kelas-dosen">
                        <img src="../assets/images/conference.png" class="kelas-icon" />
                        <span>Kelas 2B</span>
                      </div>
                      <div class="kelas-ruang">
                        <img src="../assets/images/classroom.png" class="kelas-icon" />
                        <span>Ruang Kelas</span>
                      </div>
                    </div>
                  </div>
                  <div class="kelas-card">
                    <div class="kelas-header">Mata Kuliah 5</div>
                    <div class="kelas-subheader">Prodi Mahasiswa</div>
                    <div class="kelas-divider"></div>
                    <div class="kelas-info">
                      <div class="kelas-waktu">
                        <img src="../assets/images/clock.png" class="kelas-icon" />
                        <span>DD, HH:MM - HH:MM</span>
                      </div>
                      <div class="kelas-dosen">
                        <img src="../assets/images/conference.png" class="kelas-icon" />
                        <span>Kelas 2B</span>
                      </div>
                      <div class="kelas-ruang">
                        <img src="../assets/images/classroom.png" class="kelas-icon" />
                        <span>Ruang Kelas</span>
                      </div>
                    </div>
                  </div>
                  <div class="kelas-card">
                    <div class="kelas-header">Mata Kuliah 6</div>
                    <div class="kelas-subheader">Prodi Mahasiswa</div>
                    <div class="kelas-divider"></div>
                    <div class="kelas-info">
                      <div class="kelas-waktu">
                        <img src="../assets/images/clock.png" class="kelas-icon" />
                        <span>DD, HH:MM - HH:MM</span>
                      </div>
                      <div class="kelas-dosen">
                        <img src="../assets/images/conference.png" class="kelas-icon" />
                        <span>Kelas 2B</span>
                      </div>
                      <div class="kelas-ruang">
                        <img src="../assets/images/classroom.png" class="kelas-icon" />
                        <span>Ruang Kelas</span>
                      </div>
                    </div>
                  </div>
                  <div class="kelas-card">
                    <div class="kelas-header">Mata Kuliah 7</div>
                    <div class="kelas-subheader">Prodi Mahasiswa</div>
                    <div class="kelas-divider"></div>
                    <div class="kelas-info">
                      <div class="kelas-waktu">
                        <img src="../assets/images/clock.png" class="kelas-icon" />
                        <span>DD, HH:MM - HH:MM</span>
                      </div>
                      <div class="kelas-dosen">
                        <img src="../assets/images/conference.png" class="kelas-icon" />
                        <span>Kelas 2B</span>
                      </div>
                      <div class="kelas-ruang">
                        <img src="../assets/images/classroom.png" class="kelas-icon" />
                        <span>Ruang Kelas</span>
                      </div>
                    </div>
                  </div>
                  <div class="kelas-card">
                    <div class="kelas-header">Mata Kuliah 8</div>
                    <div class="kelas-subheader">Prodi Mahasiswa</div>
                    <div class="kelas-divider"></div>
                    <div class="kelas-info">
                      <div class="kelas-waktu">
                        <img src="../assets/images/clock.png" class="kelas-icon" />
                        <span>DD, HH:MM - HH:MM</span>
                      </div>
                      <div class="kelas-dosen">
                        <img src="../assets/images/conference.png" class="kelas-icon" />
                        <span>Kelas 2B</span>
                      </div>
                      <div class="kelas-ruang">
                        <img src="../assets/images/classroom.png" class="kelas-icon" />
                        <span>Ruang Kelas</span>
                      </div>
                    </div>
                  </div>
                  <div class="kelas-card">
                    <div class="kelas-header">Mata Kuliah 9</div>
                    <div class="kelas-subheader">Prodi Mahasiswa</div>
                    <div class="kelas-divider"></div>
                    <div class="kelas-info">
                      <div class="kelas-waktu">
                        <img src="../assets/images/clock.png" class="kelas-icon" />
                        <span>DD, HH:MM - HH:MM</span>
                      </div>
                      <div class="kelas-dosen">
                        <img src="../assets/images/conference.png" class="kelas-icon" />
                        <span>Kelas 2B</span>
                      </div>
                      <div class="kelas-ruang">
                        <img src="../assets/images/classroom.png" class="kelas-icon" />
                        <span>Ruang Kelas</span>
                      </div>
                    </div>
                  </div>
                  <div class="kelas-card">
                    <div class="kelas-header">Mata Kuliah 10</div>
                    <div class="kelas-subheader">Prodi Mahasiswa</div>
                    <div class="kelas-divider"></div>
                    <div class="kelas-info">
                      <div class="kelas-waktu">
                        <img src="../assets/images/clock.png" class="kelas-icon" />
                        <span>DD, HH:MM - HH:MM</span>
                      </div>
                      <div class="kelas-dosen">
                        <img src="../assets/images/conference.png" class="kelas-icon" />
                        <span>Kelas 2B</span>
                      </div>
                      <div class="kelas-ruang">
                        <img src="../assets/images/classroom.png" class="kelas-icon" />
                        <span>Ruang Kelas</span>
                      </div>
                    </div>
                  </div>                   
            </div>
        </div>
    </div>
</body>
</html>