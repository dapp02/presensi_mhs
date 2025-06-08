<?php
namespace App\Models;
use PDO;

class DosenModel {
    private PDO $pdo;
    public function __construct(PDO $pdo) { $this->pdo = $pdo; }

    public function getAllForDropdown(): array {
        // PERBAIKAN: Ubah 'd.id_dosen' menjadi 'd.nidn'
        $sql = "SELECT d.nidn, p.nama_lengkap
                FROM dosen d
                JOIN pengguna p ON d.id_pengguna = p.id_pengguna
                ORDER BY p.nama_lengkap ASC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}