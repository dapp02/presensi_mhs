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
        try {
            $sql = "SELECT 
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
}