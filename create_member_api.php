<?php

header('Content-Type: application/json');
ini_set('display_errors', 0);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Database configuration
$db_host = '127.0.0.1';
$db_user = 'root';
$db_pass = '';
$db_name = 'inlislite_v3';
$db_port = 3309;

// Create database connection
$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name, $db_port);

// Check connection
if ($mysqli->connect_error) {
    http_response_code(500);
    echo json_encode(['message' => 'Koneksi database gagal: ' . $mysqli->connect_error]);
    exit;
}

// Check if user is logged in
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['message' => 'Harap login terlebih dahulu.']);
    exit;
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Validate required fields
        $required_fields = [
            'IdentityType_id', 'IdentityNo', 'Fullname', 'DateOfBirth',
            'Sex_id', 'JenisAnggota_id', 'StatusAnggota_id'
        ];
        
        foreach ($required_fields as $field) {
            if (!isset($_POST[$field]) || empty($_POST[$field])) {
                throw new Exception("Field $field diperlukan.");
            }
        }

        // Validate Job_id first if provided
        $job = null;
        if (isset($_POST['Job_id']) && !empty($_POST['Job_id'])) {
            $check_job = $mysqli->prepare("SELECT id FROM master_pekerjaan WHERE id = ?");
            $job_id = (int)$_POST['Job_id'];
            $check_job->bind_param("i", $job_id);
            $check_job->execute();
            $result = $check_job->get_result();
            
            if ($result->num_rows > 0) {
                $job = $job_id;
            }
            $check_job->close();
        }

        // Generate member number
        $member_no = 'M' . date('Ym') . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
        $create_terminal = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'unknown';

        // Prepare variables for binding
        $identity_type = (int)$_POST['IdentityType_id'];
        $identity_no = $_POST['IdentityNo'];
        $fullname = $_POST['Fullname'];
        $place_of_birth = isset($_POST['PlaceOfBirth']) ? $_POST['PlaceOfBirth'] : '';
        $date_of_birth = $_POST['DateOfBirth'];
        $address = isset($_POST['Address']) ? $_POST['Address'] : '';
        $phone = isset($_POST['Phone']) ? $_POST['Phone'] : '';
        $email = isset($_POST['Email']) ? $_POST['Email'] : '';
        $education = null;
if (isset($_POST['EducationLevel_id']) && !empty($_POST['EducationLevel_id'])) {
    $check_education = $mysqli->prepare("SELECT id FROM master_pendidikan WHERE id = ?");
    $education_id = (int)$_POST['EducationLevel_id'];
    $check_education->bind_param("i", $education_id);
    $check_education->execute();
    $result = $check_education->get_result();
    
    if ($result->num_rows > 0) {
        $education = $education_id;
    } else {
        throw new Exception("ID Pendidikan tidak valid");
    }
    $check_education->close();
}
        $sex = (int)$_POST['Sex_id'];
        $marital = null;
        if (isset($_POST['MaritalStatus_id']) && !empty($_POST['MaritalStatus_id'])) {
            $check_marital = $mysqli->prepare("SELECT id FROM master_status_perkawinan WHERE id = ?");
            $marital_id = (int)$_POST['MaritalStatus_id'];
            $check_marital->bind_param("i", $marital_id);
            $check_marital->execute();
            $result = $check_marital->get_result();
            
            if ($result->num_rows > 0) {
                $marital = $marital_id;
            } else {
                throw new Exception("ID Status Perkawinan tidak valid");
            }
            $check_marital->close();
        }
        $member_type = (int)$_POST['JenisAnggota_id'];
        $status = (int)$_POST['StatusAnggota_id'];
        $create_by = (int)$_SESSION['user_id'];

        // Prepare the statement
        $stmt = $mysqli->prepare("INSERT INTO members (
            IdentityType_id, IdentityNo, Fullname, PlaceOfBirth, 
            DateOfBirth, Address, Phone, Email, EducationLevel_id, 
            Sex_id, MaritalStatus_id, Job_id, RegisterDate, EndDate,
            JenisAnggota_id, StatusAnggota_id, CreateBy, CreateDate,
            MemberNo, CreateTerminal
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
            NOW(), DATE_ADD(NOW(), INTERVAL 1 YEAR),
            ?, ?, ?, NOW(), ?, ?
        )");

        if (!$stmt) {
            throw new Exception($mysqli->error);
        }

        // Bind parameters
        $stmt->bind_param(
            "isssssssiiiiiiiss",
            $identity_type,    // IdentityType_id (i)
            $identity_no,      // IdentityNo (s)
            $fullname,         // Fullname (s)
            $place_of_birth,   // PlaceOfBirth (s)
            $date_of_birth,    // DateOfBirth (s)
            $address,          // Address (s)
            $phone,            // Phone (s)
            $email,           // Email (s)
            $education,       // EducationLevel_id (i)
            $sex,             // Sex_id (i)
            $marital,         // MaritalStatus_id (i)
            $job,             // Job_id (i)
            $member_type,     // JenisAnggota_id (i)
            $status,          // StatusAnggota_id (i)
            $create_by,       // CreateBy (i)
            $member_no,       // MemberNo (s)
            $create_terminal  // CreateTerminal (s)
        );

        // Execute the statement
        if (!$stmt->execute()) {
            throw new Exception($stmt->error);
        }

        // Return success response
        echo json_encode([
            'status' => 'success',
            'message' => 'Member berhasil ditambahkan.',
            'member_no' => $member_no
        ]);

    } else {
        throw new Exception('Method not allowed');
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Gagal menambahkan member: ' . $e->getMessage()
    ]);
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    $mysqli->close();
}
?>