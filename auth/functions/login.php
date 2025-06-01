<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

function loginUser($username, $password) {
    $database = new Database();
    $conn = $database->connect();
    // Session::start(); // Session sudah di-start di auth/handlers/login.php atau middleware

    error_log("--- LOGIN ATTEMPT START ---");
    error_log("LOGIN INPUT: Username: '" . $username . "'");
    // HATI-HATI: Log password plaintext hanya untuk debug SEMENTARA di lingkungan pengembangan.
    // JANGAN PERNAH log password plaintext di produksi.
    error_log("LOGIN INPUT: Password Plaintext (DEBUG ONLY): '" . $password . "'");

    try {
        // Prepare statement untuk mencegah SQL injection
        $stmt = $conn->prepare("SELECT id_pengguna, nama_lengkap, username, password, role FROM pengguna WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user) {
            error_log("LOGIN DB: User FOUND. Username from DB: '" . $user['username'] . "'");
            error_log("LOGIN DB: Hashed Password from DB: '" . $user['password'] . "' (Length: " . strlen($user['password']) . ")");

            // VERIFIKASI PASSWORD
            error_log("LOGIN VERIFY: Plaintext for verify: '" . $password . "'"); // Pastikan ini password dari input
            error_log("LOGIN VERIFY: Hash for verify: '" . $user['password'] . "'");

            $isPasswordCorrect = password_verify($password, $user['password']);
            error_log("LOGIN VERIFY: password_verify() result: " . ($isPasswordCorrect ? 'TRUE (MATCH)' : 'FALSE (NO MATCH)'));

            if ($isPasswordCorrect) {
                error_log("LOGIN SUCCESS: Password MATCH for user: " . $username);
                Session::set('user_id', $user['id_pengguna']);
                Session::set('username', $user['username']);
                Session::set('nama_lengkap', $user['nama_lengkap']);
                Session::set('role', $user['role']);
                Session::set('logged_in', true); // Pastikan ini di-set
                error_log("LOGIN SUCCESS: Session variables SET for user: " . $username);
                error_log("--- LOGIN ATTEMPT END (SUCCESS) ---");
                return ['success' => true, 'user' => ['role' => $user['role']]];
            } else {
                error_log("LOGIN DEBUG: Password verification FAILED for username: " . $username);
                error_log("LOGIN FAIL: Password NO MATCH for user: " . $username);
                error_log("--- LOGIN ATTEMPT END (FAIL - Incorrect Password) ---");
                return ['success' => false, 'message' => 'Username atau password salah. (LUP2)'];
            }
        } else {
            error_log("LOGIN FAIL: User NOT FOUND in DB for username: '" . $username . "'");
            error_log("--- LOGIN ATTEMPT END (FAIL - User Not Found) ---");
            return ['success' => false, 'message' => 'Username atau password salah. (LUF2)']; // Atau pesan 'Username tidak ditemukan'
        }
    } catch (Exception $e) {
        error_log("LOGIN EXCEPTION: " . $e->getMessage());
        error_log("--- LOGIN ATTEMPT END (EXCEPTION) ---");
        return ['success' => false, 'message' => $e->getMessage()];
    }

    // Fallback terakhir jika ada jalur yang terlewat (seharusnya tidak tercapai)
    error_log("Peringatan: Fallback return di loginUser tercapai untuk username: " . $username);
    return ['success' => false, 'message' => 'Kesalahan internal tidak terduga pada proses login. (LUF)'];
}
