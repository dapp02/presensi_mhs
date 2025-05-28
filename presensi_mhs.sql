-- Skema Database Final untuk Aplikasi Presensi Mahasiswa
-- Versi Hybrid yang menggabungkan logika bisnis dan praktik terbaik

-- 1. Tabel Program Studi
CREATE TABLE `program_studi` (
  `id_prodi` INT AUTO_INCREMENT PRIMARY KEY,
  `nama_prodi` VARCHAR(100) NOT NULL UNIQUE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 2. Tabel Pengguna (menyimpan data login umum untuk semua peran)
CREATE TABLE `pengguna` (
  `id_pengguna` INT AUTO_INCREMENT PRIMARY KEY,
  `nama_lengkap` VARCHAR(255) NOT NULL,
  `username` VARCHAR(100) NOT NULL UNIQUE,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL, -- Di-hash menggunakan password_hash()
  `role` ENUM('mahasiswa', 'dosen', 'admin') NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 3. Tabel Dosen (menyimpan data spesifik dosen, termasuk NIDN)
CREATE TABLE `dosen` (
  `nidn` VARCHAR(20) PRIMARY KEY,
  `id_pengguna` INT NOT NULL UNIQUE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna`(`id_pengguna`) ON DELETE CASCADE
);

-- 4. Tabel Kelas (dengan penambahan `id_dosen_wali` untuk hak akses)
CREATE TABLE `kelas` (
  `id_kelas` INT AUTO_INCREMENT PRIMARY KEY,
  `nama_kelas` VARCHAR(50) NOT NULL,
  `id_prodi` INT NOT NULL,
  `id_dosen_wali` VARCHAR(20) NOT NULL, -- Kunci untuk hak akses Dosen Wali
  `tahun_ajaran` VARCHAR(10) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`id_prodi`) REFERENCES `program_studi`(`id_prodi`),
  FOREIGN KEY (`id_dosen_wali`) REFERENCES `dosen`(`nidn`)
);

-- 5. Tabel Mahasiswa (menyimpan data spesifik mahasiswa, termasuk NIM)
CREATE TABLE `mahasiswa` (
  `nim` VARCHAR(20) PRIMARY KEY,
  `id_pengguna` INT NOT NULL UNIQUE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna`(`id_pengguna`) ON DELETE CASCADE
);

-- 6. Tabel Mahasiswa Kelas (tabel perantara untuk fleksibilitas)
CREATE TABLE `mahasiswa_kelas` (
  `id_mahasiswa_kelas` INT AUTO_INCREMENT PRIMARY KEY,
  `nim_mahasiswa` VARCHAR(20) NOT NULL,
  `id_kelas` INT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`nim_mahasiswa`) REFERENCES `mahasiswa`(`nim`) ON DELETE CASCADE,
  FOREIGN KEY (`id_kelas`) REFERENCES `kelas`(`id_kelas`) ON DELETE CASCADE,
  UNIQUE KEY `unique_mahasiswa_di_kelas` (`nim_mahasiswa`, `id_kelas`)
);

-- 7. Tabel Mata Kuliah
CREATE TABLE `mata_kuliah` (
  `id_matkul` INT AUTO_INCREMENT PRIMARY KEY,
  `kode_matkul` VARCHAR(20) NOT NULL UNIQUE,
  `nama_matkul` VARCHAR(255) NOT NULL,
  `sks` INT NOT NULL,
  `id_prodi` INT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`id_prodi`) REFERENCES `program_studi`(`id_prodi`)
);

-- 8. Tabel Dosen Mengajar (tabel perantara untuk penugasan dosen)
CREATE TABLE `dosen_mengajar` (
  `id_dosen_mengajar` INT AUTO_INCREMENT PRIMARY KEY,
  `nidn_dosen` VARCHAR(20) NOT NULL,
  `id_matkul` INT NOT NULL,
  `id_kelas` INT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`nidn_dosen`) REFERENCES `dosen`(`nidn`) ON DELETE CASCADE,
  FOREIGN KEY (`id_matkul`) REFERENCES `mata_kuliah`(`id_matkul`) ON DELETE CASCADE,
  FOREIGN KEY (`id_kelas`) REFERENCES `kelas`(`id_kelas`) ON DELETE CASCADE,
  UNIQUE KEY `unique_dosen_matkul_kelas` (`nidn_dosen`, `id_matkul`, `id_kelas`)
);

-- 9. Tabel Jadwal Kuliah (mendefinisikan waktu dan tempat)
CREATE TABLE `jadwal_kuliah` (
  `id_jadwal` INT AUTO_INCREMENT PRIMARY KEY,
  `id_dosen_mengajar` INT NOT NULL,
  `hari` ENUM('Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu') NOT NULL,
  `jam_mulai` TIME NOT NULL,
  `jam_selesai` TIME NOT NULL,
  `ruangan` VARCHAR(50) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`id_dosen_mengajar`) REFERENCES `dosen_mengajar`(`id_dosen_mengajar`) ON DELETE CASCADE
);

-- 10. Tabel Absensi (tabel transaksi untuk mencatat kehadiran)
CREATE TABLE `absensi` (
  `id_absensi` INT AUTO_INCREMENT PRIMARY KEY,
  `id_jadwal` INT NOT NULL,
  `nim_mahasiswa` VARCHAR(20) NOT NULL,
  `tanggal_absensi` DATE NOT NULL,
  `status_kehadiran` ENUM('Hadir', 'Izin', 'Sakit', 'Alpha') NOT NULL,
  `keterangan` TEXT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`id_jadwal`) REFERENCES `jadwal_kuliah`(`id_jadwal`) ON DELETE CASCADE,
  FOREIGN KEY (`nim_mahasiswa`) REFERENCES `mahasiswa`(`nim`) ON DELETE CASCADE,
  UNIQUE KEY `unique_absensi` (`id_jadwal`, `nim_mahasiswa`, `tanggal_absensi`)
);