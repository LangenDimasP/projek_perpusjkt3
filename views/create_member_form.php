<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Database configuration
$db_host = '127.0.0.1';
$db_user = 'root';
$db_pass = '';
$db_name = 'inlislite_v3';
$db_port = 3309;

// Enable strict error reporting for mysqli
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Create database connection
$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name, $db_port);

// Check connection
if ($mysqli->connect_error) {
    die('<h1>Koneksi database gagal: ' . $mysqli->connect_error . '</h1>');
}

// Check if user is logged in
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

try {
    // Fetch dropdown data with error checking
    $identity_types = $mysqli->query("SELECT id, Nama FROM master_jenis_identitas ORDER BY Nama ASC");
    if (!$identity_types) {
        throw new Exception("Error loading identity types: " . $mysqli->error);
    }
    
    $sexes = $mysqli->query("SELECT ID, Name FROM jenis_kelamin ORDER BY Name ASC");
    if (!$sexes) {
        throw new Exception("Error loading sexes: " . $mysqli->error);
    }
    
    $educations = $mysqli->query("SELECT id, Nama FROM master_pendidikan ORDER BY Nama ASC");
    if (!$educations) {
        throw new Exception("Error loading educations: " . $mysqli->error);
    }
    
    $jobs = $mysqli->query("SELECT id, Pekerjaan FROM master_pekerjaan ORDER BY Pekerjaan ASC");
    if (!$jobs) {
        throw new Exception("Error loading jobs: " . $mysqli->error);
    }
    
    $member_types = $mysqli->query("SELECT id, jenisanggota FROM jenis_anggota ORDER BY jenisanggota ASC");
    if (!$member_types) {
        throw new Exception("Error loading member types: " . $mysqli->error);
    }
    
    $marital_statuses = $mysqli->query("SELECT id, Nama FROM master_status_perkawinan ORDER BY Nama ASC");
    if (!$marital_statuses) {
        throw new Exception("Error loading marital statuses: " . $mysqli->error);
    }
    
    $member_statuses = $mysqli->query("SELECT id, Nama FROM status_anggota ORDER BY Nama ASC");
    if (!$member_statuses) {
        throw new Exception("Error loading member statuses: " . $mysqli->error);
    }
    
    // Fetch member list data with joins
    $members_query = "
        SELECT 
            m.MemberNo,
            m.Fullname,
            m.DateOfBirth,
            m.Address,
            m.Phone,
            ji.Nama AS JenisIdentitas,
            jk.Name AS JenisKelamin,
            ja.jenisanggota AS JenisAnggota,
            sa.Nama AS Status
        FROM members m
        LEFT JOIN master_jenis_identitas ji ON m.IdentityType_id = ji.id
        LEFT JOIN jenis_kelamin jk ON m.Sex_id = jk.ID
        LEFT JOIN jenis_anggota ja ON m.JenisAnggota_id = ja.id
        LEFT JOIN status_anggota sa ON m.StatusAnggota_id = sa.id
        ORDER BY m.CreateDate DESC
    ";
    $members = $mysqli->query($members_query);
    if (!$members) {
        throw new Exception("Error loading members: " . $mysqli->error);
    }
    
} catch (Exception $e) {
    die("Database Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Anggota</title>
    <link href="../dist/output.css" rel="stylesheet">
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="../assets/images/logo.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../assets/images/logo.png">
    
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
</head>
<body class="bg-gray-50 font-sans">
    <div class="flex min-h-screen">
        <?php include '../partials/sidebar.php'; ?>

        <!-- Main Content -->
        <main class="ml-64 flex-1 p-6">
            <div class="max-w-7xl mx-auto">
                <!-- Header Section -->
                <div class="mb-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">Kelola Anggota</h1>
                            <p class="text-gray-600 mt-1">Tambah, lihat, dan kelola data anggota perpustakaan</p>
                        </div>
                        <div class="flex items-center space-x-2 text-sm text-gray-500">
                            <i data-feather="users" class="w-4 h-4"></i>
                            <span><?php echo $members->num_rows; ?> Total Anggota</span>
                        </div>
                    </div>
                </div>

                <!-- Toast Success -->
                <div id="toast-success" class="hidden fixed top-6 right-6 z-50 max-w-sm w-full bg-white border-l-4 border-green-400 rounded-lg shadow-lg transform transition-all duration-300 ease-out translate-x-full">
                    <div class="flex items-start p-4">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                                <i data-feather="check" class="w-5 h-5 text-green-600"></i>
                            </div>
                        </div>
                        <div class="ml-3 flex-1">
                            <h4 class="text-sm font-semibold text-gray-800">Berhasil!</h4>
                            <p class="text-sm text-gray-600 mt-1" id="toast-success-message">Member berhasil ditambahkan!</p>
                        </div>
                        <button onclick="hideToast()" class="ml-3 flex-shrink-0 text-gray-400 hover:text-gray-600">
                            <i data-feather="x" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <!-- Tabs Navigation -->
                    <div class="border-b border-gray-200 bg-gray-50">
                        <nav class="flex justify-center">
                            <button onclick="openTab(event, 'add-member')" class="tab-button flex items-center px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-600 hover:text-gray-800 hover:border-gray-300 transition-all duration-200" id="defaultOpen">
                                <i data-feather="user-plus" class="w-4 h-4 mr-2"></i>
                                Tambah Anggota Baru
                            </button>
                            <button onclick="openTab(event, 'member-list')" class="tab-button flex items-center px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-600 hover:text-gray-800 hover:border-gray-300 transition-all duration-200">
                                <i data-feather="list" class="w-4 h-4 mr-2"></i>
                                Daftar Anggota
                            </button>
                        </nav>
                    </div>
                    
                    <!-- Tab Content: Add Member -->
                    <div id="add-member" class="tab-content p-8">
                        <form id="memberForm" action="../create_member_api.php" method="POST" class="space-y-8">
                            <!-- Required Information Section -->
                            <div class="bg-blue-50 rounded-lg p-6">
                                <div class="flex items-center mb-6">
                                    <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center mr-3">
                                        <i data-feather="info" class="w-4 h-4 text-white"></i>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900">Informasi Wajib</h3>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                                    <div class="form-group">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Jenis Identitas <span class="text-red-500">*</span>
                                        </label>
                                        <select name="IdentityType_id" class="form-select w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200" required>
                                            <option value="">Pilih Jenis Identitas</option>
                                            <?php while ($type = $identity_types->fetch_assoc()) { ?>
                                                <option value="<?php echo $type['id']; ?>"><?php echo htmlspecialchars($type['Nama']); ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Nomor Identitas <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" name="IdentityNo" class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200" placeholder="Masukkan nomor identitas" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Nama Lengkap <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" name="Fullname" class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200" placeholder="Masukkan nama lengkap" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Tanggal Lahir <span class="text-red-500">*</span>
                                        </label>
                                        <input type="date" name="DateOfBirth" class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Jenis Kelamin <span class="text-red-500">*</span>
                                        </label>
                                        <select name="Sex_id" class="form-select w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200" required>
                                            <option value="">Pilih Jenis Kelamin</option>
                                            <?php while ($sex = $sexes->fetch_assoc()) { ?>
                                                <option value="<?php echo $sex['ID']; ?>"><?php echo htmlspecialchars($sex['Name']); ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Jenis Anggota <span class="text-red-500">*</span>
                                        </label>
                                        <select name="JenisAnggota_id" class="form-select w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200" required>
                                            <option value="">Pilih Jenis Anggota</option>
                                            <?php while ($type = $member_types->fetch_assoc()) { ?>
                                                <option value="<?php echo $type['id']; ?>"><?php echo htmlspecialchars($type['jenisanggota']); ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Status Anggota <span class="text-red-500">*</span>
                                        </label>
                                        <select name="StatusAnggota_id" class="form-select w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200" required>
                                            <option value="">Pilih Status Anggota</option>
                                            <?php while ($status = $member_statuses->fetch_assoc()) { ?>
                                                <option value="<?php echo $status['id']; ?>"><?php echo htmlspecialchars($status['Nama']); ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Additional Information Section -->
                            <div class="bg-gray-50 rounded-lg p-6">
                                <div class="flex items-center mb-6">
                                    <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center mr-3">
                                        <i data-feather="plus-circle" class="w-4 h-4 text-white"></i>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900">Informasi Tambahan</h3>
                                    <span class="ml-2 text-sm text-gray-500">(Opsional)</span>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                                    <div class="form-group">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Tempat Lahir</label>
                                        <input type="text" name="PlaceOfBirth" class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200" placeholder="Masukkan tempat lahir">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Pendidikan</label>
                                        <select name="EducationLevel_id" class="form-select w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                                            <option value="">Pilih Pendidikan</option>
                                            <?php while ($edu = $educations->fetch_assoc()) { ?>
                                                <option value="<?php echo $edu['id']; ?>"><?php echo htmlspecialchars($edu['Nama']); ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Pekerjaan</label>
                                        <select name="Job_id" class="form-select w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                                            <option value="">Pilih Pekerjaan</option>
                                            <?php while ($job = $jobs->fetch_assoc()) { ?>
                                                <option value="<?php echo $job['id']; ?>"><?php echo htmlspecialchars($job['Pekerjaan']); ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Status Perkawinan</label>
                                        <select name="MaritalStatus_id" class="form-select w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                                            <option value="">Pilih Status Perkawinan</option>
                                            <?php while ($status = $marital_statuses->fetch_assoc()) { ?>
                                                <option value="<?php echo $status['id']; ?>"><?php echo htmlspecialchars($status['Nama']); ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Telepon</label>
                                        <input type="text" name="Phone" class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200" placeholder="Masukkan nomor telepon">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                        <input type="email" name="Email" class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200" placeholder="Masukkan alamat email">
                                    </div>
                                </div>
                                
                                <div class="mt-6">
                                    <div class="form-group">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
                                        <textarea name="Address" rows="4" class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 resize-none" placeholder="Masukkan alamat lengkap"></textarea>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                                <button type="submit" class="px-8 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 font-medium shadow-sm">
                                    <i data-feather="save" class="w-4 h-4 inline mr-2"></i>
                                    Simpan Data
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Tab Content: Member List -->
                    <div id="member-list" class="tab-content hidden">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-lg font-semibold text-gray-900">Daftar Anggota</h3>
                                <div class="flex items-center space-x-4">
                                    <div class="relative">
                                        <input 
                                            type="text" 
                                            placeholder="Cari anggota..." 
                                            class="pl-6 pr-10 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-full"
                                            >
                                            <i data-feather="search" class="w-4 h-4 text-gray-400 absolute top-1/2 right-3 -translate-y-1/2"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="overflow-hidden border border-gray-200 rounded-lg">
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">No. Anggota</th>
                                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">Nama Lengkap</th>
                                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">Tanggal Lahir</th>
                                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">Telepon</th>
                                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">Jenis Kelamin</th>
                                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">Jenis Anggota</th>
                                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            <?php if ($members->num_rows > 0) { ?>
                                                <?php while ($member = $members->fetch_assoc()) { ?>
                                                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($member['MemberNo']); ?></div>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($member['Fullname']); ?></div>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <div class="text-sm text-gray-600"><?php echo date('d/m/Y', strtotime($member['DateOfBirth'])); ?></div>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <div class="text-sm text-gray-600"><?php echo htmlspecialchars($member['Phone']) ?: '-'; ?></div>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <div class="text-sm text-gray-600"><?php echo htmlspecialchars($member['JenisKelamin']); ?></div>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                                <?php echo htmlspecialchars($member['JenisAnggota']); ?>
                                                            </span>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                                <?php echo htmlspecialchars($member['Status']); ?>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            <?php } else { ?>
                                                <tr>
                                                    <td colspan="8" class="px-6 py-12 text-center">
                                                        <div class="flex flex-col items-center">
                                                            <i data-feather="users" class="w-12 h-12 text-gray-400 mb-4"></i>
                                                            <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada anggota</h3>
                                                            <p class="text-gray-500">Tambahkan anggota baru untuk memulai</p>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Initialize Feather Icons
        feather.replace();
        
        function openTab(evt, tabName) {
            var i, tabcontent, tabbuttons;
            tabcontent = document.getElementsByClassName("tab-content");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].classList.add("hidden");
            }
            tabbuttons = document.getElementsByClassName("tab-button");
            for (i = 0; i < tabbuttons.length; i++) {
                tabbuttons[i].classList.remove("border-blue-600", "text-blue-600", "bg-blue-50");
                tabbuttons[i].classList.add("border-transparent", "text-gray-600");
            }
            document.getElementById(tabName).classList.remove("hidden");
            evt.currentTarget.classList.add("border-blue-600", "text-blue-600", "bg-blue-50");
            evt.currentTarget.classList.remove("border-transparent", "text-gray-600");
        }

        // Open the default tab
        document.getElementById("defaultOpen").click();

        function showToast(message) {
            const toast = document.getElementById('toast-success');
            const messageEl = document.getElementById('toast-success-message');
            messageEl.textContent = message;
            
            toast.classList.remove('hidden', 'translate-x-full');
            toast.classList.add('translate-x-0');
            
            setTimeout(() => {
                hideToast();
            }, 3000);
        }

        function hideToast() {
            const toast = document.getElementById('toast-success');
            toast.classList.add('translate-x-full');
            setTimeout(() => {
                toast.classList.add('hidden');
                toast.classList.remove('translate-x-0');
            }, 300);
        }

        // Form submission handler
        document.getElementById('memberForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i data-feather="loader" class="w-4 h-4 inline mr-2 animate-spin"></i>Menyimpan...';
            feather.replace();
            
            const formData = new FormData(this);
            
            fetch('../create_member_api.php', {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.message === 'Member berhasil ditambahkan.') {
                    showToast(`Member berhasil ditambahkan!\nNomor Anggota: ${data.member_no}`);
                    this.reset(); // Reset form
                    setTimeout(() => {
                        window.location.reload(); // Reload to update member list
                    }, 2000);
                } else {
                    throw new Error(data.message || 'Unknown error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error: ' + error.message);
            })
            .finally(() => {
                // Reset button state
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                feather.replace();
            });
        });

        // Form validation enhancements
        document.querySelectorAll('.form-input, .form-select').forEach(element => {
            element.addEventListener('focus', function() {
                this.classList.add('ring-2', 'ring-blue-500', 'border-blue-500');
            });
            
            element.addEventListener('blur', function() {
                this.classList.remove('ring-2', 'ring-blue-500', 'border-blue-500');
                if (this.hasAttribute('required') && !this.value) {
                    this.classList.add('border-red-500');
                } else {
                    this.classList.remove('border-red-500');
                }
            });
        });

        // Search functionality for member list
        function setupSearch() {
            const searchInput = document.querySelector('input[placeholder="Cari anggota..."]');
            const tableRows = document.querySelectorAll('#member-list tbody tr');
            
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    
                    tableRows.forEach(row => {
                        if (row.children.length === 1) return; // Skip empty state row
                        
                        const memberNo = row.children[0].textContent.toLowerCase();
                        const fullname = row.children[1].textContent.toLowerCase();
                        const phone = row.children[3].textContent.toLowerCase();
                        
                        const matches = memberNo.includes(searchTerm) || 
                                      fullname.includes(searchTerm) || 
                                      phone.includes(searchTerm);
                        
                        row.style.display = matches ? '' : 'none';
                    });
                });
            }
        }

        // Setup search when member list tab is opened
        document.querySelector('button[onclick*="member-list"]').addEventListener('click', function() {
            setTimeout(setupSearch, 100);
        });
    </script>
</body>
</html>
<?php
$identity_types->free_result();
$sexes->free_result();
$educations->free_result();
$jobs->free_result();
$member_types->free_result();
$marital_statuses->free_result();
$member_statuses->free_result();
$members->free_result();

$mysqli->close();
?>