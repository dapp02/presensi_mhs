<?php
require_once '../../auth/config/database.php';

class DbOperations {
    private $conn;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }
    
    // Fungsi untuk mahasiswa
    public function getMahasiswa() {
        $query = "SELECT m.nim, p.nama_lengkap, ps.nama_prodi, k.nama_kelas, p.email 
                 FROM mahasiswa m 
                 JOIN pengguna p ON m.id_pengguna = p.id_pengguna 
                 JOIN mahasiswa_kelas mk ON m.nim = mk.nim_mahasiswa 
                 JOIN kelas k ON mk.id_kelas = k.id_kelas 
                 JOIN program_studi ps ON k.id_prodi = ps.id_prodi";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMahasiswaByNim($nim) {
        $query = "SELECT m.nim, p.nama_lengkap, ps.nama_prodi, k.nama_kelas, p.email 
                 FROM mahasiswa m 
                 JOIN pengguna p ON m.id_pengguna = p.id_pengguna 
                 JOIN mahasiswa_kelas mk ON m.nim = mk.nim_mahasiswa 
                 JOIN kelas k ON mk.id_kelas = k.id_kelas 
                 JOIN program_studi ps ON k.id_prodi = ps.id_prodi 
                 WHERE m.nim = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$nim]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function addMahasiswa($nim, $nama, $prodi, $kelas, $email) {
        try {
            $this->conn->beginTransaction();
            
            // Cek apakah prodi ada
            $prodiId = $this->getProdiIdByName($prodi);
            if (!$prodiId) {
                // Jika tidak ada, buat prodi baru
                $prodiId = $this->createProdi($prodi);
            }
            
            // Cek apakah kelas ada
            $kelasId = $this->getKelasIdByName($kelas, $prodiId);
            if (!$kelasId) {
                // Jika tidak ada, buat kelas baru
                $kelasId = $this->createKelas($kelas, $prodiId);
            }
            
            // Buat pengguna baru
            $password = password_hash($nim, PASSWORD_DEFAULT); // Default password adalah NIM
            $userId = $this->createUser($nama, $nim, $email, $password, 'mahasiswa');
            
            // Buat mahasiswa baru
            $query = "INSERT INTO mahasiswa (nim, id_pengguna) VALUES (?, ?)";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$nim, $userId]);
            
            // Tambahkan mahasiswa ke kelas
            $query = "INSERT INTO mahasiswa_kelas (nim_mahasiswa, id_kelas) VALUES (?, ?)";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$nim, $kelasId]);
            
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }
    
    public function updateMahasiswa($nim, $nama, $prodi, $kelas, $email) {
        try {
            $this->conn->beginTransaction();
            
            // Dapatkan id_pengguna dari mahasiswa
            $query = "SELECT id_pengguna FROM mahasiswa WHERE nim = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$nim]);
            $userId = $stmt->fetchColumn();
            
            if (!$userId) {
                return false;
            }
            
            // Update data pengguna
            $query = "UPDATE pengguna SET nama_lengkap = ?, email = ? WHERE id_pengguna = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$nama, $email, $userId]);
            
            // Cek apakah prodi ada
            $prodiId = $this->getProdiIdByName($prodi);
            if (!$prodiId) {
                // Jika tidak ada, buat prodi baru
                $prodiId = $this->createProdi($prodi);
            }
            
            // Cek apakah kelas ada
            $kelasId = $this->getKelasIdByName($kelas, $prodiId);
            if (!$kelasId) {
                // Jika tidak ada, buat kelas baru
                $kelasId = $this->createKelas($kelas, $prodiId);
            }
            
            // Update kelas mahasiswa
            $query = "UPDATE mahasiswa_kelas SET id_kelas = ? WHERE nim_mahasiswa = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$kelasId, $nim]);
            
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    public function deleteMahasiswa($nim) {
        try {
            $this->conn->beginTransaction();
            
            // Dapatkan id_pengguna dari mahasiswa
            $query = "SELECT id_pengguna FROM mahasiswa WHERE nim = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$nim]);
            $userId = $stmt->fetchColumn();
            
            if (!$userId) {
                return false;
            }
            
            // Hapus dari mahasiswa_kelas
            $query = "DELETE FROM mahasiswa_kelas WHERE nim_mahasiswa = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$nim]);
            
            // Hapus dari mahasiswa
            $query = "DELETE FROM mahasiswa WHERE nim = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$nim]);
            
            // Hapus dari pengguna
            $query = "DELETE FROM pengguna WHERE id_pengguna = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$userId]);
            
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }
    
    // Fungsi untuk kelas
    public function getKelas() {
        $query = "SELECT k.id_kelas, k.nama_kelas, ps.nama_prodi, 
                 (SELECT COUNT(*) FROM mahasiswa_kelas mk WHERE mk.id_kelas = k.id_kelas) as jumlah_mahasiswa 
                 FROM kelas k 
                 JOIN program_studi ps ON k.id_prodi = ps.id_prodi";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function addKelas($kode, $nama, $prodi, $jumlah) {
        try {
            $this->conn->beginTransaction();
            
            // Cek apakah prodi ada
            $prodiId = $this->getProdiIdByName($prodi);
            if (!$prodiId) {
                // Jika tidak ada, buat prodi baru
                $prodiId = $this->createProdi($prodi);
            }
            
            // Buat kelas baru
            $query = "INSERT INTO kelas (nama_kelas, id_prodi, id_dosen_wali, tahun_ajaran) VALUES (?, ?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$nama, $prodiId, 'D001', '2023/2024']); // Default dosen wali dan tahun ajaran
            
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }
    
    public function updateKelas($kode, $nama, $prodi, $jumlah) {
        try {
            $this->conn->beginTransaction();
            
            // Dapatkan id kelas
            $query = "SELECT id_kelas FROM kelas WHERE nama_kelas = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$kode]);
            $kelasId = $stmt->fetchColumn();
            
            if (!$kelasId) {
                return false;
            }
            
            // Cek apakah prodi ada
            $prodiId = $this->getProdiIdByName($prodi);
            if (!$prodiId) {
                // Jika tidak ada, buat prodi baru
                $prodiId = $this->createProdi($prodi);
            }
            
            // Update kelas
            $query = "UPDATE kelas SET nama_kelas = ?, id_prodi = ? WHERE id_kelas = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$nama, $prodiId, $kelasId]);
            
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }
    
    public function deleteKelas($kode) {
        try {
            $this->conn->beginTransaction();
            
            // Dapatkan id kelas
            $query = "SELECT id_kelas FROM kelas WHERE nama_kelas = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$kode]);
            $kelasId = $stmt->fetchColumn();
            
            if (!$kelasId) {
                return false;
            }
            
            // Hapus dari mahasiswa_kelas
            $query = "DELETE FROM mahasiswa_kelas WHERE id_kelas = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$kelasId]);
            
            // Hapus dari kelas
            $query = "DELETE FROM kelas WHERE id_kelas = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$kelasId]);
            
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }
    
    // Fungsi untuk mata kuliah
    public function getMataKuliah() {
        $query = "SELECT kode_matkul, nama_matkul, sks, id_prodi FROM mata_kuliah";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function addMataKuliah($kode, $nama, $sks, $semester) {
        try {
            // Default prodi ID (Teknik Informatika)
            $prodiId = 1;
            
            $query = "INSERT INTO mata_kuliah (kode_matkul, nama_matkul, sks, id_prodi) VALUES (?, ?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$kode, $nama, $sks, $prodiId]);
            
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function updateMataKuliah($kode, $nama, $sks, $semester) {
        try {
            $query = "UPDATE mata_kuliah SET nama_matkul = ?, sks = ? WHERE kode_matkul = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$nama, $sks, $kode]);
            
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function deleteMataKuliah($kode) {
        try {
            $query = "DELETE FROM mata_kuliah WHERE kode_matkul = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$kode]);
            
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    
    // Fungsi untuk jadwal
    public function getJadwal() {
        $query = "SELECT mk.nama_matkul, k.nama_kelas, jk.hari, 
                 CONCAT(jk.jam_mulai, ' - ', jk.jam_selesai) as jam, jk.ruangan 
                 FROM jadwal_kuliah jk 
                 JOIN dosen_mengajar dm ON jk.id_dosen_mengajar = dm.id_dosen_mengajar 
                 JOIN mata_kuliah mk ON dm.id_matkul = mk.id_matkul 
                 JOIN kelas k ON dm.id_kelas = k.id_kelas";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function addJadwal($matkul, $kelas, $hari, $jam, $ruang) {
        try {
            $this->conn->beginTransaction();
            
            // Dapatkan id mata kuliah
            $query = "SELECT id_matkul FROM mata_kuliah WHERE nama_matkul = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$matkul]);
            $matkulId = $stmt->fetchColumn();
            
            if (!$matkulId) {
                return false;
            }
            
            // Dapatkan id kelas
            $query = "SELECT id_kelas FROM kelas WHERE nama_kelas = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$kelas]);
            $kelasId = $stmt->fetchColumn();
            
            if (!$kelasId) {
                return false;
            }
            
            // Cek apakah dosen_mengajar sudah ada
            $query = "SELECT id_dosen_mengajar FROM dosen_mengajar WHERE id_matkul = ? AND id_kelas = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$matkulId, $kelasId]);
            $dosenMengajarId = $stmt->fetchColumn();
            
            if (!$dosenMengajarId) {
                // Buat dosen_mengajar baru
                $query = "INSERT INTO dosen_mengajar (nidn_dosen, id_matkul, id_kelas) VALUES (?, ?, ?)";
                $stmt = $this->conn->prepare($query);
                $stmt->execute(['D001', $matkulId, $kelasId]); // Default dosen
                $dosenMengajarId = $this->conn->lastInsertId();
            }
            
            // Parse jam
            $jamArray = explode(' - ', $jam);
            $jamMulai = $jamArray[0] . ':00';
            $jamSelesai = $jamArray[1] . ':00';
            
            // Buat jadwal baru
            $query = "INSERT INTO jadwal_kuliah (id_dosen_mengajar, hari, jam_mulai, jam_selesai, ruangan) VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$dosenMengajarId, $hari, $jamMulai, $jamSelesai, $ruang]);
            
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }
    
    public function updateJadwal($oldMatkul, $matkul, $kelas, $hari, $jam, $ruang) {
        try {
            $this->conn->beginTransaction();
            
            // Dapatkan id mata kuliah
            $query = "SELECT id_matkul FROM mata_kuliah WHERE nama_matkul = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$matkul]);
            $matkulId = $stmt->fetchColumn();
            
            if (!$matkulId) {
                return false;
            }
            
            // Dapatkan id kelas
            $query = "SELECT id_kelas FROM kelas WHERE nama_kelas = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$kelas]);
            $kelasId = $stmt->fetchColumn();
            
            if (!$kelasId) {
                return false;
            }
            
            // Dapatkan id dosen_mengajar lama
            $query = "SELECT dm.id_dosen_mengajar 
                     FROM dosen_mengajar dm 
                     JOIN mata_kuliah mk ON dm.id_matkul = mk.id_matkul 
                     WHERE mk.nama_matkul = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$oldMatkul]);
            $oldDosenMengajarId = $stmt->fetchColumn();
            
            if (!$oldDosenMengajarId) {
                return false;
            }
            
            // Cek apakah dosen_mengajar baru sudah ada
            $query = "SELECT id_dosen_mengajar FROM dosen_mengajar WHERE id_matkul = ? AND id_kelas = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$matkulId, $kelasId]);
            $newDosenMengajarId = $stmt->fetchColumn();
            
            if (!$newDosenMengajarId) {
                // Buat dosen_mengajar baru
                $query = "INSERT INTO dosen_mengajar (nidn_dosen, id_matkul, id_kelas) VALUES (?, ?, ?)";
                $stmt = $this->conn->prepare($query);
                $stmt->execute(['D001', $matkulId, $kelasId]); // Default dosen
                $newDosenMengajarId = $this->conn->lastInsertId();
            }
            
            // Parse jam
            $jamArray = explode(' - ', $jam);
            $jamMulai = $jamArray[0] . ':00';
            $jamSelesai = $jamArray[1] . ':00';
            
            // Update jadwal
            $query = "UPDATE jadwal_kuliah SET id_dosen_mengajar = ?, hari = ?, jam_mulai = ?, jam_selesai = ?, ruangan = ? WHERE id_dosen_mengajar = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$newDosenMengajarId, $hari, $jamMulai, $jamSelesai, $ruang, $oldDosenMengajarId]);
            
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }
    
    public function deleteJadwal($matkul) {
        try {
            $this->conn->beginTransaction();
            
            // Dapatkan id dosen_mengajar
            $query = "SELECT dm.id_dosen_mengajar 
                     FROM dosen_mengajar dm 
                     JOIN mata_kuliah mk ON dm.id_matkul = mk.id_matkul 
                     WHERE mk.nama_matkul = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$matkul]);
            $dosenMengajarId = $stmt->fetchColumn();
            
            if (!$dosenMengajarId) {
                return false;
            }
            
            // Hapus jadwal
            $query = "DELETE FROM jadwal_kuliah WHERE id_dosen_mengajar = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$dosenMengajarId]);
            
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }
    
    // Helper functions
    private function getProdiIdByName($prodiName) {
        $query = "SELECT id_prodi FROM program_studi WHERE nama_prodi = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$prodiName]);
        return $stmt->fetchColumn();
    }
    
    private function createProdi($prodiName) {
        $query = "INSERT INTO program_studi (nama_prodi) VALUES (?)";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$prodiName]);
        return $this->conn->lastInsertId();
    }
    
    private function getKelasIdByName($kelasName, $prodiId) {
        $query = "SELECT id_kelas FROM kelas WHERE nama_kelas = ? AND id_prodi = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$kelasName, $prodiId]);
        return $stmt->fetchColumn();
    }
    
    private function createKelas($kelasName, $prodiId) {
        $query = "INSERT INTO kelas (nama_kelas, id_prodi, id_dosen_wali, tahun_ajaran) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$kelasName, $prodiId, 'D001', '2023/2024']); // Default dosen wali dan tahun ajaran
        return $this->conn->lastInsertId();
    }
    
    private function createUser($nama, $username, $email, $password, $role) {
        $query = "INSERT INTO pengguna (nama_lengkap, username, email, password, role) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$nama, $username, $email, $password, $role]);
        return $this->conn->lastInsertId();
    }
    
    // Fungsi untuk mendapatkan daftar mata kuliah untuk dropdown
    public function getMatkulForDropdown() {
        $query = "SELECT kode_matkul, nama_matkul FROM mata_kuliah ORDER BY nama_matkul ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Fungsi untuk mendapatkan daftar kelas untuk dropdown
    public function getKelasForDropdown() {
        $query = "SELECT k.id_kelas, k.nama_kelas, ps.nama_prodi 
                 FROM kelas k 
                 JOIN program_studi ps ON k.id_prodi = ps.id_prodi 
                 ORDER BY ps.nama_prodi, k.nama_kelas ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}