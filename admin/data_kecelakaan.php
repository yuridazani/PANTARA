<?php
$page_title = "Data Kecelakaan";
include 'template/header_admin.php';
require_once '../config/database.php';

$message = '';
$message_type = ''; // 'success' atau 'error'
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    // Cek apakah ada status spesifik dari proses_impor atau proses_crud
    if (isset($_SESSION['impor_status'])) {
        $message_type = ($_SESSION['impor_status'] == 'success') ? 'success' : 'error';
        unset($_SESSION['impor_status']);
    } elseif (isset($_SESSION['crud_status'])) { // Kamu bisa set 'crud_status' di proses_crud.php
        $message_type = ($_SESSION['crud_status'] == 'success') ? 'success' : 'error';
        unset($_SESSION['crud_status']);
    } else {
        // Fallback sederhana jika status tidak di-set eksplisit
        $message_type = (strpos(strtolower($message), 'berhasil') !== false || strpos(strtolower($message), 'diimpor') !== false) ? 'success' : 'error';
    }
    unset($_SESSION['message']);
}

$sql = "SELECT * FROM tabel_kecelakaan ORDER BY tanggal_kejadian DESC, waktu_kejadian DESC";
$result = $conn->query($sql);
?>

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
    <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Manajemen Data Kecelakaan</h1>
    <a href="form_tambah_kecelakaan.php" class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 px-5 rounded-lg shadow-md hover:shadow-lg transition-all duration-150 ease-in-out text-center text-sm flex items-center justify-center space-x-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
        </svg>
        <span>Tambah Data Baru</span>
    </a>
</div>

<?php if ($message): ?>
    <div class="mb-6 p-4 rounded-lg text-sm flex items-start space-x-3 <?php 
        if ($message_type == 'success') echo 'bg-green-50 text-green-700 border border-green-300';
        else echo 'bg-red-50 text-red-700 border border-red-300'; 
    ?>" role="alert">
        <div class="flex-shrink-0">
            <?php if ($message_type == 'success'): ?>
                <svg class="h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
            <?php else: ?>
                <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                     <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
            <?php endif; ?>
        </div>
        <div>
            <?php echo nl2br(htmlspecialchars($message)); // nl2br untuk menampilkan \n dari proses_impor ?>
        </div>
    </div>
<?php endif; ?>

<div class="bg-white shadow-lg rounded-xl overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lat</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lon</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $row['id_kecelakaan']; ?></td>
                        <td class="px-6 py-4 text-sm text-gray-700 max-w-xs truncate hover:whitespace-normal hover:overflow-visible" title="<?php echo htmlspecialchars($row['deskripsi']); ?>"><?php echo htmlspecialchars(substr($row['deskripsi'], 0, 50)) . (strlen($row['deskripsi']) > 50 ? '...' : ''); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo date("d M Y", strtotime($row['tanggal_kejadian'])); ?></td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo date("H:i", strtotime($row['waktu_kejadian'])); ?></td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo $row['latitude']; ?></td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo $row['longitude']; ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-3">
                            <a href="form_edit_kecelakaan.php?id=<?php echo $row['id_kecelakaan']; ?>" class="text-indigo-600 hover:text-indigo-900 hover:underline">Edit</a>
                            <a href="proses_crud.php?action=delete&id=<?php echo $row['id_kecelakaan']; ?>" 
                               onclick="return confirm('Apakah Anda yakin ingin menghapus data ini? ID: <?php echo $row['id_kecelakaan']; ?>');" 
                               class="text-red-600 hover:text-red-900 hover:underline">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Belum ada data kecelakaan.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
$conn->close();
include 'template/footer_admin.php';
?>