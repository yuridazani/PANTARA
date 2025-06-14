<?php
$page_title = "Tambah Data Kecelakaan";
include 'template/header_admin.php';
// Tidak perlu koneksi database di sini, form akan di-submit ke proses_crud.php
?>

<div class="flex justify-between items-center mb-6">
   <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Tambah Data Kecelakaan Baru</h1>
   <a href="data_kecelakaan.php" class="text-sm text-indigo-600 hover:underline">&larr; Kembali ke Data Kecelakaan</a>
</div>


<div class="bg-white p-6 sm:p-8 rounded-xl shadow-lg max-w-2xl mx-auto">
    <form action="proses_crud.php?action=create" method="POST">
        <div class="mb-4">
            <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Singkat Kejadian <span class="text-red-500">*</span></label>
            <textarea name="deskripsi" id="deskripsi" rows="3" required class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Mis: Tabrakan motor di perempatan A"></textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label for="tanggal_kejadian" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Kejadian <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal_kejadian" id="tanggal_kejadian" required class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label for="waktu_kejadian" class="block text-sm font-medium text-gray-700 mb-1">Waktu Kejadian <span class="text-red-500">*</span></label>
                <input type="time" name="waktu_kejadian" id="waktu_kejadian" required class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label for="latitude" class="block text-sm font-medium text-gray-700 mb-1">Latitude <span class="text-red-500">*</span></label>
                <input type="text" name="latitude" id="latitude" required pattern="^-?(\d{1,2}(\.\d{1,8})?|90(\.0{1,8})?)$" title="Format desimal, mis: -7.12345678 (antara -90 dan 90, maks 8 desimal)" class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="-7.xxxxxxx">
                <p class="text-xs text-gray-500 mt-1">Contoh: -7.54890000</p>
            </div>
            <div>
                <label for="longitude" class="block text-sm font-medium text-gray-700 mb-1">Longitude <span class="text-red-500">*</span></label>
                <input type="text" name="longitude" id="longitude" required pattern="^-?(\d{1,3}(\.\d{1,8})?|180(\.0{1,8})?)$" title="Format desimal, mis: 112.12345678 (antara -180 dan 180, maks 8 desimal)" class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="112.xxxxxxx">
                <p class="text-xs text-gray-500 mt-1">Contoh: 112.70000000</p>
            </div>
        </div>
        <p class="text-xs text-indigo-600 mb-4">Tips: Dapatkan koordinat dari Google Maps (klik kanan di peta -> koordinat akan muncul).</p>


        <div class="mb-4">
            <label for="jenis_kendaraan" class="block text-sm font-medium text-gray-700 mb-1">Jenis Kendaraan Terlibat</label>
            <input type="text" name="jenis_kendaraan" id="jenis_kendaraan" class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Mis: Motor, Mobil, Truk (pisahkan koma jika lebih dari satu)">
        </div>

        <div class="mb-4">
            <label for="tingkat_keparahan" class="block text-sm font-medium text-gray-700 mb-1">Tingkat Keparahan</label>
            <select name="tingkat_keparahan" id="tingkat_keparahan" class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">Pilih Tingkat Keparahan</option>
                <option value="Ringan">Ringan</option>
                <option value="Sedang">Sedang</option>
                <option value="Berat">Berat</option>
                <option value="Fatal">Fatal</option>
            </select>
        </div>

        <div class="mb-6">
            <label for="catatan_tambahan" class="block text-sm font-medium text-gray-700 mb-1">Catatan Tambahan</label>
            <textarea name="catatan_tambahan" id="catatan_tambahan" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Informasi lain yang relevan"></textarea>
        </div>

        <div class="flex items-center justify-end space-x-3 mt-6">
            <a href="data_kecelakaan.php" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition duration-150">Batal</a>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-150 ease-in-out">
                Simpan Data
            </button>
        </div>
    </form>
</div>

<?php include 'template/footer_admin.php'; ?>