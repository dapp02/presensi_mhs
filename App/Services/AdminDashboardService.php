<?php

namespace App\Services;

use PDO;
use PDOException;
use App\Models\JadwalModel;

require_once __DIR__ . '/../Models/JadwalModel.php';

class AdminDashboardService
{
    private PDO $pdo;
    private JadwalModel $jadwalModel;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->jadwalModel = new JadwalModel($this->pdo);
    }

    /**
     * Mengumpulkan semua data yang dibutuhkan untuk dashboard_admin.php.
     *
     * @param string $nidn_dosen NIDN dosen yang sedang login.
     * @param string $nama_lengkap_dosen Nama lengkap dosen yang login.
     * @return array Array data untuk ditampilkan di view.
     */
    public function prepareDashboardData(string $nidn_dosen, string $nama_lengkap_dosen): array
    {
        $data_untuk_view = [];

        // Data untuk Header
        $data_untuk_view['nama_dosen_header'] = $nama_lengkap_dosen;

        // Data untuk Panel Kiri (Jadwal Minggu Ini & Informasi Kelas Hari Ini)
        $nama_hari_english = date('l'); // Contoh: 'Monday', 'Tuesday'
        $data_untuk_view['jadwal_dosen_hari_ini'] = $this->jadwalModel->getJadwalDosenHariIni($nidn_dosen, $nama_hari_english);

        // Siapkan tanggal yang akan ditampilkan di UI (misalnya, format "DD, Bulan YYYY")
        // Anda bisa menyesuaikan format ini sesuai kebutuhan UI
        $data_untuk_view['tanggal_hari_ini_display'] = date('d, F Y'); // Contoh: '03, June 2025'

        // Data untuk Panel Kanan (Kartu-kartu Jadwal Dosen)
        $data_untuk_view['semua_jadwal_dosen'] = $this->jadwalModel->getAllJadwalDosen($nidn_dosen);

        return $data_untuk_view;
    }
}