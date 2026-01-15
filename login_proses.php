<?php
session_start();

// ==============================================
// DEBUG MODE - untuk troubleshooting
// ==============================================
define('DEBUG_MODE', true); // Ubah ke false setelah berhasil

// ==============================================
// INCLUDE KONEKSI DATABASE
// ==============================================
include 'config/koneksi.php';

// ==============================================
// LOG DEBUG INFO
// ==============================================
if (DEBUG_MODE) {
    error_log("=== LOGIN ATTEMPT START ===");
    error_log("Username POST: " . ($_POST['username'] ?? 'NOT SET'));
    error_log("Database connected: " . ($koneksi ? 'YES' : 'NO'));
}

// ==============================================
// VALIDASI INPUT
// ==============================================
if (!isset($_POST['username']) || !isset($_POST['password'])) {
    header('Location: login.php?status=empty');
    exit;
}

$username = trim($_POST['username']);
$password = $_POST['password'];

if (empty($username) || empty($password)) {
    header('Location: login.php?status=empty');
    exit;
}

// ==============================================
// CEK TABEL USERS (Debugging)
// ==============================================
if (DEBUG_MODE) {
    // Cek apakah database ada
    $check_db = mysqli_select_db($koneksi, "hris_aradea_store");
    error_log("Database selected: " . ($check_db ? 'YES' : 'NO'));
    
    // Cek apakah tabel users ada
    $check_table = mysqli_query($koneksi, "SHOW TABLES LIKE 'users'");
    if ($check_table) {
        $table_exists = mysqli_num_rows($check_table) > 0;
        error_log("Table 'users' exists: " . ($table_exists ? 'YES' : 'NO'));
        
        if (!$table_exists) {
            // Coba lihat tabel apa saja yang ada
            $all_tables = mysqli_query($koneksi, "SHOW TABLES");
            $tables = [];
            while ($row = mysqli_fetch_array($all_tables)) {
                $tables[] = $row[0];
            }
            error_log("Available tables: " . implode(', ', $tables));
        }
    }
}

// ==============================================
// QUERY USER DENGAN PREPARED STATEMENT
// ==============================================
$query = "SELECT * FROM users WHERE username = ?";
$stmt = mysqli_prepare($koneksi, $query);

if (DEBUG_MODE) {
    error_log("Prepare statement: " . ($stmt ? 'SUCCESS' : 'FAILED: ' . mysqli_error($koneksi)));
}

if (!$stmt) {
    error_log("Prepare statement error: " . mysqli_error($koneksi));
    header('Location: login.php?status=error&msg=prepare_failed');
    exit;
}

mysqli_stmt_bind_param($stmt, "s", $username);
$execute_result = mysqli_stmt_execute($stmt);

if (DEBUG_MODE) {
    error_log("Execute statement: " . ($execute_result ? 'SUCCESS' : 'FAILED'));
}

if (!$execute_result) {
    error_log("Execute error: " . mysqli_error($koneksi));
    header('Location: login.php?status=error&msg=execute_failed');
    exit;
}

$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if (DEBUG_MODE) {
    error_log("User found: " . ($user ? 'YES' : 'NO'));
    if ($user) {
        error_log("User data: " . print_r($user, true));
        error_log("Password in DB: " . $user['password']);
        error_log("Password length: " . strlen($user['password']));
        error_log("Is MD5? " . (strlen($user['password']) == 32 ? 'POSSIBLY' : 'NO'));
    }
}

// ==============================================
// VERIFIKASI USER
// ==============================================
if ($user) {
    $db_password = $user['password'];
    
    // Cek tipe password hash
    // Jika panjang 32 karakter dan hex, kemungkinan MD5
    if (strlen($db_password) == 32 && ctype_xdigit($db_password)) {
        // MD5 hash
        $password_match = (md5($password) == $db_password);
        if (DEBUG_MODE) {
            error_log("Using MD5 verification");
            error_log("Input MD5: " . md5($password));
            error_log("DB MD5: " . $db_password);
            error_log("Password match: " . ($password_match ? 'YES' : 'NO'));
        }
    } else {
        // password_hash() verification
        $password_match = password_verify($password, $db_password);
        if (DEBUG_MODE) {
            error_log("Using password_verify()");
            error_log("Password match: " . ($password_match ? 'YES' : 'NO'));
        }
    }
    
    if ($password_match) {
        // ==============================================
        // LOGIN SUKSES
        // ==============================================
        if (DEBUG_MODE) {
            error_log("LOGIN SUCCESS for user: " . $username);
        }
        
        // Regenerasi session
        session_regenerate_id(true);
        
        // Set session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['nama_lengkap'] = $user['nama_lengkap'] ?? '';
        $_SESSION['email'] = $user['email'] ?? '';
        $_SESSION['login_time'] = time();
        
        // Redirect berdasarkan role
        if ($user['role'] == 'Admin' || $user['role'] == 'Manager') {
            header('Location: admin/dashboard.php');
        } else {
            header('Location: karyawan/dashboard.php');
        }
        
        // Update last login
        $update_sql = "UPDATE users SET last_login = NOW() WHERE id = " . $user['id'];
        mysqli_query($koneksi, $update_sql);
        
        exit;
    } else {
        // Password salah
        if (DEBUG_MODE) {
            error_log("PASSWORD MISMATCH for user: " . $username);
        }
        header('Location: login.php?status=failed');
        exit;
    }
} else {
    // User tidak ditemukan
    if (DEBUG_MODE) {
        error_log("USER NOT FOUND: " . $username);
    }
    header('Location: login.php?status=err_user');
    exit;
}

// Tutup koneksi
mysqli_stmt_close($stmt);
mysqli_close($koneksi);

if (DEBUG_MODE) {
    error_log("=== LOGIN ATTEMPT END ===");
}
?>