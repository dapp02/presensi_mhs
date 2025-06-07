<?php
header('Content-Type: application/json');
define('ROOT_PATH_FOR_API_DOSEN', dirname(__DIR__, 2));

require_once ROOT_PATH_FOR_API_DOSEN . '/auth/config/database.php';
require_once ROOT_PATH_FOR_API_DOSEN . '/App/models/AbsensiModel.php';

$log_file = ROOT_PATH_FOR_API_DOSEN . '/logs/app_debug.log';

function api_dosen_absen_log($message, $log_file) {
    error_log("[" . date("Y-m-d H:i:s") . "] API_SUBMIT_ABSENSI_DOSEN: " . $message . "\n", 3, $log_file);
}

$response = ['success' => false, 'message' => 'Permintaan tidak valid.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_jadwal = $_POST['id_jadwal'] ?? null;
    $tanggal_absensi = $_POST['tanggal_absensi'] ?? null;
    $statuses = $_POST['status'] ?? [];

    api_dosen_absen_log("Request diterima. id_jadwal: {$id_jadwal}, tanggal: {$tanggal_absensi}, jumlah status: " . count($statuses), $log_file);

    if (empty($id_jadwal) || empty($tanggal_absensi) || empty($statuses)) {
        $response['message'] = 'Data form tidak lengkap.';
        api_dosen_absen_log("Error: Data form tidak lengkap. POST data: " . json_encode($_POST), $log_file);
        echo json_encode($response);
        exit;
    }

    try {
        $db_instance = new Database();
        $pdo_connection = $db_instance->connect();
        $absensiModel = new \App\Models\AbsensiModel($pdo_connection);

        $berhasil = 0;
        $gagal = 0;

        foreach ($statuses as $nim => $status) {
            if ($absensiModel->saveOrUpdateAbsensi($id_jadwal, $nim, $tanggal_absensi, $status)) {
                $berhasil++;
            } else {
                $gagal++;
                api_dosen_absen_log("Gagal menyimpan untuk NIM: {$nim}", $log_file);
            }
        }

        if ($gagal > 0) {
             $response['message'] = "{$berhasil} data absensi berhasil disimpan, namun {$gagal} data gagal.";
        } else {
             $response['success'] = true;
             $response['message'] = "Semua data absensi ({$berhasil} mahasiswa) berhasil disimpan.";
        }
        api_dosen_absen_log("Proses selesai. Berhasil: {$berhasil}, Gagal: {$gagal}", $log_file);

    } catch (Exception $e) {
        $response['message'] = "Error sistem: " . $e->getMessage();
        api_dosen_absen_log("Exception: " . $e->getMessage(), $log_file);
    }
} else {
    $response['message'] = 'Metode request tidak valid. Hanya POST yang diizinkan.';
}

echo json_encode($response);
exit;
?>