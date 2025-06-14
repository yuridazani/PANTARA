<?php
header('Content-Type: application/json');
require_once 'config/database.php';

$data_kecelakaan = [];
// Ambil parameter tahun jika ada
$tahun = isset($_GET['tahun']) ? intval($_GET['tahun']) : null;

$sql = "SELECT id_kecelakaan, deskripsi, tanggal_kejadian, waktu_kejadian, latitude, longitude, jenis_kendaraan, tingkat_keparahan, catatan_tambahan FROM tabel_kecelakaan";

if ($tahun) {
    $sql .= " WHERE YEAR(tanggal_kejadian) = ?";
}

$sql .= " ORDER BY tanggal_kejadian DESC, waktu_kejadian DESC";

$stmt = $conn->prepare($sql);

if ($stmt === false) {
    http_response_code(500);
    echo json_encode(["error" => "Gagal menyiapkan statement SQL (get_data_kecelakaan): " . $conn->error]);
    $conn->close();
    exit;
}

if ($tahun) {
    $stmt->bind_param("i", $tahun);
}

$stmt->execute();
$result = $stmt->get_result();

if ($result) {
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            // Format waktu agar hanya HH:MM
            if ($row['waktu_kejadian']) {
                $timeObj = date_create_from_format('H:i:s', $row['waktu_kejadian']);
                if ($timeObj) {
                    $row['waktu_kejadian'] = $timeObj->format('H:i');
                }
            }
            $data_kecelakaan[] = $row;
        }
    }
    // Tidak ada error jika 0 baris, kembalikan array kosong
} else {
    http_response_code(500);
    echo json_encode(["error" => "Gagal mengambil data kecelakaan: " . $stmt->error]);
    $stmt->close();
    $conn->close();
    exit;
}

$stmt->close();
$conn->close();
echo json_encode($data_kecelakaan);
?>