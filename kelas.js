document.addEventListener('DOMContentLoaded', () => {
    // Constants
    const API_URL = '../api.php';
    
    // Tab Elements
    const tabTambahKelas = document.getElementById('tab-tambah-kelas');
    const tabNaikKelas = document.getElementById('tab-naik-kelas');
    const tabBeriKelas = document.getElementById('tab-beri-kelas');
    
    // Content Elements
    const tambahKelasContent = document.getElementById('tambah-kelas-content');
    const naikKelasContent = document.getElementById('naik-kelas-content');
    const beriKelasContent = document.getElementById('beri-kelas-content');
    const tambahKelasForm = document.getElementById('tambah-kelas-form');

    // Form Elements
    const namaKelasInput = document.getElementById('nama-kelas-input');
    const tambahKelasBtn = document.getElementById('tambah-kelas-btn');
    const statusDiv = document.getElementById('status');

    // User Elements
    const userStatus = document.getElementById('user-status');
    const userFullname = document.getElementById('user-fullname');
    const logoutBtn = document.getElementById('logout-btn');

    // Global State
    let isLoggedIn = false;
    let currentUser = null;

    // Tab Switching Function
    function switchTab(tabElement, contentElement) {
        // Remove active class from all tabs
        [tabTambahKelas, tabNaikKelas, tabBeriKelas].forEach(tab => {
            tab.classList.remove('text-blue-600', 'border-b-2', 'border-blue-600');
            tab.classList.add('text-gray-600');
        });

        // Hide all content
        [tambahKelasContent, naikKelasContent, beriKelasContent].forEach(content => {
            content.classList.add('hidden');
        });

        // Activate selected tab
        tabElement.classList.remove('text-gray-600');
        tabElement.classList.add('text-blue-600', 'border-b-2', 'border-blue-600');

        // Show selected content
        contentElement.classList.remove('hidden');
        
        // Show form if it's tambah kelas tab
        if (contentElement === tambahKelasContent && tambahKelasForm) {
            tambahKelasForm.classList.remove('hidden');
        }
    }

    // Status Message Function
    function showStatus(message, type = 'success') {
        statusDiv.textContent = message;
        statusDiv.classList.remove('hidden', 'bg-green-100', 'text-green-700', 'bg-red-100', 'text-red-700');
        
        if (type === 'success') {
            statusDiv.classList.add('bg-green-100', 'text-green-700');
        } else {
            statusDiv.classList.add('bg-red-100', 'text-red-700');
        }

        setTimeout(() => {
            statusDiv.classList.add('hidden');
        }, 3000);
    }

    // Check Login Status
    async function checkLoginStatus() {
        try {
            const response = await fetch(`${API_URL}?action=check_login`);
            if (!response.ok) {
                throw new Error('Gagal memeriksa status login');
            }
            const result = await response.json();
            
            isLoggedIn = result.isLoggedIn;
            currentUser = result.user;
            
            if (isLoggedIn && currentUser) {
                userFullname.textContent = currentUser.Fullname;
                userStatus.classList.remove('hidden');
                tambahKelasForm?.classList.remove('hidden');
            } else {
                window.location.href = '../login.php';
            }
        } catch (error) {
            console.error('Error:', error);
            window.location.href = '../login.php';
        }
    }

    // Add Event Listeners
    if (tabTambahKelas) {
        tabTambahKelas.addEventListener('click', () => switchTab(tabTambahKelas, tambahKelasContent));
    }
    if (tabNaikKelas) {
        tabNaikKelas.addEventListener('click', () => switchTab(tabNaikKelas, naikKelasContent));
    }
    if (tabBeriKelas) {
        tabBeriKelas.addEventListener('click', () => switchTab(tabBeriKelas, beriKelasContent));
    }

    // Tambah Kelas Form Handler
    if (tambahKelasBtn) {
        tambahKelasBtn.addEventListener('click', async () => {
            const namaKelas = namaKelasInput.value.trim();
            if (!namaKelas) {
                showStatus('Nama kelas tidak boleh kosong', 'error');
                return;
            }

            try {
                const response = await fetch(API_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'tambah_kelas',
                        nama_kelas: namaKelas
                    })
                });

                const result = await response.json();
                if (result.success) {
                    showStatus('Kelas berhasil ditambahkan');
                    namaKelasInput.value = '';
                } else {
                    showStatus(result.message || 'Gagal menambahkan kelas', 'error');
                }
            } catch (error) {
                showStatus('Terjadi kesalahan saat menambahkan kelas', 'error');
            }
        });
    }

    // Logout Handler
    if (logoutBtn) {
        logoutBtn.addEventListener('click', async () => {
            try {
                const response = await fetch(API_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ action: 'logout' })
                });

                const result = await response.json();
                if (result.success) {
                    window.location.href = '../login.php';
                }
            } catch (error) {
                console.error('Logout error:', error);
            }
        });
    }

    // Initialize
    checkLoginStatus();
    if (tabTambahKelas) {
        switchTab(tabTambahKelas, tambahKelasContent);
    }
});