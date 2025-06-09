<?php
namespace App\Models;
use PDO;

class PenggunaModel {
    private PDO $pdo;
    public function __construct(PDO $pdo) { $this->pdo = $pdo; }

    // Mengambil semua pengguna beserta NIM/NIDN jika ada
    public function getAllWithDetails(): array {
        $sql = "SELECT p.id_pengguna, p.nama_lengkap, p.username, p.email, p.role, m.nim, d.nidn
                FROM pengguna p
                LEFT JOIN mahasiswa m ON p.id_pengguna = m.id_pengguna
                LEFT JOIN dosen d ON p.id_pengguna = d.id_pengguna
                ORDER BY p.nama_lengkap ASC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Mengambil satu pengguna berdasarkan ID
    public function getByIdWithDetails(int $id_pengguna) {
        $sql = "SELECT p.id_pengguna, p.nama_lengkap, p.role, m.nim, d.nidn
                FROM pengguna p
                LEFT JOIN mahasiswa m ON p.id_pengguna = m.id_pengguna
                LEFT JOIN dosen d ON p.id_pengguna = d.id_pengguna
                WHERE p.id_pengguna = :id_pengguna";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id_pengguna' => $id_pengguna]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Menetapkan atau memperbarui NIM untuk mahasiswa
    public function assignNim(int $id_pengguna, string $nim): bool {
        // 'ON DUPLICATE KEY UPDATE' akan meng-update jika id_pengguna sudah ada,
        // atau meng-insert jika belum ada.
        $sql = "INSERT INTO mahasiswa (id_pengguna, nim) VALUES (:id_pengguna, :nim)
                ON DUPLICATE KEY UPDATE nim = :nim_update";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'id_pengguna' => $id_pengguna,
            'nim' => $nim,
            'nim_update' => $nim
        ]);
    }

    // Menetapkan atau memperbarui NIDN untuk dosen
    public function assignNidn(int $id_pengguna, string $nidn): bool {
        $sql = "INSERT INTO dosen (id_pengguna, nidn) VALUES (:id_pengguna, :nidn)
                ON DUPLICATE KEY UPDATE nidn = :nidn_update";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'id_pengguna' => $id_pengguna,
            'nidn' => $nidn,
            'nidn_update' => $nidn
        ]);
    }
}