<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Presensi Mahasiswa</title>
    <link rel="stylesheet" href="main.css">
    <link rel="stylesheet" href="kelas.css">
    <link rel="stylesheet" href="jadwal.css">
    <link rel="stylesheet" href="header_admin.css">
    <link rel="stylesheet" href="absen.css">
</head>
<body>
    <div class="header-container">
        <header id="main-header" class="header">
            <div id="header-left" class="header-left">
              <img id="logo" src="knowledge.png" alt="Logo" class="logo">
              <span id="app-title">Presensi Mahasiswa</span>
            </div>
          
            <div id="main-menu" class="header-center">
                <div id="home-menu" class="menu-item">
                    <img style="filter: invert();" src="home.png" alt="Beranda" class="menu-icon">
                    <a style="color: white; text-decoration: none;" href="dashboard_user.php">
                    <span>Beranda</span>
                    </a>
                </div>
                <div id="logout-menu" class="menu-item">
                    <img style="filter: invert();" src="logout.png" alt="Keluar" class="menu-icon">
                    <a style="color: white; text-decoration: none;" href="login.php">
                    <span style="text-decoration: none;">Keluar</span>
                    </a>
                </div>
            </div>
            <div class="header-right">
              <span class="user-name">Nama Mahasiswa</span>
              <img style="filter: invert();" src="user.png" alt="Foto Profil" class="user-photo">
            </div>
          </header>   
    </div>
    <div class="content-container">
        <div class="left-container">
            <div class="jadwal-container">
                <div class="jadwal-header">
                    <h2>Jadwal Minggu Ini</h2>
                    <span class="tanggal-hari">01, Februari 1990</span>
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
                        <img src="teachings.png" alt="icon" class="info-icon">
                        <span>Praktik Pemrograman<br>berbasis web</span>
                      </div>
                      <div class="info-item">
                        <img src="clock.png" alt="icon" class="info-icon">
                        <span>08-00 - 12-00</span>
                      </div>
                      <div class="info-item">
                        <img src="classroom.png " alt="icon" class="info-icon">
                        <span>Laboraturium</span>
                      </div>
                      <div class="info-item">
                        <img src="conference.png" alt="icon" class="info-icon">
                        <span>Gus Nanang Syafuddin,<br>S.Kom., M.Kom.</span>
                      </div>
                    </div>
                  </div>                  
                  
            </div>
            <div class="container">
                <div class="absen-container">
                    <div class="absen-header">
                        <h2>Status Absensi</h2>
                        <span class="absen-subtitle">Mata Kuliah</span>
                    </div>
                    
                    <div class="absen-status">
                        <div class="absen-icon">
                            <img src="presentation.png" alt="Status Icon" id="statusIcon">
                        </div>
                        <p class="absen-text" id="statusText">Kamu Belum Absen</p>
                        <div class="absen-text-line"></div>
                    </div>
                    <div id="beforeAttendance">
                        <div class="absen-action-label">Ajukan Absensi :</div>
                        <div class="absen-actions">
                            <div class="absen-btn" onclick="markAttendance('absen')">
                                <div class="absen-btn-icon">
                                    <img class="image" src="absenteeism.png".png" alt="Absen">
                                </div>
                                <span>Absen</span>
                            </div>
                            
                            <div class="absen-btn" onclick="markAttendance('izin')">
                                <div class="absen-btn-icon">
                                    <img class="image" src="absent.png" alt="Izin">
                                </div>
                                <span>Izin</span>
                            </div>
                            
                            <div class="absen-btn" onclick="markAttendance('sakit')">
                                <div class="absen-btn-icon">
                                    <img class="image" src="patient.png" alt="Sakit">
                                </div>
                                <span>Sakit</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Attendance stats (after check-in) -->
                    <div id="afterAttendance" class="hidden">
                        <div class="absen-action-label">Kehadiran :</div>
                        <div class="kehadiran-stats">
                            <div class="kehadiran-item">
                                <div class="kehadiran-icon">
                                    <img class="image" src="absenteeism.png" alt="Absen">
                                </div>
                                <span class="kehadiran-label">Absen</span>
                                <span class="kehadiran-value" id="absenCount">0</span>
                            </div>
                            
                            <div class="kehadiran-item">
                                <div class="kehadiran-icon">
                                    <img class="image" src="absent.png" alt="Izin">
                                </div>
                                <span class="kehadiran-label">Izin</span>
                                <span class="kehadiran-value" id="izinCount">0</span>
                            </div>
                            
                            <div class="kehadiran-item">
                                <div class="kehadiran-icon">
                                    <img class="image" src="patient.png" alt="Sakit">
                                </div>
                                <span class="kehadiran-label">Sakit</span>
                                <span class="kehadiran-value" id="sakitCount">0</span>
                            </div>
                            
                            <div class="kehadiran-item">
                                <div class="kehadiran-icon">
                                    <img class="image" src="alpha.png" alt="Alpha">
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
                    <input type="text" class="search-bar" placeholder="Cari Berdasarkan nama kelas atau dosen">
                    <img src="search-interface-symbol.png" alt="Search Icon" class="search-icon">
                </div>
            </div>
            <div class="kelas-container">
                <div id="matkul-1" class="kelas-card">
                    <div id="matkul-1-header" class="kelas-header">Mata Kuliah 1</div>
                    <div id="matkul-1-subheader" class="kelas-subheader">TRPL</div>
                    <div class="kelas-divider"></div>
                    <div class="kelas-info">
                        <div class="kelas-waktu">
                            <img src="clock.png" alt="Jam" class="kelas-icon">
                            <span>Jumat,<br> 09:00 -<br> 10:00</span>
                        </div>
                        <div class="kelas-dosen">
                            <img src="conference.png" alt="Dosen" class="kelas-icon">
                            <span>Nama Dosen</span>
                        </div>
                    </div>
                    <div class="kehadiran">Kehadiran: 0 dari 16 sesi</div>
                    <div class="progress-bar">
                        <div class="progress" width="0%"></div>
                    </div>
                </div>
                <div id="matkul-2" class="kelas-card">
                    <div id="matkul-2-header" class="kelas-header">Mata Kuliah 2</div>
                    <div id="matkul-2-subheader" class="kelas-subheader">TRPL</div>
                    <div class="kelas-divider"></div>
                    <div class="kelas-info">
                        <div class="kelas-waktu">
                            <img src="clock.png" alt="Jam" class="kelas-icon">
                            <span>Jumat,<br> 09:00 -<br> 10:00</span>
                        </div>
                        <div class="kelas-dosen">
                            <img src="conference.png" alt="Dosen" class="kelas-icon">
                            <span>Nama Dosen 2</span>
                        </div>
                    </div>
                    <div class="kehadiran">Kehadiran: 0 dari 16 sesi</div>
                    <div class="progress-bar">
                        <div class="progress" width="0%"></div>
                    </div>
                </div>
                <div id="matkul-3" class="kelas-card">  
                    <div id="matkul-3-header" class="kelas-header">Mata Kuliah 3</div>  
                    <div id="matkul-3-subheader" class="kelas-subheader">TRPL</div>
                    <div class="kelas-divider"></div>
                    <div class="kelas-info">
                        <div class="kelas-waktu">
                            <img src="clock.png" alt="Jam" class="kelas-icon">      
                            <span>Jumat,<br> 09:00 -<br> 10:00</span>
                        </div>
                        <div class="kelas-dosen">
                            <img src="conference.png" alt="Dosen" class="kelas-icon">
                            <span>Nama Dosen 3</span>
                        </div>
                    </div>
                    <div class="kehadiran">Kehadiran: 0 dari 16 sesi</div>  
                    <div class="progress-bar">
                        <div class="progress" width="0%"></div>
                    </div>
                </div>
                <div id="matkul-4" class="kelas-card">
                    <div id="matkul-4-header" class="kelas-header">Mata kuliah 4</div>
                    <div id="matkul-4-subheader" class="kelas-subheader">TRPL</div>
                    <div class="kelas-divider"></div>
                    <div class="kelas-info">
                        <div class="kelas-waktu">
                            <img src="clock.png" alt="Jam" class="kelas-icon">
                            <span>Jumat,<br> 09:00 -<br> 10:00</span>
                        </div>
                        <div class="kelas-dosen">
                            <img src="conference.png" alt="Dosen" class="kelas-icon">
                            <span>Nama Dosen 4</span>
                        </div>
                    </div>
                    <div class="kehadiran">Kehadiran: 0 dari 16 sesi</div>
                    <div class="progress-bar">
                        <div class="progress" width="0%"></div>
                    </div>
                </div>
                <div id="matkul-5" class="kelas-card">
                    <div id="matkul-5-header" class="kelas-header">Mata Kuliah 5</div>
                    <div id="matkul-5-subheader" class="kelas-subheader">TRPL</div>
                    <div class="kelas-divider"></div>
                    <div class="kelas-info">
                        <div class="kelas-waktu">
                            <img src="clock.png" alt="Jam" class="kelas-icon">
                            <span>Jumat,<br> 09:00 -<br> 10:00</span>
                        </div>
                        <div class="kelas-dosen">
                            <img src="conference.png" alt="Dosen" class="kelas-icon">   
                            <span>Nama Dosen 5</span>
                        </div>
                    </div>
                    <div class="kehadiran">Kehadiran: 0 dari 16 sesi</div>
                    <div class="progress-bar">
                        <div class="progress" width="0%"></div>
                    </div>
                </div>
                <div id="matkul-6" class="kelas-card">
                    <div id="matkul-6-header" class="kelas-header">Mata Kuliah 6</div>
                    <div id="matkul-6-subheader" class="kelas-subheader">TRPL</div>
                    <div class="kelas-divider"></div>
                    <div class="kelas-info">
                        <div class="kelas-waktu">
                            <img src="clock.png" alt="Jam" class="kelas-icon">
                            <span>Jumat,<br> 09:00 -<br> 10:00</span>
                        </div>
                        <div class="kelas-dosen">
                            <img src="conference.png" alt="Dosen" class="kelas-icon">
                            <span>Nama Dosen 6</span>
                        </div>
                    </div>
                    <div class="kehadiran">Kehadiran: 0 dari 16 sesi</div>
                    <div class="progress-bar">
                        <div class="progress" width="0%"></div>
                    </div>
                </div>
                <div id="matkul-7" class="kelas-card">
                    <div id="matkul-7-header" class="kelas-header">Mata Kuliah 7</div>
                    <div id="matkul-7-subheader" class="kelas-subheader">TRPL</div>
                    <div class="kelas-divider"></div>
                    <div class="kelas-info">
                        <div class="kelas-waktu">
                            <img src="clock.png" alt="Jam" class="kelas-icon">
                            <span>Jumat,<br> 09:00 -<br> 10:00</span>
                        </div>
                        <div class="kelas-dosen">
                            <img src="conference.png" alt="Dosen" class="kelas-icon">
                            <span>Nama Dosen 7</span>
                        </div>
                    </div>
                    <div class="kehadiran">Kehadiran: 0 dari 16 sesi</div>
                    <div class="progress-bar">
                        <div class="progress" width="0%"></div>
                    </div>
                </div>
                <div id="matkul-8" class="kelas-card">
                    <div id="matkul-8-header" class="kelas-header">Mata Kuliah 8</div>
                    <div id="matkul-8-subheader" class="kelas-subheader">TRPL</div>
                    <div class="kelas-divider"></div>
                    <div class="kelas-info">
                        <div class="kelas-waktu">
                            <img src="clock.png" alt="Jam" class="kelas-icon">  
                            <span>Jumat,<br> 09:00 -<br> 10:00</span>
                        </div>
                        <div class="kelas-dosen">
                            <img src="conference.png" alt="Dosen" class="kelas-icon">
                            <span>Nama Dosen 8</span>
                        </div>
                    </div>
                    <div class="kehadiran">Kehadiran: 0 dari 16 sesi</div>
                    <div class="progress-bar">
                        <div class="progress" width="0%"></div>
                    </div>
                </div>
                <div id="matkul-9" class="kelas-card">
                    <div id="matkul-9-header" class="kelas-header">Mata Kuliah 9</div>
                    <div id="matkul-9-subheader" class="kelas-subheader">TRPL</div>
                    <div class="kelas-divider"></div>   
                    <div class="kelas-info">
                        <div class="kelas-waktu">
                            <img src="clock.png" alt="Jam" class="kelas-icon">
                            <span>Jumat,<br> 09:00 -<br> 10:00</span>
                        </div>
                        <div class="kelas-dosen">   
                            <img src="conference.png" alt="Dosen" class="kelas-icon">
                            <span>Nama Dosen 9</span>
                        </div>
                    </div>
                    <div class="kehadiran">Kehadiran: 0 dari 16 sesi</div>
                    <div class="progress-bar">
                        <div class="progress" width="0%"></div>
                    </div>
                </div>
                <div id="matkul-10" class="kelas-card">
                    <div id="matkul-10-header" class="kelas-header">Mata Kuliah 10</div>
                    <div id="matkul-10-subheader" class="kelas-subheader">TRPL</div>
                    <div class="kelas-divider"></div>
                    <div class="kelas-info">
                        <div class="kelas-waktu">
                            <img src="clock.png" alt="Jam" class="kelas-icon">
                            <span>Jumat,<br> 09:00 -<br> 10:00</span>
                        </div>
                        <div class="kelas-dosen">
                            <img src="conference.png" alt="Dosen" class="kelas-icon">
                            <span>Nama Dosen 10</span>
                        </div>
                    </div>
                    <div class="kehadiran">Kehadiran: 0 dari 16 sesi</div>
                    <div class="progress-bar">
                        <div class="progress" width="0%"></div>
                    </div>  
                </div>
            </div>
        </div>
    </div>
    <script src="absen_functions.js"></script>
</body>
</html>
<div id="absensi-section">
    <div id="absensi-status">
        <div class="absen-icon">
            <img src="presentation.png" alt="Status Icon" id="statusIcon">
        </div>
        <p class="absen-text" id="statusText">Kamu Belum Absen</p>
        <div class="absen-text-line"></div>
    </div>
    <div id="absensi-actions">
        <div class="absen-action-label">Ajukan Absensi :</div>
        <div class="absen-actions">
            <div class="absen-btn" onclick="markAttendance('absen')">
                <div class="absen-btn-icon">
                    <img class="image" src="absenteeism.png".png" alt="Absen">
                </div>
                <span>Absen</span>
            </div>
            
            <div class="absen-btn" onclick="markAttendance('izin')">
                <div class="absen-btn-icon">
                    <img class="image" src="absent.png" alt="Izin">
                </div>
                <span>Izin</span>
            </div>
            
            <div class="absen-btn" onclick="markAttendance('sakit')">
                <div class="absen-btn-icon">
                    <img class="image" src="patient.png" alt="Sakit">
                </div>
                <span>Sakit</span>
            </div>
        </div>
    </div>
</div>
