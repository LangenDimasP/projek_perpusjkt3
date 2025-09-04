<?php
session_start();
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header('Location: /naik_kelas/login');
    exit;
}

// Database configuration
$db_host = '127.0.0.1';
$db_user = 'root';
$db_pass = '';
$db_name = 'inlislite_v3';
$db_port = 3309;

// Koneksi database
try {
    $db = new PDO("mysql:host=$db_host;dbname=$db_name;port=$db_port", $db_user, $db_pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}

// Ambil daftar status anggota untuk dropdown
$stmt = $db->query("SELECT id, Nama FROM status_anggota ORDER BY Nama");
$statuses = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmtKelas = $db->query("SELECT id, namakelassiswa FROM kelas_siswa ORDER BY namakelassiswa");
$kelasList = $stmtKelas->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perubahan Status Anggota Massal - Inlislite</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="../assets/images/logo.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../assets/images/logo.png">
    <link href="../dist/output.css" rel="stylesheet"> <!-- Tailwind CSS -->
</head>
<body class="bg-gradient-to-br from-blue-50 via-white to-blue-100 min-h-screen">
    <div class="flex min-h-screen">
        <?php include '../partials/sidebar.php'; ?>

        <main class="ml-64 flex-1 p-8 flex flex-col">
            <div class="max-w-4xl w-full mx-auto">
                <!-- Main Content -->
                <div class="bg-white rounded-2xl shadow-xl p-8">
                    <h1 class="text-2xl font-bold text-blue-500 mb-6 flex items-center">
                        <i class="fas fa-users w-6 h-6 mr-3 text-blue-500"></i>
                        Perubahan Status Anggota Secara Massal
                    </h1>

                    <!-- Form utama -->
                    <form id="status-form">
                        <!-- Dropdown Status Awal & Tujuan Menyamping -->
                        <div class="mb-8 flex gap-4">
                            <div class="flex-1 min-w-0">
                                <label for="status_awal" class="flex items-center text-sm font-semibold text-gray-700 mb-3">
                                    <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                    Pilih Status Anggota Awal
                                </label>
                                <select id="status_awal" name="status_awal" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all duration-200 bg-white shadow-sm">
                                    <option value="">-- Pilih Status --</option>
                                    <?php foreach ($statuses as $status) { ?>
                                        <option value="<?php echo $status['id']; ?>"><?php echo htmlspecialchars($status['Nama']); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="flex-1 min-w-0">
                                <label for="status_tujuan" class="flex items-center text-sm font-semibold text-gray-700 mb-3">
                                    <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                    </svg>
                                    Pilih Status Anggota Tujuan
                                </label>
                                <select id="status_tujuan" name="status_tujuan" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all duration-200 bg-white shadow-sm">
                                    <option value="">-- Pilih Status Tujuan --</option>
                                    <?php foreach ($statuses as $status) { ?>
                                        <option value="<?php echo $status['id']; ?>"><?php echo htmlspecialchars($status['Nama']); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <!-- Filter Berdasarkan Kelas & Search -->
                        <div id="filter-container" class="flex gap-4 mb-8">
                            <div class="w-1/5 flex flex-col justify-end">
                                <label for="kelas_filter" class="flex items-center text-sm font-semibold text-gray-700 mb-3">
                                    <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                                    </svg>
                                    Filter Berdasarkan Kelas
                                </label>
                                <select id="kelas_filter" name="kelas_filter" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all duration-200 bg-white shadow-sm">
                                    <option value="">-- Semua Kelas --</option>
                                    <?php foreach ($kelasList as $kelas) { ?>
                                        <option value="<?php echo $kelas['id']; ?>"><?php echo htmlspecialchars($kelas['namakelassiswa']); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="flex-1 flex flex-col justify-end">
                                <label for="search_member" class="flex items-center text-sm font-semibold text-gray-700 mb-3">
                                    <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"></path>
                                    </svg>
                                    Cari Nama Anggota
                                </label>
                                <input type="text" id="search_member" name="search_member" placeholder="Ketik nama anggota..." class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all duration-200 bg-white shadow-sm">
                            </div>
                        </div>
<!-- Input Search -->
<div class="mb-8">
                            <div id="anggota-container" class="mb-8">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-semibold text-gray-800">Daftar Anggota</h3>
                                    <label class="flex items-center cursor-pointer">
                                        <input type="checkbox" id="select-all" class="w-4 h-4 text-blue-500 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                                        <span class="ml-2 text-sm font-medium text-gray-700">Pilih Semua</span>
                                    </label>
                                </div>
                                <div class="bg-blue-50 rounded-xl p-6">
                                    <div id="selected-preview" class="mb-2 text-sm text-blue-700 font-semibold"></div>
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                                            <thead>
                                                <tr class="bg-blue-500 text-white">
                                                    <th class="py-3 px-4 border-b text-left">Pilih</th>
                                                    <th class="py-3 px-4 border-b text-left">Nama</th>
                                                    <th class="py-3 px-4 border-b text-left">Nomor Anggota</th>
                                                    <th class="py-3 px-4 border-b text-left">Status Saat Ini</th>
                                                </tr>
                                            </thead>
                                            <tbody id="member-table-body">
                                                <!-- Daftar member akan dimuat via AJAX -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                        <!-- Tombol Submit -->
                        <button type="button" id="submit-btn" class="w-full bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold py-4 px-6 rounded-xl transition-all duration-200 hover:scale-105 focus:ring-4 focus:ring-blue-200 shadow-lg flex items-center justify-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                            </svg>
                            <span>Proses Perubahan Status</span>
                        </button>
                    </form>

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
            </div>
        </main>
    </div>

<script type="text/javascript">
// Wrap semua dalam IIFE untuk menghindari konflik
(function() {
    'use strict';
    
    // Global variables
    let searchTimeout;
    let allMembers = [];
    let statusTimeout;

    // PENTING: Prevent default form submission
    function preventFormSubmission() {
        const form = document.getElementById('status-form');
        if (form) {
            // Remove default form action
            form.removeAttribute('action');
            form.removeAttribute('method');
            
            // Prevent form submission
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            });
        }
    }

    // Fungsi showStatus yang robust
    function showStatus(message, type) {
        
        // Remove existing status message
        var existingStatus = document.getElementById('status-message');
        if (existingStatus) {
            existingStatus.remove();
        }
        
        // Clear timeout sebelumnya
        if (statusTimeout) {
            clearTimeout(statusTimeout);
        }
        
        // Tentukan class berdasarkan type
        var classes = '';
        if (type === 'success') {
            classes = 'bg-green-100 text-green-700 border-green-200';
        } else if (type === 'error') {
            classes = 'bg-red-100 text-red-700 border-red-200';
        } else {
            classes = 'bg-blue-100 text-blue-700 border-blue-200';
        }
        
        // Buat element status
        var statusDiv = document.createElement('div');
        statusDiv.id = 'status-message';
        statusDiv.className = 'mt-6 p-4 rounded-xl border ' + classes + ' opacity-0 transition-opacity duration-300';
        statusDiv.innerHTML = `
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    ${type === 'error' ? 
                        '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>' :
                        '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>'
                    }
                </svg>
                <span class="font-medium">${message}</span>
            </div>
        `;
        
        // Insert setelah form
        var form = document.getElementById('status-form');
        if (form.nextSibling) {
            form.parentNode.insertBefore(statusDiv, form.nextSibling);
        } else {
            form.parentNode.appendChild(statusDiv);
        }
        
        // Animate in
        setTimeout(function() {
            statusDiv.style.opacity = '1';
        }, 50);
        
        // Scroll ke message
        statusDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        
        // Auto remove untuk pesan sukses setelah 8 detik
        if (type === 'success') {
            statusTimeout = setTimeout(function() {
                if (statusDiv && statusDiv.parentNode) {
                    statusDiv.style.opacity = '0';
                    setTimeout(function() {
                        if (statusDiv && statusDiv.parentNode) {
                            statusDiv.remove();
                        }
                    }, 300);
                }
            }, 8000); // 8 detik untuk pesan sukses
        }
    }

    // Load members dari server
    function loadMembers() {
        var statusId = document.getElementById('status_awal').value;
        var kelasId = document.getElementById('kelas_filter').value;
        var search = document.getElementById('search_member').value;
        
        if (!statusId) {
            document.getElementById('member-table-body').innerHTML = '';
            document.getElementById('selected-preview').innerHTML = '';
            return;
        }
        
        var xhr = new XMLHttpRequest();
        var url = '../api/status_anggota.api.php?action=get_members&status_id=' + statusId;
        if (kelasId) url += '&kelas_id=' + kelasId;
        if (search) url += '&search=' + encodeURIComponent(search);
        
        xhr.open('GET', url, true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                try {
                    var data = JSON.parse(xhr.responseText);
                    allMembers = data;
                    renderMemberTable(data);
                } catch (e) {
                    console.error('Error parsing JSON:', e);
                    showStatus('Error memuat data anggota', 'error');
                }
            }
        };
        xhr.send();
    }

    // Render tabel member
    function renderMemberTable(data) {
        var tableBody = document.getElementById('member-table-body');
        tableBody.innerHTML = '';
        
        if (data.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="4" class="py-8 px-4 text-center text-gray-500">Tidak ada anggota ditemukan</td></tr>';
            document.getElementById('selected-preview').innerHTML = '';
            return;
        }
        
        for (var i = 0; i < data.length; i++) {
            var member = data[i];
            var row = document.createElement('tr');
            row.innerHTML = 
                '<td class="py-3 px-4 border-b"><input type="checkbox" name="selected_members[]" value="' + member.ID + '" class="member-checkbox w-4 h-4 text-blue-500 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2"></td>' +
                '<td class="py-3 px-4 border-b">' + escapeHtml(member.Fullname) + '</td>' +
                '<td class="py-3 px-4 border-b">' + escapeHtml(member.MemberNo) + '</td>' +
                '<td class="py-3 px-4 border-b">' + escapeHtml(member.StatusAnggota.Nama) + '</td>';
            tableBody.appendChild(row);
        }
        
        addCheckboxListeners();
        updateSelectedPreview();
        document.getElementById('select-all').checked = false;
    }

    // Escape HTML
    function escapeHtml(text) {
        if (!text) return '';
        var map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.toString().replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    // Update selected preview
    function updateSelectedPreview() {
        var checkboxes = document.getElementsByClassName('member-checkbox');
        var selectedCount = 0;
        for (var i = 0; i < checkboxes.length; i++) {
            if (checkboxes[i].checked) {
                selectedCount++;
            }
        }
        var selectedPreview = document.getElementById('selected-preview');
        if (selectedPreview) {
            selectedPreview.innerHTML = selectedCount > 0 ? selectedCount + ' anggota dipilih' : '';
        }
    }

    // Add checkbox listeners
    function addCheckboxListeners() {
        var checkboxes = document.getElementsByClassName('member-checkbox');
        for (var i = 0; i < checkboxes.length; i++) {
            checkboxes[i].addEventListener('change', function() {
                updateSelectedPreview();
            });
        }
    }

    // Update visibility functions
    function updateAnggotaVisibility() {
        var statusAwal = document.getElementById('status_awal').value;
        var anggotaContainer = document.getElementById('anggota-container');
        if (anggotaContainer) {
            if (!statusAwal) {
                anggotaContainer.style.display = 'none';
            } else {
                anggotaContainer.style.display = '';
            }
        }
    }

    function updateFilterVisibility() {
        var statusAwal = document.getElementById('status_awal').value;
        var filterContainer = document.getElementById('filter-container');
        if (filterContainer) {
            if (!statusAwal) {
                filterContainer.style.display = 'none';
            } else {
                filterContainer.style.display = '';
            }
        }
    }

    // Handle submit - PURE AJAX, NO PAGE RELOAD
    function handleStatusSubmit(event) {
    console.log('Submit button clicked');
    event.preventDefault();
    event.stopPropagation();
    event.stopImmediatePropagation();

    var statusAwal = document.getElementById('status_awal').value;
    var statusTujuan = document.getElementById('status_tujuan').value;

    // Validasi: status awal dan tujuan tidak boleh sama
    if (statusAwal === statusTujuan) {
        showStatus('Status awal dan status tujuan tidak boleh sama!', 'error');
        return;
    }

    if (!statusTujuan) {
        showStatus('Pilih status tujuan terlebih dahulu!', 'error');
        return;
    }

        var checkboxes = document.getElementsByClassName('member-checkbox');
        var selected = [];
        for (var i = 0; i < checkboxes.length; i++) {
            if (checkboxes[i].checked) {
                selected.push(checkboxes[i].value);
            }
        }
        
        if (selected.length === 0) {
            showStatus('Pilih setidaknya satu anggota!', 'error');
            return;
        }

        // Show confirmation modal
        var confirmMessage = document.getElementById('confirm-message');
        confirmMessage.innerHTML = 'Apakah Anda yakin ingin mengubah status ' + selected.length + ' anggota ke status baru?';
        var confirmModal = document.getElementById('confirm-modal');
        confirmModal.classList.remove('hidden');

        // Handle confirm proceed
        document.getElementById('confirm-proceed').onclick = function() {
            
            // Disable button
            var submitBtn = document.getElementById('submit-btn');
            var originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<div class="flex items-center justify-center"><svg class="w-5 h-5 animate-spin mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg><span>Memproses...</span></div>';
            submitBtn.disabled = true;
            
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '../api/status_anggota.api.php?action=update_status', true);
            xhr.setRequestHeader('Content-Type', 'application/json');
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4) {
                    
                    // Re-enable button
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                    
                    // Hide modal
                    confirmModal.classList.add('hidden');
                    
                    if (xhr.status == 200) {
                        try {
                            var response = JSON.parse(xhr.responseText);    
                            
                            if (response.success) {
                                // JANGAN RELOAD PAGE - hanya tampilkan pesan
                                showStatus('Status berhasil diubah untuk ' + selected.length + ' anggota!', 'success');
                                
                                // Reset selections tanpa reload
                                document.getElementById('select-all').checked = false;
                                var checkboxes = document.getElementsByClassName('member-checkbox');
                                for (var i = 0; i < checkboxes.length; i++) {
                                    checkboxes[i].checked = false;
                                }
                                updateSelectedPreview();
                                
                                // OPTIONAL: Reload data setelah delay panjang (atau skip sama sekali)
                                setTimeout(function() {
                                    loadMembers();
                                }, 1000); // 5 detik delay
                                
                            } else {
                                showStatus('❌ Gagal mengubah status: ' + (response.error || 'Unknown error'), 'error');
                            }
                        } catch (e) {
                            console.error('Error parsing response:', e);
                            showStatus('❌ Error dalam memproses response dari server', 'error');
                        }
                    } else {
                        showStatus('❌ Error koneksi ke server (HTTP ' + xhr.status + ')', 'error');
                    }
                }
            };
            
            xhr.send(JSON.stringify({ 
                selected_members: selected, 
                new_status: statusTujuan 
            }));
        };

        // Handle confirm cancel
        document.getElementById('confirm-cancel').onclick = function() {
            confirmModal.classList.add('hidden');
        };
    }

    // Load members dari server
    function loadMembers() {
        var statusId = document.getElementById('status_awal').value;
        var kelasId = document.getElementById('kelas_filter').value;
        var search = document.getElementById('search_member').value;
        
        
        if (!statusId) {
            document.getElementById('member-table-body').innerHTML = '';
            document.getElementById('selected-preview').innerHTML = '';
            return;
        }
        
        var xhr = new XMLHttpRequest();
        var url = '../api/status_anggota.api.php?action=get_members&status_id=' + statusId;
        if (kelasId) url += '&kelas_id=' + kelasId;
        if (search) url += '&search=' + encodeURIComponent(search);
        
        
        xhr.open('GET', url, true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                try {
                    var data = JSON.parse(xhr.responseText);
                    allMembers = data;
                    renderMemberTable(data);
                } catch (e) {
                    showStatus('Error memuat data anggota', 'error');
                }
            }
        };
        xhr.send();
    }

    // Render tabel member
    function renderMemberTable(data) {
        var tableBody = document.getElementById('member-table-body');
        tableBody.innerHTML = '';
        
        if (data.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="4" class="py-8 px-4 text-center text-gray-500">Tidak ada anggota ditemukan</td></tr>';
            document.getElementById('selected-preview').innerHTML = '';
            return;
        }
        
        for (var i = 0; i < data.length; i++) {
            var member = data[i];
            var row = document.createElement('tr');
            row.innerHTML = 
                '<td class="py-3 px-4 border-b"><input type="checkbox" name="selected_members[]" value="' + member.ID + '" class="member-checkbox w-4 h-4 text-blue-500 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2"></td>' +
                '<td class="py-3 px-4 border-b">' + escapeHtml(member.Fullname) + '</td>' +
                '<td class="py-3 px-4 border-b">' + escapeHtml(member.MemberNo) + '</td>' +
                '<td class="py-3 px-4 border-b">' + escapeHtml(member.StatusAnggota.Nama) + '</td>';
            tableBody.appendChild(row);
        }
        
        addCheckboxListeners();
        updateSelectedPreview();
        document.getElementById('select-all').checked = false;
    }

    // Escape HTML
    function escapeHtml(text) {
        if (!text) return '';
        var map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.toString().replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    // Update selected preview
    function updateSelectedPreview() {
        var checkboxes = document.getElementsByClassName('member-checkbox');
        var selectedCount = 0;
        for (var i = 0; i < checkboxes.length; i++) {
            if (checkboxes[i].checked) {
                selectedCount++;
            }
        }
        var selectedPreview = document.getElementById('selected-preview');
        if (selectedPreview) {
            selectedPreview.innerHTML = selectedCount > 0 ? selectedCount + ' anggota dipilih' : '';
        }
    }

    // Add checkbox listeners
    function addCheckboxListeners() {
        var checkboxes = document.getElementsByClassName('member-checkbox');
        for (var i = 0; i < checkboxes.length; i++) {
            checkboxes[i].addEventListener('change', function() {
                updateSelectedPreview();
            });
        }
    }

    // Update visibility functions
    function updateAnggotaVisibility() {
        var statusAwal = document.getElementById('status_awal').value;
        var anggotaContainer = document.getElementById('anggota-container');
        if (anggotaContainer) {
            if (!statusAwal) {
                anggotaContainer.style.display = 'none';
            } else {
                anggotaContainer.style.display = '';
            }
        }
    }

    function updateFilterVisibility() {
        var statusAwal = document.getElementById('status_awal').value;
        var filterContainer = document.getElementById('filter-container');
        if (filterContainer) {
            if (!statusAwal) {
                filterContainer.style.display = 'none';
            } else {
                filterContainer.style.display = '';
            }
        }
    }

    // Inisialisasi saat DOM ready
    document.addEventListener('DOMContentLoaded', function() {
        // PERTAMA: Prevent form submission
        preventFormSubmission();
        
        // Inisialisasi visibility
        updateAnggotaVisibility();
        updateFilterVisibility();
        
        // Event listeners
        var statusAwalSelect = document.getElementById('status_awal');
        if (statusAwalSelect) {
            statusAwalSelect.addEventListener('change', function() {
                updateAnggotaVisibility();
                updateFilterVisibility();
                loadMembers();
            });
        }
        
        var kelasFilter = document.getElementById('kelas_filter');
        if (kelasFilter) {
            kelasFilter.addEventListener('change', function() {
                loadMembers();
            });
        }
        
        var searchMember = document.getElementById('search_member');
        if (searchMember) {
            searchMember.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(loadMembers, 300);
            });
        }
        
        var selectAll = document.getElementById('select-all');
        if (selectAll) {
            selectAll.addEventListener('change', function() {
                var checkboxes = document.getElementsByClassName('member-checkbox');
                for (var i = 0; i < checkboxes.length; i++) {
                    checkboxes[i].checked = this.checked;
                }
                updateSelectedPreview();
            });
        }
        
        // Submit button - PREVENT DEFAULT
        var submitBtn = document.getElementById('submit-btn');
        if (submitBtn) {
            submitBtn.addEventListener('click', function(event) {
                event.preventDefault();
                event.stopPropagation();
                handleStatusSubmit(event);
                return false;
            });
        }
        
        // Close modal handlers
        var confirmModal = document.getElementById('confirm-modal');
        if (confirmModal) {
            confirmModal.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.add('hidden');
                }
            });
        }
        
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                var modal = document.getElementById('confirm-modal');
                if (modal && !modal.classList.contains('hidden')) {
                    modal.classList.add('hidden');
                }
            }
        });
    });

})();
</script>
        <!-- Toast Notification -->
    <div id="toast-status" class="fixed top-6 right-6 z-50 min-w-[220px] max-w-xs bg-white border border-green-300 shadow-lg rounded-xl px-4 py-3 text-green-700 font-semibold hidden transition-all duration-300"></div>
</body>
</html>