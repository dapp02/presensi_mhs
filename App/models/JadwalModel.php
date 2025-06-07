<?php

namespace App\Models;

use PDO;
use PDOException;

class JadwalModel
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Mengambil semua jadwal mengajar untuk seorang dosen pada hari tertentu.
     *
     * @param string $nidn_dosen NIDN dosen yang login.
     * @param string $nama_hari_english Nama hari dalam bahasa Inggris (contoh: 'Monday', 'Tuesday').
     * @return array Array dari associative arrays, setiap elemen mewakili satu jadwal.
     */
    public function getJadwalDosenHariIni(string $nidn_dosen, string $nama_hari_english): array
    {
        error_log("JADWAL_MODEL: getJadwalDosenHariIni dipanggil dengan NIDN: " . $nidn_dosen . ", Hari: " . $nama_hari_english, 3, __DIR__ . '/../../logs/app_debug.log');
        try {
            $sql = "SELECT 
                        jk.id_jadwal,
                        mk.nama_matkul,
                        jk.jam_mulai,
                        jk.jam_selesai,
                        k.nama_kelas,
                        jk.ruangan
                    FROM 
                        jadwal_kuliah jk
                    JOIN 
                        dosen_mengajar dm ON jk.id_dosen_mengajar = dm.id_dosen_mengajar
                    JOIN 
                        mata_kuliah mk ON dm.id_matkul = mk.id_matkul
                    JOIN 
                        kelas k ON dm.id_kelas = k.id_kelas
                    WHERE 
                        dm.nidn_dosen = :nidn_dosen AND jk.hari = :nama_hari
                    ORDER BY 
                        jk.jam_mulai ASC;";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':nidn_dosen', $nidn_dosen, PDO::PARAM_STR);
            $stmt->bindParam(':nama_hari', $nama_hari_english, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getJadwalDosenHariIni: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Mengambil semua jadwal yang pernah atau akan diajar oleh seorang dosen.
     *
     * @param string $nidn_dosen NIDN dosen yang login.
     * @return array Array dari associative arrays, setiap elemen mewakili satu jadwal.
     */
    public function getAllJadwalDosen(string $nidn_dosen): array
    {
        try {
            $sql = "SELECT 
                        jk.id_jadwal,
                        mk.nama_matkul,
                        ps.nama_prodi,
                        jk.hari,
                        jk.jam_mulai,
                        jk.jam_selesai,
                        k.nama_kelas,
                        jk.ruangan
                    FROM 
                        jadwal_kuliah jk
                    JOIN 
                        dosen_mengajar dm ON jk.id_dosen_mengajar = dm.id_dosen_mengajar
                    JOIN 
                        mata_kuliah mk ON dm.id_matkul = mk.id_matkul
                    JOIN 
                        program_studi ps ON mk.id_prodi = ps.id_prodi
                    JOIN 
                        kelas k ON dm.id_kelas = k.id_kelas
                    WHERE 
                        dm.nidn_dosen = :nidn_dosen
                    ORDER BY 
                        FIELD(jk.hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'), 
                        jk.jam_mulai ASC;";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':nidn_dosen', $nidn_dosen, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getAllJadwalDosen: " . $e->getMessage());
            return [];
        }
    }

    public function getJadwalMahasiswaHariIni(string $nim_mahasiswa, string $nama_hari_indonesia): array
    {
        $custom_log_file = __DIR__ . '/../../logs/app_debug.log';
        try {
            $query = "
                SELECT
                    jk.id_jadwal,
                    mk.nama_matkul,
                    jk.jam_mulai,
                    jk.jam_selesai,
                    jk.ruangan,
                    k.nama_kelas,
                    p_dosen.nama_lengkap AS nama_dosen
                FROM
                    mahasiswa_kelas mhs_k
                JOIN
                    kelas k ON mhs_k.id_kelas = k.id_kelas
                JOIN
                    dosen_mengajar dm ON k.id_kelas = dm.id_kelas
                JOIN
                    jadwal_kuliah jk ON dm.id_dosen_mengajar = jk.id_dosen_mengajar
                JOIN
                    mata_kuliah mk ON dm.id_matkul = mk.id_matkul
                JOIN
                    dosen d ON dm.nidn_dosen = d.nidn
                JOIN
                    pengguna p_dosen ON d.id_pengguna = p_dosen.id_pengguna
                WHERE
                    mhs_k.nim_mahasiswa = :nim_mahasiswa AND jk.hari = :nama_hari
                ORDER BY
                    jk.jam_mulai ASC
            ";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':nim_mahasiswa', $nim_mahasiswa, PDO::PARAM_STR);
            $stmt->bindParam(':nama_hari', $nama_hari_indonesia, PDO::PARAM_STR);
            $stmt->execute();
            error_log("[" . date("Y-m-d H:i:s") . "] DEBUG: Fetched jadwal for NIM: " . $nim_mahasiswa . " on day: " . $nama_hari_indonesia . PHP_EOL, 3, $custom_log_file);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("[" . date("Y-m-d H:i:s") . "] ERROR: Failed to fetch jadwal for NIM " . $nim_mahasiswa . " on day " . $nama_hari_indonesia . ": " . $e->getMessage() . PHP_EOL, 3, $custom_log_file);
            return [];
        }
    }

    public function getAllJadwalMahasiswa(string $nim_mahasiswa): array
    {
        $custom_log_file = __DIR__ . '/../../logs/app_debug.log';
        try {
            $query = "
                SELECT DISTINCT
                    jk.id_jadwal,
                    mk.nama_matkul,
                    ps.nama_prodi,
                    jk.hari,
                    jk.jam_mulai,
                    jk.jam_selesai,
                    k.nama_kelas,
                    jk.ruangan,
                    p_dosen.nama_lengkap AS nama_dosen
                FROM
                    mahasiswa_kelas mhs_k
                JOIN
                    kelas k ON mhs_k.id_kelas = k.id_kelas
                JOIN
                    dosen_mengajar dm ON k.id_kelas = dm.id_kelas
                JOIN
                    jadwal_kuliah jk ON dm.id_dosen_mengajar = jk.id_dosen_mengajar
                JOIN
                    mata_kuliah mk ON dm.id_matkul = mk.id_matkul
                JOIN
                    program_studi ps ON mk.id_prodi = ps.id_prodi
                JOIN
                    dosen d ON dm.nidn_dosen = d.nidn
                JOIN
                    pengguna p_dosen ON d.id_pengguna = p_dosen.id_pengguna
                WHERE
                    mhs_k.nim_mahasiswa = :nim_mahasiswa
                ORDER BY FIELD(jk.hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'), jk.jam_mulai ASC
            ";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':nim_mahasiswa', $nim_mahasiswa, PDO::PARAM_STR);
            $stmt->execute();
            error_log("[" . date("Y-m-d H:i:s") . "] DEBUG: Fetched all jadwal for NIM: " . $nim_mahasiswa . PHP_EOL, 3, $custom_log_file);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("[" . date("Y-m-d H:i:s") . "] ERROR: Failed to fetch all jadwal for NIM " . $nim_mahasiswa . ": " . $e->getMessage() . PHP_EOL, 3, $custom_log_file);
            return [];
        }
    }
}