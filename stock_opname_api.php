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
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['message' => 'Koneksi database gagal: ' . $mysqli->connect_error]);
    exit;
}

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Form submission for adding stock opname
    if (isset($_POST['add_stock_opname'])) {
        if (!isset($_SESSION['user_id'])) {
            header('Location: views/stock_opname.php?error=Harap login terlebih dahulu.');
            exit;
        }

        $project_name = isset($_POST['project_name']) ? trim($mysqli->real_escape_string($_POST['project_name'])) : '';
        $tahun = isset($_POST['tahun']) ? (int)$_POST['tahun'] : 0;
        $koordinator = isset($_POST['koordinator']) ? trim($mysqli->real_escape_string($_POST['koordinator'])) : '';
        $tgl_mulai = !empty($_POST['tgl_mulai']) ? $mysqli->real_escape_string($_POST['tgl_mulai']) : null;
        $keterangan = !empty($_POST['keterangan']) ? trim($mysqli->real_escape_string($_POST['keterangan'])) : null;
        $create_by = $_SESSION['user_id'];
        $create_date = date('Y-m-d H:i:s');
        $create_terminal = isset($_SERVER['REMOTE_ADDR']) ? $mysqli->real_escape_string($_SERVER['REMOTE_ADDR']) : 'unknown';

        if (empty($project_name) || $tahun <= 0 || empty($koordinator)) {
            header('Location: views/stock_opname.php?error=Semua kolom wajib diisi.');
            exit;
        }

        $stmt = $mysqli->prepare("
            INSERT INTO stockopname (ProjectName, Tahun, Koordinator, TglMulai, Keterangan, CreateBy, CreateDate, CreateTerminal)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param(
            "sisssiss",
            $project_name, $tahun, $koordinator, $tgl_mulai, $keterangan, $create_by, $create_date, $create_terminal
        );
        if ($stmt->execute()) {
            header('Location: views/stock_opname.php?success=Stock opname berhasil ditambahkan.');
        } else {
            header('Location: views/stock_opname.php?error=Gagal menambahkan stock opname: ' . $mysqli->error);
        }
        $stmt->close();
        exit;
    }

    // JSON API requests
    header('Content-Type: application/json');
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input || !isset($input['action'])) {
        http_response_code(400);
        echo json_encode(['message' => 'Permintaan tidak valid.']);
        exit;
    }

    switch ($input['action']) {
        case 'get_detail':
            $project_id = isset($input['project_id']) ? (int)$input['project_id'] : 0;
            $unverified_limit = isset($input['unverified_limit']) ? $input['unverified_limit'] : 20;
            $unverified_page = isset($input['unverified_page']) ? (int)$input['unverified_page'] : 1;
            $results_limit = isset($input['results_limit']) ? $input['results_limit'] : 20;
            $results_page = isset($input['results_page']) ? (int)$input['results_page'] : 1;
            $category_id = isset($input['category_id']) && $input['category_id'] !== 'all' ? (int)$input['category_id'] : null;

            if ($project_id <= 0) {
                http_response_code(400);
                echo json_encode(['message' => 'ID proyek tidak valid.']);
                exit;
            }

            // Get project details
            $stmt = $mysqli->prepare("SELECT ProjectName, Tahun FROM stockopname WHERE ID = ?");
            $stmt->bind_param("i", $project_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $project = $result->fetch_assoc();
            $stmt->close();
            if (!$project) {
                http_response_code(404);
                echo json_encode(['message' => 'Proyek tidak ditemukan.']);
                exit;
            }

            // Calculate pagination for unverified collections
            $unverified_offset = ($unverified_page - 1) * ($unverified_limit === 'all' ? PHP_INT_MAX : $unverified_limit);
            $query = "
    SELECT COUNT(*) as total
    FROM collections c
    WHERE NOT EXISTS (
        SELECT 1 
        FROM stockopnamedetail sod
        WHERE sod.CollectionID = c.ID AND sod.StockOpnameID = ?
    )
";
if ($category_id !== null) {
    $query .= " AND c.Category_id = ?";
}
$stmt = $mysqli->prepare($query);
if ($category_id !== null) {
    $stmt->bind_param("ii", $project_id, $category_id);
} else {
    $stmt->bind_param("i", $project_id);
}
            $stmt->execute();
            $unverified_total = $stmt->get_result()->fetch_assoc()['total'];
            $stmt->close();
            $unverified_total_pages = $unverified_limit === 'all' ? 1 : ceil($unverified_total / $unverified_limit);

            // Get unverified collections
            $query = "
    SELECT DISTINCT c.NomorBarcode, c.TanggalPengadaan, c.NoInduk, c.CallNumber, 
           m.Name AS Media, s.Name AS Source, cat.Name AS Category, r.Name AS Akses, 
           st.Name AS Status, ll.Name AS LokasiPerpustakaan, l.Name AS Lokasi, c.ISOPAC
    FROM collections c
    LEFT JOIN collectionmedias m ON c.Media_id = m.ID
    LEFT JOIN collectionsources s ON c.Source_id = s.ID
    LEFT JOIN collectioncategorys cat ON c.Category_id = cat.ID
    LEFT JOIN collectionrules r ON c.Rule_id = r.ID
    LEFT JOIN collectionstatus st ON c.Status_id = st.ID
    LEFT JOIN location_library ll ON c.Location_Library_id = ll.ID
    LEFT JOIN locations l ON c.Location_id = l.ID
    WHERE NOT EXISTS (
        SELECT 1 
        FROM stockopnamedetail sod
        WHERE sod.CollectionID = c.ID AND sod.StockOpnameID = ?
    )
";
if ($category_id !== null) {
    $query .= " AND c.Category_id = ?";
}
$query .= " ORDER BY c.NomorBarcode ASC";
if ($unverified_limit !== 'all') {
    $query .= " LIMIT ? OFFSET ?";
}
$stmt = $mysqli->prepare($query);
if ($category_id !== null && $unverified_limit !== 'all') {
    $stmt->bind_param("iiii", $project_id, $category_id, $unverified_limit, $unverified_offset);
} elseif ($category_id !== null) {
    $stmt->bind_param("ii", $project_id, $category_id);
} elseif ($unverified_limit !== 'all') {
    $stmt->bind_param("iii", $project_id, $unverified_limit, $unverified_offset);
} else {
    $stmt->bind_param("i", $project_id);
}
            $stmt->execute();
            $result = $stmt->get_result();
            $unverified_html = "";
            $no = ($unverified_page - 1) * ($unverified_limit === 'all' ? $unverified_total : $unverified_limit) + 1;
            while ($row = $result->fetch_assoc()) {
                $unverified_html .= "<tr class='hover:bg-gray-50'>";
                $unverified_html .= "<td class='px-4 py-3 text-sm text-gray-900'>$no</td>";
                $unverified_html .= "<td class='px-4 py-3 text-sm text-gray-900'>" . htmlspecialchars($row['NomorBarcode']) . "</td>";
                $unverified_html .= "<td class='px-4 py-3 text-sm text-gray-900'>" . ($row['TanggalPengadaan'] ? htmlspecialchars($row['TanggalPengadaan']) : '-') . "</td>";
                $unverified_html .= "<td class='px-4 py-3 text-sm text-gray-900'>" . ($row['NoInduk'] ? htmlspecialchars($row['NoInduk']) : '-') . "</td>";
                $unverified_html .= "<td class='px-4 py-3 text-sm text-gray-900'>" . ($row['CallNumber'] ? htmlspecialchars($row['CallNumber']) : '-') . "</td>";
                $unverified_html .= "<td class='px-4 py-3 text-sm text-gray-900'>" . ($row['Media'] ? htmlspecialchars($row['Media']) : '-') . "</td>";
                $unverified_html .= "<td class='px-4 py-3 text-sm text-gray-900'>" . ($row['Source'] ? htmlspecialchars($row['Source']) : '-') . "</td>";
                $unverified_html .= "<td class='px-4 py-3 text-sm text-gray-900'>" . ($row['Category'] ? htmlspecialchars($row['Category']) : '-') . "</td>";
                $unverified_html .= "<td class='px-4 py-3 text-sm text-gray-900'>" . ($row['Akses'] ? htmlspecialchars($row['Akses']) : '-') . "</td>";
                $unverified_html .= "<td class='px-4 py-3 text-sm text-gray-900'>" . ($row['Status'] ? htmlspecialchars($row['Status']) : '-') . "</td>";
                $unverified_html .= "<td class='px-4 py-3 text-sm text-gray-900'>" . ($row['LokasiPerpustakaan'] ? htmlspecialchars($row['LokasiPerpustakaan']) : '-') . "</td>";
                $unverified_html .= "<td class='px-4 py-3 text-sm text-gray-900'>" . ($row['Lokasi'] ? htmlspecialchars($row['Lokasi']) : '-') . "</td>";
                $unverified_html .= "<td class='px-4 py-3 text-sm text-gray-900'>" . ($row['ISOPAC'] ? 'Ya' : 'Tidak') . "</td>";
                $unverified_html .= "</tr>";
                $no++;
            }
            if ($no == 1) {
                $unverified_html = '<tr><td colspan="13" class="px-6 py-12 text-center text-gray-500"><i class="fas fa-check-circle text-4xl mb-4 text-green-300"></i><div class="text-lg font-medium">Semua koleksi sudah diperiksa</div></td></tr>';
            }
            $stmt->close();

            // Calculate pagination for stock opname results
            $results_offset = ($results_page - 1) * ($results_limit === 'all' ? PHP_INT_MAX : $results_limit);
            $query = "
                SELECT COUNT(*) as total
                FROM stockopnamedetail sd
                LEFT JOIN collections c ON sd.CollectionID = c.ID
                WHERE sd.StockOpnameID = ?
            ";
            if ($category_id !== null) {
                $query .= " AND c.Category_id = ?";
            }
            $stmt = $mysqli->prepare($query);
            if ($category_id !== null) {
                $stmt->bind_param("ii", $project_id, $category_id);
            } else {
                $stmt->bind_param("i", $project_id);
            }
            $stmt->execute();
            $results_total = $stmt->get_result()->fetch_assoc()['total'];
            $stmt->close();

            $verified_recap = [];
$query_verified = "
    SELECT cat.Name AS Category, COUNT(*) AS jumlah
    FROM stockopnamedetail sod
    LEFT JOIN collections c ON sod.CollectionID = c.ID
    LEFT JOIN collectioncategorys cat ON c.Category_id = cat.ID
    WHERE sod.StockOpnameID = ?
    GROUP BY cat.Name
";
$stmt = $mysqli->prepare($query_verified);
$stmt->bind_param("i", $project_id);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $verified_recap[$row['Category'] ?: 'Tanpa Kategori'] = (int)$row['jumlah'];
}
$stmt->close();

// --- REKAP BELUM DIPERIKSA (UNVERIFIED) PER KATEGORI ---
$unverified_recap = [];
$query_unverified = "
    SELECT cat.Name AS Category, COUNT(*) AS jumlah
    FROM collections c
    LEFT JOIN collectioncategorys cat ON c.Category_id = cat.ID
    WHERE NOT EXISTS (
        SELECT 1 FROM stockopnamedetail sod
        WHERE sod.CollectionID = c.ID AND sod.StockOpnameID = ?
    )
";
if ($category_id !== null) {
    $query_unverified .= " AND c.Category_id = ?";
}
$query_unverified .= " GROUP BY cat.Name";
if ($category_id !== null) {
    $stmt = $mysqli->prepare($query_unverified);
    $stmt->bind_param("ii", $project_id, $category_id);
} else {
    $stmt = $mysqli->prepare($query_unverified);
    $stmt->bind_param("i", $project_id);
}
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $unverified_recap[$row['Category'] ?: 'Tanpa Kategori'] = (int)$row['jumlah'];
}
$stmt->close();

            $results_total_pages = $results_limit === 'all' ? 1 : ceil($results_total / $results_limit);

            // Get stock opname results
            $query = "
                SELECT sd.CreateDate, c.NomorBarcode, c.NoInduk, cat.Title AS Judul, 
                       cat.Author AS Pengarang, cat.Publisher AS Penerbit, pl.Name AS PrevLocation, 
                       cl.Name AS CurrentLocation, ps.Name AS PrevStatus, cs.Name AS CurrentStatus,
                       pr.Name AS PrevRule, cr.Name AS CurrentRule, u.Fullname AS User
                FROM stockopnamedetail sd
                LEFT JOIN collections c ON sd.CollectionID = c.ID
                LEFT JOIN catalogs cat ON c.Catalog_id = cat.ID
                LEFT JOIN locations pl ON sd.PrevLocationID = pl.ID
                LEFT JOIN locations cl ON sd.CurrentLocationID = cl.ID
                LEFT JOIN collectionstatus ps ON sd.PrevStatusID = ps.ID
                LEFT JOIN collectionstatus cs ON sd.CurrentStatusID = cs.ID
                LEFT JOIN collectionrules pr ON sd.PrevCollectionRuleID = pr.ID
                LEFT JOIN collectionrules cr ON sd.CurrentCollectionRuleID = cr.ID
                LEFT JOIN users u ON sd.CreateBy = u.ID
                WHERE sd.StockOpnameID = ?
            ";
            if ($category_id !== null) {
                $query .= " AND c.Category_id = ?";
            }
            $query .= " ORDER BY sd.CreateDate DESC";
            if ($results_limit !== 'all') {
                $query .= " LIMIT ? OFFSET ?";
            }
            $stmt = $mysqli->prepare($query);
            if ($category_id !== null && $results_limit !== 'all') {
                $stmt->bind_param("iiii", $project_id, $category_id, $results_limit, $results_offset);
            } elseif ($category_id !== null) {
                $stmt->bind_param("ii", $project_id, $category_id);
            } elseif ($results_limit !== 'all') {
                $stmt->bind_param("iii", $project_id, $results_limit, $results_offset);
            } else {
                $stmt->bind_param("i", $project_id);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            $results_html = "";
            $no = ($results_page - 1) * ($results_limit === 'all' ? $results_total : $results_limit) + 1;
            while ($row = $result->fetch_assoc()) {
                $results_html .= "<tr class='hover:bg-gray-50'>";
                $results_html .= "<td class='px-4 py-3 text-sm text-gray-900'>$no</td>";
                $results_html .= "<td class='px-4 py-3 text-sm text-gray-900'>" . ($row['CreateDate'] ? htmlspecialchars($row['CreateDate']) : '-') . "</td>";
                $results_html .= "<td class='px-4 py-3 text-sm text-gray-900'>" . htmlspecialchars($row['NomorBarcode']) . "</td>";
                $results_html .= "<td class='px-4 py-3 text-sm text-gray-900'>" . ($row['NoInduk'] ? htmlspecialchars($row['NoInduk']) : '-') . "</td>";
                $results_html .= "<td class='px-4 py-3 text-sm text-gray-900'>" . ($row['Judul'] ? htmlspecialchars($row['Judul']) : '-') . "</td>";
                $results_html .= "<td class='px-4 py-3 text-sm text-gray-900'>" . ($row['Pengarang'] ? htmlspecialchars($row['Pengarang']) : '-') . "</td>";
                $results_html .= "<td class='px-4 py-3 text-sm text-gray-900'>" . ($row['Penerbit'] ? htmlspecialchars($row['Penerbit']) : '-') . "</td>";
                $results_html .= "<td class='px-4 py-3 text-sm text-gray-900'>" . ($row['PrevLocation'] ? htmlspecialchars($row['PrevLocation']) : '-') . "</td>";
                $results_html .= "<td class='px-4 py-3 text-sm text-gray-900'>" . ($row['CurrentLocation'] ? htmlspecialchars($row['CurrentLocation']) : '-') . "</td>";
                $results_html .= "<td class='px-4 py-3 text-sm text-gray-900'>" . ($row['PrevStatus'] ? htmlspecialchars($row['PrevStatus']) : '-') . "</td>";
                $results_html .= "<td class='px-4 py-3 text-sm text-gray-900'>" . ($row['CurrentStatus'] ? htmlspecialchars($row['CurrentStatus']) : '-') . "</td>";
                $results_html .= "<td class='px-4 py-3 text-sm text-gray-900'>" . ($row['PrevRule'] ? htmlspecialchars($row['PrevRule']) : '-') . "</td>";
                $results_html .= "<td class='px-4 py-3 text-sm text-gray-900'>" . ($row['CurrentRule'] ? htmlspecialchars($row['CurrentRule']) : '-') . "</td>";
                $results_html .= "<td class='px-4 py-3 text-sm text-gray-900'>" . ($row['User'] ? htmlspecialchars($row['User']) : '-') . "</td>";
                $results_html .= "</tr>";
                $no++;
            }
            if ($no == 1) {
                $results_html = '<tr><td colspan="14" class="px-6 py-12 text-center text-gray-500"><i class="fas fa-inbox text-4xl mb-4 text-gray-300"></i><div class="text-lg font-medium">Belum ada hasil stock opname</div></td></tr>';
            }
            $stmt->close();

            echo json_encode([
    'project_name' => $project['ProjectName'],
    'tahun' => $project['Tahun'],
    'unverified_html' => $unverified_html,
    'unverified_count' => $unverified_total,
    'unverified_total_pages' => $unverified_total_pages,
    'results_html' => $results_html,
    'results_count' => $results_total,
    'results_total_pages' => $results_total_pages,
    // --- TAMBAHKAN REKAP DI RESPONSE ---
    'recap' => [
        'verified' => $verified_recap,
        'unverified' => $unverified_recap
    ]
]);
            break;



        case 'submit_barcode':
            error_reporting(0);
            header('Content-Type: application/json');

            if (!isset($_SESSION['user_id'])) {
                http_response_code(401);
                echo json_encode(['message' => 'Harap login terlebih dahulu.']);
                exit;
            }

            $project_id = isset($input['project_id']) ? (int)$input['project_id'] : 0;
            $barcode = isset($input['barcode']) ? trim($mysqli->real_escape_string($input['barcode'])) : '';

            if ($project_id <= 0 || empty($barcode)) {
                http_response_code(400);
                echo json_encode(['message' => 'ID proyek atau barcode tidak valid.']);
                exit;
            }

            try {
                $mysqli->begin_transaction();

                $stmt = $mysqli->prepare("
                    SELECT ID, Location_id, Status_id, Rule_id 
                    FROM collections 
                    WHERE NomorBarcode = ?
                ");
                $stmt->bind_param("s", $barcode);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows === 0) {
                    throw new Exception('Barcode tidak ditemukan.');
                }

                $collection = $result->fetch_assoc();
                $collection_id = $collection['ID'];

                $stmt = $mysqli->prepare("
                    SELECT 1 
                    FROM stockopnamedetail 
                    WHERE CollectionID = ? AND StockOpnameID = ?
                ");
                $stmt->bind_param("ii", $collection_id, $project_id);
                $stmt->execute();

                if ($stmt->get_result()->num_rows > 0) {
                    throw new Exception('Barcode sudah discan dalam proyek ini.');
                }

                $stmt = $mysqli->prepare("
                    INSERT INTO stockopnamedetail (
                        StockOpnameID, CollectionID, 
                        PrevLocationID, CurrentLocationID,
                        PrevStatusID, CurrentStatusID,
                        PrevCollectionRuleID, CurrentCollectionRuleID,
                        CreateBy, CreateDate, CreateTerminal
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)
                ");

                $terminal = $_SERVER['REMOTE_ADDR'];
                $stmt->bind_param(
                    "iiiiiiiiis",
                    $project_id, $collection_id,
                    $collection['Location_id'], $collection['Location_id'],
                    $collection['Status_id'], $collection['Status_id'],
                    $collection['Rule_id'], $collection['Rule_id'],
                    $_SESSION['user_id'], $terminal
                );

                if (!$stmt->execute()) {
                    throw new Exception('Gagal menyimpan data scan.');
                }

                $mysqli->commit();
                
                echo json_encode([
                    'message' => 'Barcode submitted successfully',
                    'collection_id' => $collection_id
                ]);

            } catch (Exception $e) {
                $mysqli->rollback();
                http_response_code(400);
                echo json_encode(['message' => $e->getMessage()]);
            }
            $stmt->close();
            break;

        default:
            http_response_code(400);
            echo json_encode(['message' => 'Aksi tidak valid.']);
            break;
    }
    exit;
}
?>