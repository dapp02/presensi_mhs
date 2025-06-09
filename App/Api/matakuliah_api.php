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
        $action = $_GET['action'] ?? 'get_all';

        switch ($action) {
            case 'get_all':
                $response['success'] = true;
                $response['data'] = $mataKuliahModel->getAll();
                break;
            case 'get_by_id':
                if (isset($_GET['id'])) {
                    $id_matkul = $_GET['id'];
                    $matkul = $mataKuliahModel->getById($id_matkul);
                    if ($matkul) {
                        $response['success'] = true;
                        $response['data'] = $matkul;
                        $response['message'] = 'Mata Kuliah berhasil ditemukan.';
                    } else {
                        $response['message'] = 'Mata Kuliah tidak ditemukan.';
                    }
                } else {
                    $response['message'] = 'ID Mata Kuliah tidak disediakan.';
                }
                break;
            default:
                $response['message'] = 'Aksi GET tidak valid.';
                break;
        }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        $action = $input['action'] ?? '';

        switch ($action) {
            case 'create':
                if (isset($input['kode_matkul'], $input['nama_matkul'], $input['sks'], $input['id_prodi'])) {
                    $kode_matkul = $input['kode_matkul'];
                    $nama_matkul = $input['nama_matkul'];
                    $sks = (int)$input['sks'];
                    $id_prodi = (int)$input['id_prodi'];

                    if ($mataKuliahModel->create($kode_matkul, $nama_matkul, $sks, $id_prodi)) {
                        $response['success'] = true;
                        $response['message'] = 'Mata Kuliah berhasil ditambahkan.';
                    } else {
                        $response['message'] = 'Gagal menambahkan Mata Kuliah.';
                    }
                } else {
                    $response['message'] = 'Data tidak lengkap untuk membuat Mata Kuliah.';
                }
                break;
            case 'update':
                if (isset($input['id_matkul'], $input['kode_matkul'], $input['nama_matkul'], $input['sks'], $input['id_prodi'])) {
                    $id_matkul = $input['id_matkul'];
                    $kode_matkul = $input['kode_matkul'];
                    $nama_matkul = $input['nama_matkul'];
                    $sks = (int)$input['sks'];
                    $id_prodi = (int)$input['id_prodi'];

                    if ($mataKuliahModel->update($id_matkul, $kode_matkul, $nama_matkul, $sks, $id_prodi)) {
                        $response['success'] = true;
                        $response['message'] = 'Mata Kuliah berhasil diperbarui.';
                    } else {
                        $response['message'] = 'Gagal memperbarui Mata Kuliah.';
                    }
                } else {
                    $response['message'] = 'Data tidak lengkap untuk memperbarui Mata Kuliah.';
                }
                break;
            case 'delete':
                if (isset($input['id_matkul'])) {
                    $id_matkul = $input['id_matkul'];
                    if ($mataKuliahModel->delete($id_matkul)) {
                        $response['success'] = true;
                        $response['message'] = 'Mata Kuliah berhasil dihapus.';
                    } else {
                        $response['message'] = 'Gagal menghapus Mata Kuliah.';
                    }
                } else {
                    $response['message'] = 'ID Mata Kuliah tidak disediakan untuk dihapus.';
                }
                break;
            default:
                $response['message'] = 'Aksi tidak valid.';
                break;
        }
    }

} catch (Throwable $e) {
    http_response_code(500);
    $response['message'] = 'Server Error: ' . $e->getMessage();
}

echo json_encode($response);