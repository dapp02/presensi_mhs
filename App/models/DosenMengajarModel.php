<?php
namespace App\Models;
use PDO;

class DosenMengajarModel {
    private PDO $pdo;
    public function __construct(PDO $pdo) { $this->pdo = $pdo; }

    public function findOrCreate(string $nidn_dosen, int $id_matkul, int $id_kelas): int {
        // Cek apakah sudah ada
        $stmt = $this->pdo->prepare("SELECT id_dosen_mengajar FROM dosen_mengajar WHERE nidn_dosen = ? AND id_matkul = ? AND id_kelas = ?");
        $stmt->execute([$nidn_dosen, $id_matkul, $id_kelas]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            return (int)$existing['id_dosen_mengajar'];
        }

        // Jika tidak ada, buat baru
        $stmt_insert = $this->pdo->prepare("INSERT INTO dosen_mengajar (nidn_dosen, id_matkul, id_kelas) VALUES (?, ?, ?)");
        $stmt_insert->execute([$nidn_dosen, $id_matkul, $id_kelas]);
        return (int)$this->pdo->lastInsertId();
    }
}