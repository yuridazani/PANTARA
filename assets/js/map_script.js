document.addEventListener('DOMContentLoaded', function () {
    const mapElement = document.getElementById('mapPantara');
    if (!mapElement) {
        console.error("Elemen peta #mapPantara tidak ditemukan.");
        return;
    }
    mapElement.innerHTML = ''; // Hapus pesan "Memuat peta..." dari HTML

    const pandaanCoords = [-7.5400, 112.6900];
    const mapZoomLevel = 13;
    let map = L.map('mapPantara', {
        preferCanvas: true
    }).setView(pandaanCoords, mapZoomLevel);

    // 1. Tambahkan Tile Layer OSM segera setelah map diinisialisasi
    const osmTile = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    });
    osmTile.addTo(map);

    // Inisialisasi Layer Groups
    let markersLayer = L.layerGroup().addTo(map); // Langsung tambahkan ke map, default aktif
    let heatLayerGroup = L.layerGroup(); // Jangan tambahkan ke map dulu, biar bisa di-toggle
    let contextualLayers = {};
    let allAccidentData = [];
    let layerControl; // Akan diinisialisasi setelah data pertama dimuat

    const timeSlider = document.getElementById('timeSlider');
    const sliderValueDisplay = document.getElementById('sliderValue');

    function createPopupContent(data, type = 'kecelakaan') {
        let content = `<div class="space-y-1 p-1">`;
        if (type === 'kecelakaan') {
            content += `<h3 class="font-bold text-base text-indigo-700 mb-1">${data.deskripsi || 'Informasi Kecelakaan'}</h3>`;
            content += `<p class="text-xs"><strong class="font-medium">Tanggal:</strong> ${data.tanggal_kejadian ? new Date(data.tanggal_kejadian).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' }) : '-'}</p>`;
            content += `<p class="text-xs"><strong class="font-medium">Waktu:</strong> ${data.waktu_kejadian || '-'}</p>`;
            if (data.jenis_kendaraan) content += `<p class="text-xs"><strong class="font-medium">Kendaraan:</strong> ${data.jenis_kendaraan}</p>`;
            if (data.tingkat_keparahan) content += `<p class="text-xs"><strong class="font-medium">Keparahan:</strong> ${data.tingkat_keparahan}</p>`;
            if (data.catatan_tambahan) content += `<p class="text-xs mt-1 pt-1 border-t border-gray-200"><strong class="font-medium">Catatan:</strong> ${data.catatan_tambahan}</p>`;
        } else if (type === 'kontekstual') {
            content += `<h3 class="font-semibold text-base text-teal-700 mb-1">${data.tipe_objek || 'Info Kontekstual'}</h3>`;
            content += `<p class="text-xs"><strong class="font-medium">Layer:</strong> ${data.nama_layer || '-'}</p>`;
            if (data.deskripsi) content += `<p class="text-xs mt-1 pt-1 border-t border-gray-200">${data.deskripsi}</p>`;
        }
        content += `</div>`;
        return L.popup({maxWidth: 280}).setContent(content);
    }

    function displayFilteredAccidents(selectedYear) {
        markersLayer.clearLayers();
        heatLayerGroup.clearLayers(); // Hapus marker lama dari heatLayerGroup juga

        const pointsForHeatmap = [];
        const currentFilteredData = allAccidentData.filter(item => {
            if (!item.tanggal_kejadian) return false; // Pastikan tanggal_kejadian ada
            const itemYear = new Date(item.tanggal_kejadian).getFullYear();
            return !selectedYear || itemYear == selectedYear;
        });

        if (currentFilteredData.length === 0) {
            console.warn(`Tidak ada data kecelakaan untuk tahun ${selectedYear || 'semua'}.`);
            // Jika heatLayerGroup sedang aktif di peta, hapus
            if (map.hasLayer(heatLayerGroup)) {
                map.removeLayer(heatLayerGroup);
            }
            return;
        }

        currentFilteredData.forEach(kecelakaan => {
            if (kecelakaan.latitude && kecelakaan.longitude) {
                const lat = parseFloat(kecelakaan.latitude);
                const lon = parseFloat(kecelakaan.longitude);
                const redIcon = L.divIcon({
                    className: 'custom-div-icon', // Pastikan class ini ada di CSS jika ingin style custom
                    html: `<div style="background-color:red;width:10px;height:10px;border-radius:50%;border:1px solid white;box-shadow:0 0 3px rgba(0,0,0,0.7);"></div>`,
                    iconSize: [10, 10],
                    iconAnchor: [5, 5]
                });
                const marker = L.marker([lat, lon], { icon: redIcon }).bindPopup(createPopupContent(kecelakaan, 'kecelakaan'));
                markersLayer.addLayer(marker); // markersLayer sudah di map, jadi marker langsung tampil
                pointsForHeatmap.push([lat, lon, 0.5]); // Intensitas bisa disesuaikan
            }
        });

        if (pointsForHeatmap.length > 0) {
            const heat = L.heatLayer(pointsForHeatmap, { radius: 20, blur: 15, maxZoom: 18, gradient: {0.4: 'blue', 0.65: 'lime', 1: 'red'} });
            heatLayerGroup.addLayer(heat); // Tambahkan sub-layer ke heatLayerGroup
            // Jangan tambahkan heatLayerGroup ke map di sini, biarkan layer control yang mengatur
        }
    }

    function loadContextualLayers() {
        fetch('get_data_kontekstual.php')
            .then(response => {
                if (!response.ok) throw new Error('Gagal mengambil data kontekstual: ' + response.statusText);
                return response.json();
            })
            .then(data => {
                if (data.error) {
                    console.error('Error dari server (kontekstual):', data.error);
                    return;
                }
                if (!Array.isArray(data) || data.length === 0) {
                    console.warn('Tidak ada data kontekstual.');
                    return;
                }

                const groupedByLayerName = data.reduce((acc, item) => {
                    acc[item.nama_layer] = acc[item.nama_layer] || [];
                    acc[item.nama_layer].push(item);
                    return acc;
                }, {});

                for (const layerName in groupedByLayerName) {
                    if (!contextualLayers[layerName]) {
                        contextualLayers[layerName] = L.layerGroup(); // Buat jika belum ada
                        if (layerControl) { // Tambahkan ke kontrol jika kontrol sudah dibuat
                            layerControl.addOverlay(contextualLayers[layerName], `<span class='font-semibold'>${layerName}</span>`);
                        }
                    }
                    contextualLayers[layerName].clearLayers();

                    groupedByLayerName[layerName].forEach(item => {
                        if (item.latitude && item.longitude) {
                            let iconColor = 'purple'; // Warna default untuk ikon kontekstual
                            if (item.ikon === 'jalan-rusak') iconColor = 'orange';
                            else if (item.ikon === 'penerangan-minim') iconColor = 'gold';
                            else if (item.ikon === 'pasar') iconColor = 'green';

                            const customHtmlIcon = L.divIcon({
                                className: 'custom-div-icon',
                                html: `<div style="background-color:${iconColor};width:10px;height:10px;border-radius:50%;border:1px solid #333;box-shadow:0 0 3px rgba(0,0,0,0.5);"></div>`,
                                iconSize: [10, 10],
                                iconAnchor: [5, 5]
                            });
                            const marker = L.marker([parseFloat(item.latitude), parseFloat(item.longitude)], { icon: customHtmlIcon })
                                .bindPopup(createPopupContent(item, 'kontekstual'));
                            contextualLayers[layerName].addLayer(marker);
                        }
                    });
                }
            })
            .catch(error => console.error('Error loading contextual data:', error));
    }
    
    function initializeMapAndData() {
        // Pesan loading sudah dihapus di awal
        
        fetch('get_data_kecelakaan.php')
            .then(response => {
                if (!response.ok) throw new Error('Gagal mengambil data kecelakaan: ' + response.statusText);
                return response.json();
            })
            .then(data => {
                if (data.error) {
                    console.error('Error dari server (kecelakaan):', data.error);
                    alert('Gagal memuat data kecelakaan: ' + data.error);
                    return;
                }
                if (!Array.isArray(data)) {
                    console.warn('Data kecelakaan dari server bukan array.');
                    return;
                }
                
                allAccidentData = data;

                const baseMaps = { "OpenStreetMap": osmTile };
                const overlayMaps = {
                    "<span class='font-semibold'>Titik Kecelakaan</span>": markersLayer, // markersLayer sudah di map
                };
                if (allAccidentData.length > 0) { // Hanya tambahkan heatmap ke kontrol jika ada data awal
                    overlayMaps["<span class='font-semibold'>Heatmap Konsentrasi</span>"] = heatLayerGroup;
                }
                
                if (layerControl) map.removeControl(layerControl); // Hapus kontrol lama jika ada
                layerControl = L.control.layers(baseMaps, overlayMaps, { position: 'topright', collapsed: false }).addTo(map);

                // Muat layer kontekstual setelah layer control utama dibuat
                // agar bisa ditambahkan ke layer control yang sudah ada
                loadContextualLayers(); 

                if (timeSlider) {
                    const initialYear = timeSlider.value;
                    displayFilteredAccidents(initialYear);
                    if (sliderValueDisplay) sliderValueDisplay.textContent = initialYear;
                } else {
                    displayFilteredAccidents(null); // Tampilkan semua jika tidak ada slider
                }
            })
            .catch(error => {
                console.error('Error saat inisialisasi data peta:', error);
                alert('Terjadi kesalahan: ' + error.message);
            });
    }

    if (timeSlider) {
        timeSlider.addEventListener('input', function () {
            if (sliderValueDisplay) sliderValueDisplay.textContent = this.value;
        });
        // Gunakan 'change' untuk performa lebih baik daripada 'input' saat data banyak
        timeSlider.addEventListener('change', function () { 
            displayFilteredAccidents(this.value);
        });
    }

    initializeMapAndData(); // Panggil fungsi utama
});