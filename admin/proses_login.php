<?php
session_start(); // Pastikan session_start() ada di paling atas
require_once '../config/database.php';
require_once '../config/functions.php'; // Untuk sanitize_input

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = sanitize_input($_POST['username']);
    $password = $_POST['password']; // Password tidak di-sanitize dengan htmlspecialchars karena akan diverifikasi

    if (empty($username) || empty($password)) {
        $_SESSION['login_error'] = "Username dan password tidak boleh kosong.";
        header("Location: index.php");
        exit;
    }

    $sql = "SELECT id_admin, username, password, nama_lengkap FROM tabel_admin WHERE username = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        // Error saat prepare statement
        $_SESSION['login_error'] = "Terjadi kesalahan pada server (prepare). Kode: DB-PLP01";
        error_log("MySQLi prepare error: " . $conn->error); // Log error untuk admin server
        header("Location: index.php");
        exit;
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();
        if (password_verify($password, $admin['password'])) {
            // Password cocok
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['id_admin'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_nama_lengkap'] = $admin['nama_lengkap'];

            unset($_SESSION['login_error']); // Hapus pesan error jika sukses
            header("Location: dashboard.php"); // Arahkan ke dashboard admin
            exit;
        } else {
            // Password tidak cocok
            $_SESSION['login_error'] = "Username atau password salah.";
            header("Location: index.php");
            exit;
        }
    } else {
        // Username tidak ditemukan
        $_SESSION['login_error'] = "Username atau password salah.";
        header("Location: index.php");
        exit;
    }
    $stmt->close();
} else {
    // Jika bukan metode POST, arahkan kembali
    header("Location: index.php");
    exit;
}
$conn->close();
?>