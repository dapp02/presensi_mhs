<?php

namespace App\Models;

use PDO;
use PDOException;

class AbsensiModel {
    private $conn;
    private $table_name = "absensi";

    public $id;
    public $nim;
    public $id_jadwal;
    public $status_kehadiran;
    public $tanggal_absensi;
    public $waktu_absen;
    public $keterangan; // Add this line

    public function __construct($db) {
        $this->conn = $db;
    }

    // Remove the submitAbsensi method as its logic is now handled in the API
    // public function submitAbsensi() {
    //     // Check if an entry for this nim, id_jadwal, and tanggal_absensi already exists
    //     $query = "SELECT id FROM " . $this->table_name . " WHERE nim = :nim AND id_jadwal = :id_jadwal AND tanggal_absensi = :tanggal_absensi LIMIT 0,1";
    //     $stmt = $this->conn->prepare($query);
    //
    //     $this->nim = htmlspecialchars(strip_tags($this->nim));
    //     $this->id_jadwal = htmlspecialchars(strip_tags($this->id_jadwal));
    //     $this->tanggal_absensi = htmlspecialchars(strip_tags($this->tanggal_absensi));
    // 
    //     $stmt->bindParam(':nim', $this->nim);
    //     $stmt->bindParam(':id_jadwal', $this->id_jadwal);
    //     $stmt->bindParam(':tanggal_absensi', $this->tanggal_absensi);
    //     $stmt->execute();
    // 
    //     $existing_entry = $stmt->fetch(PDO::FETCH_ASSOC);
    // 
    //     if ($existing_entry) {
    //         // Update existing entry
    //         $query = "UPDATE " . $this->table_name . "
    //                   SET status_kehadiran = :status_kehadiran, waktu_absen = CURRENT_TIMESTAMP
    //                   WHERE id = :id";
    //         $stmt = $this->conn->prepare($query);
    // 
    //         $this->status_kehadiran = htmlspecialchars(strip_tags($this->status_kehadiran));
    //         $this->id = $existing_entry['id'];
    // 
    //         $stmt->bindParam(':status_kehadiran', $this->status_kehadiran);
    //         $stmt->bindParam(':id', $this->id);
    // 
    //         if ($stmt->execute()) {
    //             return true;
    //         }
    //     } else {
    //         // Insert new entry
    //         $query = "INSERT INTO " . $this->table_name . "
    //                   SET
    //                       nim = :nim,
    //                       id_jadwal = :id_jadwal,
    //                       status_kehadiran = :status_kehadiran,
    //                       tanggal_absensi = :tanggal_absensi,
    //                       waktu_absen = CURRENT_TIMESTAMP";
    // 
    //         $stmt = $this->conn->prepare($query);
    // 
    //         $this->nim = htmlspecialchars(strip_tags($this->nim));
    //         $this->id_jadwal = htmlspecialchars(strip_tags($this->id_jadwal));
    //         $this->status_kehadiran = htmlspecialchars(strip_tags($this->status_kehadiran));
    //         $this->tanggal_absensi = htmlspecialchars(strip_tags($this->tanggal_absensi));
    // 
    //         $stmt->bindParam(':nim', $this->nim);
    //         $stmt->bindParam(':id_jadwal', $this->id_jadwal);
    //         $stmt->bindParam(':status_kehadiran', $this->status_kehadiran);
    //         $stmt->bindParam(':tanggal_absensi', $this->tanggal_absensi);
    // 
    //         if ($stmt->execute()) {
    //             return true;
    //         }
    //     }
    // 
    //     return false;
    // }

    public function getAbsensiStatus($nim, $id_jadwal, $tanggal_absensi) {
        $query = "SELECT status_kehadiran FROM " . $this->table_name . " WHERE nim = :nim AND id_jadwal = :id_jadwal AND tanggal_absensi = :tanggal_absensi LIMIT 0,1";
        $stmt = $this->conn->prepare($query);

        $nim = htmlspecialchars(strip_tags($nim));
        $id_jadwal = htmlspecialchars(strip_tags($id_jadwal));
        $tanggal_absensi = htmlspecialchars(strip_tags($tanggal_absensi));

        $stmt->bindParam(':nim', $nim);
        $stmt->bindParam(':id_jadwal', $id_jadwal);
        $stmt->bindParam(':tanggal_absensi', $tanggal_absensi);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $row['status_kehadiran'] : null;
    }

    public function getAbsensiByJadwalAndTanggal($idJadwal, $tanggal)
    {
        // PERBAIKAN: Ubah 'status_absensi' menjadi 'status_kehadiran'
        $query = "SELECT nim_mahasiswa, status_kehadiran FROM " . $this->table_name . " WHERE id_jadwal = :id_jadwal AND tanggal_absensi = :tanggal";
        $stmt = $this->conn->prepare($query);

        $idJadwal = htmlspecialchars(strip_tags($idJadwal));
        $tanggal = htmlspecialchars(strip_tags($tanggal));

        $stmt->bindParam(':id_jadwal', $idJadwal);
        $stmt->bindParam(':tanggal', $tanggal);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function saveOrUpdateAbsensi(int $id_jadwal, string $nim, string $tanggal, string $status): bool
    {
        $log_file = __DIR__ . '/../../logs/app_debug.log';
        try {
            // Gunakan ON DUPLICATE KEY UPDATE untuk efisiensi
            $sql = "INSERT INTO absensi (id_jadwal, nim_mahasiswa, tanggal_absensi, status_kehadiran)
                    VALUES (:id_jadwal, :nim_mahasiswa, :tanggal_absensi, :status_kehadiran)
                    ON DUPLICATE KEY UPDATE
                    status_kehadiran = VALUES(status_kehadiran),
                    updated_at = CURRENT_TIMESTAMP";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id_jadwal', $id_jadwal, PDO::PARAM_INT);
            $stmt->bindParam(':nim_mahasiswa', $nim, PDO::PARAM_STR);
            $stmt->bindParam(':tanggal_absensi', $tanggal, PDO::PARAM_STR);
            $stmt->bindParam(':status_kehadiran', $status, PDO::PARAM_STR);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("[" . date("Y-m-d H:i:s") . "] ABSENSI_MODEL_ERROR: di saveOrUpdateAbsensi: " . $e->getMessage() . "\n", 3, $log_file);
            return false;
        }
    }
}
?>