<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/functions.php';
check_admin_login();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin PANTARA - <?php echo isset($page_title) ? htmlspecialchars($page_title) : 'Panel Admin'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            overscroll-behavior: none;
        }
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #c4c4c4; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #a5a5a5; }
        
        .sidebar-link-active {
            background-color: #4338ca; /* indigo-700 */
            color: white;
            font-weight: 600;
        }
        #mobileMenu a.sidebar-link-active {
            background-color: #e0e7ff; /* indigo-100 */
            color: #3730a3; /* indigo-800 */
        }

        /* --- Sidebar Collapsible Styles --- */
        #sidebar {
            transition: width 0.3s ease-in-out, transform 0.3s ease-in-out;
            width: 16rem; /* 256px atau w-64 */
        }
        #sidebar.collapsed {
            width: 4.5rem; /* Lebar saat collapsed (hanya ikon) atau 0 jika mau hilang total */
            /* transform: translateX(-100%); */ /* Jika mau hilang total dari kiri */
        }
        #sidebar.collapsed .sidebar-text {
            display: none; /* Sembunyikan teks saat collapsed */
        }
        #sidebar.collapsed .sidebar-brand-text {
             display: none; /* Sembunyikan teks brand */
        }
        #sidebar.collapsed .sidebar-logout-text {
            display: none; /* Sembunyikan teks logout */
        }
         #sidebar.collapsed .sidebar-link a {
            justify-content: center; /* Pusatkan ikon */
        }
        #sidebar .sidebar-link svg {
            min-width: 1.5rem; /* Agar ikon tetap terlihat saat teks hilang */
        }

        /* Konten utama menyesuaikan */
        #mainContent.sidebar-collapsed {
            margin-left: 4.5rem; /* Sesuaikan dengan lebar sidebar collapsed */
        }
        #mainContent {
            transition: margin-left 0.3s ease-in-out;
            margin-left: 16rem; /* Default margin */
        }
        @media (max-width: 767px) { /* md breakpoint */
            #sidebar {
                transform: translateX(-100%); /* Sembunyikan di mobile */
                position: fixed; /* Agar bisa overlay */
                height: 100vh;
                z-index: 40; /* Di atas konten lain tapi di bawah mobile header jika perlu */
            }
            #sidebar.open {
                transform: translateX(0%);
            }
            #mainContent {
                margin-left: 0 !important; /* Konten utama full width di mobile */
            }
        }

    </style>
</head>
<body class="bg-gray-100 text-gray-800 antialiased">
    <div class="flex h-screen bg-gray-100">
        <aside id="sidebar" class="bg-indigo-600 text-indigo-100 p-4 sm:p-6 space-y-6 flex flex-col shadow-lg fixed md:relative h-full z-40">
            <div class="flex items-center justify-between mb-6">
                <a href="dashboard.php" class="flex items-center space-x-2 text-white text-2xl font-bold">
                    <svg class="w-8 h-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503-8.25a4.5 4.5 0 1 1-8.498 1.542A4.5 4.5 0 0 1 18.503 6.75Z" />
                    </svg>
                    <span class="sidebar-brand-text">PANTARA</span>
                </a>
                <button id="desktopSidebarToggler" class="hidden md:block p-1 rounded-md text-indigo-200 hover:bg-indigo-700">
                    <svg class="w-6 h-6" id="desktopTogglerIconOpen" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                    </svg>
                    <svg class="w-6 h-6 hidden" id="desktopTogglerIconClose" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                    </svg>
                </button>
            </div>

            <nav class="space-y-1 flex-grow">
                <div class="sidebar-link">
                    <a href="dashboard.php" 
                       class="flex items-center space-x-3 px-4 py-2.5 rounded-lg hover:bg-indigo-700 hover:text-white transition duration-200 
                              <?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'sidebar-link-active' : 'text-indigo-100'; ?>">
                        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25A2.25 2.25 0 0 1 13.5 8.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" /></svg>
                        <span class="sidebar-text">Dashboard</span>
                    </a>
                </div>
                <div class="sidebar-link">
                    <a href="data_kecelakaan.php" 
                       class="flex items-center space-x-3 px-4 py-2.5 rounded-lg hover:bg-indigo-700 hover:text-white transition duration-200 
                              <?php echo (strpos(basename($_SERVER['PHP_SELF']), 'data_kecelakaan.php') !== false || 
                                         strpos(basename($_SERVER['PHP_SELF']), 'form_tambah_kecelakaan.php') !== false || 
                                         strpos(basename($_SERVER['PHP_SELF']), 'form_edit_kecelakaan.php') !== false) 
                                         ? 'sidebar-link-active' : 'text-indigo-100'; ?>">
                        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8.25v-1.5m0 1.5c-1.355 0-2.697.056-4.024.166C6.84 6.988 6 7.655 6 8.588v3.823c0 .933.84 1.6 1.976 1.666C9.303 14.144 10.645 14.2 12 14.2m0-5.95c1.355 0 2.697-.056 4.024-.166C17.16 7.988 18 7.322 18 6.412v-3.823c0-.933-.84-1.6-1.976-1.666C14.697 1.056 13.355 1 12 1m0 0a2.25 2.25 0 1 0 0 4.5 2.25 2.25 0 0 0 0-4.5ZM12 19.5v-1.5m0 1.5c-1.355 0-2.697-.056-4.024-.166C6.84 17.688 6 16.979 6 16.046v-3.823c0-.933.84-1.6 1.976-1.666C9.303 10.456 10.645 10.4 12 10.4m0 9.1c1.355 0 2.697.056 4.024.166c1.136.066 1.976.733 1.976 1.666v3.823c0 .933-.84 1.6-1.976 1.666C14.697 22.944 13.355 23 12 23m0 0a2.25 2.25 0 1 0 0-4.5 2.25 2.25 0 0 0 0 4.5Z" /></svg>
                        <span class="sidebar-text">Data Kecelakaan</span>
                    </a>
                </div>
                <div class="sidebar-link">
                    <a href="form_impor_data.php" 
                       class="flex items-center space-x-3 px-4 py-2.5 rounded-lg hover:bg-indigo-700 hover:text-white transition duration-200 
                              <?php echo (basename($_SERVER['PHP_SELF']) == 'form_impor_data.php') ? 'sidebar-link-active' : 'text-indigo-100'; ?>">
                        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0 3 3m-3-3-3 3M6.75 19.5a4.5 4.5 0 0 1-1.41-8.775 5.25 5.25 0 0 1 10.338-2.32 5.75 5.75 0 0 1-1.046 9.041c-.507 1.097-.986 2.265-1.417 3.459a1.125 1.125 0 0 1-2.06 0c-.431-1.194-.91-2.362-1.417-3.459Z" /></svg>
                        <span class="sidebar-text">Impor Data</span>
                    </a>
                </div>
            </nav>
            <div class="mt-auto pt-4 border-t border-indigo-500">
                 <a href="logout.php" 
                    class="flex items-center space-x-3 w-full text-center px-4 py-2.5 rounded-lg bg-red-500 hover:bg-red-600 text-white transition duration-200 font-semibold">
                     <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" /></svg>
                     <span class="sidebar-logout-text">Logout</span>
                 </a>
            </div>
        </aside>

        <div id="mainContent" class="flex-1 flex flex-col overflow-hidden md:margin-left-64">
            <header class="bg-white shadow-md p-4">
                <div class="flex justify-between items-center">
                    <button id="mobileSidebarToggler" class="md:hidden p-2 rounded-md text-gray-600 hover:bg-gray-200">
                         <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>
                    <div class="relative hidden sm:block">
                        <input type="text" id="adminSearch" placeholder="Cari data kecelakaan..."
                               class="w-full sm:w-64 md:w-96 px-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" >
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                     <div class="text-sm text-gray-600">
                        Halo, <span class="font-medium text-indigo-600"><?php echo htmlspecialchars($_SESSION['admin_nama_lengkap'] ?? $_SESSION['admin_username']); ?></span>
                    </div>
                </div>
            </header>
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-4 sm:p-6">
                <div class="container mx-auto">