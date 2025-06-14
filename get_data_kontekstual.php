<?php
header('Content-Type: application/json');
require_once 'config/database.php';

$data_kontekstual = [];
$sql = "SELECT id_konteks, nama_layer, tipe_objek, latitude, longitude, deskripsi, ikon FROM tabel_kontekstual ORDER BY nama_layer, id_konteks";

$result = $conn->query($sql);

if ($result) {
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $data_kontekstual[] = $row;
        }
    }
} else {
    http_response_code(500);
    echo json_encode(["error" => "Gagal mengambil data kontekstual: " . $conn->error]);
    $conn->close();
    exit;
}
$conn->close();
echo json_encode($data_kontekstual);
?>