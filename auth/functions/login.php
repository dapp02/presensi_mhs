<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

function loginUser($username, $password) {
    $custom_log_file = __DIR__ . '/../../logs/app_debug.log';

    $database = new Database();
    $conn = $database->connect();
    // Session::start(); // Session sudah di-start di auth/handlers/login.php atau middleware

    error_log("[" . date("Y-m-d H:i:s") . "] --- LOGIN ATTEMPT START ---" . PHP_EOL, 3, $custom_log_file);
    error_log("[" . date("Y-m-d H:i:s") . "] LOGIN INPUT: Username: '" . $username . "'" . PHP_EOL, 3, $custom_log_file);
    // HATI-HATI: Log password plaintext hanya untuk debug SEMENTARA di lingkungan pengembangan.
    // JANGAN PERNAH log password plaintext di produksi.
    error_log("[" . date("Y-m-d H:i:s") . "] LOGIN INPUT: Password Plaintext (DEBUG ONLY): '" . $password . "'" . PHP_EOL, 3, $custom_log_file);

    try {
        // Prepare statement untuk mencegah SQL injection
        $stmt = $conn->prepare("SELECT id_pengguna, nama_lengkap, username, password, role FROM pengguna WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user) {
            error_log("[" . date("Y-m-d H:i:s") . "] LOGIN DB: User FOUND. Username from DB: '" . $user['username'] . "'" . PHP_EOL, 3, $custom_log_file);
            error_log("[" . date("Y-m-d H:i:s") . "] LOGIN DB: Hashed Password from DB: '" . $user['password'] . "' (Length: " . strlen($user['password']) . ")" . PHP_EOL, 3, $custom_log_file);

            // VERIFIKASI PASSWORD
            error_log("[" . date("Y-m-d H:i:s") . "] LOGIN VERIFY: Plaintext for verify: '" . $password . "'" . PHP_EOL, 3, $custom_log_file); // Pastikan ini password dari input
            error_log("[" . date("Y-m-d H:i:s") . "] LOGIN VERIFY: Hash for verify: '" . $user['password'] . "'" . PHP_EOL, 3, $custom_log_file);

            $isPasswordCorrect = password_verify($password, $user['password']);
            error_log("[" . date("Y-m-d H:i:s") . "] LOGIN VERIFY: password_verify() result: " . ($isPasswordCorrect ? 'TRUE (MATCH)' : 'FALSE (NO MATCH)') . PHP_EOL, 3, $custom_log_file);

            if ($isPasswordCorrect) {
                error_log("[" . date("Y-m-d H:i:s") . "] LOGIN SUCCESS: Password MATCH for user: " . $username . PHP_EOL, 3, $custom_log_file);
                Session::set('user_id', $user['id_pengguna']);
                Session::set('username', $user['username']);
                Session::set('nama_lengkap', $user['nama_lengkap']);
                Session::set('role', $user['role']);
                Session::set('logged_in', true); // Pastikan ini di-set
                error_log("[" . date("Y-m-d H:i:s") . "] LOGIN SUCCESS: Session variables SET for user: " . $username . PHP_EOL, 3, $custom_log_file);

                if ($user['role'] === 'dosen' || $user['role'] === 'admin') {
                    // Ambil NIDN dari tabel dosen
                    $stmt_dosen = $conn->prepare("SELECT nidn FROM dosen WHERE id_pengguna = ?");
                    $stmt_dosen->execute([$user['id_pengguna']]);
                    $dosen_data = $stmt_dosen->fetch();

                    if ($dosen_data && !empty($dosen_data['nidn'])) {
                        Session::set('nidn', $dosen_data['nidn']);
                        error_log("[" . date("Y-m-d H:i:s") . "] LOGIN SUCCESS: NIDN '" . $dosen_data['nidn'] . "' SET to session for user: " . $username . PHP_EOL, 3, $custom_log_file);
                    } else {
                        // NIDN tidak ditemukan untuk dosen/admin ini. Ini mungkin masalah data atau konfigurasi.
                        // Dasbor admin akan memerlukan NIDN.
                        Session::set('nidn', null); // Set null atau jangan set sama sekali dan tangani di dasbor
                        error_log("[" . date("Y-m-d H:i:s") . "] LOGIN WARNING: NIDN not found for user_id: " . $user['id_pengguna'] . " (username: " . $username . ") with role: " . $user['role'] . PHP_EOL, 3, $custom_log_file);
                    }
                }
                error_log("[" . date("Y-m-d H:i:s") . "] --- LOGIN ATTEMPT END (SUCCESS) ---" . PHP_EOL, 3, $custom_log_file);
                return ['success' => true, 'user' => ['role' => $user['role']]];
            } else {
                error_log("[" . date("Y-m-d H:i:s") . "] LOGIN DEBUG: Password verification FAILED for username: " . $username . PHP_EOL, 3, $custom_log_file);
                error_log("[" . date("Y-m-d H:i:s") . "] LOGIN FAIL: Password NO MATCH for user: " . $username . PHP_EOL, 3, $custom_log_file);
                error_log("[" . date("Y-m-d H:i:s") . "] --- LOGIN ATTEMPT END (FAIL - Incorrect Password) ---" . PHP_EOL, 3, $custom_log_file);
                return ['success' => false, 'message' => 'Username atau password salah. (LUP2)'];
            }
        } else {
            error_log("[" . date("Y-m-d H:i:s") . "] LOGIN FAIL: User NOT FOUND in DB for username: '" . $username . "'" . PHP_EOL, 3, $custom_log_file);
            error_log("[" . date("Y-m-d H:i:s") . "] --- LOGIN ATTEMPT END (FAIL - User Not Found) ---" . PHP_EOL, 3, $custom_log_file);
            return ['success' => false, 'message' => 'Username atau password salah. (LUF2)']; // Atau pesan 'Username tidak ditemukan'
        }
    } catch (Exception $e) {
        error_log("[" . date("Y-m-d H:i:s") . "] LOGIN EXCEPTION: " . $e->getMessage() . PHP_EOL, 3, $custom_log_file);
        error_log("[" . date("Y-m-d H:i:s") . "] --- LOGIN ATTEMPT END (EXCEPTION) ---" . PHP_EOL, 3, $custom_log_file);
        return ['success' => false, 'message' => $e->getMessage()];
    }

    // Fallback terakhir jika ada jalur yang terlewat (seharusnya tidak tercapai)
    error_log("[" . date("Y-m-d H:i:s") . "] Peringatan: Fallback return di loginUser tercapai untuk username: " . $username . PHP_EOL, 3, $custom_log_file);
    return ['success' => false, 'message' => 'Kesalahan internal tidak terduga pada proses login. (LUF)'];
}
