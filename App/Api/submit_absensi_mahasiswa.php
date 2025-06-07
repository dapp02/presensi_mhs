<?php 
 header('Content-Type: application/json'); 
 define('ROOT_PATH_FOR_ABSENSI_API', dirname(__DIR__, 2)); 
 
 require_once ROOT_PATH_FOR_ABSENSI_API . '/auth/config/database.php'; 
 require_once ROOT_PATH_FOR_ABSENSI_API . '/auth/config/session.php'; // Untuk mengambil NIM jika perlu verifikasi server-side 
 // Pertimbangkan membuat AbsensiModel.php 
 // require_once ROOT_PATH_FOR_ABSENSI_API . '/App/Models/AbsensiModel.php'; 
 
 $custom_log_file_api_absen = ROOT_PATH_FOR_ABSENSI_API . '/logs/app_debug.log'; 
 function api_absen_log($message, $log_file) { 
     $timestamp = date("Y-m-d H:i:s"); 
     error_log("[" . $timestamp . "] API_SUBMIT_ABSENSI_MHS: " . $message . PHP_EOL, 3, $log_file); 
 } 
 
 $response = ['success' => false, 'message' => 'Permintaan tidak valid.']; 
 
 // Ambil data dari POST request (JavaScript akan mengirim sebagai FormData atau JSON) 
 if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
     // GANTI BLOK INI:
     // $nim_mahasiswa = $_POST['nim'] ?? null;
     // $id_jadwal = $_POST['id_jadwal'] ?? null;
     // $status_kehadiran = $_POST['status_kehadiran'] ?? null;
     // $keterangan = $_POST['keterangan'] ?? null;
     
     // MENJADI BLOK INI:
     $input = json_decode(file_get_contents('php://input'), true);
     $nim_mahasiswa = $input['nim'] ?? null;
     $id_jadwal = $input['id_jadwal'] ?? null;
     $status_kehadiran = $input['status_kehadiran'] ?? null;
     $keterangan = $input['keterangan'] ?? null;
     
     $tanggal_absensi = date('Y-m-d');
     api_absen_log("Request Body Diterima: " . file_get_contents('php://input'), $custom_log_file_api_absen);
     api_absen_log("Request diproses: NIM={$nim_mahasiswa}, IDJadwal={$id_jadwal}, Status={$status_kehadiran}, Tgl={$tanggal_absensi}, Ket: {$keterangan}", $custom_log_file_api_absen);
     
     if (empty($nim_mahasiswa) || empty($id_jadwal) || empty($status_kehadiran)) {
         $response['message'] = 'Data tidak lengkap (NIM, ID Jadwal, Status).';
         api_absen_log("Error: Data tidak lengkap. Input: " . json_encode($input), $custom_log_file_api_absen);
         echo json_encode($response);
         exit;
     }
 
     $status_valid = ['Hadir', 'Izin', 'Sakit']; // Alpha tidak diinput mahasiswa 
     if (!in_array($status_kehadiran, $status_valid)) { 
         $response['message'] = 'Status kehadiran tidak valid.'; 
         api_absen_log("Error: Status kehadiran tidak valid: " . $status_kehadiran, $custom_log_file_api_absen); 
         echo json_encode($response); 
         exit; 
     } 
 
     try { 
         $db_instance = new Database(); 
         $pdo_connection = $db_instance->connect(); 
 
         // Gunakan INSERT ... ON DUPLICATE KEY UPDATE untuk menangani jika mahasiswa sudah absen/izin/sakit 
         // pada jadwal dan tanggal yang sama. Kolom (id_jadwal, nim_mahasiswa, tanggal_absensi) 
         // harus memiliki UNIQUE constraint di database agar ON DUPLICATE KEY UPDATE berfungsi. 
         // Tabel `absensi` Anda sudah memiliki UNIQUE KEY `unique_absensi` (`id_jadwal`, `nim_mahasiswa`, `tanggal_absensi`) 
 
         $sql = "INSERT INTO absensi (id_jadwal, nim_mahasiswa, tanggal_absensi, status_kehadiran, keterangan) 
                 VALUES (:id_jadwal, :nim_mahasiswa, :tanggal_absensi, :status_kehadiran, :keterangan) 
                 ON DUPLICATE KEY UPDATE 
                 status_kehadiran = VALUES(status_kehadiran), 
                 keterangan = VALUES(keterangan), 
                 updated_at = CURRENT_TIMESTAMP"; 
         
         $stmt = $pdo_connection->prepare($sql); 
         $stmt->bindParam(':id_jadwal', $id_jadwal, PDO::PARAM_INT); 
         $stmt->bindParam(':nim_mahasiswa', $nim_mahasiswa, PDO::PARAM_STR); 
         $stmt->bindParam(':tanggal_absensi', $tanggal_absensi, PDO::PARAM_STR); 
         $stmt->bindParam(':status_kehadiran', $status_kehadiran, PDO::PARAM_STR); 
         $stmt->bindParam(':keterangan', $keterangan, PDO::PARAM_STR); 
 
         if ($stmt->execute()) { 
             $response['success'] = true; 
             $response['message'] = 'Absensi berhasil disimpan: ' . $status_kehadiran; 
             // Kirim kembali data rekap jika perlu untuk update UI statistik 
             // $response['rekap'] = ['hadir' => ..., 'izin' => ..., ...]; // Ini memerlukan query tambahan 
             api_absen_log("Sukses: Absensi disimpan. NIM={$nim_mahasiswa}, IDJadwal={$id_jadwal}, Status={$status_kehadiran}", $custom_log_file_api_absen); 
         } else { 
             $response['message'] = 'Gagal menyimpan absensi ke database.'; 
             api_absen_log("Error DB: Gagal execute statement. " . json_encode($stmt->errorInfo()), $custom_log_file_api_absen); 
         } 
 
     } catch (PDOException $e) { 
         $response['message'] = "Error database: " . $e->getMessage(); // Untuk dev, di prod mungkin pesan generik 
         api_absen_log("PDOException: " . $e->getMessage(), $custom_log_file_api_absen); 
     } catch (Throwable $e) { 
         $response['message'] = "Error sistem: Terjadi kesalahan tidak terduga."; 
         api_absen_log("Throwable: " . $e->getMessage(), $custom_log_file_api_absen); 
     } 
 } else { 
     $response['message'] = 'Metode request tidak valid. Hanya POST yang diizinkan.'; 
     api_absen_log("Error: Metode request bukan POST.", $custom_log_file_api_absen); 
 } 
 
 echo json_encode($response); 
 exit; 
 ?>