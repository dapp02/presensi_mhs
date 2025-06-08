<?php
namespace App\Models;
use PDO;
use PDOException;

class CrudMahasiswaModel {
    private PDO $pdo;
    public function __construct(PDO $pdo) { $this->pdo = $pdo; }

    public function getAllMahasiswaForCrud(): array {
        try {
            $sql = "SELECT 
                        m.nim, 
                        p.nama_lengkap, 
                        ps.nama_prodi, 
                        k.nama_kelas,
                        k.id_kelas
                    FROM mahasiswa m
                    JOIN pengguna p ON m.id_pengguna = p.id_pengguna
                    INNER JOIN mahasiswa_kelas mk ON m.nim = mk.nim_mahasiswa
                    INNER JOIN kelas k ON mk.id_kelas = k.id_kelas
                    LEFT JOIN program_studi ps ON k.id_prodi = ps.id_prodi
                    ORDER BY p.nama_lengkap ASC";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error di CrudMahasiswaModel::getAllMahasiswaForCrud: " . $e->getMessage());
            return [];
        }
    }

    public function getAllMahasiswaSimple(): array {
        try {
            $sql = "SELECT m.nim, p.nama_lengkap FROM mahasiswa m JOIN pengguna p ON m.id_pengguna = p.id_pengguna ORDER BY p.nama_lengkap ASC";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error di CrudMahasiswaModel::getAllMahasiswaSimple: " . $e->getMessage());
            return [];
        }
    }
    public function assignMahasiswaToKelas(string $nim, int $id_kelas): bool
    {
        try {
            $this->pdo->beginTransaction();

            // Check if the student (NIM) exists
            $stmtCheckMahasiswa = $this->pdo->prepare("SELECT COUNT(*) FROM mahasiswa WHERE nim = ?");
            $stmtCheckMahasiswa->execute([$nim]);
            if ($stmtCheckMahasiswa->fetchColumn() == 0) {
                $this->pdo->rollBack();
                error_log("Error di CrudMahasiswaModel::assignMahasiswaToKelas: Mahasiswa dengan NIM " . $nim . " tidak ditemukan.");
                return false; // Mahasiswa not found
            }

            // Check if the assignment already exists
            $stmtCheckAssignment = $this->pdo->prepare("SELECT COUNT(*) FROM mahasiswa_kelas WHERE nim_mahasiswa = ? AND id_kelas = ?");
            $stmtCheckAssignment->execute([$nim, $id_kelas]);
            if ($stmtCheckAssignment->fetchColumn() > 0) {
                $this->pdo->rollBack();
                error_log("Error di CrudMahasiswaModel::assignMahasiswaToKelas: Mahasiswa dengan NIM " . $nim . " sudah terdaftar di kelas " . $id_kelas . ".");
                return false; // Assignment already exists
            }

            // Assign student to class
            $stmtMahasiswaKelas = $this->pdo->prepare("INSERT INTO mahasiswa_kelas (nim_mahasiswa, id_kelas) VALUES (?, ?)");
            $stmtMahasiswaKelas->execute([$nim, $id_kelas]);

            $this->pdo->commit();
            return true;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Error di CrudMahasiswaModel::assignMahasiswaToKelas: " . $e->getMessage());
            return false;
        }
    }

    public function updateMahasiswa(string $nim, string $nama_lengkap, ?int $id_prodi, ?int $id_kelas, ?string $password = null): bool
    {
        try {
            $this->pdo->beginTransaction();

            // Dapatkan id_pengguna dari nim mahasiswa
            $stmtGetIdPengguna = $this->pdo->prepare("SELECT id_pengguna FROM mahasiswa WHERE nim = ?");
            $stmtGetIdPengguna->execute([$nim]);
            $id_pengguna = $stmtGetIdPengguna->fetchColumn();

            if (!$id_pengguna) {
                $this->pdo->rollBack();
                return false; // Mahasiswa tidak ditemukan
            }

            // 1. Update tabel pengguna
            $sqlPengguna = "UPDATE pengguna SET nama_lengkap = ?";
            $paramsPengguna = [$nama_lengkap];
            if ($password !== null && $password !== '') {
                $sqlPengguna .= ", password = ?";
                $paramsPengguna[] = password_hash($password, PASSWORD_DEFAULT);
            }
            $sqlPengguna .= " WHERE id_pengguna = ?";
            $paramsPengguna[] = $id_pengguna;

            $stmtPengguna = $this->pdo->prepare($sqlPengguna);
            $stmtPengguna->execute($paramsPengguna);

            // 2. Update atau insert di mahasiswa_kelas
            // Hapus entri lama jika ada
            $stmtDeleteMahasiswaKelas = $this->pdo->prepare("DELETE FROM mahasiswa_kelas WHERE nim_mahasiswa = ?");
            $stmtDeleteMahasiswaKelas->execute([$nim]);

            // Tambahkan entri baru jika id_kelas diberikan
            if ($id_kelas !== null) {
                $stmtInsertMahasiswaKelas = $this->pdo->prepare("INSERT INTO mahasiswa_kelas (nim_mahasiswa, id_kelas) VALUES (?, ?)");
                $stmtInsertMahasiswaKelas->execute([$nim, $id_kelas]);
            }

            $this->pdo->commit();
            return true;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Error di CrudMahasiswaModel::updateMahasiswa: " . $e->getMessage());
            return false;
        }
    }

    public function removeMahasiswaFromKelas(string $nim, int $id_kelas): bool
    {
        try {
            $this->pdo->beginTransaction();

            // Delete from mahasiswa_kelas
            $stmtDeleteMahasiswaKelas = $this->pdo->prepare("DELETE FROM mahasiswa_kelas WHERE nim_mahasiswa = ? AND id_kelas = ?");
            $stmtDeleteMahasiswaKelas->execute([$nim, $id_kelas]);

            $this->pdo->commit();
            return true;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Error di CrudMahasiswaModel::removeMahasiswaFromKelas: " . $e->getMessage());
            return false;
        }
    }

    public function getMahasiswaById(string $nim): ?array
    {
        try {
            $sql = "SELECT 
                        m.nim, 
                        p.nama_lengkap, 
                        ps.id_prodi, 
                        k.id_kelas, 
                        p.password
                    FROM mahasiswa m
                    JOIN pengguna p ON m.id_pengguna = p.id_pengguna
                    INNER JOIN mahasiswa_kelas mk ON m.nim = mk.nim_mahasiswa
                    INNER JOIN kelas k ON mk.id_kelas = k.id_kelas
                    LEFT JOIN program_studi ps ON k.id_prodi = ps.id_prodi
                    WHERE m.nim = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$nim]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            error_log("Error di CrudMahasiswaModel::getMahasiswaById: " . $e->getMessage());
            return null;
        }
    }
}