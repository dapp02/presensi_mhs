<?php

namespace App\Services;

use PDO;
use App\Models\JadwalModel;
use App\Models\MahasiswaModel;
use App\Models\AbsensiModel;

// Perhatikan path 'models' dengan huruf kecil
require_once __DIR__ . '/../models/JadwalModel.php';
require_once __DIR__ . '/../models/MahasiswaModel.php';
require_once __DIR__ . '/../models/AbsensiModel.php';

class AbsensiAdminService
{
    private PDO $pdo;
    private JadwalModel $jadwalModel;
    private MahasiswaModel $mahasiswaModel;
    private AbsensiModel $absensiModel;
    private string $log_file = __DIR__ . '/../../logs/app_debug.log';

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->jadwalModel = new JadwalModel($this->pdo);
        $this->mahasiswaModel = new MahasiswaModel($this->pdo);
        $this->absensiModel = new AbsensiModel($this->pdo);
    }

    private function custom_service_log($message) {
        error_log("[" . date("Y-m-d H:i:s") . "] ABSENSI_ADMIN_SERVICE: " . $message . "\n", 3, $this->log_file);
    }

    public function prepareAbsensiPageData(int $id_jadwal, string $tanggal): array
    {
        $this->custom_service_log("Mempersiapkan data untuk halaman absensi, id_jadwal: {$id_jadwal}, tanggal: {$tanggal}");
        $data_untuk_view = ['detail_jadwal' => null, 'daftar_mahasiswa' => []];

        $detail_jadwal = $this->jadwalModel->getJadwalDetailById($id_jadwal);
        if (!$detail_jadwal) {
            $this->custom_service_log("Error: Detail jadwal tidak ditemukan.");
            return $data_untuk_view;
        }
        $data_untuk_view['detail_jadwal'] = $detail_jadwal;
        $this->custom_service_log("Detail jadwal ditemukan: " . json_encode($detail_jadwal));

        $id_kelas = $detail_jadwal['id_kelas'];
        $daftar_mahasiswa = $this->mahasiswaModel->getMahasiswaByKelas($id_kelas);
        $catatan_absensi_raw = $this->absensiModel->getAbsensiByJadwalAndTanggal($id_jadwal, $tanggal);
        
        $catatan_absensi = [];
        foreach ($catatan_absensi_raw as $absensi) {
            $catatan_absensi[$absensi['nim_mahasiswa']] = $absensi['status_kehadiran'];
        }

        foreach ($daftar_mahasiswa as &$mahasiswa) {
            $nim = $mahasiswa['nim'];
            $mahasiswa['status_kehadiran'] = $catatan_absensi[$nim] ?? null;
        }
        unset($mahasiswa);

        $data_untuk_view['daftar_mahasiswa'] = $daftar_mahasiswa;
        $this->custom_service_log("Data akhir untuk view disiapkan. Jumlah mahasiswa: " . count($daftar_mahasiswa));

        return $data_untuk_view;
    }
}