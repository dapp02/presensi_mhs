<?php 
 
 namespace App\Services; 
 
 use PDO; 
 // Pastikan namespace model sudah benar jika menggunakan 'use' 
 // use App\Models\JadwalModel; 
 // use App\Models\PenggunaModel; // Jika perlu mengambil nama dari DB jika tidak dari sesi 
 
 // Require model jika tidak menggunakan autoloader PSR-4 secara penuh 
 require_once __DIR__ . '/../Models/JadwalModel.php'; // Sesuaikan path jika struktur berubah 
 // require_once __DIR__ . '/../Models/PenggunaModel.php'; 
 
 class UserDashboardService 
 {
     private PDO $pdo; 
     private \App\Models\JadwalModel $jadwalModel; // Gunakan FQN atau 'use' statement 
     // private \App\Models\PenggunaModel $penggunaModel; 
 
     // Path log kustom dari App/Services/ 
     private string $log_file = __DIR__ . '/../../logs/app_debug.log'; 
 
     public function __construct(PDO $pdo) 
     {
         $this->pdo = $pdo; 
         $this->jadwalModel = new \App\Models\JadwalModel($this->pdo); 
         // $this->penggunaModel = new \App\Models\PenggunaModel($this->pdo); 
     } 
 
     private function custom_service_log($message) {
         $timestamp = date("Y-m-d H:i:s"); 
         error_log("[" . $timestamp . "] USER_DASH_SERVICE: " . $message . PHP_EOL, 3, $this->log_file); 
     } 
 
     public function prepareUserDashboardData(string $nim_mahasiswa, string $nama_lengkap_mahasiswa): array 
     {
         $this->custom_service_log("Mempersiapkan data untuk NIM: " . $nim_mahasiswa); 
         $data_untuk_view = []; 
 
         // Set locale untuk nama bulan dalam Bahasa Indonesia 
         setlocale(LC_TIME, 'id_ID.utf8', 'id_ID', 'id'); 
 
         // 1. Data Header 
         $data_untuk_view['nama_mahasiswa_header'] = $nama_lengkap_mahasiswa; 
         $this->custom_service_log("Nama Header: " . $nama_lengkap_mahasiswa); 
 
         // 2. Data Tanggal dan Kalender Mingguan (mirip AdminDashboardService) 
         $today = new \DateTime(); 
         // Menggunakan strftime untuk format tanggal dengan nama bulan Indonesia 
         $data_untuk_view['tanggal_hari_ini_display'] = strftime('%d %B %Y', $today->getTimestamp()); 
         $this->custom_service_log("Tanggal Display: " . $data_untuk_view['tanggal_hari_ini_display']); 
 
         $nama_hari_english_server = $today->format('l'); 
         $hari_map_english_ke_indo = [
             'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 
             'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu' 
         ]; 
         $nama_hari_untuk_query = $hari_map_english_ke_indo[$nama_hari_english_server] ?? $nama_hari_english_server; 
         
         $this->custom_service_log("Hari ini (English): " . $nama_hari_english_server . ", Untuk Query (Indonesia): " . $nama_hari_untuk_query); 
 
         // Logika Kalender Mingguan (sama seperti AdminDashboardService) 
         $monday_this_week = clone $today; 
         if ((int)$today->format('N') > 1) {
             $monday_this_week->modify('-' . ((int)$today->format('N') - 1) . ' days'); 
         } 
         $nama_hari_pendek_map = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min']; 
         $data_untuk_view['kalender_mingguan'] = []; 
         $current_day_iterate = clone $monday_this_week; 
         for ($i = 0; $i < 7; $i++) {
             $day_english_iter = $current_day_iterate->format('l'); 
             $is_today_flag = ($current_day_iterate->format('Y-m-d') === $today->format('Y-m-d')); 
             $data_untuk_view['kalender_mingguan'][] = [
                 'nama_pendek'       => $nama_hari_pendek_map[$i], 
                 'tanggal_angka'     => $current_day_iterate->format('d'), 
                 'nama_panjang_indo' => $hari_map_english_ke_indo[$day_english_iter] ?? $day_english_iter, 
                 'is_hari_ini'       => $is_today_flag, 
                 'is_masa_lalu'      => $current_day_iterate < $today && !$is_today_flag, 
                 'is_masa_depan'     => $current_day_iterate > $today && !$is_today_flag, 
                 'full_date_iso'     => $current_day_iterate->format('Y-m-d') 
             ]; 
             if ($i < 6) $current_day_iterate->modify('+1 day'); 
         } 
         $this->custom_service_log("Data Kalender Mingguan: " . json_encode($data_untuk_view['kalender_mingguan'])); 
 
         // 3. Jadwal Mahasiswa Hari Ini 
         $data_untuk_view['jadwal_mahasiswa_hari_ini'] = $this->jadwalModel->getJadwalMahasiswaHariIni($nim_mahasiswa, $nama_hari_untuk_query); 
         $this->custom_service_log("Jadwal Mahasiswa Hari Ini (Jumlah: " . count($data_untuk_view['jadwal_mahasiswa_hari_ini']) . "): " . json_encode($data_untuk_view['jadwal_mahasiswa_hari_ini'])); 
 
         // 4. Semua Jadwal/Mata Kuliah Mahasiswa (untuk panel kanan) 
         $data_untuk_view['semua_jadwal_mahasiswa'] = $this->jadwalModel->getAllJadwalMahasiswa($nim_mahasiswa); 
         $this->custom_service_log("Semua Jadwal Mahasiswa (Jumlah: " . count($data_untuk_view['semua_jadwal_mahasiswa']) . "): " . json_encode($data_untuk_view['semua_jadwal_mahasiswa'])); 
         
         // 5. Data Rekap Kehadiran Dasar (akan diperluas nanti jika perlu) 
         // Untuk setiap item di $data_untuk_view['semua_jadwal_mahasiswa'], Anda mungkin ingin menambahkan 
         // informasi jumlah hadir dan total pertemuan. Ini bisa jadi query tambahan per mata kuliah atau 
         // subquery di getAllJadwalMahasiswa. Untuk Sesi DU-2.1, ini bisa di-skip dulu dan kartu hanya menampilkan detail jadwal. 
 
         return $data_untuk_view; 
     } 
 } 
 ?>