<?php
header('Content-Type: application/json');
define('ROOT_PATH_FOR_API', dirname(__DIR__, 2));
require_once ROOT_PATH_FOR_API . '/auth/config/database.php';
require_once ROOT_PATH_FOR_API . '/App/models/MataKuliahModel.php';

$response = ['success' => false, 'message' => 'Invalid Request'];

try {
    $db = new Database();
    $pdo = $db->connect();
    if (!$pdo) throw new Exception("Koneksi DB Gagal.");

    $mataKuliahModel = new \App\Models\MataKuliahModel($pdo);

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $response['success'] = true;
        $response['data'] = $mataKuliahModel->getAll();
    }

} catch (Throwable $e) {
    http_response_code(500);
    $response['message'] = 'Server Error: ' . $e->getMessage();
}

echo json_encode($response);