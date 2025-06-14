<?php
session_start();
// Jika sudah login, arahkan ke dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: dashboard.php");
    exit;
}
// Tidak perlu require_once '../config/database.php'; karena tidak ada query di sini
$error_message = '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin PANTARA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-sm">
        <div class="text-center mb-8">
           <a href="../index.php" class="text-3xl font-bold text-indigo-600 hover:text-indigo-700">PANTARA</a>
        </div>
        <h2 class="text-xl font-semibold text-center text-gray-700 mb-6">Login Admin</h2>

        <?php if (!empty($_SESSION['login_error'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-4 text-sm" role="alert">
                <span class="block sm:inline"><?php echo $_SESSION['login_error']; ?></span>
            </div>
            <?php unset($_SESSION['login_error']); // Hapus pesan error setelah ditampilkan ?>
        <?php endif; ?>

        <form action="proses_login.php" method="POST">
            <div class="mb-4">
                <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                <input type="text" name="username" id="username" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                       placeholder="Masukkan username">
            </div>
            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" name="password" id="password" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                       placeholder="Masukkan password">
            </div>
            <button type="submit"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 px-4 rounded-lg shadow-md transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50">
                Login
            </button>
        </form>
        <p class="text-xs text-gray-500 text-center mt-6">
            Kembali ke <a href="../index.php" class="text-indigo-600 hover:underline">Halaman Utama Publik</a>
        </p>
    </div>
</body>
</html>