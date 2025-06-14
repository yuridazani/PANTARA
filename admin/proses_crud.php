<?php
require_once '../config/functions.php'; // Ini akan menjalankan session_start()
require_once '../config/database.php';

check_admin_login(); // Pastikan hanya admin yang bisa akses

$action = $_GET['action'] ?? ''; // Ambil aksi dari URL

// Proses CREATE
if ($_SERVER["REQUEST_METHOD"] == "POST" && $action == 'create') {
    $deskripsi = sanitize_input($_POST['deskripsi']);
    $tanggal_kejadian = sanitize_input($_POST['tanggal_kejadian']);
    $waktu_kejadian = sanitize_input($_POST['waktu_kejadian']);
    $latitude = filter_var($_POST['latitude'], FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $longitude = filter_var($_POST['longitude'], FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $jenis_kendaraan = isset($_POST['jenis_kendaraan']) ? sanitize_input($_POST['jenis_kendaraan']) : null;
    $tingkat_keparahan = isset($_POST['tingkat_keparahan']) ? sanitize_input($_POST['tingkat_keparahan']) : null;
    $catatan_tambahan = isset($_POST['catatan_tambahan']) ? sanitize_input($_POST['catatan_tambahan']) : null;

    // Validasi dasar
    if (empty($deskripsi) || empty($tanggal_kejadian) || empty($waktu_kejadian) || $latitude === false || $longitude === false) {
        $_SESSION['message'] = "Error: Semua field bertanda (*) wajib diisi dan koordinat harus berupa angka yang valid.";
        header("Location: form_tambah_kecelakaan.php");
        exit;
    }
    if ($latitude < -90 || $latitude > 90 || $longitude < -180 || $longitude > 180) {
        $_SESSION['message'] = "Error: Nilai Latitude (-90 s/d 90) atau Longitude (-180 s/d 180) tidak valid.";
        header("Location: form_tambah_kecelakaan.php");
        exit;
    }

    $sql = "INSERT INTO tabel_kecelakaan (deskripsi, tanggal_kejadian, waktu_kejadian, latitude, longitude, jenis_kendaraan, tingkat_keparahan, catatan_tambahan) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        $_SESSION['message'] = "Error: Gagal menyiapkan statement SQL (create). " . $conn->error;
    } else {
        $stmt->bind_param("sssddsss", $deskripsi, $tanggal_kejadian, $waktu_kejadian, $latitude, $longitude, $jenis_kendaraan, $tingkat_keparahan, $catatan_tambahan);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Data kecelakaan berhasil ditambahkan.";
        } else {
            $_SESSION['message'] = "Error: Gagal menambahkan data. " . $stmt->error;
        }
        $stmt->close();
    }
    header("Location: data_kecelakaan.php");
    exit;
}
// Proses UPDATE
elseif ($_SERVER["REQUEST_METHOD"] == "POST" && $action == 'update') {
    $id_kecelakaan = isset($_POST['id_kecelakaan']) ? intval($_POST['id_kecelakaan']) : 0;
    $deskripsi = sanitize_input($_POST['deskripsi']);
    $tanggal_kejadian = sanitize_input($_POST['tanggal_kejadian']);
    $waktu_kejadian = sanitize_input($_POST['waktu_kejadian']);
    $latitude = filter_var($_POST['latitude'], FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $longitude = filter_var($_POST['longitude'], FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $jenis_kendaraan = isset($_POST['jenis_kendaraan']) ? sanitize_input($_POST['jenis_kendaraan']) : null;
    $tingkat_keparahan = isset($_POST['tingkat_keparahan']) ? sanitize_input($_POST['tingkat_keparahan']) : null;
    $catatan_tambahan = isset($_POST['catatan_tambahan']) ? sanitize_input($_POST['catatan_tambahan']) : null;

    if (empty($deskripsi) || empty($tanggal_kejadian) || empty($waktu_kejadian) || $latitude === false || $longitude === false || empty($id_kecelakaan)) {
        $_SESSION['message'] = "Error: Semua field bertanda (*) wajib diisi, ID valid, dan koordinat harus berupa angka yang valid.";
        header("Location: form_edit_kecelakaan.php?id=" . $id_kecelakaan);
        exit;
    }
     if ($latitude < -90 || $latitude > 90 || $longitude < -180 || $longitude > 180) {
        $_SESSION['message'] = "Error: Nilai Latitude (-90 s/d 90) atau Longitude (-180 s/d 180) tidak valid.";
        header("Location: form_edit_kecelakaan.php?id=" . $id_kecelakaan);
        exit;
    }

    $sql = "UPDATE tabel_kecelakaan SET deskripsi=?, tanggal_kejadian=?, waktu_kejadian=?, latitude=?, longitude=?, jenis_kendaraan=?, tingkat_keparahan=?, catatan_tambahan=? WHERE id_kecelakaan=?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        $_SESSION['message'] = "Error: Gagal menyiapkan statement SQL (update). " . $conn->error;
    } else {
        $stmt->bind_param("sssddsssi", $deskripsi, $tanggal_kejadian, $waktu_kejadian, $latitude, $longitude, $jenis_kendaraan, $tingkat_keparahan, $catatan_tambahan, $id_kecelakaan);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Data kecelakaan berhasil diperbarui.";
        } else {
            $_SESSION['message'] = "Error: Gagal memperbarui data. " . $stmt->error;
        }
        $stmt->close();
    }
    header("Location: data_kecelakaan.php");
    exit;
}
// Proses DELETE
elseif ($_SERVER["REQUEST_METHOD"] == "GET" && $action == 'delete') {
    $id_kecelakaan = isset($_GET['id']) ? intval($_GET['id']) : 0;
    if (empty($id_kecelakaan)) {
        $_SESSION['message'] = "Error: ID Kecelakaan tidak valid untuk dihapus.";
        header("Location: data_kecelakaan.php");
        exit;
    }

    $sql = "DELETE FROM tabel_kecelakaan WHERE id_kecelakaan = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        $_SESSION['message'] = "Error: Gagal menyiapkan statement SQL (delete). " . $conn->error;
    } else {
        $stmt->bind_param("i", $id_kecelakaan);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Data kecelakaan berhasil dihapus.";
        } else {
            $_SESSION['message'] = "Error: Gagal menghapus data. " . $stmt->error;
        }
        $stmt->close();
    }
    header("Location: data_kecelakaan.php");
    exit;
}
// Jika aksi tidak dikenali
else {
    $_SESSION['message'] = "Aksi tidak valid atau metode request salah.";
    header("Location: data_kecelakaan.php");
    exit;
}

$conn->close();
?>