<?php
$page_title = "Dashboard Admin";
include 'template/header_admin.php';
require_once '../config/database.php';

// Ambil data summary
$total_kecelakaan = 0;
$kecelakaan_bulan_ini = 0;
$total_kontekstual = 0;
$data_terbaru_timestamp = null;

// Total Kecelakaan
$sql_total_laka = "SELECT COUNT(*) as total FROM tabel_kecelakaan";
$res_total_laka = $conn->query($sql_total_laka);
if ($res_total_laka && $res_total_laka->num_rows > 0) {
    $total_kecelakaan = $res_total_laka->fetch_assoc()['total'];
}

// Kecelakaan Bulan Ini
$bulan_ini_angka = date('m');
$tahun_ini_angka = date('Y');
$sql_laka_bulan_ini = "SELECT COUNT(*) as total FROM tabel_kecelakaan WHERE MONTH(tanggal_kejadian) = ? AND YEAR(tanggal_kejadian) = ?";
$stmt_laka_bulan = $conn->prepare($sql_laka_bulan_ini);
if ($stmt_laka_bulan) {
    $stmt_laka_bulan->bind_param("ss", $bulan_ini_angka, $tahun_ini_angka);
    $stmt_laka_bulan->execute();
    $res_laka_bulan_ini = $stmt_laka_bulan->get_result();
    if ($res_laka_bulan_ini && $res_laka_bulan_ini->num_rows > 0) {
        $kecelakaan_bulan_ini = $res_laka_bulan_ini->fetch_assoc()['total'];
    }
    $stmt_laka_bulan->close();
}

// Total Data Kontekstual
$sql_total_konteks = "SELECT COUNT(*) as total FROM tabel_kontekstual";
$res_total_konteks = $conn->query($sql_total_konteks);
if ($res_total_konteks && $res_total_konteks->num_rows > 0) {
    $total_kontekstual = $res_total_konteks->fetch_assoc()['total'];
}

// Data Terbaru Dimasukkan (dari tabel kecelakaan)
$sql_terbaru = "SELECT MAX(created_at) as terbaru FROM tabel_kecelakaan";
$res_terbaru = $conn->query($sql_terbaru);
if ($res_terbaru && $res_terbaru->num_rows > 0) {
    $row_terbaru = $res_terbaru->fetch_assoc();
    if ($row_terbaru['terbaru']) {
        $data_terbaru_timestamp = date("d M Y, H:i", strtotime($row_terbaru['terbaru']));
    } else {
        $data_terbaru_timestamp = "-";
    }
}


$conn->close();
?>

<div class="mb-8">
    <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Selamat Datang, <?php echo htmlspecialchars($_SESSION['admin_nama_lengkap'] ?? $_SESSION['admin_username']); ?>!</h1>
    <p class="text-gray-600 mt-1">Ringkasan data dari Sistem Informasi Geografis PANTARA.</p>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
    <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-indigo-500 hover:shadow-xl transition-shadow duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Kecelakaan</p>
                <p class="text-3xl font-bold text-gray-800 mt-1"><?php echo $total_kecelakaan; ?></p>
            </div>
            <div class="bg-indigo-100 text-indigo-600 p-3 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-teal-500 hover:shadow-xl transition-shadow duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Kecelakaan Bulan Ini</p>
                <p class="text-3xl font-bold text-gray-800 mt-1"><?php echo $kecelakaan_bulan_ini; ?></p>
            </div>
            <div class="bg-teal-100 text-teal-600 p-3 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-amber-500 hover:shadow-xl transition-shadow duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Data Kontekstual</p>
                <p class="text-3xl font-bold text-gray-800 mt-1"><?php echo $total_kontekstual; ?></p>
            </div>
            <div class="bg-amber-100 text-amber-600 p-3 rounded-full">
                 <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
        </div>
    </div>
    
    <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-rose-500 hover:shadow-xl transition-shadow duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Update Data Terakhir</p>
                <p class="text-xl font-semibold text-gray-800 mt-1"><?php echo $data_terbaru_timestamp; ?></p>
            </div>
            <div class="bg-rose-100 text-rose-600 p-3 rounded-full">
                 <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            </div>
        </div>
    </div>
</div>

<div class="mt-10 grid grid-cols-1 md:grid-cols-2 gap-6">
    <a href="data_kecelakaan.php" class="block bg-gradient-to-r from-indigo-500 to-purple-600 p-6 rounded-xl shadow-lg hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-300 text-white">
        <h2 class="text-2xl font-semibold mb-3">Kelola Data Kecelakaan</h2>
        <p class="text-indigo-100 text-sm">Tambah, lihat, edit, atau hapus data kecelakaan yang akan ditampilkan di peta publik.</p>
    </a>
    <a href="form_impor_data.php" class="block bg-gradient-to-r from-teal-500 to-cyan-600 p-6 rounded-xl shadow-lg hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-300 text-white">
        <h2 class="text-2xl font-semibold mb-3">Impor Data Excel/CSV</h2>
        <p class="text-teal-100 text-sm">Unggah file Excel atau CSV untuk menambahkan data kecelakaan secara massal.</p>
    </a>
</div>

<?php include 'template/footer_admin.php'; ?>