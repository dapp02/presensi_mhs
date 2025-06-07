<?php
// Atur header untuk respons JSON
header('Content-Type: application/json');

// Path ke direktori root proyek (sesuaikan jika struktur Anda berbeda)
define('ROOT_PATH_FOR_API', dirname(__DIR__, 2)); // Naik dua level dari App/Api ke presensi_mhs/

require_once ROOT_PATH_FOR_API . '/auth/config/database.php';
require_once ROOT_PATH_FOR_API . '/App/models/JadwalModel.php'; // Menggunakan JadwalModel yang sudah ada

// Definisikan path ke file log kustom dari lokasi API ini
$custom_log_file_api = ROOT_PATH_FOR_API . '/logs/app_debug.log';

function api_error_log($message, $log_file) {
    $timestamp = date("Y-m-d H:i:s");
    error_log("[" . $timestamp . "] API_GET_JADWAL: " . $message . PHP_EOL, 3, $log_file);
}

$response = ['success' => false, 'data' => [], 'message' => ''];

// 1. Ambil dan Validasi Parameter GET
$nidn_dosen = $_GET['nidn'] ?? null;
$nama_hari = $_GET['nama_hari'] ?? null; // Ini seharusnya sudah dalam Bahasa Indonesia (misal "Jumat")

api_error_log("Request diterima. NIDN: " . ($nidn_dosen ?? 'NULL') . ", Hari: " . ($nama_hari ?? 'NULL'), $custom_log_file_api);

if (empty($nidn_dosen) || empty($nama_hari)) {
    $response['message'] = 'Parameter NIDN dan nama_hari dibutuhkan.';
    api_error_log("Error: Parameter NIDN atau nama_hari kosong. " . json_encode($_GET), $custom_log_file_api);
    echo json_encode($response);
    exit;
}

// (Opsional) Sanitasi lebih lanjut jika diperlukan, meskipun bindParam sudah membantu
// $nidn_dosen = filter_var($nidn_dosen, FILTER_SANITIZE_STRING);
// $nama_hari = filter_var($nama_hari, FILTER_SANITIZE_STRING);

// Daftar hari yang valid (sesuai ENUM di DB dan map Anda)
$hari_valid = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
if (!in_array($nama_hari, $hari_valid)) {
    $response['message'] = 'Parameter nama_hari tidak valid.';
    api_error_log("Error: Parameter nama_hari tidak valid: " . $nama_hari, $custom_log_file_api);
    echo json_encode($response);
    exit;
}

// 2. Inisialisasi Koneksi Database dan Model
try {
    $db_instance = new Database();
    $pdo_connection = $db_instance->connect();

    if (!$pdo_connection) {
        throw new Exception("Koneksi database gagal.");
    }

    // Karena JadwalModel ada di namespace App\Models, gunakan FQN
    $jadwalModel = new \App\Models\JadwalModel($pdo_connection); //

    // 3. Panggil Metode Model
    // getJadwalDosenHariIni menerima NIDN dan nama hari (sudah Bahasa Indonesia)
    $jadwal_data = $jadwalModel->getJadwalDosenHariIni($nidn_dosen, $nama_hari); //

    $response['success'] = true;
    $response['data'] = $jadwal_data;
    if (empty($jadwal_data)) {
        $response['message'] = 'Tidak ada jadwal ditemukan untuk hari ini.';
    } else {
        $response['message'] = 'Jadwal berhasil diambil.';
    }
    api_error_log("Sukses: Data jadwal diambil. Jumlah: " . count($jadwal_data) . ". Data: " . json_encode($jadwal_data), $custom_log_file_api);

} catch (PDOException $e) {
    $response['message'] = "Error database: " . $e->getMessage();
    api_error_log("PDOException: " . $e->getMessage(), $custom_log_file_api);
} catch (Exception $e) {
    $response['message'] = "Error: " . $e->getMessage();
    api_error_log("Exception: " . $e->getMessage(), $custom_log_file_api);
}

// 4. Kembalikan Respons JSON
echo json_encode($response);
exit;
?>