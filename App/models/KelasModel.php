<?php
namespace App\Models;
use PDO;
use PDOException;

class KelasModel {
    private PDO $pdo;
    public function __construct(PDO $pdo) { $this->pdo = $pdo; }

    public function getAll(): array {
        $sql = "SELECT k.id_kelas, k.nama_kelas, k.id_prodi, ps.nama_prodi, p_dosen.nama_lengkap as nama_dosen_wali, k.tahun_ajaran 
                 FROM kelas k 
                 LEFT JOIN program_studi ps ON k.id_prodi = ps.id_prodi 
                 LEFT JOIN dosen d ON k.id_dosen_wali = d.nidn 
                 LEFT JOIN pengguna p_dosen ON d.id_pengguna = p_dosen.id_pengguna 
                 ORDER BY k.nama_kelas ASC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id_kelas): ?array {
        $sql = "SELECT id_kelas, nama_kelas, id_prodi, id_dosen_wali, tahun_ajaran FROM kelas WHERE id_kelas = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_kelas]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function getAllForDropdown(): array {
        try {
            // Query yang jauh lebih sederhana dan cepat
            $sql = "SELECT id_kelas, nama_kelas FROM kelas ORDER BY nama_kelas ASC";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error di KelasModel::getAllForDropdown: " . $e->getMessage());
            return [];
        }
    }

    public function create(string $nama_kelas, int $id_prodi, string $nidn_dosen_wali, string $tahun_ajaran): bool {
        $sql = "INSERT INTO kelas (nama_kelas, id_prodi, id_dosen_wali, tahun_ajaran) VALUES (?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$nama_kelas, $id_prodi, $nidn_dosen_wali, $tahun_ajaran]);
    }

    public function update(int $id_kelas, string $nama_kelas, int $id_prodi, string $nidn_dosen_wali, string $tahun_ajaran): bool {
        $sql = "UPDATE kelas SET nama_kelas = ?, id_prodi = ?, id_dosen_wali = ?, tahun_ajaran = ? WHERE id_kelas = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$nama_kelas, $id_prodi, $nidn_dosen_wali, $tahun_ajaran, $id_kelas]);
    }

    public function delete(int $id_kelas): bool {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM kelas WHERE id_kelas = ?");
            return $stmt->execute([$id_kelas]);
        } catch (\PDOException $e) { return false; }
    }
}