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
              <span class="user-name">Nama Dosen</span>
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
                <h3 class="section-title">Nama Mata Kuliah</h3>
                
                <div id="course-info-section" class="course-info">
                    <div id="program-info" class="info-row">
                        <span class="info-label">Prodi</span>
                        <span class="info-value">: Program Studi</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Kelas</span>
                        <span class="info-value">: XX</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Tahun</span>
                        <span class="info-value">: YYYY</span>
                    </div>
                </div>
                
                <table class="attendance-table">
                    <thead>
                        <tr>
                            <th>NIM</th>
                            <th>Nama Mahasiswa</th>
                            <th>Absen</th>
                            <th class="izin">Izin</th>
                            <th>Sakit</th>
                            <th>Alpha</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>000000</td>
                            <td>Nama Mahasiswa</td>
                            <td class="action-cell">
                                <button class="action-button absen-btn">
                                    <img src="../assets/images/absenteeism.png" alt="" class="image">
                                    <span class="deskripsi">Absen</span>
                                </button>
                            </td>
                            <td class="action-cell">
                                <button class="action-button izin-btn">
                                    <img src="../assets/images/absent.png" alt="" class="image">
                                    <span class="deskripsi">Izin</span>
                                </button>
                            </td>
                            <td class="action-cell">
                                <button class="action-button sakit-btn">
                                    <img src="../assets/images/patient.png" alt="" class="image">
                                    <span class="deskripsi">Sakit</span>
                                </button>
                            </td>
                            <td class="action-cell">
                                <button class="action-button alpha-btn">
                                    <img src="../assets/images/alpha.png" alt="" class="image">
                                    <span class="deskripsi">Alpha</span>
                                </button>
                            </td>
                        </tr>
                        <tr id="student-row-1">
                            <td id="student-nim-1">000000</td>
                            <td id="student-name-1">Nama Mahasiswa</td>
                            <td id="absen-cell-1" class="action-cell">
                                <button id="absen-btn-1" class="action-button absen-btn">
                                    <img src="../assets/images/absenteeism.png" alt="" class="image">
                                    <span class="deskripsi">Absen</span>
                                </button>
                            </td>
                            <td class="action-cell">
                                <button class="action-button izin-btn">
                                    <img src="../assets/images/absent.png" alt="" class="image">
                                    <span class="deskripsi">Izin</span>
                                </button>
                            </td>
                            <td class="action-cell">
                                <button class="action-button sakit-btn">
                                    <img src="../assets/images/patient.png" alt="" class="image">
                                    <span class="deskripsi">Sakit</span>
                                </button>
                            </td>
                            <td class="action-cell">
                                <button class="action-button alpha-btn">
                                    <img src="../assets/images/alpha.png" alt="" class="image">
                                    <span class="deskripsi">Alpha</span>
                                </button>
                            </td>
                        </tr>
                        </tr>
                    </tbody>
                </table>
                
                <div class="button-container">
                    <button class="btn cancel-btn">Batal</button>
                    <button class="btn submit-btn">Kirim</button>
                </div>
            </div>
        </div>
    </main>
<script src="../assets/js/absen_functions.js"></script>
</body>
</html>