<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

$db_host = '127.0.0.1';
$db_user = 'root';
$db_pass = '';
$db_name = 'inlislite_v3';
$db_port = 3309;

$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name, $db_port);

if ($mysqli->connect_error) {
    if (isset($_GET['action']) || $_SERVER['REQUEST_METHOD'] === 'POST') {
        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode(['message' => 'Koneksi database gagal: ' . $mysqli->connect_error]);
    } else {
        die('<h1>Koneksi database gagal: ' . $mysqli->connect_error . '</h1>');
    }
    exit;
}

// Handle GET requests
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    header('Content-Type: application/json');
    
    if (!isset($_GET['action'])) {
        http_response_code(400);
        echo json_encode(['message' => 'Parameter action diperlukan.']);
        exit;
    }
    switch ($_GET['action']) {
        case 'search_kelas':
            $searchQuery = isset($_GET['query']) ? $_GET['query'] : '';
            $sql = "SELECT id, namakelassiswa FROM kelas_siswa";
            
            if (!empty($searchQuery)) {
                $searchQuery = '%' . $mysqli->real_escape_string($searchQuery) . '%';
                $stmt = $mysqli->prepare("SELECT id, namakelassiswa FROM kelas_siswa WHERE namakelassiswa LIKE ? ORDER BY namakelassiswa ASC LIMIT 10");
                $stmt->bind_param("s", $searchQuery);
                $stmt->execute();
                $result = $stmt->get_result();
            } else {
                $result = $mysqli->query("SELECT id, namakelassiswa FROM kelas_siswa ORDER BY namakelassiswa ASC LIMIT 10");
            }
            
            $data = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode($data);
            break;

        case 'get_members':
            $data = [];
            if (!empty($_GET['kelasId'])) {
                $kelasId = (int)$_GET['kelasId'];
                $stmt = $mysqli->prepare("SELECT ID, Fullname FROM members WHERE Kelas_id = ? ORDER BY Fullname ASC");
                $stmt->bind_param("i", $kelasId);
                $stmt->execute();
                $result = $stmt->get_result();
                $data = $result->fetch_all(MYSQLI_ASSOC);
            } elseif (!empty($_GET['search_query'])) {
                $searchQuery = '%' . $mysqli->real_escape_string($_GET['search_query']) . '%';
                $stmt = $mysqli->prepare("SELECT ID, Fullname FROM members WHERE Fullname LIKE ? AND Kelas_id IS NOT NULL ORDER BY Fullname ASC");
                $stmt->bind_param("s", $searchQuery);
                $stmt->execute();
                $result = $stmt->get_result();
                $data = $result->fetch_all(MYSQLI_ASSOC);
            } else {
                http_response_code(400);
                echo json_encode(['message' => 'Parameter kelasId atau search_query diperlukan.']);
                exit;
            }
            echo json_encode($data);
            break;

        case 'get_members_no_class':
            $searchQuery = isset($_GET['search_query']) ? '%' . $mysqli->real_escape_string($_GET['search_query']) . '%' : '%';
            $stmt = $mysqli->prepare("SELECT ID, Fullname FROM members WHERE Kelas_id IS NULL AND Fullname LIKE ? ORDER BY Fullname ASC");
            $stmt->bind_param("s", $searchQuery);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode($data);
            break;

        case 'check_login':
            $isLoggedIn = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
            $user = null;
            if ($isLoggedIn) {
                $stmt = $mysqli->prepare("SELECT ID, Fullname FROM users WHERE ID = ? AND IsActive = 1");
                $stmt->bind_param("i", $_SESSION['user_id']);
                $stmt->execute();
                $result = $stmt->get_result();
                $user = $result->fetch_assoc();
                if (!$user) {
                    $isLoggedIn = false;
                    unset($_SESSION['user_id']);
                }
            }
            echo json_encode(['isLoggedIn' => $isLoggedIn, 'user' => $user]);
            break;

        default:
            http_response_code(400);
            echo json_encode(['message' => 'Aksi tidak valid.']);
            break;
    }
    exit;
}

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['action'])) {
        http_response_code(400);
        echo json_encode(['message' => 'Permintaan tidak valid.']);
        exit;
    }

    switch ($input['action']) {
        case 'login':
            $username = isset($input['username']) ? trim($input['username']) : '';
            $password = isset($input['password']) ? trim($input['password']) : '';
        
            if (empty($username) || empty($password)) {
                http_response_code(400);
                echo json_encode(['message' => 'Username dan password diperlukan.']);
                exit;
            }
            $stmt = $mysqli->prepare("SELECT ID, Fullname, password_hash, password FROM users WHERE username = ? AND IsActive = 1");
            if (!$stmt) {
                http_response_code(500);
                echo json_encode(['message' => 'Prepare failed: ' . $mysqli->error]);
                exit;
            }
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            
            $isValid = false;
            if ($user) {
                if (!empty($user['password_hash'])) {
                    $isValid = password_verify($password, $user['password_hash']);
                } elseif (!empty($user['password'])) {
                    // Cek SHA1
                    $isValid = (sha1($password) === $user['password']);
                }
            }
            
            if ($isValid) {
                $_SESSION['user_id'] = $user['ID'];
                echo json_encode(['message' => 'Login berhasil.', 'user' => ['ID' => $user['ID'], 'Fullname' => $user['Fullname']]]);
            } else {
                http_response_code(401);
                echo json_encode(['message' => 'Username atau password salah.']);
            }
            // ...existing code...
            break;

        case 'logout':
            session_unset();
            session_destroy();
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                    $params["path"], $params["domain"],
                    $params["secure"], $params["httponly"]
                );
            }
            echo json_encode(['message' => 'Logout berhasil.']);
            break;

        case 'add_kelas':
            if (!isset($_SESSION['user_id'])) {
                http_response_code(401);
                echo json_encode(['message' => 'Harap login terlebih dahulu.']);
                exit;
            }

            $namaKelas = isset($input['namakelassiswa']) ? trim($input['namakelassiswa']) : '';
            if (empty($namaKelas)) {
                http_response_code(400);
                echo json_encode(['message' => 'Nama kelas diperlukan.']);
                exit;
            }

            if (strlen($namaKelas) > 50) {
                http_response_code(400);
                echo json_encode(['message' => 'Nama kelas tidak boleh lebih dari 50 karakter.']);
                exit;
            }

            // Check if class name already exists
            $stmt = $mysqli->prepare("SELECT id FROM kelas_siswa WHERE namakelassiswa = ?");
            $stmt->bind_param("s", $namaKelas);
            $stmt->execute();
            if ($stmt->get_result()->num_rows > 0) {
                http_response_code(400);
                echo json_encode(['message' => 'Nama kelas sudah ada.']);
                exit;
            }

            $stmt = $mysqli->prepare("INSERT INTO kelas_siswa (namakelassiswa, CreateBy, CreateDate) VALUES (?, ?, NOW())");
            $stmt->bind_param("si", $namaKelas, $_SESSION['user_id']);
            if ($stmt->execute()) {
                echo json_encode(['message' => 'Kelas berhasil ditambahkan.']);
            } else {
                http_response_code(500);
                echo json_encode(['message' => 'Gagal menambahkan kelas: ' . $mysqli->error]);
            }
            break;

        case 'move_students':
            $keKelasId = isset($input['keKelasId']) ? (int)$input['keKelasId'] : 0;
            $memberIds = isset($input['memberIds']) && is_array($input['memberIds']) ? $input['memberIds'] : [];
            $dariKelasId = isset($input['dariKelasId']) ? (int)$input['dariKelasId'] : null;

            if ($keKelasId <= 0) {
                http_response_code(400);
                echo json_encode(['message' => 'Kelas tujuan tidak valid.']);
                exit;
            }

            if (empty($memberIds)) {
                http_response_code(400);
                echo json_encode(['message' => 'Harap pilih minimal satu siswa.']);
                exit;
            }

            // Validate if keKelasId exists
            $stmt = $mysqli->prepare("SELECT id FROM kelas_siswa WHERE id = ?");
            $stmt->bind_param("i", $keKelasId);
            $stmt->execute();
            if ($stmt->get_result()->num_rows === 0) {
                http_response_code(400);
                echo json_encode(['message' => 'Kelas tujuan tidak ditemukan.']);
                exit;
            }

            // If dariKelasId is provided, validate it
            if ($dariKelasId !== null) {
                $stmt = $mysqli->prepare("SELECT id FROM kelas_siswa WHERE id = ?");
                $stmt->bind_param("i", $dariKelasId);
                $stmt->execute();
                if ($stmt->get_result()->num_rows === 0) {
                    http_response_code(400);
                    echo json_encode(['message' => 'Kelas asal tidak ditemukan.']);
                    exit;
                }

                // Validate that dariKelasId and keKelasId are different
                if ($dariKelasId === $keKelasId) {
                    http_response_code(400);
                    echo json_encode(['message' => 'Kelas asal dan tujuan tidak boleh sama.']);
                    exit;
                }
            }

            // Validate member IDs
            $validMemberIds = [];
            $stmt = $mysqli->prepare("SELECT ID FROM members WHERE ID = ?");
            foreach ($memberIds as $memberId) {
                $stmt->bind_param("i", $memberId);
                $stmt->execute();
                if ($stmt->get_result()->num_rows > 0) {
                    $validMemberIds[] = (int)$memberId;
                }
            }

            if (empty($validMemberIds)) {
                http_response_code(400);
                echo json_encode(['message' => 'Tidak ada siswa valid yang dipilih.']);
                exit;
            }

            // If dariKelasId is provided, verify that selected members belong to that class
            if ($dariKelasId !== null) {
                $stmt = $mysqli->prepare("SELECT ID FROM members WHERE ID = ? AND Kelas_id = ?");
                foreach ($validMemberIds as $memberId) {
                    $stmt->bind_param("ii", $memberId, $dariKelasId);
                    $stmt->execute();
                    if ($stmt->get_result()->num_rows === 0) {
                        http_response_code(400);
                        echo json_encode(['message' => 'Satu atau lebih siswa tidak termasuk dalam kelas asal yang dipilih.']);
                        exit;
                    }
                }
            }

            // Update members' Kelas_id
            $stmt = $mysqli->prepare("UPDATE members SET Kelas_id = ? WHERE ID = ?");
            $successCount = 0;
            foreach ($validMemberIds as $memberId) {
                $stmt->bind_param("ii", $keKelasId, $memberId);
                if ($stmt->execute()) {
                    $successCount++;
                }
            }

            if ($successCount === count($validMemberIds)) {
                echo json_encode(['message' => sprintf('%d siswa berhasil dipindahkan ke kelas tujuan.', $successCount)]);
            } else {
                http_response_code(500);
                echo json_encode(['message' => sprintf('Berhasil memindahkan %d dari %d siswa.', $successCount, count($validMemberIds))]);
            }
            break;

            case 'beri_kelas':
            $keKelasId = isset($input['keKelasId']) ? (int)$input['keKelasId'] : 0;
            $memberIds = isset($input['memberIds']) && is_array($input['memberIds']) ? $input['memberIds'] : [];

            if ($keKelasId <= 0) {
                http_response_code(400);
                echo json_encode(['message' => 'Kelas tujuan tidak valid.']);
                exit;
            }

            if (empty($memberIds)) {
                http_response_code(400);
                echo json_encode(['message' => 'Harap pilih minimal satu siswa.']);
                exit;
            }

            // Validate if keKelasId exists
            $stmt = $mysqli->prepare("SELECT id FROM kelas_siswa WHERE id = ?");
            $stmt->bind_param("i", $keKelasId);
            $stmt->execute();
            if ($stmt->get_result()->num_rows === 0) {
                http_response_code(400);
                echo json_encode(['message' => 'Kelas tujuan tidak ditemukan.']);
                exit;
            }

            // Validate member IDs
            $validMemberIds = [];
            $stmt = $mysqli->prepare("SELECT ID FROM members WHERE ID = ?");
            foreach ($memberIds as $memberId) {
                $stmt->bind_param("i", $memberId);
                $stmt->execute();
                if ($stmt->get_result()->num_rows > 0) {
                    $validMemberIds[] = (int)$memberId;
                }
            }

            if (empty($validMemberIds)) {
                http_response_code(400);
                echo json_encode(['message' => 'Tidak ada siswa valid yang dipilih.']);
                exit;
            }

            // Update members' Kelas_id (beri kelas)
            $stmt = $mysqli->prepare("UPDATE members SET Kelas_id = ? WHERE ID = ?");
            $successCount = 0;
            foreach ($validMemberIds as $memberId) {
                $stmt->bind_param("ii", $keKelasId, $memberId);
                if ($stmt->execute()) {
                    $successCount++;
                }
            }

            if ($successCount === count($validMemberIds)) {
                echo json_encode(['message' => sprintf('%d siswa berhasil diberi kelas.', $successCount)]);
            } else {
                http_response_code(500);
                echo json_encode(['message' => sprintf('Berhasil memberi kelas pada %d dari %d siswa.', $successCount, count($validMemberIds))]);
            }
            break;

        default:
            http_response_code(400);
            echo json_encode(['message' => 'Aksi tidak valid.']);
            break;
    }
    exit;
}
?>