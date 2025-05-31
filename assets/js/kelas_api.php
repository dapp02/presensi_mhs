<?php
require_once 'db_operations.php';

// Inisialisasi objek DbOperations
$db = new DbOperations();

// Tangani permintaan berdasarkan metode HTTP
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Ambil data kelas
        $kelas = $db->getKelas();
        echo json_encode(['status' => 'success', 'data' => $kelas]);
        break;
        
    case 'POST':
        // Tambah atau update kelas
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (isset($data['action'])) {
            switch ($data['action']) {
                case 'add':
                    $result = $db->addKelas(
                        $data['kode'],
                        $data['nama'],
                        $data['prodi'],
                        $data['jumlah']
                    );
                    
                    if ($result) {
                        echo json_encode(['status' => 'success', 'message' => 'Kelas berhasil ditambahkan']);
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'Gagal menambahkan kelas']);
                    }
                    break;
                    
                case 'update':
                    $result = $db->updateKelas(
                        $data['kode'],
                        $data['nama'],
                        $data['prodi'],
                        $data['jumlah']
                    );
                    
                    if ($result) {
                        echo json_encode(['status' => 'success', 'message' => 'Kelas berhasil diperbarui']);
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui kelas']);
                    }
                    break;
                    
                case 'delete':
                    $result = $db->deleteKelas($data['kode']);
                    
                    if ($result) {
                        echo json_encode(['status' => 'success', 'message' => 'Kelas berhasil dihapus']);
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus kelas']);
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