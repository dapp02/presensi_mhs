<?php
header('Content-Type: application/json');
define('ROOT_PATH_FOR_API', dirname(__DIR__, 2));

// Pastikan semua require_once benar
require_once ROOT_PATH_FOR_API . '/auth/config/database.php';
require_once ROOT_PATH_FOR_API . '/App/models/ProdiModel.php';

// TIDAK PERLU 'use Response;' jika kelas Response tidak di-namespace
// require_once ROOT_PATH_FOR_API . '/auth/utils/response.php'; // Pastikan ini di-require jika Anda ingin menggunakannya

$db_instance = new Database();
$pdo_connection = $db_instance->connect();
$prodiModel = new \App\Models\ProdiModel($pdo_connection);

$method = $_SERVER['REQUEST_METHOD'];
$response = ['success' => false, 'message' => 'Permintaan tidak valid.'];

// --- BLOK PERBAIKAN & DEBUGGING ---

if ($method === 'POST') {
    $raw_input = file_get_contents('php://input');
    $data = json_decode($raw_input, true);

    if (json_last_error() === JSON_ERROR_NONE && isset($data['action'])) {
        switch ($data['action']) {
            case 'create':
                if (!empty($data['nama_prodi'])) {
                    if ($prodiModel->create($data['nama_prodi'])) {
                        $response = ['success' => true, 'message' => 'Program Studi berhasil ditambahkan.'];
                    } else {
                        $response['message'] = 'Gagal menambahkan Program Studi.';
                    }
                } else {
                    $response['message'] = 'Nama prodi tidak boleh kosong untuk aksi create.';
                }
                break;
            
            case 'update':
                if (!empty($data['id_prodi']) && !empty($data['nama_prodi'])) {
                    if ($prodiModel->update($data['id_prodi'], $data['nama_prodi'])) {
                        $response = ['success' => true, 'message' => 'Program Studi berhasil diperbarui.'];
                    } else {
                        $response['message'] = 'Gagal memperbarui Program Studi.';
                    }
                } else {
                    $response['message'] = 'ID dan Nama prodi tidak boleh kosong untuk aksi update.';
                }
                break;

            case 'delete':
                if (!empty($data['id_prodi'])) {
                    if ($prodiModel->delete($data['id_prodi'])) {
                        $response = ['success' => true, 'message' => 'Program Studi berhasil dihapus.'];
                    } else {
                        $response['message'] = 'Gagal menghapus Program Studi. Mungkin masih digunakan oleh data lain (Kelas atau Mata Kuliah).';
                    }
                } else {
                    $response['message'] = 'ID prodi tidak boleh kosong untuk aksi delete.';
                }
                break;
                
            default:
                $response['message'] = 'Aksi tidak valid.';
                break;
        }
    } else {
        $response['message'] = 'Aksi tidak ditemukan atau format JSON tidak valid.';
    }
} elseif ($method === 'GET') {
    $response['success'] = true;
    $response['data'] = $prodiModel->getAll();
}

echo json_encode($response);
?>