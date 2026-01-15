<?php
session_start();

// Simpan pesan logout
$_SESSION['logout_message'] = 'Anda telah berhasil logout.';

// Hancurkan semua session
session_unset();
session_destroy();

// Redirect ke login dengan pesan
header('Location: login.php?status=logout');
exit;
?>