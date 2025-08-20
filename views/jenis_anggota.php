<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$mysqli = new mysqli('127.0.0.1', 'root', '', 'inlislite_v3', 3309);
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Fetch user info
$userId = $_SESSION['user_id'];
$userQuery = $mysqli->query("SELECT Fullname FROM users WHERE ID = $userId");
$user = $userQuery->fetch_assoc();

// Fetch collection categories
$collectionCategories = $mysqli->query("SELECT * FROM collectioncategorys");

// Fetch location libraries
$locationLibraries = $mysqli->query("SELECT * FROM location_library");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jenis Anggota - INLISLite QuickAccess</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="../assets/images/logo.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../assets/images/logo.png">
    
    <!-- Tailwind CSS -->
    <link href="../dist/output.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Custom Styles -->
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .card-shadow {
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .tab-active {
            background: #4f46e5;
            color: white;
        }
        .disabled-tab { 
            color: #9ca3af; 
            pointer-events: none; 
            opacity: 0.5;
        }
        .btn-gradient {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            transition: all 0.3s ease;
        }
        .btn-gradient:hover {
            background: linear-gradient(135deg, #4338ca, #6d28d9);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.4);
        }
        .notification-slide {
            animation: slideInRight 0.3s ease-out;
        }
        .tab-active:hover {
    background: #4f46e5 !important;
    color: white !important;
    cursor: default;
}
        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 to-blue-50 text-gray-800 min-h-screen">
    <!-- Include Sidebar -->
    <?php include '../partials/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="ml-64 p-6 min-h-screen">
        <div class="container mx-auto max-w-7xl">
            <!-- Header Section -->
            <div class="mb-8">
                <div class="bg-purple-600 rounded-2xl p-8 text-white card-shadow">
                    <div class="flex items-center space-x-4">
                        <div class="bg-white/20 p-3 rounded-full">
                            <i class="fas fa-users text-2xl"></i>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold mb-2">Manajemen Jenis Anggota</h1>
                            <p class="text-white/80">Kelola jenis anggota, kategori default, dan lokasi perpustakaan</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notification -->
            <div id="notification" class="hidden fixed top-4 right-4 p-4 rounded-lg shadow-lg text-white z-50 notification-slide"></div>

            <!-- Tabs Navigation -->
            <div class="mb-8">
                <div class="bg-white rounded-xl card-shadow p-2">
                    <ul class="flex flex-wrap text-sm font-medium" id="tab-nav">
                        <li class="flex-1">
                            <a href="#tab-daftar" class="flex items-center justify-center p-4 rounded-lg transition-all duration-300 hover:bg-gray-100 active-tab">
                                <i class="fas fa-list mr-2"></i>
                                Daftar Jenis Anggota
                            </a>
                        </li>
                        <li class="flex-1">
                            <a href="#tab-tambah" class="flex items-center justify-center p-4 rounded-lg transition-all duration-300 hover:bg-gray-100">
                                <i class="fas fa-plus mr-2"></i>
                                Tambah Jenis Anggota
                            </a>
                        </li>
                        <li class="flex-1">
                            <a href="#tab-default-kategori" class="flex items-center justify-center p-4 rounded-lg transition-all duration-300 hover:bg-gray-100 disabled-tab" id="tab-default-kategori-link" aria-disabled="true">
                                <i class="fas fa-tags mr-2"></i>
                                Default Kategori Koleksi
                            </a>
                        </li>
                        <li class="flex-1">
                            <a href="#tab-default-lokasi" class="flex items-center justify-center p-4 rounded-lg transition-all duration-300 hover:bg-gray-100 disabled-tab" id="tab-default-lokasi-link" aria-disabled="true">
                                <i class="fas fa-map-marker-alt mr-2"></i>
                                Default Lokasi Perpustakaan
                            </a>
                        </li>
                        <li class="flex-1">
                            <a href="#tab-sync-member" class="flex items-center justify-center p-4 rounded-lg transition-all duration-300 hover:bg-gray-100" id="tab-sync-member-link">
                                <i class="fas fa-sync mr-2"></i>
                                Sinkronisasi Member
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Tabs Content -->
            <div class="mt-6">
                <!-- Tab 1: Daftar Jenis Anggota -->
                <div id="tab-daftar" class="tab-content">
    <div class="bg-white rounded-xl card-shadow overflow-hidden">
        <div class="bg-blue-500 p-6">
            <h3 class="text-xl font-semibold text-white flex items-center">
                <i class="fas fa-table mr-3"></i>
                Daftar Jenis Anggota
            </h3>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table id="jenis-table" class="min-w-full bg-white border border-gray-200 rounded-lg shadow">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Anggota</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Masa Berlaku (hari)</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Max Pinjam Koleksi</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Max Loan Days</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Warning Due Day</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Day Perpanjang</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Count Perpanjang</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Denda Type</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Suspend Type</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="jenis-table-body" class="divide-y divide-gray-200"></tbody>
                </table>
            </div>
        </div>
        <p id="warning-message" class="text-red-500 p-6 hidden bg-red-50 border-l-4 border-red-500">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            Silahkan memilih jenis anggota di tab 1
        </p>
    </div>
</div>

                <!-- Tab 2: Tambah Jenis Anggota -->
                <div id="tab-tambah" class="tab-content hidden">
                    <div class="bg-white rounded-xl card-shadow overflow-hidden">
                        <div class="bg-green-500 p-6">
                            <h3 class="text-xl font-semibold text-white flex items-center">
                                <i class="fas fa-user-plus mr-3"></i>
                                Tambah Jenis Anggota Baru
                            </h3>
                        </div>
                        <form id="form-tambah" class="p-8 space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="md:col-span-2">
            <label for="jenisanggota" class="block text-sm font-semibold text-gray-700 mb-2">
                <i class="fas fa-user-tag mr-2 text-blue-500"></i>
                Jenis Anggota
            </label>
            <input type="text" id="jenisanggota" name="jenisanggota" 
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300" 
                   placeholder="Masukkan jenis anggota..." required>
        </div>
        <div>
            <label for="masaberlaku" class="block text-sm font-semibold text-gray-700 mb-2">
                <i class="fas fa-calendar-alt mr-2 text-purple-500"></i>
                Masa Berlaku (hari)
            </label>
            <input type="number" id="masaberlaku" name="masaberlaku" value="365" min="0" 
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300">
        </div>
        <div>
            <label for="maxpinjam" class="block text-sm font-semibold text-gray-700 mb-2">
                <i class="fas fa-book mr-2 text-green-500"></i>
                Max Pinjam Koleksi
            </label>
            <input type="number" id="maxpinjam" name="maxpinjam" value="1000" min="0" 
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-300">
        </div>
        <div>
            <label for="maxloandays" class="block text-sm font-semibold text-gray-700 mb-2">
                <i class="fas fa-clock mr-2 text-orange-500"></i>
                Max Hari Pinjam
            </label>
            <input type="number" id="maxloandays" name="maxloandays" value="0" min="0" 
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-300">
        </div>
        <div>
            <label for="biayapendaftaran" class="block text-sm font-semibold text-gray-700 mb-2">
                <i class="fas fa-money-bill-wave mr-2 text-red-500"></i>
                Biaya Pendaftaran
            </label>
            <input type="number" id="biayapendaftaran" name="biayapendaftaran" value="0" min="0" 
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-300">
        </div>
        <div>
            <label for="biayaperpanjangan" class="block text-sm font-semibold text-gray-700 mb-2">
                <i class="fas fa-credit-card mr-2 text-indigo-500"></i>
                Biaya Perpanjangan
            </label>
            <input type="number" id="biayaperpanjangan" name="biayaperpanjangan" value="0" min="0" 
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-300">
        </div>
        <!-- Field baru -->
        <div>
            <label for="warningloandueday" class="block text-sm font-semibold text-gray-700 mb-2">
                <i class="fas fa-bell mr-2 text-yellow-500"></i>
                Jeda Hari Peringatan Peminjaman utk Kembali
            </label>
            <input type="number" id="warningloandueday" name="warningloandueday" value="0" min="0" 
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-all duration-300">
        </div>
        <div>
            <label for="dayperpanjang" class="block text-sm font-semibold text-gray-700 mb-2">
                <i class="fas fa-calendar-plus mr-2 text-teal-500"></i>
                Maks. Lama Perpanjangan
            </label>
            <input type="number" id="dayperpanjang" name="dayperpanjang" value="0" min="0" 
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent transition-all duration-300">
        </div>
        <div>
            <label for="countperpanjang" class="block text-sm font-semibold text-gray-700 mb-2">
                <i class="fas fa-redo mr-2 text-pink-500"></i>
                Maks. Banyaknya Perpanjang
            </label>
            <input type="number" id="countperpanjang" name="countperpanjang" value="0" min="0" 
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all duration-300">
        </div>
        <div class="md:col-span-2">
            <label class="flex items-center cursor-pointer">
                <input type="checkbox" id="uploaddokumen" name="uploaddokumen" value="1" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                <span class="ml-2 text-sm font-semibold text-gray-700">Upload Dokumen Keanggotaan Online</span>
            </label>
        </div>
    </div>
    
    <!-- Denda Section -->
    <div class="border-t pt-4">
        <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Denda</label>
        <div class="flex space-x-4">
            <label class="inline-flex items-center">
                <input type="radio" name="dendatype" value="Konstan" class="DendaTypeRadio form-radio" checked>
                <span class="ml-2">Konstan</span>
            </label>
            <label class="inline-flex items-center">
                <input type="radio" name="dendatype" value="Berkelipatan" class="DendaTypeRadio form-radio">
                <span class="ml-2">Berkelipatan</span>
            </label>
        </div>
        <div class="mt-4">
            <label for="dendapertenor" class="block text-sm font-medium text-gray-700 mb-2">Jumlah Denda</label>
            <input type="number" id="dendapertenor" name="dendapertenor" min="0" value="0" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        </div>
        <div id="denda-tenor-pack" class="mt-4 field-jenisanggota-dendatenorjumlah-pack hidden">
            <label class="block text-sm font-medium text-gray-700 mb-2">Satuan Tenor Denda</label>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <input type="number" id="dendatenorjumlah" name="dendatenorjumlah" min="1" value="1" class="w-full px-4 py-3 rounded-lg border border-gray-300">
                <select name="dendatenorsatuan" class="w-full px-4 py-3 rounded-lg border border-gray-300">
                    <option value="Hari">Harian</option>
                    <option value="Minggu">Mingguan</option>
                    <option value="Bulan">Bulanan</option>
                    <option value="Tahun">Tahunan</option>
                </select>
            </div>
        </div>
        <div id="denda-multiply" class="mt-4 field-jenisanggota-dendatenormultiply hidden">
            <label for="dendatenormultiply" class="block text-sm font-medium text-gray-700 mb-2">Pengali Tenor Denda</label>
            <input type="number" id="dendatenormultiply" name="dendatenormultiply" min="1" value="1" class="w-full px-4 py-3 rounded-lg border border-gray-300">
            <p class="text-sm text-gray-500 mt-1">Kali</p>
        </div>
    </div>

    <!-- Skorsing Section -->
    <div class="border-t pt-4">
        <div class="flex items-center mb-4">
            <input type="checkbox" id="suspendmember" name="suspendmember" value="1" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
            <label for="suspendmember" class="ml-2 block text-sm font-medium text-gray-700">Aktifkan Skorsing Member</label>
        </div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Skorsing</label>
        <div class="flex space-x-4">
            <label class="inline-flex items-center">
                <input type="radio" name="suspendtype" value="Konstan" class="SuspendTypeRadio form-radio" checked>
                <span class="ml-2">Konstan</span>
            </label>
            <label class="inline-flex items-center">
                <input type="radio" name="suspendtype" value="Berkelipatan" class="SuspendTypeRadio form-radio">
                <span class="ml-2">Berkelipatan</span>
            </label>
        </div>
        <div class="mt-4">
            <label for="daysuspend" class="block text-sm font-medium text-gray-700 mb-2">Lama Skorsing</label>
            <input type="number" id="daysuspend" name="daysuspend" min="0" value="0" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            <p class="text-sm text-gray-500 mt-1">Hari</p>
        </div>
        <div id="suspend-tenor-pack" class="mt-4 field-jenisanggota-suspendtenorjumlah-pack hidden">
            <label class="block text-sm font-medium text-gray-700 mb-2">Satuan Tenor Skorsing</label>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <input type="number" id="suspendtenorjumlah" name="suspendtenorjumlah" min="1" value="1" class="w-full px-4 py-3 rounded-lg border border-gray-300">
                <select name="suspendtenorsatuan" class="w-full px-4 py-3 rounded-lg border border-gray-300">
                    <option value="Hari">Harian</option>
                    <option value="Minggu">Mingguan</option>
                    <option value="Bulan">Bulanan</option>
                    <option value="Tahun">Tahunan</option>
                </select>
            </div>
        </div>
        <div id="suspend-multiply" class="mt-4 field-jenisanggota-suspendtenormultiply hidden">
            <label for="suspendtenormultiply" class="block text-sm font-medium text-gray-700 mb-2">Pengali Tenor Skorsing</label>
            <input type="number" id="suspendtenormultiply" name="suspendtenormultiply" min="1" value="1" class="w-full px-4 py-3 rounded-lg border border-gray-300">
            <p class="text-sm text-gray-500 mt-1">Kali</p>
        </div>
    </div>

    <div class="mt-8">
        <button type="submit" class="bg-green-500 text-white px-8 py-3 rounded-lg font-semibold flex items-center">
            <i class="fas fa-plus mr-2"></i>
            Tambah Jenis Anggota
        </button>
    </div>
</form>
                    </div>
                </div>

                <!-- Tab 3: Default Kategori Koleksi -->
                <div id="tab-default-kategori" class="tab-content hidden">
                    <div class="bg-white rounded-xl card-shadow overflow-hidden">
                        <div class="bg-purple-500 p-6">
                            <h3 class="text-xl font-semibold text-white flex items-center">
                                <i class="fas fa-tags mr-3"></i>
                                Pilih Default Kategori Koleksi
                            </h3>
                        </div>
                        <form id="form-default-kategori" class="p-8">
                            <input type="hidden" id="selected-jenis-id-kategori" name="jenis_id">
                                <div class="mb-4">
        <label class="flex items-center cursor-pointer font-semibold text-purple-700">
            <input type="checkbox" id="select-all-kategori" class="w-4 h-4 text-purple-600 rounded focus:ring-purple-500 mr-2">
            Pilih Semua Kategori
        </label>
    </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <?php
                                $collectionCategories2 = $mysqli->query("SELECT * FROM collectioncategorys");
                                while ($cat = $collectionCategories2->fetch_assoc()):
                                ?>
                                    <div class="bg-gray-50 p-4 rounded-lg border-2 border-transparent hover:border-purple-300 transition-all duration-300">
                                        <label class="flex items-center cursor-pointer">
                                            <input type="checkbox" id="cat-<?php echo $cat['ID']; ?>" name="categories[]" 
                                                   value="<?php echo $cat['ID']; ?>" class="w-4 h-4 text-purple-600 rounded focus:ring-purple-500">
                                            <span class="ml-3 text-sm font-medium text-gray-700"><?php echo htmlspecialchars($cat['Name']); ?></span>
                                        </label>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                            <div class="mt-8">
                                <button type="submit" class="bg-purple-500 text-white px-8 py-3 rounded-lg font-semibold flex items-center">
                                    <i class="fas fa-save mr-2"></i>
                                    Simpan Default Kategori
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Tab 4: Default Lokasi Perpustakaan -->
                <div id="tab-default-lokasi" class="tab-content hidden">
                    <div class="bg-white rounded-xl card-shadow overflow-hidden">
                        <div class="bg-teal-500 p-6">
                            <h3 class="text-xl font-semibold text-white flex items-center">
                                <i class="fas fa-map-marker-alt mr-3"></i>
                                Pilih Default Lokasi Perpustakaan
                            </h3>
                        </div>
                        <form id="form-default-lokasi" class="p-8">
                            <input type="hidden" id="selected-jenis-id-lokasi" name="jenis_id">
                                <div class="mb-4">
        <label class="flex items-center cursor-pointer font-semibold text-teal-700">
            <input type="checkbox" id="select-all-lokasi" class="w-4 h-4 text-teal-600 rounded focus:ring-teal-500 mr-2">
            Pilih Semua Lokasi
        </label>
    </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <?php
                                $locationLibraries2 = $mysqli->query("SELECT * FROM location_library");
                                while ($loc = $locationLibraries2->fetch_assoc()):
                                ?>
                                    <div class="bg-gray-50 p-4 rounded-lg border-2 border-transparent hover:border-teal-300 transition-all duration-300">
                                        <label class="flex items-center cursor-pointer">
                                            <input type="checkbox" id="loc-<?php echo $loc['ID']; ?>" name="locations[]" 
                                                   value="<?php echo $loc['ID']; ?>" class="w-4 h-4 text-teal-600 rounded focus:ring-teal-500">
                                            <span class="ml-3 text-sm font-medium text-gray-700"><?php echo htmlspecialchars($loc['Name']); ?></span>
                                        </label>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                            <div class="mt-8">
                                <button type="submit" class="bg-teal-500 text-white px-8 py-3 rounded-lg font-semibold flex items-center">
                                    <i class="fas fa-save mr-2"></i>
                                    Simpan Default Lokasi
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Tab 5: Sinkronisasi Member -->
                <div id="tab-sync-member" class="tab-content hidden">
                    <div class="bg-white rounded-xl card-shadow overflow-hidden">
                        <div class="bg-gradient-to-r from-orange-500 to-red-600 p-6">
                            <h3 class="text-xl font-semibold text-white flex items-center">
                                <i class="fas fa-sync mr-3"></i>
                                Sinkronisasi Member Default
                            </h3>
                        </div>
                        <div class="p-8">
                            <div id="sync-member-list"></div>
                            <button id="sync-member-btn" class="btn-gradient text-white px-8 py-3 rounded-lg font-semibold mt-6 hidden flex items-center">
                                <i class="fas fa-sync mr-2"></i>
                                Sinkronkan Semua Member
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        var selectedJenisId = null;

        // Show notification
        function showNotification(message, isSuccess) {
            var notification = document.getElementById('notification');
            notification.innerHTML = '<i class="fas fa-' + (isSuccess ? 'check-circle' : 'exclamation-circle') + ' mr-2"></i>' + message;
            notification.className = 'fixed top-4 right-4 p-4 rounded-lg shadow-lg text-white z-50 notification-slide';
            notification.className += isSuccess ? ' bg-green-500' : ' bg-red-500';
            notification.classList.remove('hidden');
            setTimeout(function() { 
                notification.classList.add('hidden'); 
            }, 3000);
        }

        // Fetch and render jenis anggota table
        function loadJenisTable() {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', '../jenis_anggota.api.php?action=get_jenis', true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            var data = JSON.parse(xhr.responseText);
            var tbody = document.getElementById('jenis-table-body');
            tbody.innerHTML = '';
            if (data.success) {
                for (var i = 0; i < data.data.length; i++) {
                    var row = data.data[i];
                    tbody.innerHTML += `
                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">${row.id}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 font-semibold">${row.jenisanggota}</td>
                            <td class="px-4 py-3 text-sm text-gray-500">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <i class="fas fa-calendar-alt mr-1"></i>${row.MasaBerlakuAnggota} hari
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-book mr-1"></i>${row.MaxPinjamKoleksi}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    <i class="fas fa-clock mr-1"></i>${row.MaxLoanDays}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">${row.WarningLoanDueDay}</td>
                            <td class="px-4 py-3 text-sm text-gray-500">${row.DayPerpanjang}</td>
                            <td class="px-4 py-3 text-sm text-gray-500">${row.CountPerpanjang}</td>
                            <td class="px-4 py-3 text-sm text-gray-500">${row.DendaType}</td>
                            <td class="px-4 py-3 text-sm text-gray-500">${row.SuspendType}</td>
                            <td class="px-4 py-3 text-sm font-medium space-x-2">
                                <button onclick="selectJenis(${row.id}, 'kategori')" class="inline-flex items-center px-3 py-2 text-xs font-medium rounded-md text-white bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 transition-all duration-300">
                                    <i class="fas fa-tags mr-1"></i>Kategori
                                </button>
                                <button onclick="selectJenis(${row.id}, 'lokasi')" class="inline-flex items-center px-3 py-2 text-xs font-medium rounded-md text-white bg-gradient-to-r from-teal-500 to-teal-600 hover:from-teal-600 hover:to-teal-700 transition-all duration-300">
                                    <i class="fas fa-map-marker-alt mr-1"></i>Lokasi
                                </button>
                            </td>
                        </tr>`;
                }
            } else {
                showNotification(data.message, false);
            }
        }
    };
    xhr.send();
}

// Toggle logic for DendaType
document.querySelectorAll('.DendaTypeRadio').forEach(radio => {
    radio.addEventListener('change', function() {
        if (this.value === 'Konstan') {
            document.getElementById('denda-tenor-pack').classList.add('hidden');
            document.getElementById('denda-multiply').classList.add('hidden');
            document.getElementById('dendatenorjumlah').min = 0;
            document.getElementById('dendatenorjumlah').value = 0;
            document.getElementById('dendatenormultiply').min = 0;
            document.getElementById('dendatenormultiply').value = 0;
        } else {
            document.getElementById('denda-tenor-pack').classList.remove('hidden');
            document.getElementById('denda-multiply').classList.remove('hidden');
            document.getElementById('dendatenorjumlah').min = 1;
            document.getElementById('dendatenorjumlah').value = 1;
            document.getElementById('dendatenormultiply').min = 1;
            document.getElementById('dendatenormultiply').value = 1;
        }
    });
});

// Toggle logic for SuspendType
document.querySelectorAll('.SuspendTypeRadio').forEach(radio => {
    radio.addEventListener('change', function() {
        if (this.value === 'Konstan') {
            document.getElementById('suspend-tenor-pack').classList.add('hidden');
            document.getElementById('suspend-multiply').classList.add('hidden');
            document.getElementById('suspendtenorjumlah').min = 0;
            document.getElementById('suspendtenorjumlah').value = 0;
            document.getElementById('suspendtenormultiply').min = 0;
            document.getElementById('suspendtenormultiply').value = 0;
        } else {
            document.getElementById('suspend-tenor-pack').classList.remove('hidden');
            document.getElementById('suspend-multiply').classList.remove('hidden');
            document.getElementById('suspendtenorjumlah').min = 1;
            document.getElementById('suspendtenorjumlah').value = 1;
            document.getElementById('suspendtenormultiply').min = 1;
            document.getElementById('suspendtenormultiply').value = 1;
        }
    });
});

// Initialize toggle state
if (document.querySelector('.DendaTypeRadio[value="Konstan"]').checked) {
    document.getElementById('denda-tenor-pack').classList.add('hidden');
    document.getElementById('denda-multiply').classList.add('hidden');
    document.getElementById('dendatenorjumlah').min = 0;
    document.getElementById('dendatenorjumlah').value = 0;
    document.getElementById('dendatenormultiply').min = 0;
    document.getElementById('dendatenormultiply').value = 0;
}
if (document.querySelector('.SuspendTypeRadio[value="Konstan"]').checked) {
    document.getElementById('suspend-tenor-pack').classList.add('hidden');
    document.getElementById('suspend-multiply').classList.add('hidden');
    document.getElementById('suspendtenorjumlah').min = 0;
    document.getElementById('suspendtenorjumlah').value = 0;
    document.getElementById('suspendtenormultiply').min = 0;
    document.getElementById('suspendtenormultiply').value = 0;
}

        // Tab switching
        var tabs = document.getElementById('tab-nav').getElementsByTagName('a');
        for (var i = 0; i < tabs.length; i++) {
            tabs[i].onclick = function(e) {
                e.preventDefault();
                if (this.className.indexOf('disabled-tab') !== -1) return;
        
                // Remove active class from all tabs
                for (var j = 0; j < tabs.length; j++) {
                    tabs[j].className = tabs[j].className.replace(' tab-active', '');
                }
                // Add active class to clicked tab
                this.className += ' tab-active';
        
                // Hide all tab contents
                var contents = document.getElementsByClassName('tab-content');
                for (var j = 0; j < contents.length; j++) {
                    contents[j].classList.add('hidden');
                }
                // Show selected tab content
                document.getElementById(this.href.split('#')[1]).classList.remove('hidden');
        
                // Reset URL hash
                history.replaceState(null, '', location.pathname);
        
                // Disable default tabs if not from action button
                if (
                    this.id !== 'tab-default-kategori-link' &&
                    this.id !== 'tab-default-lokasi-link'
                ) {
                    document.getElementById('tab-default-kategori-link').classList.add('disabled-tab');
                    document.getElementById('tab-default-lokasi-link').classList.add('disabled-tab');
                    selectedJenisId = null;
                }
            };
        }

        // Default active tab
        tabs[0].click();
        loadJenisTable();

        // Select Jenis and enable defaults
        function selectJenis(id, tab) {
            selectedJenisId = id;
            document.getElementById('selected-jenis-id-kategori').value = id;
            document.getElementById('selected-jenis-id-lokasi').value = id;
        
            // Enable only selected default tab, disable the other
            if (tab === 'kategori') {
                document.getElementById('tab-default-kategori-link').className = document.getElementById('tab-default-kategori-link').className.replace(' disabled-tab', '');
                document.getElementById('tab-default-lokasi-link').classList.add('disabled-tab');
            } else {
                document.getElementById('tab-default-lokasi-link').className = document.getElementById('tab-default-lokasi-link').className.replace(' disabled-tab', '');
                document.getElementById('tab-default-kategori-link').classList.add('disabled-tab');
            }
        
            // Hide warning
            document.getElementById('warning-message').className += ' hidden';
        
            // Load defaults
            fetchDefaults(id);
        
            // Switch to appropriate tab
            var tabId = (tab === 'kategori') ? 'tab-default-kategori' : 'tab-default-lokasi';
            // Deactivate all tabs
            for (var j = 0; j < tabs.length; j++) {
                tabs[j].className = tabs[j].className.replace(' tab-active', '');
            }
            // Activate selected tab
            var link = document.getElementById(tab === 'kategori' ? 'tab-default-kategori-link' : 'tab-default-lokasi-link');
            link.className += ' tab-active';
        
            // Hide all tab contents
            var contents = document.getElementsByClassName('tab-content');
            for (var j = 0; j < contents.length; j++) {
                contents[j].classList.add('hidden');
            }
            // Show selected tab content
            document.getElementById(tabId).classList.remove('hidden');
        }

        // Warning on accessing default tabs without selection
        var defaultTabs = [document.getElementById('tab-default-kategori-link'), document.getElementById('tab-default-lokasi-link')];
        for (var i = 0; i < defaultTabs.length; i++) {
            defaultTabs[i].onclick = function(e) {
                if (selectedJenisId === null) {
                    e.preventDefault();
                    document.getElementById('warning-message').className = document.getElementById('warning-message').className.replace(' hidden', '');
                    tabs[0].click();
                }
            };
        }

        // Fetch defaults
        function fetchDefaults(id) {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', '../jenis_anggota.api.php?action=get_defaults&jenis_id=' + id, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    var data = JSON.parse(xhr.responseText);
                    if (data.success) {
                        // Reset checkboxes
                        var catCheckboxes = document.getElementById('form-default-kategori').getElementsByTagName('input');
                        for (var i = 0; i < catCheckboxes.length; i++) {
                            if (catCheckboxes[i].type == 'checkbox') catCheckboxes[i].checked = false;
                        }
                        var locCheckboxes = document.getElementById('form-default-lokasi').getElementsByTagName('input');
                        for (var i = 0; i < locCheckboxes.length; i++) {
                            if (locCheckboxes[i].type == 'checkbox') locCheckboxes[i].checked = false;
                        }

                        // Check selected categories
                        for (var i = 0; i < data.categories.length; i++) {
                            var cb = document.getElementById('cat-' + data.categories[i]);
                            if (cb) cb.checked = true;
                        }

                        // Check selected locations
                        for (var i = 0; i < data.locations.length; i++) {
                            var cb = document.getElementById('loc-' + data.locations[i]);
                            if (cb) cb.checked = true;
                        }
                    } else {
                        showNotification(data.message, false);
                    }
                }
            };
            xhr.send();
        }

        // Form submission for adding new jenis anggota
        document.getElementById('form-tambah').onsubmit = function(e) {
    e.preventDefault();
    var formData = {
        jenisanggota: document.getElementById('jenisanggota').value,
        masaberlaku: document.getElementById('masaberlaku').value,
        maxpinjam: document.getElementById('maxpinjam').value,
        maxloandays: document.getElementById('maxloandays').value,
        biayapendaftaran: document.getElementById('biayapendaftaran').value,
        biayaperpanjangan: document.getElementById('biayaperpanjangan').value,
        warningloandueday: document.getElementById('warningloandueday').value,
        dayperpanjang: document.getElementById('dayperpanjang').value,
        countperpanjang: document.getElementById('countperpanjang').value,
        uploaddokumen: document.getElementById('uploaddokumen').checked ? 1 : 0,
        suspendmember: document.getElementById('suspendmember').checked ? 1 : 0,
        dendatype: document.querySelector('input[name="dendatype"]:checked').value,
        dendapertenor: document.getElementById('dendapertenor').value,
        dendatenorjumlah: document.getElementById('dendatenorjumlah').value,
        dendatenorsatuan: document.querySelector('select[name="dendatenorsatuan"]').value,
        dendatenormultiply: document.getElementById('dendatenormultiply').value,
        suspendtype: document.querySelector('input[name="suspendtype"]:checked').value,
        daysuspend: document.getElementById('daysuspend').value,
        suspendtenorjumlah: document.getElementById('suspendtenorjumlah').value,
        suspendtenorsatuan: document.querySelector('select[name="suspendtenorsatuan"]').value,
        suspendtenormultiply: document.getElementById('suspendtenormultiply').value
    };

    if (!formData.jenisanggota) {
        showNotification('Jenis anggota harus diisi', false);
        return;
    }

    var xhr = new XMLHttpRequest();
    xhr.open('POST', '../jenis_anggota.api.php?action=add', true);
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            var data = JSON.parse(xhr.responseText);
            showNotification(data.message, data.success);
            if (data.success) {
                document.getElementById('form-tambah').reset();
                // Reset to default values
                document.getElementById('masaberlaku').value = '365';
                document.getElementById('maxpinjam').value = '1000';
                document.getElementById('maxloandays').value = '0';
                document.getElementById('biayapendaftaran').value = '0';
                document.getElementById('biayaperpanjangan').value = '0';
                document.getElementById('warningloandueday').value = '0';
                document.getElementById('dayperpanjang').value = '0';
                document.getElementById('countperpanjang').value = '0';
                document.getElementById('dendapertenor').value = '0';
                document.getElementById('dendatenorjumlah').value = '0';
                document.getElementById('dendatenormultiply').value = '0';
                document.getElementById('daysuspend').value = '0';
                document.getElementById('suspendtenorjumlah').value = '0';
                document.getElementById('suspendtenormultiply').value = '0';
                document.querySelector('.DendaTypeRadio[value="Konstan"]').checked = true;
                document.querySelector('.SuspendTypeRadio[value="Konstan"]').checked = true;
                document.getElementById('denda-tenor-pack').classList.add('hidden');
                document.getElementById('denda-multiply').classList.add('hidden');
                document.getElementById('suspend-tenor-pack').classList.add('hidden');
                document.getElementById('suspend-multiply').classList.add('hidden');
                loadJenisTable();
                // Pindah ke tab daftar jenis anggota setelah berhasil
                setTimeout(function() {
                    tabs[0].click();
                }, 500);
            }
        }
    };
    xhr.send(JSON.stringify(formData));
};

        // Form submission for saving kategori defaults
        document.getElementById('form-default-kategori').onsubmit = function(e) {
            e.preventDefault();
            var categories = [];
            var checkboxes = document.getElementById('form-default-kategori').getElementsByTagName('input');
            for (var i = 0; i < checkboxes.length; i++) {
                if (
                    checkboxes[i].type == 'checkbox' &&
                    checkboxes[i].checked &&
                    checkboxes[i].id.indexOf('cat-') === 0 // hanya checkbox kategori asli
                ) {
                    categories.push(checkboxes[i].value);
                }
            }
            var formData = {
                jenis_id: selectedJenisId,
                categories: categories
            };
        
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '../jenis_anggota.api.php?action=save_kategori_defaults', true);
            xhr.setRequestHeader('Content-Type', 'application/json');
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    var data = JSON.parse(xhr.responseText);
                    showNotification(data.message, data.success);
                    if (data.success) {
                        tabs[0].click(); // Pindah ke tab daftar jenis anggota
                    }
                    loadSyncMemberTab();
                }
            };
            xhr.send(JSON.stringify(formData));
        };
        
        document.getElementById('form-default-lokasi').onsubmit = function(e) {
            e.preventDefault();
            var locations = [];
            var checkboxes = document.getElementById('form-default-lokasi').getElementsByTagName('input');
            for (var i = 0; i < checkboxes.length; i++) {
                if (
                    checkboxes[i].type == 'checkbox' &&
                    checkboxes[i].checked &&
                    checkboxes[i].id.indexOf('loc-') === 0 // hanya checkbox lokasi asli
                ) {
                    locations.push(checkboxes[i].value);
                }
            }
            var formData = {
                jenis_id: selectedJenisId,
                locations: locations
            };
        
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '../jenis_anggota.api.php?action=save_lokasi_defaults', true);
            xhr.setRequestHeader('Content-Type', 'application/json');
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    var data = JSON.parse(xhr.responseText);
                    showNotification(data.message, data.success);
                    if (data.success) {
                        // Pindah ke tab daftar jenis anggota
                        tabs[0].click();
                    }
                }
            };
            xhr.send(JSON.stringify(formData));
        };
        
        // Select All Kategori
        document.getElementById('select-all-kategori').addEventListener('change', function() {
            var checked = this.checked;
            var checkboxes = document.getElementById('form-default-kategori').querySelectorAll('input[type="checkbox"][id^="cat-"]');
            checkboxes.forEach(function(cb) {
                cb.checked = checked;
            });
        });
        
        // Select All Lokasi
        document.getElementById('select-all-lokasi').addEventListener('change', function() {
            var checked = this.checked;
            var checkboxes = document.getElementById('form-default-lokasi').querySelectorAll('input[type="checkbox"][id^="loc-"]');
            checkboxes.forEach(function(cb) {
                cb.checked = checked;
            });
        });
        
        // Sinkronkan status select all jika user centang/uncentang manual
        function syncSelectAllKategori() {
            var all = document.getElementById('form-default-kategori').querySelectorAll('input[type="checkbox"][id^="cat-"]');
            var allChecked = Array.from(all).every(cb => cb.checked);
            document.getElementById('select-all-kategori').checked = allChecked;
        }
        function syncSelectAllLokasi() {
            var all = document.getElementById('form-default-lokasi').querySelectorAll('input[type="checkbox"][id^="loc-"]');
            var allChecked = Array.from(all).every(cb => cb.checked);
            document.getElementById('select-all-lokasi').checked = allChecked;
        }
        document.getElementById('form-default-kategori').querySelectorAll('input[type="checkbox"][id^="cat-"]').forEach(function(cb) {
            cb.addEventListener('change', syncSelectAllKategori);
        });
        document.getElementById('form-default-lokasi').querySelectorAll('input[type="checkbox"][id^="loc-"]').forEach(function(cb) {
            cb.addEventListener('change', syncSelectAllLokasi);
        });
        
        // Pastikan status select all sinkron saat fetchDefaults
        var originalFetchDefaults = fetchDefaults;
        fetchDefaults = function(id) {
            originalFetchDefaults(id);
            setTimeout(function() {
                syncSelectAllKategori();
                syncSelectAllLokasi();
            }, 300);
        };
        // ...existing code...

        function loadSyncMemberTab() {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', '../jenis_anggota.api.php?action=get_members_need_default_sync', true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    var data = JSON.parse(xhr.responseText);
                    var listDiv = document.getElementById('sync-member-list');
                    var btn = document.getElementById('sync-member-btn');
                    listDiv.innerHTML = '';
                    if (data.success && data.members.length > 0) {
                        listDiv.innerHTML = '<div class="overflow-x-auto">' +
                            '<table class="min-w-full bg-white border border-gray-200 rounded-lg shadow">' +
                            '<thead class="bg-gray-50">' +
                            '<tr>' +
                            '<th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Anggota</th>' +
                            '<th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>' +
                            '<th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Anggota</th>' +
                            '<th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Kategori</th>' +
                            '<th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Lokasi</th>' +
                            '</tr>' +
                            '</thead>' +
                            '<tbody class="bg-white divide-y divide-gray-200">' +
                            data.members.map(function(m) {
                                return '<tr class="hover:bg-gray-50">' +
                                    '<td class="px-4 py-3 text-sm font-medium text-gray-900">' + m.MemberNo + '</td>' +
                                    '<td class="px-4 py-3 text-sm text-gray-900">' + m.Fullname + '</td>' +
                                    '<td class="px-4 py-3 text-sm text-gray-500">' +
                                        '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">' +
                                            m.JenisAnggota +
                                        '</span>' +
                                    '</td>' +
                                    '<td class="px-4 py-3 text-sm">' +
                                        (m.missing_default_categories.length === 0 
                                            ? '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800"><i class="fas fa-check-circle mr-1"></i>Sudah di-set</span>'
                                            : '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800"><i class="fas fa-times-circle mr-1"></i>Belum di-set</span>') +
                                    '</td>' +
                                    '<td class="px-4 py-3 text-sm">' +
                                        (m.missing_default_locations.length === 0 
                                            ? '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800"><i class="fas fa-check-circle mr-1"></i>Sudah di-set</span>'
                                            : '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800"><i class="fas fa-times-circle mr-1"></i>Belum di-set</span>') +
                                    '</td>' +
                                '</tr>';
                            }).join('') +
                            '</tbody></table></div>';
                        btn.classList.remove('hidden');
                    } else {
                        listDiv.innerHTML = '<div class="text-center py-12">' +
                            '<div class="mx-auto w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mb-4">' +
                                '<i class="fas fa-check-circle text-3xl text-green-600"></i>' +
                            '</div>' +
                            '<h3 class="text-lg font-semibold text-gray-900 mb-2">Semua Member Sudah Tersinkronisasi</h3>' +
                            '<p class="text-gray-600">Semua member sudah memiliki default koleksi dan lokasi sesuai jenis anggota.</p>' +
                        '</div>';
                        btn.classList.add('hidden');
                    }
                }
            };
            xhr.send();
        }
        
        document.getElementById('tab-sync-member-link').onclick = function(e) {
            e.preventDefault();
            for (var j = 0; j < tabs.length; j++) {
                tabs[j].className = tabs[j].className.replace(' tab-active', '');
            }
            this.className += ' tab-active';
            var contents = document.getElementsByClassName('tab-content');
            for (var j = 0; j < contents.length; j++) {
                contents[j].classList.add('hidden');
            }
            document.getElementById('tab-sync-member').classList.remove('hidden');
            loadSyncMemberTab();
        };
        
        document.addEventListener('DOMContentLoaded', function() {
            var btn = document.getElementById('sync-member-btn');
            if (btn) {
                btn.onclick = function() {
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';
                    fetch('../jenis_anggota.api.php?action=sync_all_members_with_default')
                        .then(res => res.json())
                        .then(data => {
                            showNotification(data.message, data.success);
                            btn.disabled = false;
                            btn.innerHTML = '<i class="fas fa-sync mr-2"></i>Sinkronkan Semua Member';
                            if (data.success) {
                                setTimeout(function() {
                                    tabs[0].click(); // Kembali ke tab daftar jenis anggota
                                }, 500);
                            } else {
                                setTimeout(function() {
                                    location.reload();
                                }, 2000);
                            }
                        })
                        .catch(() => {
                            showNotification('Gagal sinkronisasi', false);
                            btn.disabled = false;
                            btn.innerHTML = '<i class="fas fa-sync mr-2"></i>Sinkronkan Semua Member';
                        });
                };
            }
        });
    </script>
</body>
</html>