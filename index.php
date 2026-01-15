<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: ' . ($_SESSION['role'] == 'Admin' ? 'admin/dashboard.php' : 'karyawan/dashboard.php'));
    exit;
}
header('Location: login.php');
exit;
?>