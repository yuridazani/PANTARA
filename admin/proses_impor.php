<?php
require_once '../config/functions.php'; // Ini sudah menjalankan session_start()
require_once '../config/database.php';
require_once '../vendor/autoload.php'; // Untuk PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

check_admin_login();

$_SESSION['impor_message'] = '';
$_SESSION['impor_status'] = 'error'; // Default 'error'

if (isset($_POST['submit_impor'])) {
    if (isset($_FILES['file_excel_csv']) && $_FILES['file_excel_csv']['error'] == UPLOAD_ERR_OK) {
        $fileName = $_FILES['file_excel_csv']['name'];
        $fileTmpName = $_FILES['file_excel_csv']['tmp_name'];
        $fileSize = $_FILES['file_excel_csv']['size'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExtensions = ['xlsx', 'xls', 'csv'];

        if (in_array($fileExt, $allowedExtensions)) {
            if ($fileSize < 5242880) { // Maks 5MB (5 * 1024 * 1024)
                try {
                    $spreadsheet = IOFactory::load($fileTmpName);
                    $sheet = $spreadsheet->getActiveSheet();
                    $highestRow = $sheet->getHighestRow();

                    $dataToInsert = [];
                    $skippedRowCount = 0;
                    $insertedRowCount = 0;
                    $errorMessages = [];
                    $isHeader = true; // Asumsi baris pertama adalah header

                    for ($row = 1; $row <= $highestRow; $row++) {
                        if ($isHeader) {
                            // Coba deteksi header sederhana, misal jika kolom A berisi "deskripsi"
                            $headerTest = strtolower(trim($sheet->getCell('A' . $row)->getValue()));
                            if (strpos($headerTest, 'deskripsi') !== false || strpos($headerTest, 'tanggal') !== false) {
                                $isHeader = false; // Header terdeteksi dan dilewati
                                continue; 
                            }
                            $isHeader = false; // Jika tidak terdeteksi, anggap baris pertama adalah data
                        }

                        $rowData = [
                            'deskripsi'         => trim((string)$sheet->getCell('A' . $row)->getValue()),
                            'tanggal_kejadian'  => $sheet->getCell('B' . $row)->getValue(),
                            'waktu_kejadian'    => $sheet->getCell('C' . $row)->getValue(),
                            'latitude'          => trim((string)$sheet->getCell('D' . $row)->getValue()),
                            'longitude'         => trim((string)$sheet->getCell('E' . $row)->getValue()),
                            'jenis_kendaraan'   => trim((string)$sheet->getCell('F' . $row)->getValue()),
                            'tingkat_keparahan' => trim((string)$sheet->getCell('G' . $row)->getValue()),
                            'catatan_tambahan'  => trim((string)$sheet->getCell('H' . $row)->getValue()),
                        ];

                        if (empty($rowData['deskripsi']) && empty($rowData['tanggal_kejadian']) && empty($rowData['latitude'])) {
                            // Anggap baris kosong jika field utama kosong
                            continue;
                        }

                        // Validasi data wajib
                        if (empty($rowData['deskripsi']) || empty($rowData['tanggal_kejadian']) || empty($rowData['waktu_kejadian']) || empty($rowData['latitude']) || empty($rowData['longitude'])) {
                            $errorMessages[] = "Baris $row: Data wajib (deskripsi, tanggal, waktu, lat, lon) tidak lengkap.";
                            $skippedRowCount++;
                            continue;
                        }

                        // Konversi dan Validasi Tanggal
                        $validDate = null;
                        if (is_numeric($rowData['tanggal_kejadian'])) {
                            try { $validDate = ExcelDate::excelToDateTimeObject($rowData['tanggal_kejadian'])->format('Y-m-d'); } catch (Exception $e) {}
                        } else {
                            $dateFormatsToTry = ['Y-m-d', 'd/m/Y', 'm/d/Y', 'd-m-Y', 'm-d-Y'];
                            foreach ($dateFormatsToTry as $format) {
                                $dateObj = DateTime::createFromFormat($format, $rowData['tanggal_kejadian']);
                                if ($dateObj && $dateObj->format($format) === $rowData['tanggal_kejadian']) {
                                    $validDate = $dateObj->format('Y-m-d');
                                    break;
                                }
                            }
                        }
                        if (!$validDate) {
                            $errorMessages[] = "Baris $row: Format tanggal_kejadian ('{$rowData['tanggal_kejadian']}') tidak valid. Gunakan YYYY-MM-DD atau DD/MM/YYYY.";
                            $skippedRowCount++;
                            continue;
                        }
                        $rowData['tanggal_kejadian'] = $validDate;

                        // Konversi dan Validasi Waktu
                        $validTime = null;
                        if (is_numeric($rowData['waktu_kejadian']) && $rowData['waktu_kejadian'] < 1) {
                           try { $validTime = ExcelDate::excelToDateTimeObject($rowData['waktu_kejadian'])->format('H:i:s'); } catch (Exception $e) {}
                        } else {
                            $timeFormatsToTry = ['H:i:s', 'H:i'];
                            foreach ($timeFormatsToTry as $format) {
                                $timeObj = DateTime::createFromFormat($format, $rowData['waktu_kejadian']);
                                // Untuk H:i, tambahkan :00 agar valid sebagai time SQL
                                if ($format === 'H:i' && $timeObj && $timeObj->format($format) === $rowData['waktu_kejadian']) {
                                    $validTime = $timeObj->format('H:i:00');
                                    break;
                                } elseif ($timeObj && $timeObj->format($format) === $rowData['waktu_kejadian']) {
                                    $validTime = $timeObj->format('H:i:s');
                                    break;
                                }
                            }
                        }
                        if (!$validTime) {
                            $errorMessages[] = "Baris $row: Format waktu_kejadian ('{$rowData['waktu_kejadian']}') tidak valid. Gunakan HH:MM:SS atau HH:MM.";
                            $skippedRowCount++;
                            continue;
                        }
                        $rowData['waktu_kejadian'] = $validTime;


                        // Validasi Latitude dan Longitude
                        $lat = filter_var($rowData['latitude'], FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                        $lon = filter_var($rowData['longitude'], FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

                        if ($lat === false || $lon === false || $lat < -90 || $lat > 90 || $lon < -180 || $lon > 180) {
                            $errorMessages[] = "Baris $row: Latitude ('{$rowData['latitude']}') atau Longitude ('{$rowData['longitude']}') tidak valid.";
                            $skippedRowCount++;
                            continue;
                        }
                        $rowData['latitude'] = $lat;
                        $rowData['longitude'] = $lon;

                        $dataToInsert[] = $rowData;
                    }

                    if (!empty($dataToInsert)) {
                        $conn->begin_transaction();
                        $sql = "INSERT INTO tabel_kecelakaan (deskripsi, tanggal_kejadian, waktu_kejadian, latitude, longitude, jenis_kendaraan, tingkat_keparahan, catatan_tambahan) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                        $stmt = $conn->prepare($sql);

                        if ($stmt === false) {
                            throw new Exception("Gagal menyiapkan statement SQL: " . $conn->error);
                        }

                        foreach ($dataToInsert as $item) {
                            $stmt->bind_param("sssddsss", 
                                $item['deskripsi'], $item['tanggal_kejadian'], $item['waktu_kejadian'], 
                                $item['latitude'], $item['longitude'], $item['jenis_kendaraan'], 
                                $item['tingkat_keparahan'], $item['catatan_tambahan']
                            );
                            if ($stmt->execute()) {
                                $insertedRowCount++;
                            } else {
                                $errorMessages[] = "Gagal insert: " . $stmt->error . " (Data: " . htmlspecialchars(substr(json_encode($item), 0, 100)) . "...)";
                                $skippedRowCount++;
                            }
                        }
                        $stmt->close();
                        $conn->commit();

                        $_SESSION['impor_message'] = "$insertedRowCount data berhasil diimpor.";
                        if ($skippedRowCount > 0) {
                             $_SESSION['impor_message'] .= " $skippedRowCount data dilewati karena format/validasi error.";
                        }
                        $_SESSION['impor_status'] = 'success';

                    } else if (empty($errorMessages) && $highestRow <=1) { // Hanya header atau file kosong
                        $_SESSION['impor_message'] = "Tidak ada data valid untuk diimpor dari file (mungkin hanya header atau file kosong).";
                    } else if (empty($errorMessages) && $skippedRowCount == ($highestRow-1) && $highestRow > 1){
                        $_SESSION['impor_message'] = "Tidak ada data valid untuk diimpor dari file. Semua baris data dilewati.";
                    }

                    if (!empty($errorMessages)) {
                       if (!empty($_SESSION['impor_message']) && $_SESSION['impor_status'] == 'success') { // Jika ada yang sukses dan ada yang error
                            $_SESSION['impor_message'] .= "\n\nNamun, terdapat beberapa error:";
                       } elseif (empty($_SESSION['impor_message'])) { // Jika semua error
                            $_SESSION['impor_message'] = "Proses impor gagal. Terdapat error berikut:";
                            $_SESSION['impor_status'] = 'error';
                       }
                        $_SESSION['impor_message'] .= "\n" . implode("\n", array_map('htmlspecialchars', array_slice($errorMessages, 0, 10))); // Tampilkan maks 10 error
                        if(count($errorMessages) > 10) {
                           $_SESSION['impor_message'] .= "\n...dan " . (count($errorMessages) - 10) . " error lainnya.";
                        }
                    }

                } catch (Exception $e) {
                    if(isset($conn) && $conn->ping()) { // Cek apakah koneksi masih ada sebelum rollback
                       try { $conn->rollback(); } catch(Exception $rbEx) {}
                    }
                    $_SESSION['impor_message'] = "Error saat memproses file: " . htmlspecialchars($e->getMessage());
                    error_log("Import Error: " . $e->getMessage());
                    $_SESSION['impor_status'] = 'error';
                }
            } else {
                $_SESSION['impor_message'] = "Ukuran file terlalu besar (maks 5MB).";
            }
        } else {
            $_SESSION['impor_message'] = "Format file tidak didukung. Hanya .xlsx, .xls, atau .csv.";
        }
    } else {
        $_SESSION['impor_message'] = "Tidak ada file yang diunggah atau terjadi error saat unggah (Error code: " . $_FILES['file_excel_csv']['error'] . ").";
    }
} else {
    $_SESSION['impor_message'] = "Aksi impor tidak valid.";
}

header("Location: form_impor_data.php");
exit;
?>