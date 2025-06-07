<?php
// Atur header untuk respons JSON
header('Content-Type: application/json');

// Definisikan ROOT_PATH untuk akses mudah ke file lain
define('ROOT_PATH_FOR_MAHASISWA_API', dirname(__DIR__, 2)); // Naik dua level dari App/Api/ ke presensi_mhs/

require_once ROOT_PATH_FOR_MAHASISWA_API . '/auth/config/database.php';
require_once ROOT_PATH_FOR_MAHASISWA_API . '/App/Models/JadwalModel.php'; // Menggunakan JadwalModel yang ada

// Definisikan path ke file log kustom dari lokasi API ini
$custom_log_file_api_mhs = ROOT_PATH_FOR_MAHASISWA_API . '/logs/app_debug.log';

function api_mhs_error_log($message, $log_file) {
    $timestamp = date("Y-m-d H:i:s");
    error_log("[" . $timestamp . "] API_GET_JADWAL_MHS: " . $message . PHP_EOL, 3, $log_file);
}

$response = ['success' => false, 'data' => [], 'message' => ''];

// 1. Ambil dan Validasi Parameter GET
$nim_mahasiswa = $_GET['nim'] ?? null;
$nama_hari = $_GET['nama_hari'] ?? null; // Ini seharusnya sudah dalam Bahasa Indonesia (misal "Jumat")
$tanggal = $_GET['tanggal'] ?? null; // Tambahkan ini

api_mhs_error_log("Request diterima. NIM: " . ($nim_mahasiswa ?? 'NULL') . ", Hari: " . ($nama_hari ?? 'NULL'), $custom_log_file_api_mhs);

if (empty($nim_mahasiswa) || empty($nama_hari) || empty($tanggal)) {
    $response['message'] = 'Parameter NIM, nama_hari, dan tanggal dibutuhkan.';
    api_mhs_error_log("Error: Parameter NIM, nama_hari, atau tanggal kosong. " . json_encode($_GET), $custom_log_file_api_mhs);
    echo json_encode($response);
    exit;
}

// (Opsional) Sanitasi lebih lanjut jika diperlukan. filter_var mungkin lebih baik dari FILTER_SANITIZE_STRING.
// $nim_mahasiswa = filter_var($nim_mahasiswa, FILTER_SANITIZE_STRING);
// $nama_hari = filter_var($nama_hari, FILTER_SANITIZE_STRING);

// Daftar hari yang valid (sesuai ENUM di DB dan map yang digunakan)
$hari_valid = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
if (!in_array($nama_hari, $hari_valid)) {
    $response['message'] = 'Parameter nama_hari tidak valid: ' . htmlspecialchars($nama_hari);
    api_mhs_error_log("Error: Parameter nama_hari tidak valid: " . $nama_hari, $custom_log_file_api_mhs);
    echo json_encode($response);
    exit;
}

// Validasi tambahan untuk tanggal
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
    $response['message'] = 'Parameter tanggal dengan format YYYY-MM-DD dibutuhkan.';
    api_mhs_error_log("Error: Parameter tanggal tidak valid atau kosong: " . ($tanggal ?? 'NULL'), $custom_log_file_api_mhs);
    echo json_encode($response);
    exit;
}

// 2. Inisialisasi Koneksi Database dan Model
try {
    $db_instance = new Database();
    $pdo_connection = $db_instance->connect();

    if (!$pdo_connection) {
        // Ini seharusnya tidak terjadi jika Database::connect() menangani error dengan die() atau exception
        throw new \RuntimeException("Koneksi database gagal via API mahasiswa.");
    }

    // JadwalModel ada di namespace App\Models
    $jadwalModel = new \App\Models\JadwalModel($pdo_connection);

    // 3. Panggil Metode Model
    // getJadwalMahasiswaHariIni menerima NIM, nama hari (sudah Bahasa Indonesia), dan tanggal
    $jadwal_data = $jadwalModel->getJadwalMahasiswaHariIni($nim_mahasiswa, $nama_hari, $tanggal);

    $response['success'] = true;
    $response['data'] = $jadwal_data;
    if (empty($jadwal_data)) {
        $response['message'] = 'Tidak ada jadwal kuliah ditemukan untuk hari ' . htmlspecialchars($nama_hari) . '.';
    } else {
        $response['message'] = 'Jadwal berhasil diambil.';
    }
    api_mhs_error_log("Sukses: Data jadwal mahasiswa diambil. NIM: {$nim_mahasiswa}, Hari: {$nama_hari}, Jumlah: " . count($jadwal_data), $custom_log_file_api_mhs);
    // api_mhs_error_log("Data: " . json_encode($jadwal_data), $custom_log_file_api_mhs); // Log data jika perlu

} catch (\PDOException $e) {
    $response['message'] = "Error database: Terjadi masalah saat mengambil data."; // Pesan generik untuk user
    api_mhs_error_log("PDOException: " . $e->getMessage(), $custom_log_file_api_mhs);
} catch (\Throwable $e) { // Menangkap semua jenis error lainnya
    $response['message'] = "Error sistem: Terjadi kesalahan tidak terduga."; // Pesan generik
    api_mhs_error_log("Throwable: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine(), $custom_log_file_api_mhs);
}

// 4. Kembalikan Respons JSON
echo json_encode($response);
exit;
?>