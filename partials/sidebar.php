<?php
// Check if the file is being accessed from views folder or root
$isInViewsFolder = strpos($_SERVER['PHP_SELF'], '/views/') !== false;
$baseUrl = $isInViewsFolder ? '../' : '';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>INLISLite QuickAccess</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="../assets/images/logo.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../assets/images/logo.png">
    
    <!-- Tailwind CSS -->
    <link href="../dist/output.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
<aside class="fixed inset-y-0 left-0 z-30 w-64 bg-white text-gray-700 shadow-lg transform transition-transform duration-300 flex flex-col">
    <!-- Logo/Header -->
    <div class="flex flex-col items-center justify-center p-4 border-b">
        <div class="flex items-center justify-center space-x-4 mb-3">
            <!-- Logo 1: SVG with circle -->
            <div class="w-12 h-12 bg-white shadow-md rounded-full flex items-center justify-center">
                <svg class="w-8 h-8" viewBox="0 0 120 120">
                    <style type="text/css">
                        .st0{fill:#4D96FF;}
                        .st1{fill:#FFD07A;}
                    </style>
                    <g>
                        <path class="st0" d="M94.7,63.5V50c0-1.7-1.3-3-3-3h-4.3c-0.8-3-2-6-3.6-8.5l3-3c1.2-1.2,1.2-3.1,0-4.2l-7.5-7.6   c-1.2-1.2-3.1-1.2-4.3,0l-3,3c-2.7-1.6-5.5-2.8-8.5-3.6v-4.3c0-1.7-1.3-3-3-3H50c-1.7,0-3,1.3-3,3v4.3c-3,0.8-6,2-8.5,3.6l-3.1-3.1   c-1.2-1.2-3.1-1.1-4.2,0l-7.5,7.5c-1.2,1.2-1.2,3.1,0,4.2l3,3c-1.6,2.7-2.8,5.5-3.6,8.5h-4.3c-1.7,0-3,1.3-3,3v10.6   c0,1.7,1.3,3,3,3h4.3c0.8,3,2,6,3.6,8.5l-3.1,3.1c-1.2,1.2-1.1,3.1,0,4.2l7.5,7.5c1.2,1.2,3.1,1.2,4.2,0l3-3   c2.7,1.6,5.5,2.8,8.5,3.6v4.3c0,1.7,1.3,3,3,3h10.6c1.7,0,3-1.3,3-3v-4.3c3-0.8,6-2,8.5-3.6l3,3c1.2,1.2,3.1,1.2,4.2,0l7.6-7.5   c1.2-1.2,1.2-3.1,0-4.3l-3-3c1.6-2.7,2.8-5.5,3.6-8.5L94.7,63.5L94.7,63.5z M55.3,76.6c-11.8,0-21.3-9.5-21.3-21.3   s9.5-21.3,21.3-21.3s21.3,9.5,21.3,21.3S67,76.6,55.3,76.6z"/>
                        <path class="st1" d="M66.1,43.9c-4.8-4.8-11.9-5.9-17.8-3l8.5,8.5c1.9,1.9,2.2,4.9,0.5,7.1c-2,2.5-5.7,2.7-7.8,0.5l-8.7-8.7   c-3,6.2-1.5,14,4.2,18.8c4.6,3.9,11.2,4.6,16.6,2l25.9,31.6c3.5,4.3,9.9,4.6,13.8,0.7l0,0c3.9-3.9,3.6-10.3-0.7-13.8L69.1,61.7   C72,55.8,70.9,48.7,66.1,43.9z M98.3,98.3c-1.9,1.9-5.1,1.9-7,0c-1.9-1.9-1.9-5.1,0-7s5.1-1.9,7,0C100.2,93.2,100.2,96.3,98.3,98.3z"/>
                    </g>
                </svg>
            </div>
    
            <!-- Logo 2: SVG without circle -->
            <div class="w-12 h-12 flex items-center justify-center">
                <svg class="w-8 h-8" viewBox="0 0 1024 1024" class="icon"  version="1.1" xmlns="http://www.w3.org/2000/svg"><path d="M214.192 770.757c-12.267 0-24.606-1.612-36.778-4.872-37.442-10.028-68.692-34.357-87.982-68.51l-6.396-11.333c-39.31-69.589-15.768-158.979 52.473-199.258L423.311 316.93c33.067-19.523 71.682-24.81 108.622-14.913 37.441 10.028 68.677 34.365 87.967 68.518l6.382 11.297c39.316 69.618 15.781 159.015-52.458 199.286l-25.467 15.024-30.368-51.444 25.466-15.032c40.411-23.848 54.231-76.977 30.813-118.445l-6.381-11.296c-11.348-20.084-29.601-34.357-51.415-40.199-21.354-5.725-43.649-2.633-62.792 8.65l-287.802 169.86c-40.411 23.848-54.231 76.977-30.82 118.438L141.453 668c11.334 20.064 29.596 34.336 51.415 40.178 21.354 5.71 43.655 2.654 62.785-8.657l64.28-37.939 30.368 51.445-64.28 37.937c-22.179 13.099-46.843 19.793-71.829 19.793z" fill="#3660deff" /><path d="M528.854 726.86c-12.267 0-24.613-1.612-36.794-4.87-37.434-10.028-68.67-34.365-87.959-68.518l-6.39-11.312c-39.309-69.603-15.774-158.994 52.458-199.266l25.467-15.03c14.229-8.379 32.534-3.661 40.907 10.539 8.387 14.206 3.668 32.526-10.539 40.905l-25.467 15.031c-40.402 23.847-54.222 76.985-30.806 118.444l6.382 11.312c11.341 20.069 29.594 34.343 51.408 40.184 21.347 5.733 43.655 2.654 62.8-8.657L858.124 485.77c40.411-23.847 54.231-76.977 30.813-118.43l-6.396-11.332c-11.333-20.064-29.588-34.329-51.401-40.17-21.347-5.718-43.657-2.647-62.8 8.649l-64.28 37.937c-14.222 8.417-32.52 3.683-40.906-10.537-8.387-14.207-3.668-32.527 10.539-40.906l64.28-37.938c33.067-19.532 71.624-24.833 108.628-14.923 37.435 10.036 68.679 34.365 87.961 68.518l6.395 11.326c39.316 69.589 15.783 158.98-52.466 199.259l-287.8 169.852c-22.186 13.091-46.85 19.785-71.837 19.785z" fill="#152B3C" /></svg>
            </div>
    
            <!-- Logo 3: PNG with circle -->
            <div class="w-12 h-12 bg-white shadow-md rounded-full flex items-center justify-center">
                <img src="../assets/images/logo.png" alt="Logo" class="w-8 h-8">
            </div>
        </div>
        <h2 class="text-lg font-bold text-center text-gray-800">INLISLite QuickAccess</h2>
        <div class="mt-3 text-center text-sm text-gray-600">
            <div id="current-time" class="font-medium"></div>
            <div id="current-date" class="text-xs mt-1"></div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-4 py-4 space-y-2">
        <a href="<?php echo $baseUrl; ?>index.php" class="flex items-center px-3 py-2 rounded-lg transition-colors duration-200
            <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'bg-blue-600 text-white shadow-lg' : 'hover:bg-gray-200'; ?>">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 mr-3">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h7.5" />
            </svg>
            <span>Dashboard</span>
        </a>

        <a href="<?php echo $baseUrl; ?>views/kelas.php" class="flex items-center px-3 py-2 rounded-lg transition-colors duration-200
            <?php echo (strpos($_SERVER['PHP_SELF'], 'kelas.php') !== false) ? 'bg-blue-600 text-white shadow-lg' : 'hover:bg-gray-200'; ?>">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 mr-3">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
            <span>Kelas</span>
        </a>

        <a href="<?php echo $baseUrl; ?>views/create_member_form.php" class="flex items-center px-3 py-2 rounded-lg transition-colors duration-200
            <?php echo (strpos($_SERVER['PHP_SELF'], 'create_member_form.php') !== false) ? 'bg-blue-600 text-white shadow-lg' : 'hover:bg-gray-200'; ?>">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 mr-3">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
            </svg>
            <span>Tambah Member</span>
        </a>
        <a href="<?php echo $baseUrl; ?>views/stock_opname.php" class="flex items-center px-2 lg:px-3 py-2 rounded-lg transition-colors duration-200 text-sm lg:text-base
            <?php echo (strpos($_SERVER['PHP_SELF'], 'stock_opname.php') !== false) ? 'bg-blue-600 text-white shadow-lg' : 'hover:bg-gray-200'; ?>">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 lg:w-6 h-5 lg:h-6 mr-2 lg:mr-3 flex-shrink-0">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
            </svg>
            <span class="truncate">Stock Opname</span>
        </a>
    </nav>

    <!-- User Info & Logout -->
    <?php if (isset($_SESSION['user_id'])): ?>
        <div class="p-4 border-t border-gray-200">
            <div class="flex items-center mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 mr-3 text-gray-500">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span class="text-sm text-gray-600">
                    <?php 
                    $userId = $_SESSION['user_id'];
                    $mysqli = new mysqli('127.0.0.1', 'root', '', 'inlislite_v3', 3309);
                    $user = $mysqli->query("SELECT Fullname FROM users WHERE ID = $userId")->fetch_assoc();
                    echo htmlspecialchars($user['Fullname']); 
                    ?>
                </span>
            </div>

            <a href="#" onclick="confirmLogout(event)" class="flex items-center mb-3 py-2 rounded-lg transition-colors duration-200 text-red-500 hover:bg-red-100">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 mr-3">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
    </svg>
    <span>Logout</span>
</a>
        </div>
    <?php endif; ?>
</aside>

<div id="logoutModal" class="fixed inset-0 z-50 hidden">
    <!-- Dark overlay -->
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>
    
    <!-- Modal content -->
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg p-6 shadow-2xl max-w-sm mx-auto relative">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Konfirmasi Logout</h3>
            <p class="text-gray-600 mb-6">Apakah Anda yakin ingin keluar dari aplikasi?</p>
            <div class="flex justify-end space-x-3">
                <button onclick="closeLogoutModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors">
                    Batal
                </button>
                <button onclick="proceedLogout()" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                    Ya, Logout
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function updateDateTime() {
    const timeElement = document.getElementById('current-time');
    const dateElement = document.getElementById('current-date');
    
    const now = new Date();
    
    const time = now.toLocaleTimeString('id-ID', { 
        hour: '2-digit', 
        minute: '2-digit',
        second: '2-digit'
    });
    
    const options = { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric'
    };
    const date = now.toLocaleDateString('id-ID', options);
    
    timeElement.textContent = time;
    dateElement.textContent = date;
}

function confirmLogout(event) {
    event.preventDefault();
    const modal = document.getElementById('logoutModal');
    modal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden'); // Prevent scrolling when modal is open
}

function closeLogoutModal() {
    const modal = document.getElementById('logoutModal');
    modal.classList.add('hidden');
    document.body.classList.remove('overflow-hidden'); // Re-enable scrolling
}

function proceedLogout() {
    window.location.href = '<?php echo $baseUrl; ?>views/logout.php';
}

// Update click outside handler
document.getElementById('logoutModal').addEventListener('click', function(event) {
    if (event.target === this || event.target.classList.contains('bg-black')) {
        closeLogoutModal();
    }
});

updateDateTime();
setInterval(updateDateTime, 1000);
</script>