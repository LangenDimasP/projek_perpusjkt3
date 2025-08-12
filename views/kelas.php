<?php
session_start();
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header('Location: /naik_kelas/login');
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Kenaikan Kelas Inlislite</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="../assets/images/logo.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../assets/images/logo.png">
    
    <link href="../dist/output.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-blue-50 via-white to-blue-100 min-h-screen">
    <div class="flex min-h-screen">
        <?php include '../partials/sidebar.php'; ?>
        
        <main class="ml-64 flex-1 p-8 flex flex-col">
            <div class="max-w-4xl w-full mx-auto">


                <!-- Tabs -->
                <div class="bg-white rounded-2xl shadow-xl p-8 mb-6">
                    <div class="flex justify-center border-b border-gray-200">
                        <button id="tab-tambah-kelas" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-blue-600 hover:border-b-2 hover:border-blue-600 focus:outline-none">Tambah Kelas</button>
                        <button id="tab-naik-kelas" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-blue-600 hover:border-b-2 hover:border-blue-600 focus:outline-none">Naik Kelas</button>
                        <button id="tab-beri-kelas" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-blue-600 hover:border-b-2 hover:border-blue-600 focus:outline-none">Beri Kelas</button>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="bg-white rounded-2xl shadow-xl p-8">
                    <!-- Tambah Kelas Tab Content -->
                    <div id="tambah-kelas-content" class="">
                        <div id="tambah-kelas-form">
                            <div class="mb-8">
                                <label for="nama-kelas-input" class="flex items-center text-sm font-semibold text-gray-700 mb-3">
                                    <svg class="w-5 h-5 mr-2 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    <span class="flex-1">Nama Kelas</span>
                                </label>
                                <input type="text" 
                                       id="nama-kelas-input" 
                                       placeholder="Masukkan nama kelas (maks. 50 karakter)" 
                                       maxlength="50"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all duration-200 bg-white shadow-sm">
                            </div>
                            <button id="tambah-kelas-btn" class="w-full bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold py-4 px-6 rounded-xl transition-all duration-200 hover:scale-105 focus:ring-4 focus:ring-blue-200 shadow-lg flex items-center justify-center space-x-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                <span>Tambah Kelas</span>
                            </button>
                        </div>
                    </div>

                    <!-- Naik Kelas Tab Content -->
                    <div id="naik-kelas-content" class="hidden">
                        <!-- Pilih Siswa dari Kelas -->
                        <div class="mb-8 relative">
                            <label for="dari-kelas-input" class="flex items-center text-sm font-semibold text-gray-700 mb-3">
                                <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                Pilih Siswa DARI Kelas
                            </label>
                            <div class="relative">
                                <input type="text" 
                                       id="dari-kelas-input" 
                                       placeholder="Ketik nama kelas atau pilih dari dropdown..."
                                       class="w-full px-4 py-3 pr-10 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all duration-200 bg-white shadow-sm"
                                       autocomplete="off">
                                <button type="button" id="dari-kelas-dropdown-btn" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <input type="hidden" id="dari-kelas-id" value="">
                            </div>
                            
                            <!-- Dropdown Menu for Dari Kelas -->
                            <div id="dari-kelas-dropdown" class="absolute z-10 w-full mt-1 bg-white rounded-xl shadow-lg border border-gray-200 max-h-60 overflow-y-auto transition-all duration-200 origin-top hidden">
                                <div id="dari-kelas-loading" class="flex items-center justify-center py-4 text-gray-500 hidden">
                                    <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-blue-500 mr-2"></div>
                                    <span>Mencari kelas...</span>
                                </div>
                                <div id="dari-kelas-results" class="py-2">
                                    <!-- Results will be populated here -->
                                </div>
                            </div>
                        </div>

                        <!-- Divider -->
                        <div class="flex items-center my-8">
                            <div class="flex-1 border-t border-gray-200"></div>
                            <div class="px-4 bg-blue-50 rounded-full">
                                <span class="text-sm font-medium text-blue-600">ATAU</span>
                            </div>
                            <div class="flex-1 border-t border-gray-200"></div>
                        </div>

                        <!-- Search Siswa -->
                        <div class="mb-8">
                            <label for="search-student" class="flex items-center text-sm font-semibold text-gray-700 mb-3">
                                <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                Cari Siswa Berdasarkan Nama
                            </label>
                            <div class="relative">
                                <input type="text" id="search-student" placeholder="Ketik minimal 2 huruf untuk mencari..." 
                                       class="w-full px-4 py-3 pl-12 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all duration-200 shadow-sm">
                                <svg class="w-5 h-5 text-gray-400 absolute left-4 top-1/2 transform -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </div>

                        <!-- Student Selection Area -->
                        <div id="student-selection-area" class="hidden mb-8">
                            <div class="bg-blue-50 rounded-xl p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-semibold text-gray-800">Daftar Siswa</h3>
                                    <label class="flex items-center cursor-pointer">
                                        <input type="checkbox" id="select-all-students" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                                        <span class="ml-2 text-sm font-medium text-gray-700">Pilih Semua</span>
                                    </label>
                                </div>
                                
                                <div id="selected-preview" class="mb-2 text-sm text-blue-700 font-semibold"></div>
                                <div id="student-list" class="max-h-80 overflow-y-auto bg-white rounded-lg border border-gray-200 p-4 space-y-2">
                                    <!-- Student items will be populated here -->
                                </div>
                            </div>
                        </div>

                        <!-- Divider -->
                        <div class="border-t border-gray-200 my-8"></div>

                        <!-- Kelas Tujuan -->
                        <div class="mb-8 relative">
                            <label for="ke-kelas-input" class="flex items-center text-sm font-semibold text-gray-700 mb-3">
                                <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                </svg>
                                Pindahkan Siswa yang Dipilih ke Kelas
                            </label>
                            <div class="relative">
                                <input type="text" 
                                       id="ke-kelas-input" 
                                       placeholder="Ketik nama kelas tujuan atau pilih dari dropdown..."
                                       class="w-full px-4 py-3 pr-10 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all duration-200 bg-white shadow-sm"
                                       autocomplete="off">
                                <button type="button" id="ke-kelas-dropdown-btn" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <input type="hidden" id="ke-kelas-id" value="">
                            </div>
                            
                            <!-- Dropdown Menu for Ke Kelas -->
                            <div id="ke-kelas-dropdown" class="absolute z-10 w-full mt-1 bg-white rounded-xl shadow-lg border border-gray-200 max-h-60 overflow-y-auto transition-all duration-200 origin-top hidden">
                                <div id="ke-kelas-loading" class="flex items-center justify-center py-4 text-gray-500 hidden">
                                    <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-blue-500 mr-2"></div>
                                    <span>Mencari kelas...</span>
                                </div>
                                <div id="ke-kelas-results" class="py-2">
                                    <!-- Results will be populated here -->
                                </div>
                            </div>
                        </div>

                        <!-- Process Button -->
                        <button id="proses-btn" class="w-full bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold py-4 px-6 rounded-xl transition-all duration-200 hover:scale-105 focus:ring-4 focus:ring-blue-200 shadow-lg flex items-center justify-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                            </svg>
                            <span>Proses Kenaikan Kelas</span>
                        </button>
                    </div>

                    <!-- Beri Kelas Tab Content -->
                    <div id="beri-kelas-content" class="hidden">
                        <!-- Search Siswa Tanpa Kelas -->
                        <div class="mb-8">
                            <label for="search-student-no-class" class="flex items-center text-sm font-semibold text-gray-700 mb-3">
                                <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                Cari Siswa Tanpa Kelas
                            </label>
                            <div class="relative">
                                <input type="text" id="search-student-no-class" placeholder="Ketik minimal 2 huruf untuk mencari siswa tanpa kelas..." 
                                       class="w-full px-4 py-3 pl-12 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all duration-200 shadow-sm">
                                <svg class="w-5 h-5 text-gray-400 absolute left-4 top-1/2 transform -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </div>

                        <!-- Student Selection Area for No Class -->
                        <div id="student-no-class-selection-area" class="hidden mb-8">
                            <div class="bg-blue-50 rounded-xl p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-semibold text-gray-800">Daftar Siswa Tanpa Kelas</h3>
                                    <label class="flex items-center cursor-pointer">
                                        <input type="checkbox" id="select-all-students-no-class" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                                        <span class="ml-2 text-sm font-medium text-gray-700">Pilih Semua</span>
                                    </label>
                                </div>

                                <div id="selected-preview-no-class" class="mb-2 text-sm text-blue-700 font-semibold"></div>
                                <div id="student-no-class-list" class="max-h-80 overflow-y-auto bg-white rounded-lg border border-gray-200 p-4 space-y-2">
                                    <!-- Student items will be populated here -->
                                </div>
                            </div>
                        </div>

                        <!-- Divider -->
                        <div class="border-t border-gray-200 my-8"></div>

                        <!-- Kelas Tujuan -->
                        <div class="mb-8 relative">
                            <label for="beri-kelas-input" class="flex items-center text-sm font-semibold text-gray-700 mb-3">
                                <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                </svg>
                                Beri Kelas untuk Siswa yang Dipilih
                            </label>
                            <div class="relative">
                                <input type="text" 
                                       id="beri-kelas-input" 
                                       placeholder="Ketik nama kelas atau pilih dari dropdown..."
                                       class="w-full px-4 py-3 pr-10 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all duration-200 bg-white shadow-sm"
                                       autocomplete="off">
                                <button type="button" id="beri-kelas-dropdown-btn" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <input type="hidden" id="beri-kelas-id" value="">
                            </div>
                            
                            <!-- Dropdown Menu for Beri Kelas -->
                            <div id="beri-kelas-dropdown" class="absolute z-10 w-full mt-1 bg-white rounded-xl shadow-lg border border-gray-200 max-h-60 overflow-y-auto transition-all duration-200 origin-top hidden">
                                <div id="beri-kelas-loading" class="flex items-center justify-center py-4 text-gray-500 hidden">
                                    <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-blue-500 mr-2"></div>
                                    <span>Mencari kelas...</span>
                                </div>
                                <div id="beri-kelas-results" class="py-2">
                                    <!-- Results will be populated here -->
                                </div>
                            </div>
                        </div>

                        <!-- Process Button -->
                        <button id="beri-kelas-btn" class="w-full bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold py-4 px-6 rounded-xl transition-all duration-200 hover:scale-105 focus:ring-4 focus:ring-blue-200 shadow-lg flex items-center justify-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                            </svg>
                            <span>Proses Pemberian Kelas</span>
                        </button>
                    </div>

                    <!-- Status Message -->
                    <div id="status" class="mt-6 p-4 rounded-xl hidden">
                        <!-- Status messages will appear here -->
                    </div>
                </div>

                <!-- Confirmation Modal -->
                <div id="confirm-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center transition-opacity duration-300 hidden z-50">
                    <div class="bg-white rounded-2xl shadow-xl p-8 max-w-md w-full mx-4 transition-transform duration-300">
                        <div class="flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800 text-center mb-4">Konfirmasi</h3>
                        <p id="confirm-message" class="text-gray-600 text-center mb-6"></p>
                        <div class="flex justify-center space-x-4">
                            <button id="confirm-cancel" class="px-6 py-2 bg-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-300 transition-all duration-200 focus:ring-4 focus:ring-gray-100">
                                Batal
                            </button>
                            <button id="confirm-proceed" class="px-6 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold rounded-xl hover:from-blue-600 hover:to-blue-700 transition-all duration-200 focus:ring-4 focus:ring-blue-200">
                                Ya, Lanjutkan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="../script.js"></script>
    <script>
        const passwordInput = document.getElementById('password');
        const showPasswordCheckbox = document.getElementById('show-password');
        if (showPasswordCheckbox) {
            showPasswordCheckbox.addEventListener('change', function() {
                passwordInput.type = this.checked ? 'text' : 'password';
            });
        }
    </script>
</body>
</html>