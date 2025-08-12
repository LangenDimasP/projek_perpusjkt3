// sidebar.js
document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebar-overlay');
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    
    // Menu items
    const menuTambahKelas = document.getElementById('menu-tambah-kelas');
    const menuNaikKelas = document.getElementById('menu-naik-kelas');
    const menuBeriKelas = document.getElementById('menu-beri-kelas');
    
    // Content sections
    const tambahKelasContent = document.getElementById('tambah-kelas-content');
    const naikKelasContent = document.getElementById('naik-kelas-content');
    const beriKelasContent = document.getElementById('beri-kelas-content');
    
    // Mobile menu toggle
    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', function() {
            toggleSidebar();
        });
    }
    
    // Sidebar overlay click to close
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', function() {
            closeSidebar();
        });
    }
    
    // Close sidebar on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && sidebar.classList.contains('translate-x-0')) {
            closeSidebar();
        }
    });
    
    // Menu navigation
    if (menuTambahKelas) {
        menuTambahKelas.addEventListener('click', function() {
            showContent('tambah-kelas');
            setActiveMenu(menuTambahKelas);
            closeSidebar(); // Close on mobile after selection
        });
    }
    
    if (menuNaikKelas) {
        menuNaikKelas.addEventListener('click', function() {
            showContent('naik-kelas');
            setActiveMenu(menuNaikKelas);
            closeSidebar();
        });
    }
    
    if (menuBeriKelas) {
        menuBeriKelas.addEventListener('click', function() {
            showContent('beri-kelas');
            setActiveMenu(menuBeriKelas);
            closeSidebar();
        });
    }
    
    // Functions
    function toggleSidebar() {
        if (sidebar.classList.contains('translate-x-0')) {
            closeSidebar();
        } else {
            openSidebar();
        }
    }
    
    function openSidebar() {
        sidebar.classList.remove('-translate-x-full');
        sidebar.classList.add('translate-x-0');
        if (sidebarOverlay) {
            sidebarOverlay.classList.remove('hidden');
        }
        document.body.style.overflow = 'hidden'; // Prevent body scroll on mobile
    }
    
    function closeSidebar() {
        sidebar.classList.remove('translate-x-0');
        sidebar.classList.add('-translate-x-full');
        if (sidebarOverlay) {
            sidebarOverlay.classList.add('hidden');
        }
        document.body.style.overflow = ''; // Restore body scroll
    }
    
    function showContent(contentType) {
        // Hide all content sections
        const contentSections = document.querySelectorAll('.content-section');
        contentSections.forEach(section => {
            section.classList.add('hidden');
        });
        
        // Show selected content
        switch(contentType) {
            case 'tambah-kelas':
                if (tambahKelasContent) {
                    tambahKelasContent.classList.remove('hidden');
                }
                break;
            case 'naik-kelas':
                if (naikKelasContent) {
                    naikKelasContent.classList.remove('hidden');
                }
                break;
            case 'beri-kelas':
                if (beriKelasContent) {
                    beriKelasContent.classList.remove('hidden');
                }
                break;
        }
    }
    
    function setActiveMenu(activeItem) {
        // Remove active class from all menu items
        const menuItems = document.querySelectorAll('#sidebar nav button');
        menuItems.forEach(item => {
            item.classList.remove('active-menu', 'bg-blue-50', 'text-blue-600');
            item.classList.add('text-gray-700');
            // Reset SVG colors
            const svg = item.querySelector('svg');
            if (svg) {
                svg.classList.remove('text-blue-500');
                svg.classList.add('text-gray-400');
            }
        });
        
        // Add active class to selected item
        if (activeItem) {
            activeItem.classList.add('active-menu', 'bg-blue-50', 'text-blue-600');
            activeItem.classList.remove('text-gray-700');
            // Set SVG color
            const svg = activeItem.querySelector('svg');
            if (svg) {
                svg.classList.add('text-blue-500');
                svg.classList.remove('text-gray-400');
            }
        }
    }
    
    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 1024) { // lg breakpoint
            closeSidebar();
        }
    });
    
    // Initialize - show tambah kelas by default
    showContent('tambah-kelas');
    setActiveMenu(menuTambahKelas);
    
    // User status and logout functionality
    const userFullnameElement = document.getElementById('user-fullname');
    const logoutBtn = document.getElementById('logout-btn');
    
    // Set user fullname if available in session
    if (userFullnameElement) {
        // This would typically be populated from server-side data
        // For now, using a placeholder
        const userFullname = 'Admin User'; // This should come from PHP session
        userFullnameElement.textContent = userFullname;
    }
    
    // Logout functionality
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function() {
            if (confirm('Apakah Anda yakin ingin logout?')) {
                // Redirect to logout handler
                window.location.href = '/naik_kelas/logout';
            }
        });
    }
});