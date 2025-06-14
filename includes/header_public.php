<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) . ' - ' : ''; ?>PANTARA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css"> <style>
        body { 
            font-family: 'Inter', sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        #mapPantara { 
            /* Tinggi peta diatur oleh div container di index.php atau halaman spesifik */
            min-height: 500px; /* Tinggi minimum untuk peta */
            width: 100%; 
            border-radius: 0.75rem; /* rounded-xl */
        }
        @media (max-width: 768px) {
           #mapPantara { min-height: 400px; }
        }
        .leaflet-popup-content-wrapper { border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
        .leaflet-popup-content { font-size: 14px; line-height: 1.6; max-width: 300px; }
        main { flex-grow: 1; }
        .time-slider-container { background-color: #fff; padding: 1rem; border-radius: 0.75rem; box-shadow: 0 1px 3px 0 rgba(0,0,0,0.1), 0 1px 2px 0 rgba(0,0,0,0.06); margin-top: 1rem; margin-bottom:1rem; }
        .time-slider-container label { font-weight: 600; margin-bottom: 0.5rem; display: block; color: #374151; }
        .time-slider-container input[type="range"] { width: 100%; cursor: pointer;}
        .time-slider-container .slider-labels { display: flex; justify-content: space-between; font-size: 0.875rem; color: #4B5563; margin-top: 0.25rem; }
        .leaflet-control-layers-toggle { background-image: url(data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A//www.w3.org/2000/svg%22%20viewBox%3D%220%200%2032%2032%22%3E%3Cpath%20fill%3D%22%23333%22%20d%3D%22M4%206h24v2H4zm0%209h24v2H4zm0%209h24v2H4z%22/%3E%3C/svg%3E) !important; width:36px !important; height:36px !important; border-radius: 4px !important; border: 1px solid #ccc !important; }
        .leaflet-control-layers { box-shadow: 0 1px 5px rgba(0,0,0,0.4) !important; background: white !important; border-radius: 5px !important; padding: 6px !important; }
        .leaflet-control-layers-scrollbar { overflow-y: auto; padding-right: 5px; }
        .leaflet-control-layers-selector { margin-right: 6px !important; }
    </style>
</head>
<body class="bg-slate-50 text-gray-800 antialiased">
    <header class="bg-white shadow-md sticky top-0 z-50">
        <nav class="container mx-auto px-4 sm:px-6 lg:px-8 py-3 flex justify-between items-center">
            <a href="index.php" class="text-2xl font-bold text-indigo-600 hover:text-indigo-700 transition-colors duration-150">
                PANTARA
            </a>
            <div>
                <a href="admin/" 
                   class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg shadow-sm text-sm transition-colors duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50">
                    Login Admin
                </a>
            </div>
        </nav>
    </header>
    <main class="container mx-auto p-4 md:p-6">