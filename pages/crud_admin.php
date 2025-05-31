<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Data - Presensi Mahasiswa</title>
    <link rel="stylesheet" href="../assets/css/main_admin.css">
    <link rel="stylesheet" href="../assets/css/header_admin.css">
    <link rel="stylesheet" href="../assets/css/crud_admin.css">
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
              <a style="color: white; text-decoration: none;" href="dashboard_admin.php">
                <span>Beranda</span>
              </a>
            </div>
            <div class="menu-item">
              <img style="filter: invert();" src="../assets/images/logout.png" alt="Keluar" class="menu-icon">
              <a style="color: white; text-decoration: none;" href="login.php">
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
    
    <div class="content-container">
        <!-- Bagian Kiri - Menu CRUD -->
        <div class="left-container">
            <div class="crud-container">
                <h2>Kelola Data</h2>
                <div class="crud-header"></div>
                <div class="crud-menu">
                    <div class="crud-menu-item active" data-target="mahasiswa">
                        <img src="../assets/images/student.png" alt="Mahasiswa" class="crud-menu-icon">
                        <span>Data Mahasiswa</span>
                    </div>
                    <div class="crud-menu-item" data-target="kelas">
                        <img src="../assets/images/classroom.png" alt="Kelas" class="crud-menu-icon">
                        <span>Data Kelas</span>
                    </div>
                    <div class="crud-menu-item" data-target="matakuliah">
                        <img src="../assets/images/teachings.png" alt="Mata Kuliah" class="crud-menu-icon">
                        <span>Data Mata Kuliah</span>
                    </div>
                    <div class="crud-menu-item" data-target="jadwal">
                        <img src="../assets/images/student_2.png" alt="Jadwal" class="crud-menu-icon">
                        <span>Data Jadwal</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Bagian Kanan - Konten CRUD -->
        <div class="kelas-container-main">
            <!-- CRUD Mahasiswa -->
            <div class="crud-content active" id="mahasiswa-crud">
                <div class="crud-header">
                    <div class="crud-title">Data Mahasiswa</div>
                    <div class="crud-actions">
                        <button class="crud-button add" id="add-mahasiswa">Tambah Mahasiswa</button>
                    </div>
                </div>
                
                <div class="search-container">
                    <input type="text" class="search-bar" placeholder="Cari mahasiswa berdasarkan nama atau NIM">
                    <img src="../assets/images/search-interface-symbol.png" alt="Search Icon" class="search-icon">
                </div>
                
                <table class="crud-table">
                    <thead>
                        <tr>
                            <th>NIM</th>
                            <th>Nama</th>
                            <th>Program Studi</th>
                            <th>Kelas</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data akan diisi oleh JavaScript -->
                    </tbody>
                </table>
                
                <!-- Modal untuk Mahasiswa -->
                <div class="modal-overlay" id="mahasiswa-modal">
                    <div class="modal">
                        <div class="modal-header">
                            <div class="modal-title" id="modal-title">Tambah Mahasiswa Baru</div>
                            <button class="modal-close" id="close-modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <form id="mahasiswa-form">
                                <div class="form-group">
                                    <label for="nim">NIM</label>
                                    <input type="text" id="nim" class="form-control" placeholder="Masukkan NIM">
                                </div>
                                <div class="form-group">
                                    <label for="nama">Nama Lengkap</label>
                                    <input type="text" id="nama" class="form-control" placeholder="Masukkan nama lengkap">
                                </div>
                                <div class="form-group">
                                    <label for="prodi">Program Studi</label>
                                    <select id="prodi" class="form-control">
                                        <option value="">Pilih Program Studi</option>
                                        <option value="Teknik Informatika">Teknik Informatika</option>
                                        <option value="Sistem Informasi">Sistem Informasi</option>
                                        <option value="Teknik Komputer">Teknik Komputer</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="kelas">Kelas</label>
                                    <select id="kelas" class="form-control">
                                        <option value="">Pilih Kelas</option>
                                        <option value="1A">1A</option>
                                        <option value="1B">1B</option>
                                        <option value="2A">2A</option>
                                        <option value="2B">2B</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" id="email" class="form-control" placeholder="Masukkan email">
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button class="crud-button" id="cancel-mahasiswa">Batal</button>
                            <button class="crud-button add" id="save-mahasiswa">Simpan</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- CRUD Kelas -->
            <div class="crud-content" id="kelas-crud">
                <div class="crud-header">
                    <div class="crud-title">Data Kelas</div>
                    <div class="crud-actions">
                        <button class="crud-button add" id="add-kelas">Tambah Kelas</button>
                    </div>
                </div>
                
                <div class="search-container">
                    <input type="text" class="search-bar" placeholder="Cari kelas berdasarkan nama atau kode">
                    <img src="../assets/images/search-interface-symbol.png" alt="Search Icon" class="search-icon">
                </div>
                
                <table class="crud-table">
                    <thead>
                        <tr>
                            <th>Kode Kelas</th>
                            <th>Nama Kelas</th>
                            <th>Program Studi</th>
                            <th>Jumlah Mahasiswa</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data akan diisi oleh JavaScript -->
                    </tbody>
                </table>
                
                <!-- Modal untuk Kelas -->
                <div class="modal-overlay" id="kelas-modal">
                    <div class="modal">
                        <div class="modal-header">
                            <div class="modal-title" id="modal-title-kelas">Tambah Kelas Baru</div>
                            <button class="modal-close" id="close-modal-kelas">&times;</button>
                        </div>
                        <div class="modal-body">
                            <form id="kelas-form">
                                <div class="form-group">
                                    <label for="kode-kelas">Kode Kelas</label>
                                    <input type="text" id="kode-kelas" class="form-control" placeholder="Masukkan kode kelas">
                                </div>
                                <div class="form-group">
                                    <label for="nama-kelas">Nama Kelas</label>
                                    <input type="text" id="nama-kelas" class="form-control" placeholder="Masukkan nama kelas">
                                </div>
                                <div class="form-group">
                                    <label for="prodi-kelas">Program Studi</label>
                                    <select id="prodi-kelas" class="form-control">
                                        <option value="">Pilih Program Studi</option>
                                        <option value="Teknik Informatika">Teknik Informatika</option>
                                        <option value="Sistem Informasi">Sistem Informasi</option>
                                        <option value="Teknik Komputer">Teknik Komputer</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="jumlah-mahasiswa">Jumlah Mahasiswa</label>
                                    <input type="number" id="jumlah-mahasiswa" class="form-control" placeholder="Masukkan jumlah mahasiswa">
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button class="crud-button" id="cancel-kelas">Batal</button>
                            <button class="crud-button add" id="save-kelas">Simpan</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- CRUD Mata Kuliah -->
            <div class="crud-content" id="matakuliah-crud">
                <div class="crud-header">
                    <div class="crud-title">Data Mata Kuliah</div>
                    <div class="crud-actions">
                        <button class="crud-button add" id="add-matakuliah">Tambah Mata Kuliah</button>
                    </div>
                </div>
                
                <div class="search-container">
                    <input type="text" class="search-bar" placeholder="Cari mata kuliah berdasarkan nama atau kode">
                    <img src="../assets/images/search-interface-symbol.png" alt="Search Icon" class="search-icon">
                </div>
                
                <table class="crud-table">
                    <thead>
                        <tr>
                            <th>Kode MK</th>
                            <th>Nama Mata Kuliah</th>
                            <th>SKS</th>
                            <th>Semester</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data akan diisi oleh JavaScript -->
                    </tbody>
                </table>
                
                <!-- Modal untuk Mata Kuliah -->
                <div class="modal-overlay" id="matkul-modal">
                    <div class="modal">
                        <div class="modal-header">
                            <div class="modal-title" id="modal-title-matkul">Tambah Mata Kuliah Baru</div>
                            <button class="modal-close" id="close-modal-matkul">&times;</button>
                        </div>
                        <div class="modal-body">
                            <form id="matkul-form">
                                <div class="form-group">
                                    <label for="kode-matkul">Kode MK</label>
                                    <input type="text" id="kode-matkul" class="form-control" placeholder="Masukkan kode mata kuliah">
                                </div>
                                <div class="form-group">
                                    <label for="nama-matkul">Nama Mata Kuliah</label>
                                    <input type="text" id="nama-matkul" class="form-control" placeholder="Masukkan nama mata kuliah">
                                </div>
                                <div class="form-group">
                                    <label for="sks">SKS</label>
                                    <input type="number" id="sks" class="form-control" placeholder="Masukkan jumlah SKS">
                                </div>
                                <div class="form-group">
                                    <label for="semester">Semester</label>
                                    <select id="semester" class="form-control">
                                        <option value="">Pilih Semester</option>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                        <option value="6">6</option>
                                        <option value="7">7</option>
                                        <option value="8">8</option>
                                    </select>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button class="crud-button" id="cancel-matkul">Batal</button>
                            <button class="crud-button add" id="save-matkul">Simpan</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- CRUD Jadwal -->
            <div class="crud-content" id="jadwal-crud">
                <div class="crud-header">
                    <div class="crud-title">Data Jadwal</div>
                    <div class="crud-actions">
                        <button class="crud-button add" id="add-jadwal">Tambah Jadwal</button>
                    </div>
                </div>
                
                <div class="search-container">
                    <input type="text" class="search-bar" placeholder="Cari jadwal berdasarkan mata kuliah atau kelas">
                    <img src="../assets/images/search-interface-symbol.png" alt="Search Icon" class="search-icon">
                </div>
                
                <table class="crud-table">
                    <thead>
                        <tr>
                            <th>Mata Kuliah</th>
                            <th>Kelas</th>
                            <th>Hari</th>
                            <th>Jam</th>
                            <th>Ruangan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data akan diisi oleh JavaScript -->
                    </tbody>
                </table>
                
                <!-- Modal untuk Jadwal -->
                <div class="modal-overlay" id="jadwal-modal">
                    <div class="modal">
                        <div class="modal-header">
                            <div class="modal-title" id="modal-title-jadwal">Tambah Jadwal Baru</div>
                            <button class="modal-close" id="close-modal-jadwal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <form id="jadwal-form">
                                <div class="form-group">
                                    <label for="matkul-jadwal">Mata Kuliah</label>
                                    <select id="matkul-jadwal" class="form-control">
                                        <option value="">Pilih Mata Kuliah</option>
                                        <!-- Opsi akan diisi secara dinamis oleh JavaScript -->
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="kelas-jadwal">Kelas</label>
                                    <select id="kelas-jadwal" class="form-control">
                                        <option value="">Pilih Kelas</option>
                                        <!-- Opsi akan diisi secara dinamis oleh JavaScript -->
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="hari">Hari</label>
                                    <select id="hari" class="form-control">
                                        <option value="">Pilih Hari</option>
                                        <option value="Senin">Senin</option>
                                        <option value="Selasa">Selasa</option>
                                        <option value="Rabu">Rabu</option>
                                        <option value="Kamis">Kamis</option>
                                        <option value="Jumat">Jumat</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="jam">Jam</label>
                                    <input type="text" id="jam" class="form-control" placeholder="Contoh: 08:00 - 10:30">
                                </div>
                                <div class="form-group">
                                    <label for="ruangan">Ruangan</label>
                                    <input type="text" id="ruangan" class="form-control" placeholder="Masukkan ruangan">
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button class="crud-button" id="cancel-jadwal">Batal</button>
                            <button class="crud-button add" id="save-jadwal">Simpan</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pindahkan semua script ke bagian paling bawah -->
    <script src="../assets/js/crud_functions.js"></script>
    <script src="../assets/js/mahasiswa_functions.js"></script>
    <script src="../assets/js/kelas_functions.js"></script>
    <script src="../assets/js/matkul_functions.js"></script>
    <script src="../assets/js/jadwal_functions.js"></script>
</body>
</html>