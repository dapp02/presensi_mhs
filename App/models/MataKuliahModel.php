<?php
namespace App\Models;
use PDO;

class MataKuliahModel {
    private PDO $pdo;
    public function __construct(PDO $pdo) { $this->pdo = $pdo; }

    public function getAll(): array {
        $sql = "SELECT mk.id_matkul, mk.kode_matkul, mk.nama_matkul, mk.sks, ps.nama_prodi 
                FROM mata_kuliah mk 
                LEFT JOIN program_studi ps ON mk.id_prodi = ps.id_prodi 
                ORDER BY mk.nama_matkul ASC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id_matkul): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM mata_kuliah WHERE id_matkul = ?");
        $stmt->execute([$id_matkul]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function create(string $kode_matkul, string $nama_matkul, int $sks, int $id_prodi): bool {
        $stmt = $this->pdo->prepare("INSERT INTO mata_kuliah (kode_matkul, nama_matkul, sks, id_prodi) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$kode_matkul, $nama_matkul, $sks, $id_prodi]);
    }

    public function update(int $id_matkul, string $kode_matkul, string $nama_matkul, int $sks, int $id_prodi): bool {
        $stmt = $this->pdo->prepare("UPDATE mata_kuliah SET kode_matkul = ?, nama_matkul = ?, sks = ?, id_prodi = ? WHERE id_matkul = ?");
        return $stmt->execute([$kode_matkul, $nama_matkul, $sks, $id_prodi, $id_matkul]);
    }

    public function delete(int $id_matkul): bool {
        $stmt = $this->pdo->prepare("DELETE FROM mata_kuliah WHERE id_matkul = ?");
        return $stmt->execute([$id_matkul]);
    }
}