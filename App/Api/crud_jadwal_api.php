<?php

error_reporting(E_ALL);
ini_set('display_errors', 1); // Aktifkan tampilan error untuk debugging

header('Content-Type: application/json');
define('ROOT_PATH_FOR_API', dirname(__DIR__, 2));
require_once ROOT_PATH_FOR_API . '/auth/config/database.php';
require_once ROOT_PATH_FOR_API . '/App/models/JadwalCrudModel.php';
require_once ROOT_PATH_FOR_API . '/App/models/DosenMengajarModel.php';
require_once ROOT_PATH_FOR_API . '/App/models/DosenModel.php';
require_once ROOT_PATH_FOR_API . '/App/models/KelasModel.php';
require_once ROOT_PATH_FOR_API . '/App/models/MataKuliahModel.php';

$response = ['success' => false, 'message' => 'Invalid Request'];

try {
    $db = new Database();
    $pdo = $db->connect();
    if (!$pdo) throw new Exception("Koneksi DB Gagal.");

    $jadwalCrudModel = new \App\Models\JadwalCrudModel($pdo);
    $dosenMengajarModel = new \App\Models\DosenMengajarModel($pdo);
    $dosenModel = new \App\Models\DosenModel($pdo);
    $kelasModel = new \App\Models\KelasModel($pdo);
    $mataKuliahModel = new \App\Models\MataKuliahModel($pdo);

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $action = $_GET['action'] ?? 'get_all';

        switch ($action) {
            case 'get_form_data':
                $response['success'] = true;
                $response['data'] = [
                    'dosen' => $dosenModel->getAllForDropdown(),
                    'matakuliah' => $mataKuliahModel->getAll(),
                    'kelas' => $kelasModel->getAllForDropdown()
                 ];
                 $response['message'] = 'Data untuk form berhasil diambil';
                break;
            case 'get_all':
                $response['success'] = true;
                $response['data'] = $jadwalCrudModel->getAllWithDetails();
                break;
                case 'get_by_id':
                if (isset($_GET['id'])) {
                    $id_jadwal = $_GET['id'];
                    $jadwal = $jadwalCrudModel->getByIdWithDetails($id_jadwal);
                    if ($jadwal) {
                        $response['success'] = true;
                        $response['data'] = $jadwal;
                        $response['message'] = 'Jadwal berhasil ditemukan.';
                    } else {
                        $response['message'] = 'Jadwal tidak ditemukan.';
                    }
                } else {
                    $response['message'] = 'ID Jadwal tidak disediakan.';
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
                // PERBAIKI KUNCI DI SINI
                if (isset($input['nidn_dosen'], $input['id_matkul'], $input['id_kelas'], $input['hari'], $input['jam_mulai'], $input['jam_selesai'], $input['ruangan'])) {

                    $nidn_dosen = $input['nidn_dosen']; // Di JS, ini adalah NIDN
                    $id_matkul = (int)$input['id_matkul']; // PERBAIKI NAMA VARIABEL
                    $id_kelas = (int)$input['id_kelas'];
                    $hari = $input['hari'];
                    $jam_mulai = $input['jam_mulai'];
                    $jam_selesai = $input['jam_selesai'];
                    $ruangan = $input['ruangan'];

                    // Panggil findOrCreate dengan variabel yang benar
                    $id_dosen_mengajar = $dosenMengajarModel->findOrCreate($nidn_dosen, $id_matkul, $id_kelas);

                    if ($id_dosen_mengajar) {
                        if ($jadwalCrudModel->create($id_dosen_mengajar, $hari, $jam_mulai, $jam_selesai, $ruangan)) {
                            $response['success'] = true;
                            $response['message'] = 'Jadwal berhasil ditambahkan.';
                        } else {
                            $response['message'] = 'Gagal menambahkan jadwal.';
                        }
                    } else {
                        $response['message'] = 'Gagal menemukan atau membuat entri dosen mengajar.';
                    }
                } else {
                    $response['message'] = 'Data tidak lengkap untuk membuat jadwal.';
                }
                break;
            case 'update':
                if (isset($input['id_jadwal'], $input['nidn_dosen'], $input['id_matkul'], $input['id_kelas'], $input['hari'], $input['jam_mulai'], $input['jam_selesai'], $input['ruangan'])) {
                    $id_jadwal = $input['id_jadwal'];
                    $nidn_dosen = $input['nidn_dosen'];
                    $id_mata_kuliah = (int)$input['id_matkul']; // Perbaikan: gunakan id_matkul
                    $id_kelas = (int)$input['id_kelas'];
                    $hari = $input['hari'];
                    $jam_mulai = $input['jam_mulai'];
                    $jam_selesai = $input['jam_selesai'];
                    $ruangan = $input['ruangan'];

                    $id_dosen_mengajar = $dosenMengajarModel->findOrCreate($nidn_dosen, $id_mata_kuliah, $id_kelas);

                    if ($id_dosen_mengajar) {
                        if ($jadwalCrudModel->update($id_jadwal, $id_dosen_mengajar, $hari, $jam_mulai, $jam_selesai, $ruangan)) {
                            $response['success'] = true;
                            $response['message'] = 'Jadwal berhasil diperbarui.';
                        } else {
                            $response['message'] = 'Gagal memperbarui jadwal.';
                        }
                    } else {
                        $response['message'] = 'Gagal menemukan atau membuat entri dosen mengajar.';
                    }
                } else {
                    $response['message'] = 'Data tidak lengkap untuk memperbarui jadwal.';
                }
                break;
            case 'delete':
                if (isset($input['id_jadwal'])) {
                    $id_jadwal = $input['id_jadwal'];
                    if ($jadwalCrudModel->delete($id_jadwal)) {
                        $response['success'] = true;
                        $response['message'] = 'Jadwal berhasil dihapus.';
                    } else {
                        $response['message'] = 'Gagal menghapus jadwal.';
                    }
                } else {
                    $response['message'] = 'ID Jadwal tidak disediakan untuk dihapus.';
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