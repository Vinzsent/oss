<?php
session_start();
include('config.php');
include('includes/nav.php');


// Initialize data structures to avoid undefined variable warnings
// These can be populated later if/when detailed demographic data is added
$demographic_data = [];

// Handle Add Purok modal submission
$add_message = '';
$add_message_type = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_purok'])) {
    $barangay = $_POST['barangay'] ?? '';
    $sitio_purok = trim($_POST['sitio_purok'] ?? '');
    $total_families_in = isset($_POST['total_families']) ? (int)$_POST['total_families'] : 0;
    $total_persons_in = isset($_POST['total_persons']) ? (int)$_POST['total_persons'] : 0;
    $risk_level_in = isset($_POST['risk_level']) ? (int)$_POST['risk_level'] : 0;

    if ($sitio_purok === '' || $total_families_in <= 0 || $total_persons_in <= 0 || $barangay === '') {
        $add_message = 'Please complete all required fields with valid values.';
        $add_message_type = 'danger';
    } else {
        $check_sql = "SELECT id FROM socio_data WHERE barangay = ? AND sitio_purok = ?";
        if ($stmt = mysqli_prepare($conn, $check_sql)) {
            mysqli_stmt_bind_param($stmt, 'ss', $barangay, $sitio_purok);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            $exists = $res && mysqli_num_rows($res) > 0;
            mysqli_stmt_close($stmt);
        } else {
            $exists = false;
        }

// Build a map of flood_data by purok_name for editing
$floodByPurok = [];
$fd_all_sql = "SELECT purok_name, total_families, total_persons_male, total_persons_female, infant_male, infant_female, children_male, children_female, adult_male, adult_female, elderly_male, elderly_female, pwd_male, pwd_female, sickness_male, sickness_female, pregnant_women FROM flood_data";
$fd_all_res = mysqli_query($conn, $fd_all_sql);
if ($fd_all_res) {
    while ($r = mysqli_fetch_assoc($fd_all_res)) {
        $floodByPurok[$r['purok_name']] = $r;
    }
}

// Handle Edit modal submission for age-group and special categories
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_purok_age'])) {
    $purok_name = trim($_POST['purok_name'] ?? '');
    $fields = [
        'infant_male','infant_female','children_male','children_female',
        'adult_male','adult_female','elderly_male','elderly_female',
        'pwd_male','pwd_female','sickness_male','sickness_female','pregnant_women'
    ];
    $vals = [];
    foreach ($fields as $f) { $vals[$f] = isset($_POST[$f]) ? max(0, (int)$_POST[$f]) : 0; }
    if ($purok_name !== '') {
        // Upsert: if exists update; else insert with zeros for missing totals
        $exists = isset($floodByPurok[$purok_name]);
        if ($exists) {
            $upd = "UPDATE flood_data SET infant_male=?, infant_female=?, children_male=?, children_female=?, adult_male=?, adult_female=?, elderly_male=?, elderly_female=?, pwd_male=?, pwd_female=?, sickness_male=?, sickness_female=?, pregnant_women=? WHERE purok_name=?";
            if ($st = mysqli_prepare($conn, $upd)) {
                mysqli_stmt_bind_param($st, 'iiiiiiiiiiiiis', $vals['infant_male'],$vals['infant_female'],$vals['children_male'],$vals['children_female'],$vals['adult_male'],$vals['adult_female'],$vals['elderly_male'],$vals['elderly_female'],$vals['pwd_male'],$vals['pwd_female'],$vals['sickness_male'],$vals['sickness_female'],$vals['pregnant_women'],$purok_name);
                if (!mysqli_stmt_execute($st)) {
                    $add_message = 'Error updating data: ' . mysqli_error($conn);
                    $add_message_type = 'danger';
                } else {
                    $add_message = 'Purok data updated successfully.';
                    $add_message_type = 'success';
                }
                mysqli_stmt_close($st);
            }
        } else {
            $ins = "INSERT INTO flood_data (purok_name, total_families, total_persons_male, total_persons_female, infant_male, infant_female, children_male, children_female, adult_male, adult_female, elderly_male, elderly_female, pwd_male, pwd_female, sickness_male, sickness_female, pregnant_women) VALUES (?,0,0,0,?,?,?,?,?,?,?,?,?,?,?, ?,?)";
            if ($st = mysqli_prepare($conn, $ins)) {
                mysqli_stmt_bind_param($st, 'siiiiiiiiiiiiiii', $purok_name,$vals['infant_male'],$vals['infant_female'],$vals['children_male'],$vals['children_female'],$vals['adult_male'],$vals['adult_female'],$vals['elderly_male'],$vals['elderly_female'],$vals['pwd_male'],$vals['pwd_female'],$vals['sickness_male'],$vals['sickness_female'],$vals['pregnant_women']);
                if (!mysqli_stmt_execute($st)) {
                    $add_message = 'Error inserting data: ' . mysqli_error($conn);
                    $add_message_type = 'danger';
                } else {
                    $add_message = 'Purok data added successfully.';
                    $add_message_type = 'success';
                }
                mysqli_stmt_close($st);
            }
        }
    } else {
        $add_message = 'Invalid Purok name.';
        $add_message_type = 'danger';
    }
}

        if ($exists) {
            $add_message = 'This sitio/purok already exists in the selected barangay.';
            $add_message_type = 'warning';
        } else {
            $ins_sql = "INSERT INTO socio_data (barangay, sitio_purok, total_families, total_persons, risk_level) VALUES (?, ?, ?, ?, ?)";
            if ($ins = mysqli_prepare($conn, $ins_sql)) {
                mysqli_stmt_bind_param($ins, 'ssiii', $barangay, $sitio_purok, $total_families_in, $total_persons_in, $risk_level_in);
                if (mysqli_stmt_execute($ins)) {
                    $add_message = 'Purok added successfully.';
                    $add_message_type = 'success';
                } else {
                    $add_message = 'Error adding data: ' . mysqli_error($conn);
                    $add_message_type = 'danger';
                }
                mysqli_stmt_close($ins);
            } else {
                $add_message = 'Database error. Please try again later.';
                $add_message_type = 'danger';
            }
        }
    }
}
$totals = [
    'total_families' => 0,
    'total_persons_male' => 0,
    'total_persons_female' => 0,
    'infant_male' => 0,
    'infant_female' => 0,
    'children_male' => 0,
    'children_female' => 0,
    'adult_male' => 0,
    'adult_female' => 0,
    'elderly_male' => 0,
    'elderly_female' => 0,
    'pwd_male' => 0,
    'pwd_female' => 0,
    'sickness_male' => 0,
    'sickness_female' => 0,
    'pregnant_women' => 0,
];

// Fetch flood data
$flood_query = "SELECT * FROM floodwarning";
$flood_result = mysqli_query($conn, $flood_query);

// Fetch purok data for dropdown
$purok_query = "SELECT * FROM puroks_sitios WHERE type = 'purok' ORDER BY name";
$purok_result = mysqli_query($conn, $purok_query);

// Aggregate socio totals for cards
$agg_sql = "SELECT COUNT(*) AS puroks, COALESCE(SUM(total_families),0) AS total_families, COALESCE(SUM(total_persons),0) AS total_persons FROM socio_data";
$agg_res = mysqli_query($conn, $agg_sql);
if ($agg_res && mysqli_num_rows($agg_res) === 1) {
    $agg = mysqli_fetch_assoc($agg_res);
    $totals['total_families'] = (int)$agg['total_families'];
    $totals['total_persons_total'] = (int)$agg['total_persons'];
    $total_puroks = (int)$agg['puroks'];
}

// Prefer detailed demographic totals from flood_data if available
$flood_demo_sql = "
    SELECT 
        COUNT(DISTINCT purok_name) AS puroks,
        COALESCE(SUM(total_families),0) AS total_families,
        COALESCE(SUM(total_persons_male),0) AS total_persons_male,
        COALESCE(SUM(total_persons_female),0) AS total_persons_female,
        COALESCE(SUM(infant_male),0) AS infant_male,
        COALESCE(SUM(infant_female),0) AS infant_female,
        COALESCE(SUM(children_male),0) AS children_male,
        COALESCE(SUM(children_female),0) AS children_female,
        COALESCE(SUM(adult_male),0) AS adult_male,
        COALESCE(SUM(adult_female),0) AS adult_female,
        COALESCE(SUM(elderly_male),0) AS elderly_male,
        COALESCE(SUM(elderly_female),0) AS elderly_female,
        COALESCE(SUM(pwd_male),0) AS pwd_male,
        COALESCE(SUM(pwd_female),0) AS pwd_female,
        COALESCE(SUM(sickness_male),0) AS sickness_male,
        COALESCE(SUM(sickness_female),0) AS sickness_female,
        COALESCE(SUM(pregnant_women),0) AS pregnant_women
    FROM flood_data
";
$flood_demo_res = mysqli_query($conn, $flood_demo_sql);
if ($flood_demo_res && mysqli_num_rows($flood_demo_res) === 1) {
    $fd = mysqli_fetch_assoc($flood_demo_res);
    // Override with detailed totals
    $total_puroks = (int)$fd['puroks'];
    $totals['total_families'] = (int)$fd['total_families'];
    $totals['total_persons_male'] = (int)$fd['total_persons_male'];
    $totals['total_persons_female'] = (int)$fd['total_persons_female'];
    $totals['total_persons_total'] = $totals['total_persons_male'] + $totals['total_persons_female'];
    $totals['infant_male'] = (int)$fd['infant_male'];
    $totals['infant_female'] = (int)$fd['infant_female'];
    $totals['children_male'] = (int)$fd['children_male'];
    $totals['children_female'] = (int)$fd['children_female'];
    $totals['adult_male'] = (int)$fd['adult_male'];
    $totals['adult_female'] = (int)$fd['adult_female'];
    $totals['elderly_male'] = (int)$fd['elderly_male'];
    $totals['elderly_female'] = (int)$fd['elderly_female'];
    $totals['pwd_male'] = (int)$fd['pwd_male'];
    $totals['pwd_female'] = (int)$fd['pwd_female'];
    $totals['sickness_male'] = (int)$fd['sickness_male'];
    $totals['sickness_female'] = (int)$fd['sickness_female'];
    $totals['pregnant_women'] = (int)$fd['pregnant_women'];
}

// Build map of flood_data rows keyed by purok_name for the edit modal
$floodByPurok = [];
$fd_all_sql = "SELECT purok_name, total_families, total_persons_male, total_persons_female, infant_male, infant_female, children_male, children_female, adult_male, adult_female, elderly_male, elderly_female, pwd_male, pwd_female, sickness_male, sickness_female, pregnant_women FROM flood_data";
$fd_all_res = mysqli_query($conn, $fd_all_sql);
if ($fd_all_res) {
    while ($r = mysqli_fetch_assoc($fd_all_res)) {
        $floodByPurok[$r['purok_name']] = $r;
    }
}

// Handle Edit Purok modal submission to update age-group and special categories
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_purok_age'])) {
    $purok_name = trim($_POST['purok_name'] ?? '');
    $fields = [
        'infant_male','infant_female','children_male','children_female',
        'adult_male','adult_female','elderly_male','elderly_female',
        'pwd_male','pwd_female','sickness_male','sickness_female','pregnant_women'
    ];
    $vals = [];
    foreach ($fields as $f) { $vals[$f] = isset($_POST[$f]) ? max(0, (int)$_POST[$f]) : 0; }
    if ($purok_name !== '') {
        if (isset($floodByPurok[$purok_name])) {
            $upd = "UPDATE flood_data SET infant_male=?, infant_female=?, children_male=?, children_female=?, adult_male=?, adult_female=?, elderly_male=?, elderly_female=?, pwd_male=?, pwd_female=?, sickness_male=?, sickness_female=?, pregnant_women=? WHERE purok_name=?";
            if ($st = mysqli_prepare($conn, $upd)) {
                mysqli_stmt_bind_param($st, 'iiiiiiiiiiiiis', $vals['infant_male'],$vals['infant_female'],$vals['children_male'],$vals['children_female'],$vals['adult_male'],$vals['adult_female'],$vals['elderly_male'],$vals['elderly_female'],$vals['pwd_male'],$vals['pwd_female'],$vals['sickness_male'],$vals['sickness_female'],$vals['pregnant_women'],$purok_name);
                mysqli_stmt_execute($st);
                mysqli_stmt_close($st);
            }
        } else {
            $ins = "INSERT INTO flood_data (purok_name, total_families, total_persons_male, total_persons_female, infant_male, infant_female, children_male, children_female, adult_male, adult_female, elderly_male, elderly_female, pwd_male, pwd_female, sickness_male, sickness_female, pregnant_women) VALUES (?,0,0,0,?,?,?,?,?,?,?,?,?,?,?,?,?)";
            if ($st = mysqli_prepare($conn, $ins)) {
                mysqli_stmt_bind_param($st, 'siiiiiiiiiiiii', $purok_name,$vals['infant_male'],$vals['infant_female'],$vals['children_male'],$vals['children_female'],$vals['adult_male'],$vals['adult_female'],$vals['elderly_male'],$vals['elderly_female'],$vals['pwd_male'],$vals['pwd_female'],$vals['sickness_male'],$vals['sickness_female'],$vals['pregnant_women']);
                mysqli_stmt_execute($st);
                mysqli_stmt_close($st);
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purok Demographics - Micro Online Synthesis System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f5f5f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .main-container {
            max-width: 100%;
            margin: 0 auto;
            padding: 20px;
        }
        
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .page-title {
            font-size: 2.5rem;
            font-weight: bold;
            margin: 0;
            text-align: center;
        }
        
        .page-subtitle {
            font-size: 1.2rem;
            margin: 10px 0 0 0;
            text-align: center;
            opacity: 0.9;
        }
        
        .table-container {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            overflow-x: auto;
        }
        
        .demographic-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            min-width: 1800px;
        }
        
        .demographic-table thead {
            background-color: #8b5cf6;
            color: white;
        }
        
        .demographic-table th {
            padding: 15px 8px;
            text-align: center;
            font-weight: bold;
            font-size: 0.9rem;
            border: none;
            white-space: nowrap;
        }
        
        .demographic-table tbody tr {
            background-color: #fce7f3;
            transition: background-color 0.3s ease;
        }
        
        .demographic-table tbody tr:nth-child(even) {
            background-color: #fbcfe8;
        }
        
        .demographic-table tbody tr:hover {
            background-color: #f9a8d4;
            cursor: pointer;
        }
        
        .demographic-table tbody tr:last-child {
            background-color: #e9d5f7;
        }
        
        .demographic-table tbody tr:last-child:hover {
            background-color: #d8b4fe;
        }
        
        .demographic-table td {
            padding: 12px 8px;
            text-align: center;
            border: 1px solid #e9d5f7;
            font-weight: 500;
            color: #1f2937;
            font-size: 0.9rem;
        }
        
        .demographic-table tbody tr:last-child td {
            font-weight: bold;
            color: #6b21a8;
        }
        
        .purok-name {
            text-align: left !important;
            font-weight: 600;
        }
        
        .stats-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            border-left: 4px solid #8b5cf6;
        }
        
        .stats-card h5 {
            color: #8b5cf6;
            font-weight: bold;
            margin-bottom: 15px;
        }
        
        .stat-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #f3f4f6;
        }
        
        .stat-item:last-child {
            border-bottom: none;
        }
        
        .stat-label {
            color: #6b7280;
            font-size: 0.9rem;
        }
        
        .stat-value {
            font-weight: bold;
            color: #1f2937;
            font-size: 1.1rem;
        }
        
        .export-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            transition: transform 0.2s ease;
        }
        
        .export-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
        
        .back-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            transition: transform 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }
        
        .back-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
            color: white;
        }
        
        @media (max-width: 768px) {
            .main-container {
                padding: 10px;
            }
            
            .page-title {
                font-size: 1.8rem;
            }
            
            .table-container {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="page-header">
            <h1 class="page-title">
                <i class="fas fa-users me-3"></i>Purok Demographics
            </h1>
            <p class="page-subtitle">Detailed Population Statistics by Purok - Barangay Lizada</p>
            <?php if (!empty($add_message)): ?>
                <div class="alert alert-<?php echo $add_message_type; ?> mt-3" role="alert">
                    <?php echo htmlspecialchars($add_message); ?>
                </div>
            <?php endif; ?>
            <div class="mt-4 text-center">
                <a href="population.php" class="back-btn btn-lg px-4 py-3 shadow-sm hover-shadow transition-all text-decoration-none">
                    <i class="fas fa-arrow-left me-2"></i>
                    Back to Population Demographics
                </a>
            </div>
        </div>
        
        <!-- Flood Warning Data Table -->
        <div class="row mb-5">
            <div class="col-lg-12">
                <div class="table-container">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="mb-0">
                            <i class="fas fa-exclamation-triangle me-2 text-danger"></i>
                            Flood Warning Data
                        </h4>
                        <button class="export-btn" onclick="exportTable()">
                            <i class="fas fa-download me-1"></i>Export
                        </button>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="floodTable">
                            <thead class="table-dark">
                                <tr>
                                    <th>Date</th>
                                    <th>Barangay</th>
                                    <th>Location</th>
                                    <th>Warning Level</th>
                                    <th>Status</th>
                                    <th>Recommended Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (mysqli_num_rows($flood_result) > 0) {
                                    while ($row = mysqli_fetch_assoc($flood_result)) {
                                        $warning_class = '';
                                        switch($row['warning_level']) {
                                            case 1: $warning_class = 'bg-warning bg-opacity-25'; break;
                                            case 2: $warning_class = 'bg-warning bg-opacity-50'; break;
                                            case 3: $warning_class = 'bg-danger bg-opacity-50 text-white'; break;
                                        }
                                        echo "<tr class='{$warning_class}'>";
                                        echo "<td>" . date('M d, Y', strtotime($row['date_created'])) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['barangay']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['sitio']) . "</td>";
                                        echo "<td>" . $row['warning_level'] . "</td>";
                                        echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['recommended_action']) . "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='6' class='text-center'>No flood warning data available</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                                
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Add Purok Modal -->
        <div class="modal fade" id="addPurokModal" tabindex="-1" aria-labelledby="addPurokModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addPurokModalLabel"><i class="fas fa-plus me-2"></i>Add Purok</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="post">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="barangay" class="form-label">Barangay</label>
                                <select class="form-select" id="barangay" name="barangay" required>
                                    <option value="">Select Barangay</option>
                                    <option value="Lizada">Lizada</option>
                                    <option value="Daliao">Daliao</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="sitio_purok" class="form-label">Sitio/Purok Name</label>
                                <input type="text" class="form-control" id="sitio_purok" name="sitio_purok" required>
                            </div>
                            <div class="mb-3">
                                <label for="total_families" class="form-label">Total Families</label>
                                <input type="number" class="form-control" id="total_families" name="total_families" min="1" required>
                            </div>
                            <div class="mb-3">
                                <label for="total_persons" class="form-label">Total Persons</label>
                                <input type="number" class="form-control" id="total_persons" name="total_persons" min="1" required>
                            </div>
                            <div class="mb-3">
                                <label for="risk_level" class="form-label">Risk Level</label>
                                <select class="form-select" id="risk_level" name="risk_level">
                                    <option value="0">None</option>
                                    <option value="1">Low</option>
                                    <option value="2">Medium</option>
                                    <option value="3">High</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary" name="add_purok" value="1">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Purok Modal -->
        <div class="modal fade" id="editPurokModal" tabindex="-1" aria-labelledby="editPurokModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editPurokModalLabel"><i class="fas fa-edit me-2"></i>Edit Purok Demographics</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="post">
                        <div class="modal-body">
                            <input type="hidden" name="edit_purok_age" value="1">
                            <input type="hidden" id="edit_purok_name" name="purok_name">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Infant Male</label>
                                    <input type="number" class="form-control" id="infant_male" name="infant_male" min="0">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Infant Female</label>
                                    <input type="number" class="form-control" id="infant_female" name="infant_female" min="0">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Children Male</label>
                                    <input type="number" class="form-control" id="children_male" name="children_male" min="0">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Children Female</label>
                                    <input type="number" class="form-control" id="children_female" name="children_female" min="0">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Adult Male</label>
                                    <input type="number" class="form-control" id="adult_male" name="adult_male" min="0">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Adult Female</label>
                                    <input type="number" class="form-control" id="adult_female" name="adult_female" min="0">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Elderly Male</label>
                                    <input type="number" class="form-control" id="elderly_male" name="elderly_male" min="0">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Elderly Female</label>
                                    <input type="number" class="form-control" id="elderly_female" name="elderly_female" min="0">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">PWD Male</label>
                                    <input type="number" class="form-control" id="pwd_male" name="pwd_male" min="0">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">PWD Female</label>
                                    <input type="number" class="form-control" id="pwd_female" name="pwd_female" min="0">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">With Sickness Male</label>
                                    <input type="number" class="form-control" id="sickness_male" name="sickness_male" min="0">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">With Sickness Female</label>
                                    <input type="number" class="form-control" id="sickness_female" name="sickness_female" min="0">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Pregnant Women</label>
                                    <input type="number" class="form-control" id="pregnant_women" name="pregnant_women" min="0">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Demographic Data Table -->
        <div class="row">
            <div class="col-lg-12">
                <div class="table-container">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="mb-0">
                            <i class="fas fa-chart-bar me-2 text-purple"></i>
                            Demographic Data by Purok
                        </h4>
                        <div class="d-flex align-items-center">
                            <div class="input-group ms-3" style="max-width: 300px;">
                                <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                                <input type="text" class="form-control" id="purokSearch" placeholder="Search purok...">
                            </div>
                            <button class="btn btn-primary ms-3" data-bs-toggle="modal" data-bs-target="#addPurokModal">
                                <i class="fas fa-plus me-2"></i>Add Purok
                            </button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="demographicTable">
                            <thead class="table-dark">
                                <tr>
                                    <th>PUROK NAME</th>
                                    <th>TOTAL FAMILIES</th>
                                    <th>TOTAL PERSONS</th>
                                    <th>RISK LEVEL</th>
                                    <th>ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $perPage = 10;
                                $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
                                $count_q = "SELECT COUNT(*) AS cnt FROM socio_data";
                                $count_r = mysqli_query($conn, $count_q);
                                $total_rows = 0;
                                if ($count_r && mysqli_num_rows($count_r) === 1) {
                                    $total_rows = (int)mysqli_fetch_assoc($count_r)['cnt'];
                                }
                                $total_pages = max(1, (int)ceil($total_rows / $perPage));
                                if ($page > $total_pages) { $page = $total_pages; }
                                $offset = ($page - 1) * $perPage;
                                $socio_query = "SELECT * FROM socio_data ORDER BY barangay, sitio_purok LIMIT $perPage OFFSET $offset";
                                $socio_result = mysqli_query($conn, $socio_query);

                                if (mysqli_num_rows($socio_result) > 0) {
                                    while ($row = mysqli_fetch_assoc($socio_result)) {
                                        $risk_class = '';
                                        switch($row['risk_level']) {
                                            case 1: $risk_class = 'bg-success bg-opacity-25'; break;
                                            case 2: $risk_class = 'bg-warning bg-opacity-25'; break;
                                            case 3: $risk_class = 'bg-danger bg-opacity-25'; break;
                                        }
                                        echo "<tr>";
                                        echo "<td class='purok-name'>" . htmlspecialchars($row['sitio_purok']) . "</td>";
                                        echo "<td>" . number_format($row['total_families']) . "</td>";
                                        echo "<td>" . number_format($row['total_persons']) . "</td>";
                                        echo "<td class='{$risk_class}'>" . $row['risk_level'] . "</td>";
                                        echo "<td>";
                                        echo "<button class='btn btn-sm btn-outline-primary me-1 edit-btn' data-id='" . $row['id'] . "' data-purok='" . htmlspecialchars($row['sitio_purok'], ENT_QUOTES) . "' data-bs-toggle='modal' data-bs-target='#editPurokModal'><i class='fas fa-edit'></i></button>";
                                        echo "<button class='btn btn-sm btn-outline-danger delete-btn' data-id='" . $row['id'] . "'><i class='fas fa-trash'></i></button>";
                                        echo "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='5' class='text-center'>No demographic data available</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                        <?php
                            if ($total_pages > 1) {
                                echo '<nav aria-label="Purok pagination">';
                                echo '<ul class="pagination justify-content-center">';
                                $prev_page = max(1, $page - 1);
                                $next_page = min($total_pages, $page + 1);
                                $base_url = strtok($_SERVER['REQUEST_URI'], '?');
                                $query = $_GET;
                                $query['page'] = $prev_page;
                                echo '<li class="page-item ' . ($page <= 1 ? 'disabled' : '') . '"><a class="page-link" href="' . htmlspecialchars($base_url . '?' . http_build_query($query)) . '">Previous</a></li>';
                                for ($p = 1; $p <= $total_pages; $p++) {
                                    $query['page'] = $p;
                                    $active = $p === $page ? ' active' : '';
                                    echo '<li class="page-item' . $active . '"><a class="page-link" href="' . htmlspecialchars($base_url . '?' . http_build_query($query)) . '">' . $p . '</a></li>';
                                }
                                $query['page'] = $next_page;
                                echo '<li class="page-item ' . ($page >= $total_pages ? 'disabled' : '') . '"><a class="page-link" href="' . htmlspecialchars($base_url . '?' . http_build_query($query)) . '">Next</a></li>';
                                echo '</ul>';
                                echo '</nav>';
                            }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-4">
                <div class="stats-card">
                    <h5><i class="fas fa-home me-2"></i>Families & Persons</h5>
                    <div class="stat-item">
                        <span class="stat-label">Total Families</span>
                        <span class="stat-value"><?php echo number_format($totals['total_families']); ?></span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Total Male</span>
                        <span class="stat-value"><?php echo number_format($totals['total_persons_male']); ?></span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Total Female</span>
                        <span class="stat-value"><?php echo number_format($totals['total_persons_female']); ?></span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Total Persons</span>
                        <span class="stat-value"><?php echo number_format(isset($totals['total_persons_total']) ? $totals['total_persons_total'] : ($totals['total_persons_male'] + $totals['total_persons_female'])); ?></span>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="stats-card">
                    <h5><i class="fas fa-child me-2"></i>Age Groups</h5>
                    <div class="stat-item">
                        <span class="stat-label">Infants (M+F)</span>
                        <span class="stat-value"><?php echo number_format($totals['infant_male'] + $totals['infant_female']); ?></span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Children (M+F)</span>
                        <span class="stat-value"><?php echo number_format($totals['children_male'] + $totals['children_female']); ?></span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Adults (M+F)</span>
                        <span class="stat-value"><?php echo number_format($totals['adult_male'] + $totals['adult_female']); ?></span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Elderly (M+F)</span>
                        <span class="stat-value"><?php echo number_format($totals['elderly_male'] + $totals['elderly_female']); ?></span>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="stats-card">
                    <h5><i class="fas fa-info-circle me-2"></i>Special Categories</h5>
                    <div class="stat-item">
                        <span class="stat-label">PWD (Total)</span>
                        <span class="stat-value"><?php echo number_format($totals['pwd_male'] + $totals['pwd_female']); ?></span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">With Sickness (Total)</span>
                        <span class="stat-value"><?php echo number_format($totals['sickness_male'] + $totals['sickness_female']); ?></span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Pregnant Women</span>
                        <span class="stat-value"><?php echo number_format($totals['pregnant_women']); ?></span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Total Puroks</span>
                        <span class="stat-value"><?php echo isset($total_puroks) ? number_format($total_puroks) : count($demographic_data); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Preload flood data for modal population
        const floodData = <?php echo json_encode($floodByPurok, JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT); ?>;

        // Wire up edit buttons to populate the modal
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.edit-btn');
            if (!btn) return;
            const purok = btn.getAttribute('data-purok') || '';
            const data = floodData && floodData[purok] ? floodData[purok] : {};
            const setVal = (id, val) => {
                const el = document.getElementById(id);
                if (el) el.value = Number.isFinite(val) ? val : (parseInt(val, 10) || 0);
            };
            const fields = ['infant_male','infant_female','children_male','children_female','adult_male','adult_female','elderly_male','elderly_female','pwd_male','pwd_female','sickness_male','sickness_female','pregnant_women'];
            const nameInput = document.getElementById('edit_purok_name');
            if (nameInput) nameInput.value = purok;
            fields.forEach(f => setVal(f, data && data[f] !== undefined ? data[f] : 0));
        });

        function editData(purokName) {
            // TODO: Implement edit functionality
            alert('Edit functionality to be implemented for Purok: ' + purokName);
        }
        
        function exportTable() {
            const table = document.getElementById('demographicTable');
            let csv = [];
            
            // Get headers (exclude Action column)
            const headers = [];
            table.querySelectorAll('thead th').forEach((th, index) => {
                const thText = th.textContent.trim();
                if (thText !== 'ACTION') {
                    headers.push(thText);
                }
            });
            csv.push(headers.join(','));
            
            // Get data rows (exclude Action column)
            table.querySelectorAll('tbody tr').forEach(tr => {
                const row = [];
                tr.querySelectorAll('td').forEach((td, index) => {
                    const headerCount = table.querySelectorAll('thead th').length;
                    if (index < headerCount - 1) { // Exclude Action column
                        row.push(td.textContent.trim());
                    }
                });
                csv.push(row.join(','));
            });
            
            // Create download link
            const csvContent = csv.join('\n');
            const blob = new Blob([csvContent], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'purok_demographics_' + new Date().toISOString().split('T')[0] + '.csv';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
        }
        
        // Add row click functionality (exclude action column clicks)
        document.querySelectorAll('.demographic-table tbody tr').forEach(row => {
            row.addEventListener('click', function(e) {
                // Don't highlight row if clicking on action button
                if (e.target.tagName === 'BUTTON' || e.target.tagName === 'I') {
                    return;
                }
                
                // Highlight selected row
                document.querySelectorAll('.demographic-table tbody tr').forEach(r => {
                    r.style.backgroundColor = '';
                });
                // Don't change background of total row
                if (!this.querySelector('.purok-name') || this.querySelector('.purok-name').textContent.trim() !== 'TOTAL') {
                    this.style.backgroundColor = '#c084fc';
                }
            });
        });
    </script>
</body>
</html>

