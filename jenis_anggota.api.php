<?php
ini_set('display_errors', 0);
error_reporting(0);
session_start();
header('Content-Type: application/json');

// Database connection
$mysqli = new mysqli('127.0.0.1', 'root', '', 'inlislite_v3', 3309);
if ($mysqli->connect_error) {
    echo json_encode(array('success' => false, 'message' => 'Database connection failed'));
    exit();
}

// Check user session
if (!isset($_SESSION['user_id'])) {
    echo json_encode(array('success' => false, 'message' => 'Unauthorized'));
    exit();
}

$userId = $_SESSION['user_id'];
$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($action) {
    case 'get_jenis':
        // Fetch all jenis anggota for table
        $result = $mysqli->query("SELECT id, jenisanggota, MasaBerlakuAnggota, MaxPinjamKoleksi, MaxLoanDays FROM jenis_anggota ORDER BY id DESC");
        $data = array();
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        echo json_encode(array('success' => true, 'data' => $data));
        break;

    case 'add':
        // Handle adding new jenis anggota
        $input = json_decode(file_get_contents('php://input'), true);
        $jenisanggota = $mysqli->real_escape_string(isset($input['jenisanggota']) ? $input['jenisanggota'] : '');
        $masaberlaku = isset($input['masaberlaku']) ? (int)$input['masaberlaku'] : 365;
        $maxpinjam = isset($input['maxpinjam']) ? (int)$input['maxpinjam'] : 1000;
        $maxloandays = isset($input['maxloandays']) ? (int)$input['maxloandays'] : 0;
        $biayapendaftaran = isset($input['biayapendaftaran']) ? (int)$input['biayapendaftaran'] : 0;
        $biayaperpanjangan = isset($input['biayaperpanjangan']) ? (int)$input['biayaperpanjangan'] : 0;

        if (empty($jenisanggota)) {
            echo json_encode(array('success' => false, 'message' => 'Jenis anggota harus diisi'));
            exit();
        }

        $createDate = date('Y-m-d H:i:s');
        $createTerminal = $_SERVER['REMOTE_ADDR'];
        
        $query = "INSERT INTO jenis_anggota (
            jenisanggota, MasaBerlakuAnggota, MaxPinjamKoleksi, MaxLoanDays,
            BiayaPendaftaran, BiayaPerpanjangan,
            CreateBy, CreateDate, CreateTerminal
        ) VALUES (
            '$jenisanggota', $masaberlaku, $maxpinjam, $maxloandays,
            $biayapendaftaran, $biayaperpanjangan,
            $userId, '$createDate', '$createTerminal'
        )";

        if ($mysqli->query($query)) {
            echo json_encode(array('success' => true, 'message' => 'Jenis anggota berhasil ditambahkan'));
        } else {
            echo json_encode(array('success' => false, 'message' => 'Gagal menambahkan jenis anggota'));
        }
        break;

    case 'get_defaults':
        // Fetch default collection categories and locations
        $jenis_id = isset($_GET['jenis_id']) ? (int)$_GET['jenis_id'] : 0;
        if ($jenis_id === 0) {
            echo json_encode(array('success' => false, 'message' => 'Invalid jenis anggota ID'));
            exit();
        }

        // Fetch collection categories defaults
        $catQuery = $mysqli->query("SELECT CollectionCategory_id FROM collectioncategorysdefault WHERE JenisAnggota_id = $jenis_id");
        $categories = array();
        while ($row = $catQuery->fetch_assoc()) {
            $categories[] = $row['CollectionCategory_id'];
        }

        // Fetch location library defaults
        $locQuery = $mysqli->query("SELECT Location_Library_id FROM location_library_default WHERE JenisAnggota_id = $jenis_id");
        $locations = array();
        while ($row = $locQuery->fetch_assoc()) {
            $locations[] = $row['Location_Library_id'];
        }

        echo json_encode(array(
            'success' => true,
            'categories' => $categories,
            'locations' => $locations
        ));
        break;

    case 'save_kategori_defaults':
        // Save default collection categories
        $input = json_decode(file_get_contents('php://input'), true);
        $jenis_id = isset($input['jenis_id']) ? (int)$input['jenis_id'] : 0;
        $categories = isset($input['categories']) ? $input['categories'] : array();

        if ($jenis_id === 0) {
            echo json_encode(array('success' => false, 'message' => 'Invalid jenis anggota ID'));
            exit();
        }

        // Delete existing defaults
        $mysqli->query("DELETE FROM collectioncategorysdefault WHERE JenisAnggota_id = $jenis_id");

        // Insert new defaults
        $createDate = date('Y-m-d H:i:s');
        $createTerminal = $_SERVER['REMOTE_ADDR'];
        $success = true;

        foreach ($categories as $cat_id) {
            $cat_id = (int)$cat_id;
            $query = "INSERT INTO collectioncategorysdefault (
                CollectionCategory_id, JenisAnggota_id, CreateBy, CreateDate, CreateTerminal,
                UpdateBy, UpdateDate, UpdateTerminal
            ) VALUES (
                $cat_id, $jenis_id, $userId, '$createDate', '$createTerminal',
                $userId, '$createDate', '$createTerminal'
            )";
            if (!$mysqli->query($query)) {
                $success = false;
            }
        }

        echo json_encode(array(
            'success' => $success,
            'message' => $success ? 'Default kategori koleksi berhasil disimpan' : 'Gagal menyimpan default kategori'
        ));
        break;

    case 'save_lokasi_defaults':
        // Save default location libraries
        $input = json_decode(file_get_contents('php://input'), true);
        $jenis_id = isset($input['jenis_id']) ? (int)$input['jenis_id'] : 0;
        $locations = isset($input['locations']) ? $input['locations'] : array();

        if ($jenis_id === 0) {
            echo json_encode(array('success' => false, 'message' => 'Invalid jenis anggota ID'));
            exit();
        }

        // Delete existing defaults
        $mysqli->query("DELETE FROM location_library_default WHERE JenisAnggota_id = $jenis_id");

        // Insert new defaults
        $createDate = date('Y-m-d H:i:s');
        $createTerminal = $_SERVER['REMOTE_ADDR'];
        $success = true;

        foreach ($locations as $loc_id) {
            $loc_id = (int)$loc_id;
            $query = "INSERT INTO location_library_default (
                Location_Library_id, JenisAnggota_id, CreateBy, CreateDate, CreateTeminal,
                UpdateBy, UpdateDate, UpdateTerminal
            ) VALUES (
                $loc_id, $jenis_id, $userId, '$createDate', '$createTerminal',
                $userId, '$createDate', '$createTerminal'
            )";
            if (!$mysqli->query($query)) {
                $success = false;
            }
        }

        echo json_encode(array(
            'success' => $success,
            'message' => $success ? 'Default lokasi perpustakaan berhasil disimpan' : 'Gagal menyimpan default lokasi'
        ));
        break;

case 'get_all_unsynced_members':
    $members = [];
    $result = $mysqli->query("SELECT m.ID, m.Fullname, m.JenisAnggota_id, ja.jenisanggota AS JenisAnggota FROM members m LEFT JOIN jenis_anggota ja ON m.JenisAnggota_id = ja.id");
    while ($row = $result->fetch_assoc()) {
        $mid = (int)$row['ID'];
        $jenis_id = (int)$row['JenisAnggota_id'];
        // Ambil default
        $default_cats = [];
        $res = $mysqli->query("SELECT CollectionCategory_id FROM collectioncategorysdefault WHERE JenisAnggota_id = $jenis_id");
        while ($cat = $res->fetch_assoc()) $default_cats[] = $cat['CollectionCategory_id'];
        $default_locs = [];
        $res = $mysqli->query("SELECT Location_Library_id FROM location_library_default WHERE JenisAnggota_id = $jenis_id");
        while ($loc = $res->fetch_assoc()) $default_locs[] = $loc['Location_Library_id'];
        // Ambil data member (PERBAIKI DI SINI)
        $member_cats = [];
        $res = $mysqli->query("SELECT CategoryLoan_id FROM memberloanauthorizecategory WHERE Member_id = $member_id");
        while ($cat = $res->fetch_assoc()) $member_cats[] = $cat['CategoryLoan_id'];
        $member_locs = [];
        $res = $mysqli->query("SELECT LocationLoan_id FROM memberloanauthorizelocation WHERE Member_id = $member_id");
        while ($loc = $res->fetch_assoc()) $member_locs[] = $loc['LocationLoan_id'];
        // Cek apakah sudah sama
        if (array_diff($default_cats, $member_cats) || array_diff($default_locs, $member_locs)) {
            $members[] = $row;
        }
    }
    echo json_encode(['success' => true, 'members' => $members]);
    break;

case 'sync_all_members_with_default':
    $updated = 0;
    $failed = 0;
    $result = $mysqli->query("SELECT m.ID, m.JenisAnggota_id FROM members m");
    while ($row = $result->fetch_assoc()) {
        $jenis_id = (int)$row['JenisAnggota_id'];
        $member_id = (int)$row['ID'];
        // Ambil default master
        $default_cats = [];
        $res = $mysqli->query("SELECT CollectionCategory_id FROM collectioncategorysdefault WHERE JenisAnggota_id = $jenis_id");
        while ($cat = $res->fetch_assoc()) $default_cats[] = $cat['CollectionCategory_id'];
        $default_locs = [];
        $res = $mysqli->query("SELECT Location_Library_id FROM location_library_default WHERE JenisAnggota_id = $jenis_id");
        while ($loc = $res->fetch_assoc()) $default_locs[] = $loc['Location_Library_id'];
        // Ambil relasi anggota dari tabel asli
        $member_cats = [];
        $res = $mysqli->query("SELECT CategoryLoan_id FROM memberloanauthorizecategory WHERE Member_id = $member_id");
        while ($cat = $res->fetch_assoc()) $member_cats[] = $cat['CategoryLoan_id'];
        $member_locs = [];
        $res = $mysqli->query("SELECT LocationLoan_id FROM memberloanauthorizelocation WHERE Member_id = $member_id");
        while ($loc = $res->fetch_assoc()) $member_locs[] = $loc['LocationLoan_id'];

        // Hapus relasi kategori yang tidak sesuai
        $cats_to_delete = array_diff($member_cats, $default_cats);
        foreach ($cats_to_delete as $cat_id) {
            $cat_id = (int)$cat_id;
            $sql = "DELETE FROM memberloanauthorizecategory WHERE Member_id = $member_id AND CategoryLoan_id = $cat_id";
            if ($mysqli->query($sql)) $updated++; else $failed++;
        }
        // Hapus relasi lokasi yang tidak sesuai
        $locs_to_delete = array_diff($member_locs, $default_locs);
        foreach ($locs_to_delete as $loc_id) {
            $loc_id = (int)$loc_id;
            $sql = "DELETE FROM memberloanauthorizelocation WHERE Member_id = $member_id AND LocationLoan_id = $loc_id";
            if ($mysqli->query($sql)) $updated++; else $failed++;
        }

        // Tambahkan relasi kategori yang kurang
        $missing_cats = array_diff($default_cats, $member_cats);
        foreach ($missing_cats as $cat_id) {
            $cat_id = (int)$cat_id;
            $sql = "INSERT IGNORE INTO memberloanauthorizecategory (Member_id, CategoryLoan_id) VALUES ($member_id, $cat_id)";
            if ($mysqli->query($sql)) $updated++; else $failed++;
        }
        // Tambahkan relasi lokasi yang kurang
        $missing_locs = array_diff($default_locs, $member_locs);
        foreach ($missing_locs as $loc_id) {
            $loc_id = (int)$loc_id;
            $sql = "INSERT IGNORE INTO memberloanauthorizelocation (Member_id, LocationLoan_id) VALUES ($member_id, $loc_id)";
            if ($mysqli->query($sql)) $updated++; else $failed++;
        }
    }
    echo json_encode([
        'success' => true,
        'message' => "Sinkronisasi selesai. $updated relasi berhasil ditambahkan/dihapus." . ($failed > 0 ? " $failed gagal." : "")
    ]);
    break;

    case 'create_relasi_tables':
    $sql1 = "CREATE TABLE IF NOT EXISTS members_collectioncategorys (
        Member_id INT NOT NULL,
        CollectionCategory_id INT NOT NULL,
        PRIMARY KEY (Member_id, CollectionCategory_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
    $sql2 = "CREATE TABLE IF NOT EXISTS members_location_library (
        Member_id INT NOT NULL,
        Location_Library_id INT NOT NULL,
        PRIMARY KEY (Member_id, Location_Library_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
    $success1 = $mysqli->query($sql1);
    $success2 = $mysqli->query($sql2);
    if ($success1 && $success2) {
        echo json_encode(['success' => true, 'message' => 'Tabel relasi berhasil dibuat!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal membuat tabel relasi: ' . $mysqli->error]);
    }
    break;
    
    case 'cek_default_member':
        $member_no = isset($_GET['member_no']) ? $mysqli->real_escape_string($_GET['member_no']) : '';
        $result = $mysqli->query("SELECT ID, Fullname, JenisAnggota_id FROM members WHERE MemberNo = '$member_no' LIMIT 1");
        if ($row = $result->fetch_assoc()) {
            $mid = (int)$row['ID'];
            $jenis_id = (int)$row['JenisAnggota_id'];
            // Ambil default kategori dari jenis anggota
            $default_cats = [];
            $res = $mysqli->query("SELECT cc.ID, cc.Name FROM collectioncategorysdefault cd JOIN collectioncategorys cc ON cd.CollectionCategory_id = cc.ID WHERE cd.JenisAnggota_id = $jenis_id");
            while ($cat = $res->fetch_assoc()) $default_cats[] = ['Name' => $cat['Name'], 'Status' => 'Default'];
            // Ambil kategori yang sudah di anggota (jika tabel relasi sudah ada)
            $member_cats = [];
            if ($mysqli->query("SHOW TABLES LIKE 'members_collectioncategorys'")->num_rows) {
                $res = $mysqli->query("SELECT cc.Name FROM members_collectioncategorys mc JOIN collectioncategorys cc ON mc.CollectionCategory_id = cc.ID WHERE mc.Member_id = $mid");
                while ($cat = $res->fetch_assoc()) $member_cats[] = ['Name' => $cat['Name'], 'Status' => 'Sudah di anggota'];
            }
            // Gabungkan
            $categories = array_merge($default_cats, $member_cats);
    
            // Ambil default lokasi dari jenis anggota
            $default_locs = [];
            $res = $mysqli->query("SELECT ll.ID, ll.Name FROM location_library_default ld JOIN location_library ll ON ld.Location_Library_id = ll.ID WHERE ld.JenisAnggota_id = $jenis_id");
            while ($loc = $res->fetch_assoc()) $default_locs[] = ['Name' => $loc['Name'], 'Status' => 'Default'];
            // Ambil lokasi yang sudah di anggota (jika tabel relasi sudah ada)
            $member_locs = [];
            if ($mysqli->query("SHOW TABLES LIKE 'members_location_library'")->num_rows) {
                $res = $mysqli->query("SELECT ll.Name FROM members_location_library ml JOIN location_library ll ON ml.Location_Library_id = ll.ID WHERE ml.Member_id = $mid");
                while ($loc = $res->fetch_assoc()) $member_locs[] = ['Name' => $loc['Name'], 'Status' => 'Sudah di anggota'];
            }
            // Gabungkan
            $locations = array_merge($default_locs, $member_locs);
    
            echo json_encode(['success' => true, 'categories' => $categories, 'locations' => $locations]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Member tidak ditemukan']);
        }
        break;




    case 'get_members_need_default_sync':
        $members = [];
        $result = $mysqli->query("SELECT m.ID, m.MemberNo, m.Fullname, m.JenisAnggota_id, ja.jenisanggota AS JenisAnggota
            FROM members m
            LEFT JOIN jenis_anggota ja ON m.JenisAnggota_id = ja.id");
        while ($row = $result->fetch_assoc()) {
            $jenis_id = (int)$row['JenisAnggota_id'];
            $member_id = (int)$row['ID'];
            // Ambil default master
            $default_cats = [];
            $res = $mysqli->query("SELECT CollectionCategory_id FROM collectioncategorysdefault WHERE JenisAnggota_id = $jenis_id");
            while ($cat = $res->fetch_assoc()) $default_cats[] = $cat['CollectionCategory_id'];
            $default_locs = [];
            $res = $mysqli->query("SELECT Location_Library_id FROM location_library_default WHERE JenisAnggota_id = $jenis_id");
            while ($loc = $res->fetch_assoc()) $default_locs[] = $loc['Location_Library_id'];
            // Ambil relasi anggota dari tabel asli
            $member_cats = [];
            $res = $mysqli->query("SELECT CategoryLoan_id FROM memberloanauthorizecategory WHERE Member_id = $member_id");
            while ($cat = $res->fetch_assoc()) $member_cats[] = $cat['CategoryLoan_id'];
            $member_locs = [];
            $res = $mysqli->query("SELECT LocationLoan_id FROM memberloanauthorizelocation WHERE Member_id = $member_id");
            while ($loc = $res->fetch_assoc()) $member_locs[] = $loc['LocationLoan_id'];
            // Cek apakah default master dan relasi anggota SAMA PERSIS
            if (
                array_diff($default_cats, $member_cats) ||
                array_diff($member_cats, $default_cats) ||
                array_diff($default_locs, $member_locs) ||
                array_diff($member_locs, $default_locs)
            ) {
                $row['missing_default_categories'] = array_diff($default_cats, $member_cats);
                $row['missing_default_locations'] = array_diff($default_locs, $member_locs);
                $members[] = $row;
            }
        }
        echo json_encode(['success' => true, 'members' => $members]);
        break;
}

$mysqli->close();
?>