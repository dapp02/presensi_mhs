<?php
require_once 'db_operations.php';

// Inisialisasi objek DbOperations
$db = new DbOperations();

// Tangani permintaan berdasarkan metode HTTP
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['nim'])) {
            // Ambil data mahasiswa berdasarkan NIM
            $mahasiswa = $db->getMahasiswaByNim($_GET['nim']);
            if ($mahasiswa) {
                echo json_encode(['status' => 'success', 'data' => $mahasiswa]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Mahasiswa tidak ditemukan']);
            }
        } else {
            // Ambil semua data mahasiswa
            $mahasiswa = $db->getMahasiswa();
            echo json_encode(['status' => 'success', 'data' => $mahasiswa]);
        }
        break;
        
    case 'POST':
        // Tambah atau update mahasiswa
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (isset($data['action'])) {
            switch ($data['action']) {
                case 'add':
                    $result = $db->addMahasiswa(
                        $data['nim'],
                        $data['nama'],
                        $data['prodi'],
                        $data['kelas'],
                        $data['email']
                    );
                    
                    if ($result) {
                        echo json_encode(['status' => 'success', 'message' => 'Mahasiswa berhasil ditambahkan']);
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'Gagal menambahkan mahasiswa']);
                    }
                    break;
                    
                case 'update':
                    $result = $db->updateMahasiswa(
                        $data['nim'],
                        $data['nama'],
                        $data['prodi'],
                        $data['kelas'],
                        $data['email']
                    );
                    
                    if ($result) {
                        echo json_encode(['status' => 'success', 'message' => 'Mahasiswa berhasil diperbarui']);
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui mahasiswa']);
                    }
                    break;
                    
                case 'delete':
                    $result = $db->deleteMahasiswa($data['nim']);
                    
                    if ($result) {
                        echo json_encode(['status' => 'success', 'message' => 'Mahasiswa berhasil dihapus']);
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus mahasiswa']);
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