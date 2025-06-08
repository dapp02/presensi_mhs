<?php
header('Content-Type: application/json');
define('ROOT_PATH_FOR_API', dirname(__DIR__, 2));

// Pastikan semua require_once benar
require_once ROOT_PATH_FOR_API . '/auth/config/database.php';
require_once ROOT_PATH_FOR_API . '/App/models/DosenModel.php';

$response = ['success' => false, 'message' => 'Invalid request.'];

try {
    $db_instance = new Database();
    $pdo_connection = $db_instance->connect();
    if (!$pdo_connection) {
        throw new Exception("Koneksi DB Gagal.");
    }

    $dosenModel = new \App\Models\DosenModel($pdo_connection);
    
    // API ini hanya perlu melakukan satu hal: mendapatkan semua dosen untuk dropdown
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $response['success'] = true;
        $response['data'] = $dosenModel->getAllForDropdown();
    } else {
        $response['message'] = 'Metode request tidak didukung.';
    }

} catch (Throwable $e) {
    http_response_code(500); // Server Error
    $response['success'] = false; // <-- PENTING
    $response['message'] = 'Terjadi error di server: ' . $e->getMessage();
}

echo json_encode($response);
?>