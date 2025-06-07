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

    private function custom_service_log($message) {
        $log_file_path = __DIR__ . '/../../logs/app_debug.log';
        $timestamp = date("Y-m-d H:i:s");
        error_log("[" . $timestamp . "] ADMIN_SERVICE: " . $message . PHP_EOL, 3, $log_file_path);
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
        $nama_hari_english_server = date('l'); // Misal: 'Wednesday'

        $hari_map_english_ke_indo = [
            'Monday'    => 'Senin',
            'Tuesday'   => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday'  => 'Kamis',
            'Friday'    => 'Jumat',
            'Saturday'  => 'Sabtu',
            'Sunday'    => 'Minggu'
        ];
        $nama_hari_untuk_query = $hari_map_english_ke_indo[$nama_hari_english_server] ?? $nama_hari_english_server;

        $this->custom_service_log("Hari dari date('l') (English): " . $nama_hari_english_server);
        $this->custom_service_log("Hari yang diteruskan ke JadwalModel (Indonesia): " . $nama_hari_untuk_query);

        $data_untuk_view['jadwal_dosen_hari_ini'] = $this->jadwalModel->getJadwalDosenHariIni($nidn_dosen, $nama_hari_untuk_query);

        // Siapkan tanggal yang akan ditampilkan di UI (misalnya, format "DD, Bulan YYYY")
        // Anda bisa menyesuaikan format ini sesuai kebutuhan UI
        $this->custom_service_log("Jumlah jadwal hari ini dari JadwalModel: " . count($data_untuk_view['jadwal_dosen_hari_ini']));
        if (!empty($data_untuk_view['jadwal_dosen_hari_ini'])) {
            $this->custom_service_log("Data jadwal hari ini dari JadwalModel: " . json_encode($data_untuk_view['jadwal_dosen_hari_ini'][0]));
        }

        $data_untuk_view['tanggal_hari_ini_display'] = date('d F Y');

        $data_untuk_view['semua_jadwal_dosen'] = $this->jadwalModel->getAllJadwalDosen($nidn_dosen);
        $this->custom_service_log("Jumlah semua jadwal dosen dari JadwalModel: " . count($data_untuk_view['semua_jadwal_dosen']));

        // Logika baru untuk menghasilkan array data kalender mingguan
        $today = new \DateTime(); // Tanggal server saat ini
        $this->custom_service_log("Tanggal server saat ini: " . $today->format('Y-m-d H:i:s'));

        $today_iso_day_of_week = (int)$today->format('N'); // 1 untuk Senin, 7 untuk Minggu

        // Tentukan Senin minggu ini
        $monday_this_week = clone $today;
        if ($today_iso_day_of_week > 1) {
            $monday_this_week->modify('-' . ($today_iso_day_of_week - 1) . ' days');
        }
        $this->custom_service_log("Senin minggu ini dihitung: " . $monday_this_week->format('Y-m-d'));

        $nama_hari_pendek_map = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
        // $hari_map_english_ke_indo sudah didefinisikan di atas

        $data_untuk_view['kalender_mingguan'] = [];
        $current_day_iterate = clone $monday_this_week;

        for ($i = 0; $i < 7; $i++) {
            $day_english = $current_day_iterate->format('l'); // Nama hari Inggris
            $is_today_flag = ($current_day_iterate->format('Y-m-d') === $today->format('Y-m-d'));
            
            $data_untuk_view['kalender_mingguan'][] = [
                'nama_pendek'       => $nama_hari_pendek_map[$i],
                'tanggal_angka'     => $current_day_iterate->format('d'),
                'nama_panjang_indo' => $hari_map_english_ke_indo[$day_english] ?? $day_english, // Fallback jika map tidak ada
                'is_hari_ini'       => $is_today_flag,
                'is_masa_lalu'      => $current_day_iterate < $today && !$is_today_flag,
                'is_masa_depan'     => $current_day_iterate > $today && !$is_today_flag,
                'full_date_iso'     => $current_day_iterate->format('Y-m-d') // Opsional, berguna untuk debugging/JS
            ];
            if ($i < 6) { // Hanya tambahkan hari jika belum Minggu
                $current_day_iterate->modify('+1 day');
            }
        }
        // Logging data kalender mingguan seperti yang diinstruksikan di atas
        $this->custom_service_log("Data Kalender Mingguan Dihasilkan: " . json_encode($data_untuk_view['kalender_mingguan']));

        return $data_untuk_view;
    }
}