<?php
namespace App\Models;
use PDO;
use PDOException;


class JadwalCrudModel {
    private PDO $pdo;
    public function __construct(PDO $pdo) { $this->pdo = $pdo; }

    public function getAllWithDetails(): array {
        $sql = "SELECT jk.id_jadwal, mk.nama_matkul, k.nama_kelas, p.nama_lengkap AS nama_dosen, jk.hari, jk.jam_mulai, jk.jam_selesai, jk.ruangan
                FROM jadwal_kuliah jk
                JOIN dosen_mengajar dm ON jk.id_dosen_mengajar = dm.id_dosen_mengajar
                JOIN pengguna p ON dm.nidn_dosen = (SELECT nidn FROM dosen WHERE id_pengguna = p.id_pengguna)
                JOIN mata_kuliah mk ON dm.id_matkul = mk.id_matkul
                JOIN kelas k ON dm.id_kelas = k.id_kelas
                ORDER BY FIELD(jk.hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'), jk.jam_mulai ASC";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create(int $id_dosen_mengajar, string $hari, string $jam_mulai, string $jam_selesai, string $ruangan): bool {
        $stmt = $this->pdo->prepare("INSERT INTO jadwal_kuliah (id_dosen_mengajar, hari, jam_mulai, jam_selesai, ruangan) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$id_dosen_mengajar, $hari, $jam_mulai, $jam_selesai, $ruangan]);
    }

    public function getByIdWithDetails(int $id_jadwal): ?array {
        $sql = "SELECT jk.id_jadwal, dm.nidn_dosen AS id_dosen, mk.id_matkul, k.id_kelas, jk.hari, jk.jam_mulai, jk.jam_selesai, jk.ruangan
                FROM jadwal_kuliah jk
                JOIN dosen_mengajar dm ON jk.id_dosen_mengajar = dm.id_dosen_mengajar
                JOIN mata_kuliah mk ON dm.id_matkul = mk.id_matkul
                JOIN kelas k ON dm.id_kelas = k.id_kelas
                WHERE jk.id_jadwal = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_jadwal]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function delete(int $id_jadwal): bool {
        $stmt = $this->pdo->prepare("DELETE FROM jadwal_kuliah WHERE id_jadwal = ?");
        return $stmt->execute([$id_jadwal]);
    }

    public function update(int $id_jadwal, int $id_dosen_mengajar, string $hari, string $jam_mulai, string $jam_selesai, string $ruangan): bool {
        $stmt = $this->pdo->prepare("UPDATE jadwal_kuliah SET id_dosen_mengajar = ?, hari = ?, jam_mulai = ?, jam_selesai = ?, ruangan = ? WHERE id_jadwal = ?");
        return $stmt->execute([$id_dosen_mengajar, $hari, $jam_mulai, $jam_selesai, $ruangan, $id_jadwal]);
    }
}