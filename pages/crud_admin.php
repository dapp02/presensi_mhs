<?php
// Blok PHP di atas untuk session, middleware, dan inisiasi lainnya tetap ada.
// Pastikan blok PHP yang sudah ada di file Anda tetap di sini.
// Contoh:
require_once __DIR__ . '/../auth/config/session.php';
require_once __DIR__ . '/../auth/middleware/auth.php';
require_once __DIR__ . '/../auth/middleware/role.php';

Session::start();
AuthMiddleware::requireLogin();
RoleMiddleware::requireRole(['admin']); // Pastikan hanya admin

$nama_pengguna = Session::get('nama_lengkap'); // Untuk me  nampilkan nama di header
?>
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
              <a style="color: white; text-decoration: none;" href="../auth/handlers/logout.php">
                <span>Keluar</span>
              </a>
            </div>
          </div>
        
          <div class="header-right">
            <span class="user-name"><?php echo htmlspecialchars($nama_pengguna ?? 'Admin'); ?></span>
            <img src="../assets/images/user.png" alt="Foto Profil" class="user-photo">
          </div>
        </header>   
    </div>
    
    <div class="content-container">
        <div class="left-container">
            <div class="crud-container">
                <h2>Kelola Data</h2>
                <div class="crud-header"></div>
                <div class="crud-menu">
                    <div class="crud-menu-item active" data-target="prodi">
                        <img src="../assets/images/academic.png" alt="Program Studi" class="crud-menu-icon">
                        <span>Data Program Studi</span>
                    </div>
                    <div class="crud-menu-item" data-target="kelas">
                        <img src="../assets/images/classroom.png" alt="Kelas" class="crud-menu-icon">
                        <span>Data Kelas</span>
                    </div>
                    <div class="crud-menu-item" data-target="mahasiswa">
                        <img src="../assets/images/student.png" alt="Mahasiswa" class="crud-menu-icon">
                        <span>Data Mahasiswa</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="kelas-container-main">
            <div class="crud-content active" id="prodi-crud">
                <div class="crud-header">
                    <div class="crud-title">Data Program Studi</div>
                    <button class="crud-button add" id="add-prodi">Tambah Prodi</button>
                </div>
                <div class="search-container">
                    <input type="text" id="prodi-search-input" class="search-bar" placeholder="Cari program studi berdasarkan nama">
                    <img src="../assets/images/search-interface-symbol.png" alt="Search Icon" class="search-icon">
                </div>
                <table class="crud-table">
                    <thead>
                        <tr>
                            <th>ID Prodi</th>
                            <th>Nama Program Studi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div> 
            <div class="modal-overlay" id="prodi-modal">
                <div class="modal">
                    <div class="modal-header">
                        <div class="modal-title" id="prodi-modal-title">Tambah Prodi Baru</div>
                        <button class="modal-close" id="close-prodi-modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <form id="form-prd">
                            <input type="hidden" id="prodi-id">
                            <div class="form-group">
                                <label for="prodi-nama">Nama Program Studi</label>
                                <input type="text" id="prodi-nama" class="form-control" placeholder="Masukkan nama prodi" required>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button class="crud-button" id="cancel-prodi">Batal</button>
                        <button class="crud-button add" id="save-prodi">Simpan</button>
                    </div>
                </div>
            </div>
            <div class="crud-content" id="kelas-crud">
                <div class="crud-header">
                    <div class="crud-title">Data Kelas</div>
                    <button class="crud-button add" id="add-kelas">Tambah Kelas</button>
                </div>
                <div class="search-container">
                    <input type="text" id="kelas-search-input" class="search-bar" placeholder="Cari kelas berdasarkan nama">
                    <img src="../assets/images/search-interface-symbol.png" alt="Search Icon" class="search-icon">
                </div>
                <table class="crud-table">
                    <thead>
                        <tr>
                            <th>ID Kelas</th>
                            <th>Nama Kelas</th>
                            <th>Dosen Wali</th>
                            <th>Program Studi</th>
                            <th>Tahun Ajaran</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>

            <div class="crud-content" id="mahasiswa-crud">
                <div class="crud-header">
                    <div class="crud-title">Data Mahasiswa</div>
                    <button class="crud-button add" id="add-mahasiswa">Tambah Mahasiswa</button>
                </div>
                <div class="search-container">
                    <input type="text" id="mahasiswa-search-input" class="search-bar" placeholder="Cari mahasiswa berdasarkan NIM atau Nama">
                    <img src="../assets/images/search-interface-symbol.png" alt="Search Icon" class="search-icon">
                </div>
                <table class="crud-table">
                    <thead>
                        <tr>
                            <th>NIM</th>
                            <th>Nama Lengkap</th>
                            <th>Program Studi</th>
                            <th>Kelas</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="mahasiswa-table-body">
                    </tbody>
                </table>
            </div>

            <div class="modal-overlay" id="kelas-modal">
                <div class="modal">
                    <div class="modal-header">
                        <div class="modal-title" id="kelas-modal-title">Tambah Kelas Baru</div>
                        <button class="modal-close" id="close-modal-kelas">&times;</button>
                    </div>
                    <div class="modal-body">
                        <form id="form-kls">
                            <input type="hidden" id="kelas-id">
                            <div class="form-group">
                                <label for="nama-kelas">Nama Kelas</label>
                                <input type="text" id="kls-nama" class="form-control" placeholder="Masukkan nama kelas" required>
                            </div>
                            <div class="form-group">
                                <label for="prodi-kelas">Program Studi</label>
                                <select id="prodi-kelas" name="id_prodi" class="form-control" required>
                                    <option value="">Memuat...</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="dosen-wali-kelas">Dosen Wali</label>
                                <select id="dosen-wali-kelas" name="id_dosen_wali" class="form-control" required>
                                    <option value="">Memuat...</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="tahun-ajaran-kelas">Tahun Ajaran</label>
                                <input type="text" id="tahun-ajaran-kelas" class="form-control" placeholder="Contoh: 2023/2024" required>
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
    </div>
            <div class="modal-overlay" id="mahasiswa-modal">
                <div class="modal">
                    <div class="modal-header">
                        <div class="modal-title" id="mahasiswa-modal-title">Tambah Mahasiswa Baru</div>
                        <button class="modal-close" id="close-mahasiswa-modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <form id="form-mhs">
                            <div class="form-group">
                                <label for="mhs-nim">NIM</label>
                                <select id="mhs-nim" class="form-control" required>
                                    <option value="">Pilh NIM</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="mhs-nama-lengkap">Nama Lengkap</label>
                                <input type="text" id="mhs-nama-lengkap" name="nama_lengkap" class="form-control" placeholder="Masukkan nama lengkap" required>
                            </div>
                            <div class="form-group">
                                <label for="mhs-password">Password</label>
                                <input type="password" id="mhs-password" class="form-control" placeholder="Masukkan Password" required>
                            </div>
                            <div class="form-group">
                                <label for="mhs-prodi">Program Studi</label>
                                <select id="mhs-prodi" name="id_prodi" class="form-control" required>
                                    <option value="">Pilih Program Studi</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="mhs-kelas">Kelas</label>
                                <select id="mhs-kelas" name="id_kelas" class="form-control">
                                    <option value="">Pilih Kelas (Opsional)</option>
                                </select>
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
    </div>
    <script src="../assets/js/crud_functions.js"></script>
    <script src="../assets/js/prodi_functions.js"></script>
    <script src="../assets/js/kelas_functions.js"></script>
    <script src="../assets/js/crud_mahasiswa_functions.js"></script>
</body>
</html>