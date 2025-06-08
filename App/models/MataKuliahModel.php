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
    // Metode CRUD lain akan ditambahkan nanti 
}