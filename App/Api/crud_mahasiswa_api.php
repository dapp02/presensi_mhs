<?php
header('Content-Type: application/json');
define('ROOT_PATH_FOR_API', dirname(__DIR__, 2));
require_once ROOT_PATH_FOR_API . '/auth/config/database.php';
require_once ROOT_PATH_FOR_API . '/App/models/CrudMahasiswaModel.php';

$response = ['success' => false, 'message' => 'Invalid Request'];

try {
    $db = new Database();
    $pdo = $db->connect();
    if (!$pdo) throw new Exception("Koneksi DB Gagal.");

    $mahasiswaModel = new \App\Models\CrudMahasiswaModel($pdo);
    
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (isset($_GET['action']) && $_GET['action'] === 'get_by_id' && isset($_GET['id'])) {
            $nim = $_GET['id'];
            $mahasiswa = $mahasiswaModel->getMahasiswaById($nim);
            if ($mahasiswa) {
                $response['success'] = true;
                $response['data'] = $mahasiswa;
            } else {
                $response['message'] = 'Mahasiswa tidak ditemukan.';
            }
        } elseif (isset($_GET['action']) && $_GET['action'] === 'get_all_mahasiswa_simple') {
            $response['success'] = true;
            $response['data'] = $mahasiswaModel->getAllMahasiswaSimple();
        }else {
            $response['success'] = true;
            $response['data'] = $mahasiswaModel->getAllMahasiswaForCrud();
        }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        $action = $input['action'] ?? '';
        $data = $input['data'] ?? [];

        switch ($action) {
            case 'assignMahasiswaToKelas': // This action is now for assigning an existing student to a class
                if (isset($data['nim'], $data['id_kelas'])) {
                    $nim = $data['nim'];
                    $id_kelas = $data['id_kelas'];
                    if ($mahasiswaModel->assignMahasiswaToKelas($nim, $id_kelas)) {
                        $response['success'] = true;
                        $response['message'] = 'Mahasiswa berhasil ditugaskan ke kelas.';
                    } else {
                        $response['message'] = 'Loading atau gagal menugaskan mahasiswa ke kelas.';
                    }
                } else {
                    $response['message'] = 'Data tidak lengkap untuk menugaskan mahasiswa ke kelas.';
                }
                break;
            case 'update':
                if (isset($data['nim'], $data['nama_lengkap'])) {
                    $nim = $data['nim'];
                    $nama_lengkap = $data['nama_lengkap'];
                    $id_prodi = $data['id_prodi'] ?? null;
                    $id_kelas = $data['id_kelas'] ?? null;
                    $password = $data['password'] ?? null;
                    if ($mahasiswaModel->updateMahasiswa($nim, $nama_lengkap, $id_prodi, $id_kelas, $password)) {
                        $response['success'] = true;
                        $response['message'] = 'Mahasiswa berhasil diperbarui.';
                    } else {
                        $response['message'] = 'Gagal memperbarui mahasiswa.';
                    }
                } else {
                    $response['message'] = 'Data tidak lengkap untuk memperbarui mahasiswa.';
                }
                break;
            case 'removeMahasiswaFromKelas': // This action is now for removing a student from a specific class
                if (isset($data['nim'], $data['id_kelas'])) {
                    $nim = $data['nim'];
                    $id_kelas = $data['id_kelas'];
                    if ($mahasiswaModel->removeMahasiswaFromKelas($nim, $id_kelas)) {
                        $response['success'] = true;
                        $response['message'] = 'Mahasiswa berhasil dikeluarkan dari kelas.';
                    } else {
                        $response['message'] = 'Gagal mengeluarkan mahasiswa dari kelas.';
                    }
                } else {
                    $response['message'] = 'NIM atau ID Kelas mahasiswa tidak disediakan untuk dihapus.';
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