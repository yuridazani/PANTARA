<?php
$page_title = "Peta Rawan Kecelakaan Pandaan";
include 'includes/header_public.php';
require_once 'config/database.php'; // Untuk mendapatkan min/max tahun

// Dapatkan tahun minimal dan maksimal dari data kecelakaan untuk slider
$min_year_default = date('Y') - 5; // Default 5 tahun lalu
$max_year_default = date('Y');   // Default tahun ini

$min_year = $min_year_default;
$max_year = $max_year_default;

$sql_years = "SELECT MIN(YEAR(tanggal_kejadian)) as min_t, MAX(YEAR(tanggal_kejadian)) as max_t FROM tabel_kecelakaan WHERE tanggal_kejadian IS NOT NULL AND YEAR(tanggal_kejadian) > 1900";
$result_years = $conn->query($sql_years);
if ($result_years && $result_years->num_rows > 0) {
    $row_years = $result_years->fetch_assoc();
    if ($row_years['min_t']) $min_year = $row_years['min_t'];
    if ($row_years['max_t']) $max_year = $row_years['max_t'];
}
// Jika tidak ada data sama sekali, atau data tahun tidak valid, gunakan default
if ($min_year > $max_year) {
    $min_year = $min_year_default;
    $max_year = $max_year_default;
}
$conn->close();
?>

<div class="bg-white p-4 sm:p-6 rounded-xl shadow-lg mb-6">
    <h1 class="text-xl sm:text-2xl font-bold text-gray-800 mb-2">Peta Interaktif Titik Rawan Kecelakaan Pandaan</h1>
    <p class="text-gray-600 text-sm sm:text-base">Jelajahi lokasi kecelakaan, area konsentrasi (heatmap), dan lihat perubahan berdasarkan rentang waktu.</p>
</div>

<div class="time-slider-container mb-6">
    <label for="timeSlider" class="text-gray-700 text-lg font-medium">Filter berdasarkan Tahun Kejadian:</label>
    <div class="flex items-center space-x-3 mt-2">
        <span id="sliderMinLabel" class="text-sm text-indigo-600 font-medium"><?php echo $min_year; ?></span>
        <input type="range" id="timeSlider" name="timeSlider" min="<?php echo $min_year; ?>" max="<?php echo $max_year; ?>" value="<?php echo $max_year; ?>" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-indigo-600 flex-grow">
        <span id="sliderValue" class="text-lg text-indigo-700 font-semibold w-16 text-center bg-indigo-50 p-1 rounded-md"><?php echo $max_year; ?></span>
    </div>
     <p class="text-xs text-gray-500 mt-1">Geser untuk melihat data pada tahun tertentu. Peta akan otomatis diperbarui.</p>
</div>


<div id="mapPantaraContainer" class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
    <div id="mapPantara">
        <div class="flex items-center justify-center h-full">
            <p class="text-gray-500 p-10 text-center">Memuat peta dan data kecelakaan... <br><small>Pastikan Anda memiliki koneksi internet.</small></p>
        </div>
    </div>
</div>

<div class="mt-6 p-4 bg-white rounded-xl shadow-lg text-sm text-gray-700">
    <p class="font-semibold text-base mb-2">Legenda Peta:</p>
    <ul class="list-disc list-inside ml-4 space-y-1">
        <li><span class="inline-block w-3 h-3 bg-red-600 rounded-full mr-2 align-middle shadow"></span> Titik Lokasi Kecelakaan</li>
        <li><span class="inline-block w-3 h-3 bg-orange-500 rounded-full mr-2 align-middle shadow"></span> Titik Jalan Rusak (Layer Kontekstual)</li>
        <li><span class="inline-block w-3 h-3 bg-yellow-400 rounded-full mr-2 align-middle shadow"></span> Titik Penerangan Minim (Layer Kontekstual)</li>
        <li><span class="inline-block w-3 h-3 bg-green-500 rounded-full mr-2 align-middle shadow"></span> Titik Fasilitas Umum (Contoh: Pasar)</li>
        <li>Area berwarna pada <span class="font-semibold">Heatmap</span> menunjukkan konsentrasi kejadian kecelakaan (Merah = Tinggi).</li>
    </ul>
    <p class="mt-3"><span class="font-semibold">Tip:</span> Gunakan kontrol layer di pojok kanan atas peta untuk menampilkan/menyembunyikan Titik Kecelakaan, Heatmap, dan Layer Kontekstual.</p>
</div>

<script src="assets/js/map_script.js"></script>

<?php include 'includes/footer_public.php'; ?>