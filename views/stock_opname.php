<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Opname</title>
    <link href="../dist/output.css" rel="stylesheet">
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="../assets/images/logo.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../assets/images/logo.png">
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-gray-50 to-blue-50 min-h-screen">
    <?php include '../partials/sidebar.php'; ?>
    <div class="flex">
        <div class="flex-1 ml-64 p-8">
            <div class="container mx-auto max-w-7xl">
                <!-- Page Header -->
                <div class="mb-8">
                    <h1 class="text-4xl font-bold text-gray-800 mb-2">
                        <i class="fas fa-boxes text-blue-600 mr-3"></i>
                        Stock Opname Management
                    </h1>
                    <p class="text-gray-600">Kelola dan pantau stock opname perpustakaan dengan mudah</p>
                </div>

                <!-- Tabs Navigation -->
<div class="mb-8 flex justify-center">
    <div class="bg-white rounded-xl shadow-lg p-2 inline-flex">
        <button class="tablink px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg shadow-md font-semibold transition-all duration-300 hover:shadow-lg transform hover:-translate-y-0.5" onclick="openTab(event, 'tab1')">
            <i class="fas fa-plus-circle mr-2"></i>Stock Opname
        </button>
        <button class="tablink px-6 py-3 bg-gray-100 text-gray-600 rounded-lg font-semibold ml-2 transition-all duration-300 hover:bg-gray-200" onclick="openTab(event, 'tab2')">
            <i class="fas fa-list-alt mr-2"></i>Detail Stock Opname
        </button>
        <button class="tablink px-6 py-3 bg-gray-100 text-gray-600 rounded-lg font-semibold ml-2 transition-all duration-300 hover:bg-gray-200" onclick="openTab(event, 'tab3')">
            <i class="fas fa-chart-bar mr-2"></i>Rekapan Stock Opname
        </button>
    </div>
</div>


                <!-- Tab 1: Stock Opname -->
                <div id="tab1" class="tabcontent">
                    <!-- Add Stock Opname Card -->
                    <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-8">
                        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-8 py-6">
                            <h2 class="text-2xl font-bold text-white flex items-center">
                                <i class="fas fa-plus-circle mr-3"></i>
                                Tambah Stock Opname
                            </h2>
                        </div>
                        
                        <div class="p-8">
                            <?php
                            if (isset($_GET['success'])) {
                                echo '<div class="bg-gradient-to-r from-green-500 to-green-600 text-white px-6 py-4 rounded-xl mb-6 shadow-lg flex items-center">
                                        <i class="fas fa-check-circle mr-3 text-xl"></i>
                                        <span class="font-medium">' . htmlspecialchars($_GET['success']) . '</span>
                                      </div>';
                            }
                            if (isset($_GET['error'])) {
                                echo '<div class="bg-gradient-to-r from-red-500 to-red-600 text-white px-6 py-4 rounded-xl mb-6 shadow-lg flex items-center">
                                        <i class="fas fa-exclamation-triangle mr-3 text-xl"></i>
                                        <span class="font-medium">' . htmlspecialchars($_GET['error']) . '</span>
                                      </div>';
                            }
                            ?>
                            
                            <form action="../stock_opname_api.php" method="POST" class="space-y-6">
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                    <div class="space-y-2">
                                        <label class="block text-gray-700 font-semibold flex items-center">
                                            <i class="fas fa-project-diagram text-blue-600 mr-2"></i>
                                            Project Name *
                                        </label>
                                        <input type="text" name="project_name" class="w-full p-4 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-300 font-medium" placeholder="Masukkan nama project" required>
                                    </div>
                                    
                                    <div class="space-y-2">
                                        <label class="block text-gray-700 font-semibold flex items-center">
                                            <i class="fas fa-calendar-alt text-blue-600 mr-2"></i>
                                            Tahun *
                                        </label>
                                        <input type="number" name="tahun" class="w-full p-4 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-300 font-medium" placeholder="2024" required>
                                    </div>
                                    
                                    <div class="space-y-2">
                                        <label class="block text-gray-700 font-semibold flex items-center">
                                            <i class="fas fa-user-tie text-blue-600 mr-2"></i>
                                            Koordinator *
                                        </label>
                                        <input type="text" name="koordinator" class="w-full p-4 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-300 font-medium" placeholder="Nama koordinator" required>
                                    </div>
                                    
                                    <div class="space-y-2">
                                        <label class="block text-gray-700 font-semibold flex items-center">
                                            <i class="fas fa-clock text-blue-600 mr-2"></i>
                                            Tanggal Mulai
                                        </label>
                                        <input type="datetime-local" name="tgl_mulai" class="w-full p-4 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-300 font-medium">
                                    </div>
                                </div>
                                
                                <div class="space-y-2">
                                    <label class="block text-gray-700 font-semibold flex items-center">
                                        <i class="fas fa-sticky-note text-blue-600 mr-2"></i>
                                        Keterangan
                                    </label>
                                    <textarea name="keterangan" rows="4" class="w-full p-4 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-300 resize-none font-medium" placeholder="Tambahkan keterangan (opsional)"></textarea>
                                </div>
                                
                                <button type="submit" name="add_stock_opname" class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-8 py-4 rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 flex items-center">
                                    <i class="fas fa-save mr-2"></i>
                                    Simpan Stock Opname
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Stock Opname List -->
                    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-8 py-6">
                            <h2 class="text-2xl font-bold text-white flex items-center">
                                <i class="fas fa-list mr-3"></i>
                                Daftar Stock Opname
                            </h2>
                        </div>
                        <div class="p-8">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">No</th>
                                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Project Name</th>
                                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Tahun</th>
                                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Koordinator</th>
                                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Tanggal Mulai</th>
                                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Keterangan</th>
                                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <?php
                                        $mysqli = new mysqli('127.0.0.1', 'root', '', 'inlislite_v3', 3309);
                                        if ($mysqli->connect_error) {
                                            echo '<tr><td colspan="7" class="px-6 py-4 text-center text-red-600">Koneksi database gagal: ' . htmlspecialchars($mysqli->connect_error) . '</td></tr>';
                                        } else {
                                            $result = $mysqli->query("SELECT ID, ProjectName, Tahun, Koordinator, TglMulai, Keterangan FROM stockopname ORDER BY CreateDate DESC");
                                            $no = 1;
                                            while ($row = $result->fetch_assoc()) {
                                                echo "<tr class='hover:bg-gray-50'>";
                                                echo "<td class='px-4 py-3 text-sm text-gray-900'>$no</td>";
                                                echo "<td class='px-4 py-3 text-sm text-gray-900'>" . htmlspecialchars($row['ProjectName']) . "</td>";
                                                echo "<td class='px-4 py-3 text-sm text-gray-900'>" . htmlspecialchars($row['Tahun']) . "</td>";
                                                echo "<td class='px-4 py-3 text-sm text-gray-900'>" . htmlspecialchars($row['Koordinator']) . "</td>";
                                                echo "<td class='px-4 py-3 text-sm text-gray-900'>" . ($row['TglMulai'] ? htmlspecialchars($row['TglMulai']) : '-') . "</td>";
                                                echo "<td class='px-4 py-3 text-sm text-gray-900'>" . ($row['Keterangan'] ? htmlspecialchars($row['Keterangan']) : '-') . "</td>";
                                                echo "<td class='px-4 py-3 text-sm'>
                                                        <button onclick='handleDetailClick(" . $row['ID'] . ")' 
                                                                class='bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg shadow-md transition-all duration-300 flex items-center'>
                                                            <i class='fas fa-eye mr-2'></i>Detail
                                                        </button>
                                                      </td>";
                                                echo "</tr>";
                                                $no++;
                                            }
                                            if ($no == 1) {
                                                echo '<tr><td colspan="7" class="px-6 py-12 text-center text-gray-500"><i class="fas fa-inbox text-4xl mb-4 text-gray-300"></i><div class="text-lg font-medium">Belum ada data stock opname</div></td></tr>';
                                            }
                                            $mysqli->close();
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab 2: Detail Stock Opname -->
                <div id="tab2" class="tabcontent hidden">
                    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-8 py-6">
                            <h2 id="detail-title" class="text-2xl font-bold text-white flex items-center">
                                <i class="fas fa-clipboard-list mr-3"></i>
                                Detail Stock Opname
                            </h2>
                        </div>

                        <div class="p-8 space-y-8">
                            <!-- Barcode Input -->
                            <div class="space-y-4">
                                <label class="block text-gray-700 font-semibold flex items-center">
                                    <i class="fas fa-barcode text-blue-600 mr-2"></i>
                                    Input Barcode
                                </label>
                                <div class="flex gap-4">
                                    <input type="text" id="barcode" class="w-full p-4 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-300 font-medium" placeholder="Scan atau masukkan barcode">
                                    <button onclick="submitBarcode()" class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-6 py-4 rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 flex items-center">
                                        <i class="fas fa-check mr-2"></i>Submit
                                    </button>
                                </div>
                            </div>

                            <!-- Category Filter -->
                            <div class="flex justify-end items-center mb-4">
                                <div class="flex items-center space-x-4">
                                    <label class="text-gray-700 font-semibold">Kategori:</label>
                                    <select id="category-filter" onchange="resetPages(); loadDetailData(selectedProjectId)" class="p-2 border-2 border-gray-200 rounded-lg focus:border-blue-500">
                                        <option value="all">Semua Kategori</option>
                                        <option value="7">Koleksi Umum</option>
                                        <option value="8">Koleksi Referensi</option>
                                        <option value="9">Fiksi</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Unverified Collections -->
                            <div>
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-xl font-bold text-gray-800 flex items-center">
                                        <i class="fas fa-books mr-2 text-blue-600"></i>
                                        Daftar Koleksi Belum Diperiksa
                                    </h3>
                                    <div class="flex items-center space-x-4">
                                        <label class="text-gray-700 font-semibold">Tampilkan:</label>
                                        <select id="unverified-limit" onchange="resetUnverifiedPage(); loadDetailData(selectedProjectId)" class="p-2 border-2 border-gray-200 rounded-lg focus:border-blue-500">
                                            <option value="20">20</option>
                                            <option value="50">50</option>
                                            <option value="100">100</option>
                                            <option value="200">200</option>
                                            <option value="all">Semua</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">No</th>
                                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Barcode</th>
                                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Tgl Pengadaan</th>
                                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">No Induk</th>
                                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">No Panggil</th>
                                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Media</th>
                                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Sumber</th>
                                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Kategori</th>
                                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Akses</th>
                                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Ketersediaan</th>
                                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Lokasi Perpustakaan</th>
                                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Lokasi</th>
                                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">OPAC</th>
                                            </tr>
                                        </thead>
                                        <tbody id="unverified-collections" class="bg-white divide-y divide-gray-200">
                                            <!-- Populated via JavaScript/AJAX -->
                                        </tbody>
                                    </table>
                                </div>
                                <div id="unverified-pagination" class="flex justify-between items-center mt-4">
                                    <div id="unverified-info" class="text-gray-600"></div>
                                    <div class="flex space-x-2">
                                        <button id="unverified-prev" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg disabled:opacity-50" disabled>Previous</button>
                                        <div id="unverified-page-numbers" class="flex space-x-1"></div>
                                        <button id="unverified-next" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg disabled:opacity-50" disabled>Next</button>
                                    </div>
                                </div>
                            </div>

                            <!-- Stock Opname Results -->
                            <div>
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-xl font-bold text-gray-800 flex items-center">
                                        <i class="fas fa-check-circle mr-2 text-blue-600"></i>
                                        Daftar Koleksi Hasil Stock Opname
                                    </h3>
                                    <div class="flex items-center space-x-4">
                                        <label class="text-gray-700 font-semibold">Tampilkan:</label>
                                        <select id="results-limit" onchange="resetResultsPage(); loadDetailData(selectedProjectId)" class="p-2 border-2 border-gray-200 rounded-lg focus:border-blue-500">
                                            <option value="20">20</option>
                                            <option value="50">50</option>
                                            <option value="100">100</option>
                                            <option value="200">200</option>
                                            <option value="all">Semua</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">No</th>
                                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Tgl Scan</th>
                                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Barcode</th>
                                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">No Induk</th>
                                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Judul</th>
                                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Pengarang</th>
                                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Penerbit</th>
                                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Lok Sebelum</th>
                                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Lok Sekarang</th>
                                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Tersedia Sebelum</th>
                                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Tersedia Sekarang</th>
                                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Akses Sebelum</th>
                                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Akses Sekarang</th>
                                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">User</th>
                                            </tr>
                                        </thead>
                                        <tbody id="stock-opname-results" class="bg-white divide-y divide-gray-200">
                                            <!-- Populated via JavaScript/AJAX -->
                                        </tbody>
                                    </table>
                                </div>
                                <div id="results-pagination" class="flex justify-between items-center mt-4">
                                    <div id="results-info" class="text-gray-600"></div>
                                    <div class="flex space-x-2">
                                        <button id="results-prev" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg disabled:opacity-50" disabled>Previous</button>
                                        <div id="results-page-numbers" class="flex space-x-1"></div>
                                        <button id="results-next" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg disabled:opacity-50" disabled>Next</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab 3: Rekapan Stock Opname -->
                <div id="tab3" class="tabcontent hidden">
                    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-8 py-6">
                            <h2 class="text-2xl font-bold text-white flex items-center">
                                <i class="fas fa-chart-bar mr-3"></i>
                                Rekapan Stock Opname
                            </h2>
                        </div>
                        <div class="p-8">
                            <div id="recap-content" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <!-- Populated via JavaScript/AJAX -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let selectedProjectId = null;
        let unverifiedPage = 1;
        let resultsPage = 1;

        function openTab(evt, tabName) {
            if (tabName === 'tab2' && !selectedProjectId) {
                showAlert('Silakan pilih proyek terlebih dahulu di Tab 1!', 'warning');
                return;
            }

            var i, tabcontent, tablinks;
            tabcontent = document.getElementsByClassName("tabcontent");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].classList.add("hidden");
            }
            tablinks = document.getElementsByClassName("tablink");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].classList.remove("bg-gradient-to-r", "from-blue-600", "to-blue-700", "text-white", "shadow-md");
                tablinks[i].classList.add("bg-gray-100", "text-gray-600");
            }
            document.getElementById(tabName).classList.remove("hidden");
            evt.currentTarget.classList.add("bg-gradient-to-r", "from-blue-600", "to-blue-700", "text-white", "shadow-md");
            evt.currentTarget.classList.remove("bg-gray-100", "text-gray-600");

            if (tabName === 'tab2') {
                loadDetailData(selectedProjectId);
                focusBarcodeInput();
            } else if (tabName === 'tab3') {
                loadRecapData();
            }
        }

                // Tambahkan fungsi handleDetailClick di bagian JavaScript
        function handleDetailClick(projectId) {
            selectedProjectId = projectId;
            
            // Sembunyikan semua tab content
            var tabcontents = document.getElementsByClassName("tabcontent");
            for (var i = 0; i < tabcontents.length; i++) {
                tabcontents[i].classList.add("hidden");
            }
            
            // Reset semua tab link styles
            var tablinks = document.getElementsByClassName("tablink");
            for (var i = 0; i < tablinks.length; i++) {
                tablinks[i].classList.remove("bg-gradient-to-r", "from-blue-600", "to-blue-700", "text-white", "shadow-md");
                tablinks[i].classList.add("bg-gray-100", "text-gray-600");
            }
            
            // Tampilkan tab2 dan aktifkan tablink-nya
            document.getElementById("tab2").classList.remove("hidden");
            tablinks[1].classList.add("bg-gradient-to-r", "from-blue-600", "to-blue-700", "text-white", "shadow-md");
            tablinks[1].classList.remove("bg-gray-100", "text-gray-600");
            
            // Load data dan focus ke input barcode
            loadDetailData(selectedProjectId);
            focusBarcodeInput();
        }

        function setProjectId(projectId, event) { // Tambah parameter event
            selectedProjectId = projectId;
            unverifiedPage = 1;
            resultsPage = 1;
            
            // Tambahkan kode untuk mengaktifkan tab
            var tablinks = document.getElementsByClassName("tablink");
            for (var i = 0; i < tablinks.length; i++) {
                tablinks[i].classList.remove("bg-gradient-to-r", "from-blue-600", "to-blue-700", "text-white", "shadow-md");
                tablinks[i].classList.add("bg-gray-100", "text-gray-600");
            }
            
            // Aktifkan tab Detail
            tablinks[1].classList.add("bg-gradient-to-r", "from-blue-600", "to-blue-700", "text-white", "shadow-md");
            tablinks[1].classList.remove("bg-gray-100", "text-gray-600");
        }

        function resetUnverifiedPage() {
            unverifiedPage = 1;
        }

        function resetResultsPage() {
            resultsPage = 1;
        }

        function resetPages() {
            unverifiedPage = 1;
            resultsPage = 1;
        }

        function loadDetailData(projectId) {
            const unverifiedLimit = document.getElementById('unverified-limit').value;
            const resultsLimit = document.getElementById('results-limit').value;
            const categoryFilter = document.getElementById('category-filter').value;

            fetch('../stock_opname_api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'get_detail',
                    project_id: projectId,
                    unverified_limit: unverifiedLimit,
                    unverified_page: unverifiedPage,
                    results_limit: resultsLimit,
                    results_page: resultsPage,
                    category_id: categoryFilter
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.message) {
                    showAlert(data.message, 'error');
                    return;
                }
                document.getElementById("detail-title").innerHTML = `<i class="fas fa-clipboard-list mr-3"></i>Detail Stock Opname: ${data.project_name} ${data.tahun}`;
                
                // Update unverified collections table
                document.getElementById("unverified-collections").innerHTML = data.unverified_html || '<tr><td colspan="13" class="px-6 py-12 text-center text-gray-500"><i class="fas fa-check-circle text-4xl mb-4 text-green-300"></i><div class="text-lg font-medium">Semua koleksi sudah diperiksa</div></td></tr>';
                
                // Update unverified pagination
                const unverifiedInfo = document.getElementById("unverified-info");
                const unverifiedPageNumbers = document.getElementById("unverified-page-numbers");
                const unverifiedPrev = document.getElementById("unverified-prev");
                const unverifiedNext = document.getElementById("unverified-next");
                
                unverifiedInfo.textContent = `Menampilkan ${data.unverified_count > 0 ? ((unverifiedPage - 1) * (unverifiedLimit === 'all' ? data.unverified_count : unverifiedLimit) + 1) : 0} - ${Math.min(unverifiedPage * (unverifiedLimit === 'all' ? data.unverified_count : unverifiedLimit), data.unverified_count)} dari ${data.unverified_count} data`;
                
                unverifiedPageNumbers.innerHTML = '';
                if (data.unverified_total_pages > 1) {
                    for (let i = 1; i <= data.unverified_total_pages; i++) {
                        const pageBtn = document.createElement('button');
                        pageBtn.className = `px-4 py-2 rounded-lg ${i === unverifiedPage ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700'}`;
                        pageBtn.textContent = i;
                        pageBtn.onclick = () => {
                            unverifiedPage = i;
                            loadDetailData(projectId);
                        };
                        unverifiedPageNumbers.appendChild(pageBtn);
                    }
                }
                unverifiedPrev.disabled = unverifiedPage === 1;
                unverifiedNext.disabled = unverifiedPage >= data.unverified_total_pages;
                unverifiedPrev.onclick = () => {
                    if (unverifiedPage > 1) {
                        unverifiedPage--;
                        loadDetailData(projectId);
                    }
                };
                unverifiedNext.onclick = () => {
                    if (unverifiedPage < data.unverified_total_pages) {
                        unverifiedPage++;
                        loadDetailData(projectId);
                    }
                };

                // Update stock opname results table
                document.getElementById("stock-opname-results").innerHTML = data.results_html || '<tr><td colspan="14" class="px-6 py-12 text-center text-gray-500"><i class="fas fa-inbox text-4xl mb-4 text-gray-300"></i><div class="text-lg font-medium">Belum ada hasil stock opname</div></td></tr>';
                
                // Update results pagination
                const resultsInfo = document.getElementById("results-info");
                const resultsPageNumbers = document.getElementById("results-page-numbers");
                const resultsPrev = document.getElementById("results-prev");
                const resultsNext = document.getElementById("results-next");
                
                resultsInfo.textContent = `Menampilkan ${data.results_count > 0 ? ((resultsPage - 1) * (resultsLimit === 'all' ? data.results_count : resultsLimit) + 1) : 0} - ${Math.min(resultsPage * (resultsLimit === 'all' ? data.results_count : resultsLimit), data.results_count)} dari ${data.results_count} data`;
                
                resultsPageNumbers.innerHTML = '';
                if (data.results_total_pages > 1) {
                    for (let i = 1; i <= data.results_total_pages; i++) {
                        const pageBtn = document.createElement('button');
                        pageBtn.className = `px-4 py-2 rounded-lg ${i === resultsPage ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700'}`;
                        pageBtn.textContent = i;
                        pageBtn.onclick = () => {
                            resultsPage = i;
                            loadDetailData(projectId);
                        };
                        resultsPageNumbers.appendChild(pageBtn);
                    }
                }
                resultsPrev.disabled = resultsPage === 1;
                resultsNext.disabled = resultsPage >= data.results_total_pages;
                resultsPrev.onclick = () => {
                    if (resultsPage > 1) {
                        resultsPage--;
                        loadDetailData(projectId);
                    }
                };
                resultsNext.onclick = () => {
                    if (resultsPage < data.results_total_pages) {
                        resultsPage++;
                        loadDetailData(projectId);
                    }
                };
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Gagal memuat data detail.', 'error');
            });
        }

        function loadRecapData() {
            fetch('../stock_opname_api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'get_recap' })
            })
            .then(response => response.json())
            .then(data => {
                if (data.message) {
                    showAlert(data.message, 'error');
                    return;
                }
                const recapContent = document.getElementById("recap-content");
                recapContent.innerHTML = `
                    <div class="bg-blue-100 p-6 rounded-xl shadow-lg flex items-center">
                        <i class="fas fa-books text-4xl text-blue-600 mr-4"></i>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">Total Koleksi</h3>
                            <p class="text-2xl font-bold text-blue-600">${data.total_collections}</p>
                        </div>
                    </div>
                    <div class="bg-yellow-100 p-6 rounded-xl shadow-lg flex items-center">
                        <i class="fas fa-hourglass-half text-4xl text-yellow-600 mr-4"></i>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">Koleksi Belum Diperiksa</h3>
                            <p class="text-2xl font-bold text-yellow-600">${data.unverified_collections}</p>
                        </div>
                    </div>
                    <div class="bg-green-100 p-6 rounded-xl shadow-lg flex items-center">
                        <i class="fas fa-check-circle text-4xl text-green-600 mr-4"></i>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">Koleksi Sudah Diperiksa</h3>
                            <p class="text-2xl font-bold text-green-600">${data.verified_collections}</p>
                        </div>
                    </div>
                `;
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Gagal memuat data rekapan.', 'error');
            });
        }

        function submitBarcode() {
            var barcode = document.getElementById("barcode").value.trim();
            if (!selectedProjectId || !barcode) {
                showAlert("Pilih proyek dan masukkan nomor barcode!", 'warning');
                return;
            }

            const submitBtn = event.target;
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
            submitBtn.disabled = true;

            fetch('../stock_opname_api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'submit_barcode', project_id: selectedProjectId, barcode: barcode })
            })
            .then(response => response.json())
            .then(data => {
                if (data.message === 'Barcode submitted successfully') {
                    loadDetailData(selectedProjectId);
                    document.getElementById("barcode").value = "";
                    showAlert('Barcode berhasil disubmit!', 'success');
                } else {
                    showAlert(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Gagal mengirim barcode.', 'error');
            })
            .finally(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        }

        function showAlert(message, type = 'info') {
            const alertDiv = document.createElement('div');
            const iconClass = type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-triangle' : type === 'warning' ? 'fa-exclamation-circle' : 'fa-info-circle';
            const colorClass = type === 'success' ? 'from-green-500 to-green-600' : type === 'error' ? 'from-red-500 to-red-600' : type === 'warning' ? 'from-yellow-500 to-yellow-600' : 'from-blue-500 to-blue-600';
            
            alertDiv.className = `fixed top-4 right-4 bg-gradient-to-r ${colorClass} text-white px-6 py-4 rounded-xl shadow-2xl z-50 flex items-center transform translate-x-full transition-transform duration-300`;
            alertDiv.innerHTML = `
                <i class="fas ${iconClass} mr-3 text-xl"></i>
                <span class="font-medium">${message}</span>
                <button onclick="this.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                    <i class="fas fa-times"></i>
                </button>
            `;
            
            document.body.appendChild(alertDiv);
            
            setTimeout(() => {
                alertDiv.classList.remove('translate-x-full');
            }, 100);
            
            setTimeout(() => {
                alertDiv.classList.add('translate-x-full');
                setTimeout(() => alertDiv.remove(), 300);
            }, 5000);
        }

        function focusBarcodeInput() {
            const barcodeInput = document.getElementById('barcode');
            if (barcodeInput) {
                barcodeInput.focus();
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('barcode').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    submitBarcode();
                }
            });
        });

        // Open Tab 1 by default
        document.getElementById("tab1").classList.remove("hidden");
        document.getElementsByClassName("tablink")[0].classList.add("bg-gradient-to-r", "from-blue-600", "to-blue-700", "text-white", "shadow-md");
    </script>
</body>
</html>