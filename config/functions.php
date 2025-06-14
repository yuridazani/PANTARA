<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

function check_admin_login() {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        // Tentukan path relatif ke halaman login admin berdasarkan lokasi file yang memanggil fungsi ini
        $login_page = (basename(dirname($_SERVER['PHP_SELF'])) == 'admin') ? 'index.php' : 'admin/index.php';
        
        // Jika kita sudah di admin/index.php dan belum login, jangan redirect lagi untuk menghindari loop
        if (basename($_SERVER['PHP_SELF']) == 'index.php' && basename(dirname($_SERVER['PHP_SELF'])) == 'admin' && !(isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true)) {
            return; // Sudah di halaman login, tidak perlu redirect
        }
        
        header("Location: " . $login_page);
        exit;
    }
}


function sanitize_input($data) {
    if (is_array($data)) {
        return array_map('sanitize_input', $data);
    }
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}
?>