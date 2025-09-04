<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Database connection
$mysqli = new mysqli('127.0.0.1', 'root', '', 'inlislite_v3', 3309);
if ($mysqli->connect_error) {
    die('Koneksi database gagal: ' . $mysqli->connect_error);
}

// Get basic statistics
$totalKelas = $mysqli->query("SELECT COUNT(*) as total FROM kelas_siswa")->fetch_assoc()['total'];
$totalSiswa = $mysqli->query("SELECT COUNT(*) as total FROM members")->fetch_assoc()['total'];
$totalUsers = $mysqli->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'];

// Get students per class (Bar Chart)
$kelasData = $mysqli->query("
    SELECT ks.namakelassiswa, COUNT(m.ID) as total_siswa 
    FROM kelas_siswa ks 
    LEFT JOIN members m ON ks.id = m.Kelas_id 
    GROUP BY ks.id 
    ORDER BY total_siswa DESC 
    LIMIT 5
");
$kelasLabels = [];
$kelasValues = [];
while ($row = $kelasData->fetch_assoc()) {
    $kelasLabels[] = $row['namakelassiswa'];
    $kelasValues[] = (int)$row['total_siswa'];
}

// Get collection categories (Pie Chart)
$categoryData = $mysqli->query("
    SELECT cc.Name, COUNT(c.ID) as total 
    FROM collectioncategorys cc 
    LEFT JOIN collections c ON cc.ID = c.Category_id 
    WHERE cc.ID IN (7, 8, 9)
    GROUP BY cc.ID
");
$categoryLabels = [];
$categoryValues = [];
while ($row = $categoryData->fetch_assoc()) {
    $categoryLabels[] = $row['Name'];
    $categoryValues[] = (int)$row['total'];
}

// Get stock opname progress (Line Chart)
$progressData = $mysqli->query("
    SELECT DATE(CreateDate) as scan_date, COUNT(*) as scan_count
    FROM stockopnamedetail
    WHERE CreateDate >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    GROUP BY DATE(CreateDate)
    ORDER BY scan_date
");
$progressLabels = [];
$progressValues = [];
$dateRange = [];
$startDate = new DateTime();
$startDate->modify('-6 days');
$endDate = new DateTime();
$interval = new DateInterval('P1D');
$period = new DatePeriod($startDate, $interval, $endDate);
foreach ($period as $date) {
    $dateRange[$date->format('Y-m-d')] = 0;
}
while ($row = $progressData->fetch_assoc()) {
    $dateRange[$row['scan_date']] = (int)$row['scan_count'];
}
foreach ($dateRange as $date => $count) {
    $progressLabels[] = (new DateTime($date))->format('M d');
    $progressValues[] = $count;
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistem Kenaikan Kelas & Stock Opname</title>
    <link href="dist/output.css" rel="stylesheet">

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="assets/images/logo.png">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/images/logo.png">
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body class="bg-gradient-to-br from-blue-50 via-white to-blue-100 min-h-screen">
    <div class="flex min-h-screen">
        <?php include 'partials/sidebar.php'; ?>
        
        <!-- Main Content -->
        <main class="ml-64 flex-1 p-8">
            <div class="max-w-7xl mx-auto px-4">
                <!-- Header -->
                <div class="bg-white rounded-2xl shadow-xl p-6 mb-8">
                    <div class="flex justify-between items-center">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-800 mb-2 flex items-center">
                                <i class="fas fa-tachometer-alt text-blue-600 mr-3"></i>
                                Dashboard
                            </h1>
                            <p class="text-gray-600">Ringkasan statistik sistem kenaikan kelas dan stock opname</p>
                        </div>
                    </div>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <!-- Total Kelas -->
                    <div class="bg-white rounded-2xl shadow-xl p-6 transform hover:-translate-y-1 transition-all duration-300">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-500 bg-opacity-10">
                                <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h2 class="text-2xl font-bold text-gray-900"><?php echo $totalKelas; ?></h2>
                                <p class="text-sm text-gray-500">Total Kelas</p>
                            </div>
                        </div>
                    </div>

                    <!-- Total Siswa -->
                    <div class="bg-white rounded-2xl shadow-xl p-6 transform hover:-translate-y-1 transition-all duration-300">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-500 bg-opacity-10">
                                <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h2 class="text-2xl font-bold text-gray-900"><?php echo $totalSiswa; ?></h2>
                                <p class="text-sm text-gray-500">Total Siswa</p>
                            </div>
                        </div>
                    </div>

                    <!-- Total Users -->
                    <div class="bg-white rounded-2xl shadow-xl p-6 transform hover:-translate-y-1 transition-all duration-300">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-purple-500 bg-opacity-10">
                                <svg class="w-8 h-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h2 class="text-2xl font-bold text-gray-900"><?php echo $totalUsers; ?></h2>
                                <p class="text-sm text-gray-500">Total Users</p>
                            </div>
                        </div>
                    </div>

                    <!-- Total Collections -->
                    <div class="bg-white rounded-2xl shadow-xl p-6 transform hover:-translate-y-1 transition-all duration-300">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-yellow-500 bg-opacity-10">
                                <svg class="w-8 h-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h2 id="total-collections" class="text-2xl font-bold text-gray-900">0</h2>
                                <p class="text-sm text-gray-500">Total Koleksi</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Grid: Dua kolom untuk chart lain -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Bar Chart: Students per Class -->
                    <div class="bg-white rounded-2xl shadow-xl p-6 h-96">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Jumlah Siswa per Kelas (Top 5)</h3>
                        <canvas id="polar-chart"></canvas>
                    </div>
                
                    <!-- Pie Chart: Collection Categories -->
                    <div class="bg-white rounded-2xl shadow-xl p-6 h-96">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Distribusi Kategori Koleksi</h3>
                        <canvas id="radar-chart"></canvas>
                    </div>
                </div>
                
                <!-- Chart Progres Stock Opname 7 Hari: Full Width -->
                <div class="bg-white rounded-2xl shadow-xl p-6 h-96 w-full mt-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Progres Stock Opname (7 Hari Terakhir)</h3>
                    <div style="position:relative;width:100%;height:100%;">
                        <canvas id="line-chart" style="width:100%;height:100%;"></canvas>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Ensure charts are initialized before fetching data
        window.onload = function() {

            .catch(error => {
                console.error('Fetch error:', error);
                showAlert('Gagal memuat data stock opname.', 'error');
            });
        };

        // Alert function
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
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Common chart options for responsiveness
            const commonOptions = {
                maintainAspectRatio: false,
                responsive: true,
                layout: {
                    padding: {
                        top: 20,
                        bottom: 20,
                        left: 20,
                        right: 20
                    }
                }
            };
        
            // Initialize Polar Area Chart
            new Chart(document.getElementById('polar-chart'), {
                type: 'polarArea',
                data: {
                    labels: <?php echo json_encode($kelasLabels); ?>,
                    datasets: [{
                        label: "Jumlah Siswa",
                        data: <?php echo json_encode($kelasValues); ?>,
                        backgroundColor: [
                            "rgba(59, 130, 246, 0.7)",
                            "rgba(16, 185, 129, 0.7)",
                            "rgba(139, 92, 246, 0.7)",
                            "rgba(245, 158, 11, 0.7)",
                            "rgba(239, 68, 68, 0.7)"
                        ],
                        borderColor: [
                            "#3B82F6",
                            "#10B981",
                            "#8B5CF6",
                            "#F59E0B",
                            "#EF4444"
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    ...commonOptions,
                    scales: {
                        r: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 5,
                                font: { size: 11 }
                            },
                            grid: { circular: true }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 15,
                                boxWidth: 12,
                                font: { size: 11 }
                            }
                        },
                        title: {
                            display: true,
                            text: 'Distribusi Siswa per Kelas',
                            font: { size: 14, weight: 'bold' }
                        }
                    }
                }
            });
        
            // Initialize Radar Chart
            new Chart(document.getElementById('radar-chart'), {
                type: 'radar',
                data: {
                    labels: <?php echo json_encode($categoryLabels); ?>,
                    datasets: [{
                        label: "Jumlah Koleksi",
                        data: <?php echo json_encode($categoryValues); ?>,
                        backgroundColor: "rgba(59, 130, 246, 0.4)",
                        borderColor: "#3B82F6",
                        borderWidth: 2,
                        pointBackgroundColor: "#3B82F6",
                        pointBorderColor: "#fff",
                        pointHoverBackgroundColor: "#fff",
                        pointHoverBorderColor: "#3B82F6",
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    ...commonOptions,
                    scales: {
                        r: {
                            beginAtZero: true,
                            angleLines: {
                                display: true,
                                color: 'rgba(0, 0, 0, 0.1)'
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            },
                            ticks: {
                                stepSize: Math.ceil(Math.max(...<?php echo json_encode($categoryValues); ?>) / 5),
                                font: { size: 11 }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 15,
                                boxWidth: 12,
                                font: { size: 11 }
                            }
                        }
                    }
                }
            });
        
        
            // Initialize Line Chart
            new Chart(document.getElementById('line-chart'), {
                type: 'line',
                data: {
                    labels: <?php echo json_encode($progressLabels); ?>,
                    datasets: [{
                        label: "Koleksi Discan",
                        data: <?php echo json_encode($progressValues); ?>,
                        borderColor: "#3B82F6",
                        backgroundColor: "rgba(59, 130, 246, 0.2)",
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    ...commonOptions,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: "Jumlah Koleksi",
                                font: { size: 12 }
                            },
                            ticks: {
                                font: { size: 11 }
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: "Tanggal",
                                font: { size: 12 }
                            },
                            ticks: {
                                font: { size: 11 }
                            }
                        }
                    },
                    plugins: {
                        legend: { display: false }
                    }
                }
            });
        
            // Fetch stock opname data
            fetch('stock_opname_api.php', {
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
                // Update Total Collections card
                document.getElementById('total-collections').textContent = data.total_collections || 0;
        
                // Update Doughnut Chart
                doughnutChart.data.datasets[0].data = [
                    data.verified_collections || 0,
                    data.unverified_collections || 0
                ];
                doughnutChart.update();
            })
            .catch(error => {
                console.error('Fetch error:', error);
                showAlert('Gagal memuat data stock opname.', 'error');
            });
        });
    </script>
</body>
</html>