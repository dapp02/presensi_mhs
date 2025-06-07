<?php

namespace App\Models;

use PDO;
use PDOException;

class MahasiswaModel
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

public function getMahasiswaByKelas(int $id_kelas): array
{
    $log_file = __DIR__ . '/../../logs/app_debug.log';
    try {
        // PERBAIKAN: Query ini sekarang mengambil p.nama_lengkap dari tabel pengguna
        $sql = "SELECT m.nim, p.nama_lengkap
                FROM mahasiswa_kelas mk
                JOIN mahasiswa m ON mk.nim_mahasiswa = m.nim
                JOIN pengguna p ON m.id_pengguna = p.id_pengguna
                WHERE mk.id_kelas = :id_kelas
                ORDER BY p.nama_lengkap ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_kelas', $id_kelas, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        error_log("[" . date("Y-m-d H:i:s") . "] MAHASISWA_MODEL: getMahasiswaByKelas menemukan " . count($result) . " mahasiswa untuk id_kelas: {$id_kelas}\n", 3, $log_file);
        return $result;
    } catch (PDOException $e) {
        error_log("[" . date("Y-m-d H:i:s") . "] MAHASISWA_MODEL_ERROR: di getMahasiswaByKelas: " . $e->getMessage() . "\n", 3, $log_file);
        return [];
    }
}
}