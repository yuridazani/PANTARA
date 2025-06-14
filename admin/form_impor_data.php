<?php
$page_title = "Impor Data Kecelakaan dari Excel/CSV";
include 'template/header_admin.php';
?>

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
    <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Impor Data Kecelakaan</h1>
    <a href="data_kecelakaan.php" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium whitespace-nowrap">&larr; Kembali ke Data Kecelakaan</a>
</div>

<?php if (isset($_SESSION['impor_message'])): ?>
    <div class="mb-6 p-4 rounded-lg text-sm <?php echo (isset($_SESSION['impor_status']) && $_SESSION['impor_status'] == 'success') ? 'bg-green-100 border border-green-400 text-green-700' : 'bg-red-100 border border-red-400 text-red-700'; ?>" role="alert">
        <?php echo nl2br(htmlspecialchars($_SESSION['impor_message'])); // Gunakan nl2br untuk menampilkan baris baru ?>
    </div>
    <?php 
    unset($_SESSION['impor_message']);
    unset($_SESSION['impor_status']);
    ?>
<?php endif; ?>

<div class="bg-white p-6 sm:p-8 rounded-xl shadow-xl max-w-2xl mx-auto">
    <form action="proses_impor.php" method="POST" enctype="multipart/form-data">
        <div class="space-y-6">
            <div>
                <label for="file_excel_csv" class="block text-sm font-medium text-gray-700 mb-1">Pilih File Excel (.xlsx, .xls) atau CSV (.csv) <span class="text-red-500">*</span></label>
                <input type="file" name="file_excel_csv" id="file_excel_csv" required accept=".xlsx, .xls, .csv"
                       class="w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 cursor-pointer focus:outline-none
                              file:mr-4 file:py-2 file:px-4
                              file:rounded-l-lg file:border-0
                              file:text-sm file:font-semibold
                              file:bg-indigo-50 file:text-indigo-700
                              hover:file:bg-indigo-100">
                <p class="mt-1 text-xs text-gray-500">Ukuran maksimal file: 5MB.</p>
            </div>

            <div class="bg-indigo-50 p-4 rounded-lg border border-indigo-200">
                <h3 class="text-md font-semibold text-indigo-700 mb-2">Petunjuk Struktur Kolom:</h3>
                <p class="text-xs text-indigo-600 mb-1">Baris pertama (header) akan diabaikan.</p>
                <ul class="list-disc list-inside text-sm text-gray-700 space-y-1">
                    <li>Kolom A: `deskripsi` (Teks, Wajib)</li>
                    <li>Kolom B: `tanggal_kejadian` (Format: YYYY-MM-DD atau DD/MM/YYYY, Wajib)</li>
                    <li>Kolom C: `waktu_kejadian` (Format: HH:MM:SS atau HH:MM, Wajib)</li>
                    <li>Kolom D: `latitude` (Angka Desimal, Wajib, mis: -7.12345678)</li>
                    <li>Kolom E: `longitude` (Angka Desimal, Wajib, mis: 112.12345678)</li>
                    <li>Kolom F: `jenis_kendaraan` (Teks, Opsional)</li>
                    <li>Kolom G: `tingkat_keparahan` (Teks, Opsional: Ringan, Sedang, Berat, Fatal)</li>
                    <li>Kolom H: `catatan_tambahan` (Teks, Opsional)</li>
                </ul>
            </div>
        </div>

        <div class="mt-8 flex items-center justify-end">
            <button type="submit" name="submit_impor"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 px-6 rounded-lg shadow-md transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50">
                Impor Data
            </button>
        </div>
    </form>
</div>

<?php include 'template/footer_admin.php'; ?>