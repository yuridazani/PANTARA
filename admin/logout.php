<?php
session_start(); // Akses session yang ada

// Hancurkan semua variabel session
$_SESSION = array();

// Hancurkan cookie session jika ada
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Hancurkan session
session_destroy();

// Arahkan ke halaman login
header("Location: index.php");
exit;
?>