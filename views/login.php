<?php
session_start();
if(isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Sistem Kenaikan Kelas</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="../assets/images/logo.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../assets/images/logo.png">
    <link href="../dist/output.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-blue-50 via-white to-blue-100 min-h-screen flex items-center justify-center">
    <div class="bg-white rounded-2xl shadow-xl p-8 max-w-md w-full">
        <div class="flex flex-col items-center justify-center p-4">
            <div class="flex items-center justify-center space-x-4 mb-3">
                <!-- Logo 1: SVG with circle -->
                <div class="w-16 h-16 bg-white shadow-xl rounded-full flex items-center justify-center">
                    <svg class="w-12 h-12" viewBox="0 0 120 120">
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
                <div class="w-16 h-16 flex items-center justify-center">
                    <svg class="w-10 h-10" viewBox="0 0 1024 1024" class="icon"  version="1.1" xmlns="http://www.w3.org/2000/svg"><path d="M214.192 770.757c-12.267 0-24.606-1.612-36.778-4.872-37.442-10.028-68.692-34.357-87.982-68.51l-6.396-11.333c-39.31-69.589-15.768-158.979 52.473-199.258L423.311 316.93c33.067-19.523 71.682-24.81 108.622-14.913 37.441 10.028 68.677 34.365 87.967 68.518l6.382 11.297c39.316 69.618 15.781 159.015-52.458 199.286l-25.467 15.024-30.368-51.444 25.466-15.032c40.411-23.848 54.231-76.977 30.813-118.445l-6.381-11.296c-11.348-20.084-29.601-34.357-51.415-40.199-21.354-5.725-43.649-2.633-62.792 8.65l-287.802 169.86c-40.411 23.848-54.231 76.977-30.82 118.438L141.453 668c11.334 20.064 29.596 34.336 51.415 40.178 21.354 5.71 43.655 2.654 62.785-8.657l64.28-37.939 30.368 51.445-64.28 37.937c-22.179 13.099-46.843 19.793-71.829 19.793z" fill="#3660deff" /><path d="M528.854 726.86c-12.267 0-24.613-1.612-36.794-4.87-37.434-10.028-68.67-34.365-87.959-68.518l-6.39-11.312c-39.309-69.603-15.774-158.994 52.458-199.266l25.467-15.03c14.229-8.379 32.534-3.661 40.907 10.539 8.387 14.206 3.668 32.526-10.539 40.905l-25.467 15.031c-40.402 23.847-54.222 76.985-30.806 118.444l6.382 11.312c11.341 20.069 29.594 34.343 51.408 40.184 21.347 5.733 43.655 2.654 62.8-8.657L858.124 485.77c40.411-23.847 54.231-76.977 30.813-118.43l-6.396-11.332c-11.333-20.064-29.588-34.329-51.401-40.17-21.347-5.718-43.657-2.647-62.8 8.649l-64.28 37.937c-14.222 8.417-32.52 3.683-40.906-10.537-8.387-14.207-3.668-32.527 10.539-40.906l64.28-37.938c33.067-19.532 71.624-24.833 108.628-14.923 37.435 10.036 68.679 34.365 87.961 68.518l6.395 11.326c39.316 69.589 15.783 158.98-52.466 199.259l-287.8 169.852c-22.186 13.091-46.85 19.785-71.837 19.785z" fill="#152B3C" /></svg>
                </div>
        
                <!-- Logo 3: PNG with circle -->
                <div class="w-16 h-16 bg-white shadow-xl rounded-full flex items-center justify-center">
                    <img src="../assets/images/logo.png" alt="Logo" class="w-12 h-12">
                </div>
            </div>
            <h2 class="text-lg font-bold text-center text-gray-800">INLISLite QuickAccess</h2>
        </div>
        <form id="login-form">
            <div class="mb-4">
                <label for="username" class="block text-sm font-semibold text-gray-700 mb-2">Username</label>
                <input type="text" id="username" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 bg-white shadow-sm" placeholder="Masukkan username">
            </div>
            <div class="mb-6">
                <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                <input type="password" id="password" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 bg-white shadow-sm" placeholder="Masukkan password">
            </div>
            <button type="submit" id="login-btn" class="w-full bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold py-3 px-6 rounded-xl transition-all duration-200 hover:scale-105 focus:ring-4 focus:ring-blue-200 shadow-lg flex items-center justify-center">
                <span>Login</span>
            </button>
            <div id="login-status" class="mt-4 text-center text-red-600 text-sm hidden"></div>
        </form>
    </div>
    <script>
    document.getElementById('login-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const username = document.getElementById('username').value.trim();
        const password = document.getElementById('password').value.trim();
        const statusDiv = document.getElementById('login-status');
        statusDiv.classList.add('hidden');
        if (!username || !password) {
            statusDiv.textContent = 'Username dan password wajib diisi.';
            statusDiv.classList.remove('hidden');
            return;
        }
        document.getElementById('login-btn').disabled = true;
        try {
            const response = await fetch('../api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'login', username, password })
            });
            const result = await response.json();
            if (!response.ok) throw new Error(result.message);
            // Login sukses, redirect ke index.html
            window.location.href = 'index.php';
        } catch (err) {
            statusDiv.textContent = err.message;
            statusDiv.classList.remove('hidden');
        } finally {
            document.getElementById('login-btn').disabled = false;
        }
    });


    </script>
</body>
</html>