<?php
header('Content-Type: application/json');
define('ROOT_PATH_FOR_API', dirname(__DIR__, 2));
require_once ROOT_PATH_FOR_API . '/auth/config/database.php';
require_once ROOT_PATH_FOR_API . '/App/models/KelasModel.php';
require_once ROOT_PATH_FOR_API . '/App/models/ProdiModel.php';
require_once ROOT_PATH_FOR_API . '/App/models/DosenModel.php';

$response = ['success' => false, 'message' => 'Permintaan Awal Tidak Valid'];

try {
    $db = new Database();
    $pdo = $db->connect();
    if (!$pdo) throw new Exception("Koneksi DB Gagal.");

    $method = $_SERVER['REQUEST_METHOD'];

    if ($method === 'GET') {
        $action = $_GET['action'] ?? 'get_all'; // Default action adalah get_all

        if ($action === 'get_all') {
            $kelasModel = new \App\Models\KelasModel($pdo);
            $response['success'] = true;
            $response['data'] = $kelasModel->getAll();
        } elseif ($action === 'get_form_data') {
            $prodiModel = new \App\Models\ProdiModel($pdo);
            $dosenModel = new \App\Models\DosenModel($pdo);
            $response['success'] = true;
            $response['data'] = [
                'prodi' => $prodiModel->getAll(),
                'dosen' => $dosenModel->getAllForDropdown()
            ];
        } elseif ($action === 'get_by_id' && isset($_GET['id'])) {
            $kelasModel = new \App\Models\KelasModel($pdo);
            $kelasData = $kelasModel->getById((int)$_GET['id']);
            if ($kelasData) {
                $response['success'] = true;
                $response['data'] = $kelasData;
            } else {
                $response['message'] = 'Kelas dengan ID yang diberikan tidak ditemukan.';
            }
        } else {
            $response['message'] = 'Aksi GET tidak valid.';
        }

    } elseif ($method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        $action = $data['action'] ?? '';

        $kelasModel = new \App\Models\KelasModel($pdo);

        switch ($action) {
            case 'create':
                if ($kelasModel->create($data['data']['nama_kelas'], $data['data']['id_prodi'], $data['data']['id_dosen_wali'], $data['data']['tahun_ajaran'])) {
                    $response = ['success' => true, 'message' => 'Kelas berhasil ditambahkan.'];
                } else { $response['message'] = 'Gagal menambahkan kelas.'; }
                break;
            case 'update':
                if ($kelasModel->update($data['data']['id_kelas'], $data['data']['nama_kelas'], $data['data']['id_prodi'], $data['data']['id_dosen_wali'], $data['data']['tahun_ajaran'])) {
                    $response = ['success' => true, 'message' => 'Kelas berhasil diperbarui.'];
                } else { $response['message'] = 'Gagal memperbarui kelas.'; }
                break;
            case 'delete':
                if ($kelasModel->delete($data['data']['id_kelas'])) {
                    $response = ['success' => true, 'message' => 'Kelas berhasil dihapus.'];
                } else { $response['message'] = 'Gagal menghapus kelas. Mungkin masih digunakan oleh data lain.'; }
                break;
        }
    }

} catch (Throwable $e) {
    http_response_code(500);
    $response = ['success' => false, 'message' => 'Server Error: ' . $e->getMessage()];
}

echo json_encode($response);