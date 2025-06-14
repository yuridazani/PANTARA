</div> </main>
        </div> </div> <script>
        // Sidebar Collapsible Logic
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        const desktopSidebarToggler = document.getElementById('desktopSidebarToggler');
        const desktopTogglerIconOpen = document.getElementById('desktopTogglerIconOpen');
        const desktopTogglerIconClose = document.getElementById('desktopTogglerIconClose');
        
        // Mobile Sidebar
        const mobileSidebarToggler = document.getElementById('mobileSidebarToggler');
        const mobileMenu = document.getElementById('mobileMenu'); // Dari header lama, bisa dihapus jika sidebar utama jadi mobile

        function toggleDesktopSidebar() {
            if (sidebar && mainContent && desktopTogglerIconOpen && desktopTogglerIconClose) {
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('sidebar-collapsed');
                desktopTogglerIconOpen.classList.toggle('hidden');
                desktopTogglerIconClose.classList.toggle('hidden');
                // Simpan state sidebar di localStorage
                localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
            }
        }
        
        if (desktopSidebarToggler) {
            desktopSidebarToggler.addEventListener('click', toggleDesktopSidebar);
        }

        // Inisialisasi state sidebar dari localStorage untuk Desktop
        if (sidebar && mainContent && desktopTogglerIconOpen && desktopTogglerIconClose && localStorage.getItem('sidebarCollapsed') === 'true') {
            sidebar.classList.add('collapsed');
            mainContent.classList.add('sidebar-collapsed');
            desktopTogglerIconOpen.classList.add('hidden');
            desktopTogglerIconClose.classList.remove('hidden');
        } else if (desktopTogglerIconOpen && desktopTogglerIconClose) {
            // Pastikan ikon default benar (tidak collapsed)
            desktopTogglerIconOpen.classList.remove('hidden');
            desktopTogglerIconClose.classList.add('hidden');
        }


        // Mobile Menu (Header Lama) atau Mobile Sidebar (Jika <aside> di-adjust untuk mobile)
        if (mobileSidebarToggler && sidebar) { // Menggunakan #sidebar untuk mobile juga
            mobileSidebarToggler.addEventListener('click', function(event) {
                event.stopPropagation();
                sidebar.classList.toggle('open'); // Class 'open' akan mentrigger transform: translateX(0)
            });
        }
        
        // Menutup mobile menu/sidebar jika klik di luar
        document.addEventListener('click', function(event) {
            if (sidebar && sidebar.classList.contains('open')) { // Jika sidebar mobile terbuka
                const isClickInsideSidebar = sidebar.contains(event.target);
                const isClickOnMobileToggler = mobileSidebarToggler ? mobileSidebarToggler.contains(event.target) : false;

                if (!isClickInsideSidebar && !isClickOnMobileToggler) {
                    sidebar.classList.remove('open');
                }
            }

            // Untuk mobile menu header lama (jika masih dipakai)
            if (mobileMenu && !mobileMenu.classList.contains('hidden')) {
                const isClickInsideMenu = mobileMenu.contains(event.target);
                // Asumsi mobileMenuButton adalah ID tombol header mobile lama
                const mobileMenuButton = document.getElementById('mobileMenuButton'); 
                const isClickOnOldButton = mobileMenuButton ? mobileMenuButton.contains(event.target) : false;
                if (!isClickInsideMenu && !isClickOnOldButton) {
                    mobileMenu.classList.add('hidden');
                }
            }
        });

        // Loading state sederhana untuk form submit
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('submit', function() {
                const submitButton = form.querySelector('button[type="submit"]');
                if (submitButton) {
                    submitButton.disabled = true;
                    submitButton.innerHTML = `
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Memproses...
                    `;
                }
            });
        });

    </script>
</body>
</html>