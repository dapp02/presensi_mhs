<?php
require_once 'db_operations.php';

// Inisialisasi objek DbOperations
$db = new DbOperations();

// Tangani permintaan berdasarkan metode HTTP
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Ambil data mata kuliah
        $matkul = $db->getMataKuliah();
        echo json_encode(['status' => 'success', 'data' => $matkul]);
        break;
        
    case 'POST':
        // Tambah atau update mata kuliah
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (isset($data['action'])) {
            switch ($data['action']) {
                case 'add':
                    $result = $db->addMataKuliah(
                        $data['kode'],
                        $data['nama'],
                        $data['sks'],
                        $data['semester']
                    );
                    
                    if ($result) {
                        echo json_encode(['status' => 'success', 'message' => 'Mata kuliah berhasil ditambahkan']);
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'Gagal menambahkan mata kuliah']);
                    }
                    break;
                    
                case 'update':
                    $result = $db->updateMataKuliah(
                        $data['kode'],
                        $data['nama'],
                        $data['sks'],
                        $data['semester']
                    );
                    
                    if ($result) {
                        echo json_encode(['status' => 'success', 'message' => 'Mata kuliah berhasil diperbarui']);
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui mata kuliah']);
                    }
                    break;
                    
                case 'delete':
                    $result = $db->deleteMataKuliah($data['kode']);
                    
                    if ($result) {
                        echo json_encode(['status' => 'success', 'message' => 'Mata kuliah berhasil dihapus']);
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus mata kuliah']);
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