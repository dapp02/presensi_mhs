<?php
require_once 'db_operations.php';

// Inisialisasi objek DbOperations
$db = new DbOperations();

// Tangani permintaan berdasarkan metode HTTP
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Cek jika ada parameter untuk dropdown
        if (isset($_GET['type'])) {
            switch ($_GET['type']) {
                case 'matkul':
                    // Ambil data mata kuliah untuk dropdown
                    $matkul = $db->getMatkulForDropdown();
                    echo json_encode(['status' => 'success', 'data' => $matkul]);
                    break;
                    
                case 'kelas':
                    // Ambil data kelas untuk dropdown
                    $kelas = $db->getKelasForDropdown();
                    echo json_encode(['status' => 'success', 'data' => $kelas]);
                    break;
                    
                default:
                    echo json_encode(['status' => 'error', 'message' => 'Tipe tidak valid']);
                    break;
            }
        } else {
            // Ambil data jadwal
            $jadwal = $db->getJadwal();
            echo json_encode(['status' => 'success', 'data' => $jadwal]);
        }
        break;
        
    case 'POST':
        // Tambah, update, atau delete jadwal
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (isset($data['action'])) {
            switch ($data['action']) {
                case 'add':
                    $result = $db->addJadwal(
                        $data['matkul'],
                        $data['kelas'],
                        $data['hari'],
                        $data['jam'],
                        $data['ruang']
                    );
                    
                    if ($result) {
                        echo json_encode(['status' => 'success', 'message' => 'Jadwal berhasil ditambahkan']);
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'Gagal menambahkan jadwal']);
                    }
                    break;
                    
                case 'update':
                    $result = $db->updateJadwal(
                        $data['id'],
                        $data['matkul'],
                        $data['kelas'],
                        $data['hari'],
                        $data['jam'],
                        $data['ruang']
                    );
                    
                    if ($result) {
                        echo json_encode(['status' => 'success', 'message' => 'Jadwal berhasil diperbarui']);
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui jadwal']);
                    }
                    break;
                    
                case 'delete':
                    $result = $db->deleteJadwal($data['id']);
                    
                    if ($result) {
                        echo json_encode(['status' => 'success', 'message' => 'Jadwal berhasil dihapus']);
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus jadwal']);
                    }
                    break;
                    
                default:
                    echo json_encode(['status' => 'error', 'message' => 'Aksi tidak valid']);
                    break;
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Aksi tidak ditemukan']);
        }
        break;
        
    default:
        echo json_encode(['status' => 'error', 'message' => 'Metode tidak didukung']);
        break;
}