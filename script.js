document.addEventListener('DOMContentLoaded', () => {
    const dariKelasInput = document.getElementById('dari-kelas-input');
    const dariKelasId = document.getElementById('dari-kelas-id');
    const dariKelasDropdownBtn = document.getElementById('dari-kelas-dropdown-btn');
    const dariKelasDropdown = document.getElementById('dari-kelas-dropdown');
    const dariKelasLoading = document.getElementById('dari-kelas-loading');
    const dariKelasResults = document.getElementById('dari-kelas-results');
    
    const keKelasInput = document.getElementById('ke-kelas-input');
    const keKelasId = document.getElementById('ke-kelas-id');
    const keKelasDropdownBtn = document.getElementById('ke-kelas-dropdown-btn');
    const keKelasDropdown = document.getElementById('ke-kelas-dropdown');
    const keKelasLoading = document.getElementById('ke-kelas-loading');
    const keKelasResults = document.getElementById('ke-kelas-results');
    
    const beriKelasInput = document.getElementById('beri-kelas-input');
    const beriKelasId = document.getElementById('beri-kelas-id');
    const beriKelasDropdownBtn = document.getElementById('beri-kelas-dropdown-btn');
    const beriKelasDropdown = document.getElementById('beri-kelas-dropdown');
    const beriKelasLoading = document.getElementById('beri-kelas-loading');
    const beriKelasResults = document.getElementById('beri-kelas-results');
    
    const namaKelasInput = document.getElementById('nama-kelas-input');
    const tambahKelasBtn = document.getElementById('tambah-kelas-btn');
    const tambahKelasForm = document.getElementById('tambah-kelas-form');
    const userStatus = document.getElementById('user-status');
    const userFullname = document.getElementById('user-fullname');
    const logoutBtn = document.querySelector('a[href="logout.php"]');
    
    const prosesBtn = document.getElementById('proses-btn');
    const beriKelasBtn = document.getElementById('beri-kelas-btn');
    const statusDiv = document.getElementById('status');
    const studentSelectionArea = document.getElementById('student-selection-area');
    const studentListDiv = document.getElementById('student-list');
    const searchStudentInput = document.getElementById('search-student');
    const selectAllCheckbox = document.getElementById('select-all-students');
    const studentNoClassSelectionArea = document.getElementById('student-no-class-selection-area');
    const studentNoClassListDiv = document.getElementById('student-no-class-list');
    const searchStudentNoClassInput = document.getElementById('search-student-no-class');
    const selectAllNoClassCheckbox = document.getElementById('select-all-students-no-class');
    
    const tabTambahKelas = document.getElementById('tab-tambah-kelas');
    const tabNaikKelas = document.getElementById('tab-naik-kelas');
    const tabBeriKelas = document.getElementById('tab-beri-kelas');
    const tambahKelasContent = document.getElementById('tambah-kelas-content');
    const naikKelasContent = document.getElementById('naik-kelas-content');
    const beriKelasContent = document.getElementById('beri-kelas-content');

    const BASE_URL = '../';  // Untuk navigasi ke folder utama
    const API_URL = `${BASE_URL}api.php`;  // Gabungkan dengan nama file API
    let debounceTimer;
    let kelasDebounceTimer;
    let selectedStudents = []; // Store selected students for Beri Kelas
    let isLoggedIn = false;
    let currentUser = null;
    
    // Declare dropdown controller variables
    let dariKelasDropdownController;
    let keKelasDropdownController;
    let beriKelasDropdownController;

    let selectedNaikKelas = []; // [{ID, Fullname}]
    let selectedKelasId = null; // Untuk menyimpan kelas asal siswa yang diceklis

    

    // Check login status on page load
    async function checkLoginStatus() {
        try {
            const response = await fetch(`${API_URL}?action=check_login`);
            if (!response.ok) {
                throw new Error('Gagal memeriksa status login.');
            }
            const result = await response.json();
            
            isLoggedIn = result.isLoggedIn;
            currentUser = result.user;
    
            // Update UI elements only if they exist
            if (!isLoggedIn) {
                window.location.href = '../login.php';
                return;
            }

            // Show content for logged in users
            if (tambahKelasForm) {
                tambahKelasForm.classList.remove('hidden');
            }
        } catch (error) {
            console.error('Error checking login status:', error);
            showStatus('Gagal memeriksa status login: ' + error.message, 'error');
            
            // Reset login state
            isLoggedIn = false;
            currentUser = null;
            
            // Redirect to login page on error
            window.location.href = '../login.php';
        }
    }

    if (logoutBtn) {
        logoutBtn.addEventListener('click', async () => {
            try {
                const response = await fetch(API_URL, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'logout' })
                });
                const result = await response.json();
                if (!response.ok) throw new Error(result.message);
    
                isLoggedIn = false;
                currentUser = null;
                userFullname.textContent = '';
                userStatus.classList.add('hidden');
                showStatus('Logout berhasil.', 'success');
    
                // Redirect ke halaman login setelah logout sukses
                setTimeout(() => {
                    window.location.href = 'login';
                }, 1000);
            } catch (error) {
                showStatus(error.message, 'error');
            }
        });
    }

        // Tambahkan fungsi ini di dalam DOMContentLoaded:
    function updateSelectedPreview() {
        const preview = document.getElementById('selected-preview');
        if (!preview) return;
    
        if (selectedNaikKelas.length > 0) {
            // Ambil nama unik
            const uniqueNames = [...new Set(selectedNaikKelas.map(s => s.Fullname))];
            preview.innerHTML = `Siswa terpilih: ${uniqueNames.length}<br><span class="text-xs text-gray-600">${uniqueNames.join(', ')}</span>`;
        } else {
            preview.textContent = '';
        }
    }

        function updateSelectedPreviewNoClass() {
            const preview = document.getElementById('selected-preview-no-class');
            if (!preview) return;
            if (selectedStudents.length > 0) {
                // Ambil nama unik
                const uniqueNames = [...new Set(selectedStudents.map(s => s.Fullname))];
                preview.innerHTML = `Siswa terpilih: ${uniqueNames.length}<br><span class="text-xs text-gray-600">${uniqueNames.join(', ')}</span>`;
            } else {
                preview.textContent = '';
            }
        }


    // Tab Switching
    function switchTab(tab) {
        tabTambahKelas.classList.remove('text-blue-600', 'border-b-2', 'border-blue-600');
        tabNaikKelas.classList.remove('text-blue-600', 'border-b-2', 'border-blue-600');
        tabBeriKelas.classList.remove('text-blue-600', 'border-b-2', 'border-blue-600');
        tabTambahKelas.classList.add('text-gray-600');
        tabNaikKelas.classList.add('text-gray-600');
        tabBeriKelas.classList.add('text-gray-600');
        tambahKelasContent.classList.add('hidden');
        naikKelasContent.classList.add('hidden');
        beriKelasContent.classList.add('hidden');
    
        if (tab === 'tambah-kelas') {
            tabTambahKelas.classList.add('text-blue-600', 'border-b-2', 'border-blue-600');
            tambahKelasContent.classList.remove('hidden');
            // TAMPILKAN FORM TAMBAH KELAS
            tambahKelasForm.classList.remove('hidden');
            checkLoginStatus();
        } else if (tab === 'naik-kelas') {
            tabNaikKelas.classList.add('text-blue-600', 'border-b-2', 'border-blue-600');
            naikKelasContent.classList.remove('hidden');
        } else {
            tabBeriKelas.classList.add('text-blue-600', 'border-b-2', 'border-blue-600');
            beriKelasContent.classList.remove('hidden');
        }
    }

    tabTambahKelas.addEventListener('click', () => {
        switchTab('tambah-kelas');
        localStorage.setItem('activeTab', 'tambah-kelas');
    });
    tabNaikKelas.addEventListener('click', () => {
        switchTab('naik-kelas');
        localStorage.setItem('activeTab', 'naik-kelas');
    });
    tabBeriKelas.addEventListener('click', () => {
        switchTab('beri-kelas');
        localStorage.setItem('activeTab', 'beri-kelas');
        // Tampilkan semua siswa tanpa kelas saat tab dibuka
        const promise = fetch(`${API_URL}?action=get_members_no_class`);
        displayMembers(promise, studentNoClassListDiv, studentNoClassSelectionArea, true);
    });

    // Inisialisasi tab aktif dari localStorage jika ada
    const savedTab = localStorage.getItem('activeTab') || 'tambah-kelas';
    switchTab(savedTab);
    if (savedTab === 'beri-kelas') {
        const promise = fetch(`${API_URL}?action=get_members_no_class`);
        displayMembers(promise, studentNoClassListDiv, studentNoClassSelectionArea, true);
    }


    // Logout Button Event
    logoutBtn.addEventListener('click', async () => {
        try {
            const response = await fetch(API_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'logout' })
            });
            const result = await response.json();
            if (!response.ok) throw new Error(result.message);
    
            isLoggedIn = false;
            currentUser = null;
            userFullname.textContent = '';
            userStatus.classList.add('hidden'); // SEMBUNYIKAN
            showStatus('Logout berhasil.', 'success');
        } catch (error) {
            showStatus(error.message, 'error');
        }
    });

    // Class Dropdown Functions
    function createKelasDropdown(inputElement, hiddenIdElement, dropdownElement, loadingElement, resultsElement) {
        let isDropdownOpen = false;
        
        async function searchKelas(query = '') {
            loadingElement.classList.remove('hidden');
            resultsElement.innerHTML = '';
            
            try {
                const response = await fetch(`${API_URL}?action=search_kelas&query=${encodeURIComponent(query)}`);
                if (!response.ok) throw new Error('Gagal memuat data kelas.');
                const kelasList = await response.json();
                
                loadingElement.classList.add('hidden');
                
                if (kelasList.length === 0) {
                    resultsElement.innerHTML = `
                        <div class="px-4 py-3 text-gray-500 text-center">
                            <svg class="w-8 h-8 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h10a2 2 0 012 2v14a2 2 0 01-2 2z"></path>
                            </svg>
                            Kelas tidak ditemukan
                        </div>
                    `;
                    return;
                }
                
                kelasList.forEach(kelas => {
                    const item = document.createElement('div');
                    item.className = 'dropdown-item px-4 py-3 cursor-pointer text-gray-700 hover:bg-primary-100 transition-colors duration-150';
                    item.innerHTML = `
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <span class="font-medium">${kelas.namakelassiswa}</span>
                        </div>
                    `;
                    
                    item.addEventListener('click', () => {
                        inputElement.value = kelas.namakelassiswa;
                        hiddenIdElement.value = kelas.id;
                        closeDropdown();
                    
                        // Jika ini adalah dropdown "dari kelas", reset pilihan siswa & kelasId
                        if (inputElement.id === 'dari-kelas-input') {
                            searchStudentInput.value = '';
                            selectedNaikKelas = [];
                            selectedKelasId = null;
                            updateSelectedPreview();
                            const promise = fetch(`${API_URL}?action=get_members&kelasId=${kelas.id}`);
                            displayMembers(promise, studentListDiv, studentSelectionArea);
                        }
                    });
                    
                    resultsElement.appendChild(item);
                });
                
            } catch (error) {
                loadingElement.classList.add('hidden');
                resultsElement.innerHTML = `
                    <div class="px-4 py-3 text-red-500 text-center">
                        <svg class="w-8 h-8 mx-auto mb-2 text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        ${error.message}
                    </div>
                `;
            }
        }
        
        function openDropdown() {
            isDropdownOpen = true;
            dropdownElement.classList.remove('hidden');
            dropdownElement.classList.add('opacity-100', 'scale-y-100');
            searchKelas(inputElement.value);
        }
        
        function closeDropdown() {
            isDropdownOpen = false;
            dropdownElement.classList.add('hidden');
            dropdownElement.classList.remove('opacity-100', 'scale-y-100');
        }
        
        // Event listeners
        inputElement.addEventListener('input', () => {
            clearTimeout(kelasDebounceTimer);
            hiddenIdElement.value = '';
            
            if (inputElement.value.length >= 1) {
                kelasDebounceTimer = setTimeout(() => {
                    if (!isDropdownOpen) openDropdown();
                    else searchKelas(inputElement.value);
                }, 300);
            } else {
                closeDropdown();
            }
        });
        
        inputElement.addEventListener('focus', () => {
            if (!isDropdownOpen) {
                openDropdown();
            }
        });
        
        inputElement.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeDropdown();
            }
        });
        
        // Dropdown button
        const dropdownBtn = inputElement.parentElement.querySelector('button');
        dropdownBtn.addEventListener('click', (e) => {
            e.preventDefault();
            if (isDropdownOpen) {
                closeDropdown();
            } else {
                openDropdown();
            }
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (isDropdownOpen && !inputElement.parentElement.contains(e.target)) {
                closeDropdown();
            }
        });

        return { searchKelas }; // Return searchKelas for external use
    }

    // Initialize dropdowns and store searchKelas functions
    dariKelasDropdownController = createKelasDropdown(dariKelasInput, dariKelasId, dariKelasDropdown, dariKelasLoading, dariKelasResults);
    keKelasDropdownController = createKelasDropdown(keKelasInput, keKelasId, keKelasDropdown, keKelasLoading, keKelasResults);
    beriKelasDropdownController = createKelasDropdown(beriKelasInput, beriKelasId, beriKelasDropdown, beriKelasLoading, beriKelasResults);

    // Tambah Kelas Button Event
    tambahKelasBtn.addEventListener('click', async () => {
        if (!isLoggedIn) {
            showStatus('Harap login terlebih dahulu.', 'error');
            return;
        }

        const namaKelas = namaKelasInput.value.trim();
        if (!namaKelas) {
            showStatus('Harap masukkan nama kelas.', 'error');
            return;
        }

        // Show confirmation modal
        const confirmModal = document.getElementById('confirm-modal');
        const confirmMessage = document.getElementById('confirm-message');
        const confirmProceed = document.getElementById('confirm-proceed');
        const confirmCancel = document.getElementById('confirm-cancel');

        confirmMessage.textContent = `Anda yakin ingin menambahkan kelas "${namaKelas}"?`;
        confirmModal.classList.remove('hidden');
        confirmModal.classList.add('opacity-100');

        const userConfirmed = await new Promise((resolve) => {
            confirmProceed.onclick = () => {
                confirmModal.classList.add('hidden');
                confirmModal.classList.remove('opacity-100');
                resolve(true);
            };
            confirmCancel.onclick = () => {
                confirmModal.classList.add('hidden');
                confirmModal.classList.remove('opacity-100');
                resolve(false);
            };
        });

        if (userConfirmed) {
            // Show loading state
            tambahKelasBtn.disabled = true;
            tambahKelasBtn.innerHTML = `
                <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-white mr-2"></div>
                <span>Memproses...</span>
            `;
            
            try {
                const response = await fetch(API_URL, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'add_kelas', namakelassiswa: namaKelas })
                });
                const result = await response.json();
                if (!response.ok) throw new Error(result.message);
                
                showStatus(result.message, 'success');
                
                // Reset form
                namaKelasInput.value = '';
                
                // Refresh dropdowns in other tabs
                dariKelasDropdownController.searchKelas(dariKelasInput.value);
                keKelasDropdownController.searchKelas(keKelasInput.value);
                beriKelasDropdownController.searchKelas(beriKelasInput.value);
            } catch (error) {
                showStatus(error.message, 'error');
            } finally {
                // Reset button state
                tambahKelasBtn.disabled = false;
                tambahKelasBtn.innerHTML = `
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    <span>Tambah Kelas</span>
                `;
            }
        }
    });

    async function displayMembers(fetchPromise, listDiv, selectionArea, isNoClass = false) {
        listDiv.innerHTML = `
            <div class="flex items-center justify-center py-8 text-gray-500">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mr-3"></div>
                <span>Memuat data siswa...</span>
            </div>
        `;
        selectionArea.classList.remove('hidden');
        
        try {
            const response = await fetchPromise;
            if (!response.ok) throw new Error('Gagal memuat data siswa.');
            let members = await response.json();
            
            if (!isNoClass) {
                // Cek apakah ini hasil pencarian (searchStudentInput tidak kosong)
                if (searchStudentInput && searchStudentInput.value.trim().length >= 2) {
                    // Saat pencarian, tampilkan hasil pencarian saja (tanpa merge)
                    // Filter agar siswa yang sudah diceklis tidak muncul lagi di daftar
                    members = members.filter(member => !selectedNaikKelas.some(s => s.ID === member.ID));
                } else {
                    // Saat bukan pencarian, merge agar siswa yang sudah diceklis tetap muncul
                    const memberMap = new Map();
                    selectedNaikKelas.forEach(member => memberMap.set(member.ID, member));
                    members.forEach(member => {
                        if (!memberMap.has(member.ID)) {
                            memberMap.set(member.ID, member);
                        }
                    });
                    members = Array.from(memberMap.values());
                }
            }
            
            listDiv.innerHTML = '';
            
            if (members.length === 0) {
                listDiv.innerHTML = `
                    <div class="flex items-center justify-center py-8 text-gray-500">
                        <svg class="w-12 h-12 mr-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span>Siswa tidak ditemukan</span>
                    </div>
                `;
                // Update select all checkbox
                if (isNoClass) {
                    selectAllNoClassCheckbox.checked = false;
                } else {
                    selectAllCheckbox.checked = false;
                }
                return;
            }
            
            members.forEach(member => {
                const item = document.createElement('div');
                item.className = 'flex items-center p-3 hover:bg-blue-50 rounded-lg transition-colors duration-150 cursor-pointer';
                const isChecked = isNoClass
                    ? selectedStudents.some(s => s.ID === member.ID)
                    : selectedNaikKelas.some(s => s.ID === member.ID)
                item.innerHTML = `
                    <input type="checkbox" class="student-checkbox w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2" value="${member.ID}" id="student-${member.ID}" data-kelas-id="${member.KelasId}" ${isChecked ? 'checked' : ''}>
                    <label for="student-${member.ID}" class="ml-3 text-sm font-medium text-gray-700 cursor-pointer flex-1 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        ${member.Fullname}
                    </label>
                `;
                listDiv.appendChild(item);
                updateSelectedPreview();
            });
            if (isNoClass) updateSelectedPreviewNoClass();

            // Add event listeners to checkboxes
            const checkboxes = listDiv.querySelectorAll('.student-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', async () => {
            const memberId = checkbox.value;
            const memberName = checkbox.nextElementSibling.textContent.trim();
            const memberKelasId = checkbox.getAttribute('data-kelas-id');
    
            if (isNoClass) {
                // === MODE BERI KELAS ===
                if (checkbox.checked) {
                    if (!selectedStudents.some(s => s.ID === memberId)) {
                        selectedStudents.push({ ID: memberId, Fullname: memberName });
                    }
                } else {
                    selectedStudents = selectedStudents.filter(s => s.ID !== memberId);
                }
                updateSelectedPreviewNoClass();
            } else {
                // === MODE NAIK KELAS ===
                if (checkbox.checked) {
                    // Cek apakah siswa sudah diceklis sebelumnya
                    if (selectedNaikKelas.some(s => s.ID === memberId)) {
                        checkbox.checked = false;
    
                        // Tampilkan popup
                        const confirmModal = document.getElementById('confirm-modal');
                        const confirmMessage = document.getElementById('confirm-message');
                        const confirmProceed = document.getElementById('confirm-proceed');
                        const confirmCancel = document.getElementById('confirm-cancel');
    
                        confirmMessage.textContent = 'Siswa ini sudah dipilih sebelumnya.';
                        confirmModal.classList.remove('hidden');
                        confirmModal.classList.add('opacity-100');
    
                        await new Promise((resolve) => {
                            confirmProceed.onclick = () => {
                                confirmModal.classList.add('hidden');
                                confirmModal.classList.remove('opacity-100');
                                resolve();
                            };
                            confirmCancel.onclick = () => {
                                confirmModal.classList.add('hidden');
                                confirmModal.classList.remove('opacity-100');
                                resolve();
                            };
                        });
    
                        return;
                    }
    
                    // Jika belum ada yang diceklis, simpan kelasId
                    if (selectedNaikKelas.length === 0) {
                        selectedKelasId = memberKelasId;
                    }
                    // Jika sudah ada, pastikan kelasId sama
                    if (memberKelasId !== selectedKelasId) {
                        checkbox.checked = false;
    
                        // Tampilkan popup konfirmasi
                        const confirmModal = document.getElementById('confirm-modal');
                        const confirmMessage = document.getElementById('confirm-message');
                        const confirmProceed = document.getElementById('confirm-proceed');
                        const confirmCancel = document.getElementById('confirm-cancel');
    
                        confirmMessage.textContent = 'Anda hanya bisa memilih siswa dari kelas yang sama. Silakan hapus pilihan sebelumnya jika ingin memilih dari kelas lain.';
                        confirmModal.classList.remove('hidden');
                        confirmModal.classList.add('opacity-100');
    
                        await new Promise((resolve) => {
                            confirmProceed.onclick = () => {
                                confirmModal.classList.add('hidden');
                                confirmModal.classList.remove('opacity-100');
                                resolve();
                            };
                            confirmCancel.onclick = () => {
                                confirmModal.classList.add('hidden');
                                confirmModal.classList.remove('opacity-100');
                                resolve();
                            };
                        });
    
                        return;
                    }
                    selectedNaikKelas.push({ ID: memberId, Fullname: memberName, KelasId: memberKelasId });
                } else {
                    selectedNaikKelas = selectedNaikKelas.filter(s => s.ID !== memberId);
                    if (selectedNaikKelas.length === 0) {
                        selectedKelasId = null;
                    }
                }
                updateSelectedPreview();
            }
        });
    });
            
        } catch (error) { 
            showStatus(error.message, 'error'); 
            listDiv.innerHTML = `
                <div class="flex items-center justify-center py-8 text-red-500">
                    <svg class="w-12 h-12 mr-3 text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>${error.message}</span>
                </div>
            `;
        }
    }
    
    // Search Student Input Event (Naik Kelas)
    searchStudentInput.addEventListener('input', () => {
        clearTimeout(debounceTimer);
        const query = searchStudentInput.value.trim();
    
        if (query.length >= 2) {
            // dariKelasInput.value = '';
            // dariKelasId.value = '';
            debounceTimer = setTimeout(() => {
                const promise = fetch(`${API_URL}?action=get_members&search_query=${encodeURIComponent(query)}`);
                displayMembers(promise, studentListDiv, studentSelectionArea);
            }, 500);
        } else {
            studentSelectionArea.classList.add('hidden');
        }
    });

    // Search Student No Class Input Event (Beri Kelas)
    searchStudentNoClassInput.addEventListener('input', () => {
        clearTimeout(debounceTimer);
        const query = searchStudentNoClassInput.value.trim();
    
        if (query.length >= 2) {
            debounceTimer = setTimeout(() => {
                const promise = fetch(`${API_URL}?action=get_members_no_class&search_query=${encodeURIComponent(query)}`);
                displayMembers(promise, studentNoClassListDiv, studentNoClassSelectionArea, true);
            }, 500);
        } else {
            // Tampilkan semua siswa tanpa kelas jika input kosong atau kurang dari 2 karakter
            debounceTimer = setTimeout(() => {
                const promise = fetch(`${API_URL}?action=get_members_no_class`);
                displayMembers(promise, studentNoClassListDiv, studentNoClassSelectionArea, true);
            }, 500);
        }
    });
    

    // Select All Checkbox Event (Naik Kelas)
    selectAllCheckbox.addEventListener('change', () => {
        const studentCheckboxes = studentListDiv.querySelectorAll('.student-checkbox');
        studentCheckboxes.forEach(checkbox => {
            checkbox.checked = selectAllCheckbox.checked;
            const memberId = checkbox.value;
            const memberName = checkbox.nextElementSibling.textContent.trim();
    
            if (selectAllCheckbox.checked) {
                // Tambahkan siswa hanya jika belum ada
                if (!selectedNaikKelas.some(s => s.ID === memberId)) {
                    selectedNaikKelas.push({ ID: memberId, Fullname: memberName });
                }
            } else {
                // Hapus siswa dari daftar
                selectedNaikKelas = selectedNaikKelas.filter(s => s.ID !== memberId);
            }
        });
        updateSelectedPreview();
    });

    // Select All Checkbox Event (Beri Kelas)
    selectAllNoClassCheckbox.addEventListener('change', () => {
        const studentCheckboxes = studentNoClassListDiv.querySelectorAll('.student-checkbox');
        studentCheckboxes.forEach(checkbox => {
            checkbox.checked = selectAllNoClassCheckbox.checked;
            const memberId = checkbox.value;
            const memberName = checkbox.nextElementSibling.textContent.trim();
            if (selectAllNoClassCheckbox.checked) {
                // Add to selectedStudents if not already present
                if (!selectedStudents.some(s => s.ID === memberId)) {
                    selectedStudents.push({ ID: memberId, Fullname: memberName });
                }
            } else {
                // Remove only the currently displayed students from selectedStudents
                selectedStudents = selectedStudents.filter(s => !Array.from(studentCheckboxes).some(cb => cb.value === s.ID));
            }
            updateSelectedPreviewNoClass();
        });
    });
    
    // Process Button Event (Naik Kelas)
    prosesBtn.addEventListener('click', async () => {
    const keKelasIdValue = keKelasId.value;
    const dariKelasIdValue = dariKelasId.value;
    const memberIds = selectedNaikKelas.map(s => s.ID);

    if (!dariKelasIdValue) { 
        showStatus('Harap pilih kelas asal.', 'error'); 
        return; 
    }
    if (!keKelasIdValue) { 
        showStatus('Harap pilih kelas tujuan.', 'error'); 
        return; 
    }
    if (memberIds.length === 0) { 
        showStatus('Harap pilih minimal satu siswa untuk dipindahkan.', 'error'); 
        return; 
    }

    // Validasi jika kelas asal dan tujuan sama
    if (dariKelasIdValue === keKelasIdValue) {
        showStatus('Tidak dapat memindahkan siswa ke kelas yang sama. Silakan pilih kelas tujuan yang berbeda.', 'error');
        return;
    }

        // Show custom confirmation modal
        const confirmModal = document.getElementById('confirm-modal');
        const confirmMessage = document.getElementById('confirm-message');
        const confirmProceed = document.getElementById('confirm-proceed');
        const confirmCancel = document.getElementById('confirm-cancel');

        confirmMessage.textContent = `Anda yakin ingin memindahkan ${memberIds.length} siswa terpilih ke kelas "${keKelasInput.value}"?`;
        confirmModal.classList.remove('hidden');
        confirmModal.classList.add('opacity-100');

        // Create a promise to handle the confirmation
        const userConfirmed = await new Promise((resolve) => {
            confirmProceed.onclick = () => {
                confirmModal.classList.add('hidden');
                confirmModal.classList.remove('opacity-100');
                resolve(true);
            };
            confirmCancel.onclick = () => {
                confirmModal.classList.add('hidden');
                confirmModal.classList.remove('opacity-100');
                resolve(false);
            };
        });

        if (userConfirmed) {
            // Show loading state
            prosesBtn.disabled = true;
            prosesBtn.innerHTML = `
                <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-white mr-2"></div>
                <span>Memproses...</span>
            `;
            
            try {
                const response = await fetch(API_URL, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ 
                        action: "move_students",
                        keKelasId: keKelasIdValue, 
                        memberIds: memberIds,
                        dariKelasId: dariKelasIdValue 
                    })
                });
                const result = await response.json();
                if (!response.ok) throw new Error(result.message);
                
                showStatus(result.message, 'success');
                
                // Reset form
                studentSelectionArea.classList.add('hidden');
                searchStudentInput.value = '';
                dariKelasInput.value = '';
                dariKelasId.value = '';
                keKelasInput.value = '';
                keKelasId.value = '';
                selectAllCheckbox.checked = false;
            } catch (error) { 
                showStatus(error.message, 'error'); 
            } finally {
                // Reset button state
                prosesBtn.disabled = false;
                prosesBtn.innerHTML = `
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                    </svg>
                    <span>Proses Kenaikan Kelas</span>
                `;
            }
        }
        selectedNaikKelasIds = [];
    });

    // Process Button Event (Beri Kelas)
    beriKelasBtn.addEventListener('click', async () => {
        const keKelasIdValue = beriKelasId.value;
        const memberIds = selectedStudents.map(s => s.ID);

        if (!keKelasIdValue) { 
            showStatus('Harap pilih kelas tujuan.', 'error'); 
            return; 
        }
        if (memberIds.length === 0) { 
            showStatus('Harap pilih minimal satu siswa untuk diberi kelas.', 'error'); 
            return; 
        }

        // Show custom confirmation modal
        const confirmModal = document.getElementById('confirm-modal');
        const confirmMessage = document.getElementById('confirm-message');
        const confirmProceed = document.getElementById('confirm-proceed');
        const confirmCancel = document.getElementById('confirm-cancel');

        confirmMessage.textContent = `Anda yakin ingin memberikan kelas "${beriKelasInput.value}" kepada ${memberIds.length} siswa terpilih?`;
        confirmModal.classList.remove('hidden');
        confirmModal.classList.add('opacity-100');

        // Create a promise to handle the confirmation
        const userConfirmed = await new Promise((resolve) => {
            confirmProceed.onclick = () => {
                confirmModal.classList.add('hidden');
                confirmModal.classList.remove('opacity-100');
                resolve(true);
            };
            confirmCancel.onclick = () => {
                confirmModal.classList.add('hidden');
                confirmModal.classList.remove('opacity-100');
                resolve(false);
            };
        });

        if (userConfirmed) {
            // Show loading state
            beriKelasBtn.disabled = true;
            beriKelasBtn.innerHTML = `
                <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-white mr-2"></div>
                <span>Memproses...</span>
            `;
            
            try {
                const response = await fetch(API_URL, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ 
                        action: "beri_kelas",
                        keKelasId: keKelasIdValue, 
                        memberIds: memberIds,
                        dariKelasId: null // Indicate no source class
                    })
                });
                const result = await response.json();
                if (!response.ok) throw new Error(result.message);
                
                showStatus(result.message, 'success');
                
                // Reset form
                studentNoClassSelectionArea.classList.add('hidden');
                searchStudentNoClassInput.value = '';
                beriKelasInput.value = '';
                beriKelasId.value = '';
                selectAllNoClassCheckbox.checked = false;
                selectedStudents = []; // Clear selected students after successful processing
                studentNoClassListDiv.innerHTML = ''; // Clear the list
                updateSelectedPreviewNoClass(); // Update preview
            } catch (error) { 
                showStatus(error.message, 'error'); 
            } finally {
                // Reset button state
                beriKelasBtn.disabled = false;
                beriKelasBtn.innerHTML = `
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                    </svg>
                    <span>Proses Pemberian Kelas</span>
                `;
            }
        }
    });
    
    function showStatus(message, type) {
        const isSuccess = type === 'success';
        statusDiv.className = `mt-6 p-4 rounded-xl flex items-center ${isSuccess ? 'bg-green-50 text-green-800 border border-green-200' : 'bg-red-50 text-red-800 border border-red-200'}`;
        statusDiv.innerHTML = `
            <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                ${isSuccess ? 
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>' :
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
                }
            </svg>
            <span class="font-medium">${message}</span>
        `;
        statusDiv.classList.remove('hidden');
        
        // Auto hide after 5 seconds
        setTimeout(() => {
            statusDiv.classList.add('hidden');
        }, 5000);
    }
});