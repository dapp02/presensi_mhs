<?php
header('Content-Type: application/json');
define('ROOT_PATH_FOR_API', dirname(__DIR__, 2));
require_once ROOT_PATH_FOR_API . '/auth/config/database.php';
require_once ROOT_PATH_FOR_API . '/App/models/PenggunaModel.php';

$response = ['success' => false, 'message' => 'Invalid Request'];

try {
    $db = new Database();
    $pdo = $db->connect();
    $penggunaModel = new \App\Models\PenggunaModel($pdo);

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $action = $_GET['action'] ?? 'get_all';
        if ($action === 'get_all') {
            $response['success'] = true;
            $response['data'] = $penggunaModel->getAllWithDetails();
        } elseif ($action === 'get_by_id' && isset($_GET['id'])) {
            $response['success'] = true;
            $response['data'] = $penggunaModel->getByIdWithDetails((int)$_GET['id']);
        }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        $action = $data['action'] ?? '';

        if ($action === 'assign_role' && isset($data['id_pengguna'], $data['role'], $data['id_number'])) {
            $id_pengguna = (int)$data['id_pengguna'];
            $id_number = $data['id_number'];

            if ($data['role'] === 'mahasiswa') {
                if ($penggunaModel->assignNim($id_pengguna, $id_number)) {
                    $response = ['success' => true, 'message' => 'NIM berhasil disimpan.'];
                } else {
                    $response['message'] = 'Gagal menyimpan NIM.';
                }
            } elseif ($data['role'] === 'dosen') {
                if ($penggunaModel->assignNidn($id_pengguna, $id_number)) {
                    $response = ['success' => true, 'message' => 'NIDN berhasil disimpan.'];
                } else {
                    $response['message'] = 'Gagal menyimpan NIDN.';
                }
            } else {
                $response['message'] = 'Peran tidak valid.';
            }
        }
    }
} catch (Throwable $e) {
    $response['message'] = 'Server Error: ' . $e->getMessage();
}

echo json_encode($response);