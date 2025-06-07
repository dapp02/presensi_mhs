<?php

namespace App\Models;
use PDO;
use PDOException;

class ProdiModel {
    private PDO $pdo;
    public function __construct(PDO $pdo) { $this->pdo = $pdo; }

    public function getAll(): array {
        $stmt = $this->pdo->query("SELECT id_prodi, nama_prodi FROM program_studi ORDER BY nama_prodi ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create(string $nama_prodi): bool {
        $stmt = $this->pdo->prepare("INSERT INTO program_studi (nama_prodi) VALUES (?)");
        return $stmt->execute([$nama_prodi]);
    }

    public function getProdiById(int $id_prodi): ?array {
        $stmt = $this->pdo->prepare("SELECT id_prodi, nama_prodi FROM program_studi WHERE id_prodi = ?");
        $stmt->execute([$id_prodi]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function update(int $id_prodi, string $nama_prodi): bool {
        $stmt = $this->pdo->prepare("UPDATE program_studi SET nama_prodi = ? WHERE id_prodi = ?");
        return $stmt->execute([$nama_prodi, $id_prodi]);
    }

    public function delete(int $id_prodi): bool {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM program_studi WHERE id_prodi = ?");
            return $stmt->execute([$id_prodi]);
        } catch (\PDOException $e) {
            // Menangkap error jika prodi digunakan sebagai foreign key di tabel lain
            return false;
        }
    }
}

    // Metode CRUD akan ditambahkan di sini

?>