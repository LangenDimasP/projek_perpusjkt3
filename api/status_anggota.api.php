<?php
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
    die(json_encode(array('error' => 'Koneksi database gagal: ' . $e->getMessage())));
}

// Header untuk JSON response
header('Content-Type: application/json');

// Ambil action dari GET/POST
$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : '');

switch ($action) {
    case 'get_members':
        $status_id = isset($_GET['status_id']) ? intval($_GET['status_id']) : 0;
        $kelas_id = isset($_GET['kelas_id']) ? intval($_GET['kelas_id']) : 0;
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    
        $query = "
            SELECT m.ID, m.Fullname, m.MemberNo, s.Nama
            FROM members m
            JOIN status_anggota s ON m.StatusAnggota_id = s.id
            WHERE m.StatusAnggota_id = ?
        ";
        $params = [$status_id];
    
        if ($kelas_id > 0) {
            $query .= " AND m.Kelas_id = ?";
            $params[] = $kelas_id;
        }
        if ($search !== '') {
            $query .= " AND m.Fullname LIKE ?";
            $params[] = '%' . $search . '%';
        }
    
        if ($status_id > 0) {
            $stmt = $db->prepare($query);
            $stmt->execute($params);
            $members = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            $result = [];
            foreach ($members as $member) {
                $result[] = [
                    'ID' => $member['ID'],
                    'Fullname' => $member['Fullname'],
                    'MemberNo' => $member['MemberNo'],
                    'StatusAnggota' => ['Nama' => $member['Nama']]
                ];
            }
            echo json_encode($result);
        } else {
            echo json_encode([]);
        }
        break;

    case 'update_status':
        $input = json_decode(file_get_contents('php://input'), true);
        $selected_members = isset($input['selected_members']) ? $input['selected_members'] : array();
        $new_status = isset($input['new_status']) ? intval($input['new_status']) : 0;

        if (empty($selected_members) || $new_status === 0) {
            echo json_encode(array('success' => false, 'error' => 'Parameter tidak lengkap'));
            break;
        }

        try {
            $placeholders = implode(',', array_fill(0, count($selected_members), '?'));
            $stmt = $db->prepare("UPDATE members SET StatusAnggota_id = ? WHERE ID IN ($placeholders)");
            $params = array_merge(array($new_status), $selected_members);
            $stmt->execute($params);
            echo json_encode(array('success' => true));
        } catch (PDOException $e) {
            echo json_encode(array('success' => false, 'error' => $e->getMessage()));
        }
        break;

    default:
        echo json_encode(array('error' => 'Action tidak dikenal'));
        break;
}