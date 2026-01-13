<?php
// Start session to access user data
session_start();

// Database connection
include('config.php');

// Check if user is logged in and get their role
$user_role = '';
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true && isset($_SESSION['id'])) {
    $user_id = $_SESSION['id'];
    $user_query = "SELECT role FROM users WHERE id = ?";
    $user_stmt = $conn->prepare($user_query);
    $user_stmt->bind_param("i", $user_id);
    if ($user_stmt->execute()) {
        $user_result = $user_stmt->get_result();
        if ($user_result && $user_result->num_rows > 0) {
            $user = $user_result->fetch_assoc();
            $user_role = $user['role'];
            // Debug: Show the role in the input field
            $debug_role = htmlspecialchars($user_role);
        }
    }
    $user_stmt->close();
}

// Handle form submission with redirect to prevent resubmit dialog
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $barangay = isset($_POST['barangay']) ? $_POST['barangay'] : 'lizada';
    $purok = isset($_POST['purok']) ? $_POST['purok'] : '';
    
    // Store in session and redirect
    $_SESSION['selected_barangay'] = $barangay;
    $_SESSION['selected_purok'] = $purok;
    
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

// Check if a barangay and purok are selected from session or use defaults
$selectedBarangay = isset($_SESSION['selected_barangay']) ? $_SESSION['selected_barangay'] : 'lizada';
$selectedPurok = isset($_SESSION['selected_purok']) ? $_SESSION['selected_purok'] : '';

// Query to fetch puroks for the selected barangay
$purokQuery = "SELECT DISTINCT sitio_purok FROM socio_data WHERE barangay = ?";
$purokStmt = $conn->prepare($purokQuery);
$purokStmt->bind_param("s", $selectedBarangay);
$purokStmt->execute();
$purokResult = $purokStmt->get_result();

// Query to fetch flood warning data with filtering
$sql = "SELECT * FROM FloodWarning WHERE barangay = ?";
if ($selectedPurok) {
    $sql .= " AND sitio = ?";
}
$stmt = $conn->prepare($sql);

// Bind parameters for the prepared statement
if ($selectedPurok) {
    $stmt->bind_param("ss", $selectedBarangay, $selectedPurok);
} else {
    $stmt->bind_param("s", $selectedBarangay);
}
$stmt->execute();
$result = $stmt->get_result();

// Initialize totals
$total_families = 0;
$total_persons = 0;

$user_query = "SELECT role FROM users WHERE id = ?";
$user_stmt = $conn->prepare($user_query);
$user_stmt->bind_param("i", $_SESSION['id']);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user = $user_result->fetch_assoc();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flood Warning - Micro OSS App</title>
    <!-- Use Bootstrap 5 to match evacuation.php -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            background-color: #f5f5f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .main-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
            flex: 1;
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
        
        .content-card {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            height: 100%;
        }
        
        .section-title {
            color: #6b21a8;
            font-weight: bold;
            margin-bottom: 20px;
            border-bottom: 2px solid #f3f4f6;
            padding-bottom: 10px;
        }

        .form-label {
            font-weight: 600;
            color: #4b5563;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
        
        .btn-primary:hover {
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
            transform: translateY(-2px);
        }

        .alert-info-custom {
            background-color: #e0f2fe;
            border-left: 4px solid #0ea5e9;
            color: #0c4a6e;
            padding: 15px;
            border-radius: 6px;
        }
        
        /* Table Styles from flood_warning.php adapted for new design */
        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #4b5563;
        }
        
        .table-responsive {
            border-radius: 8px;
            overflow: hidden;
        }

        footer {
            background-color: #1f2937 !important;
            color: white;
            padding: 20px 0;
            margin-top: 40px;
            text-align: center;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .main-container {
                padding: 10px;
            }
            .page-title {
                font-size: 1.8rem;
            }
            .table th, .table td {
                min-width: 100px;
                font-size: 0.85rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <?php include('includes/nav.php'); ?>

    <div class="main-container">
        <div class="page-header">
            <h1 class="page-title">
                <i class="fas fa-bullhorn me-3"></i>Flood Warning Data
            </h1>
            <p class="page-subtitle">Current alerts and monitoring for Barangay <?php echo ucfirst($selectedBarangay); ?></p>
        </div>

        <div class="row">
            <!-- Main Content Area -->
            <div class="col-lg-8 mb-4">
                <div class="content-card">
                    <h4 class="section-title">
                        <i class="fas fa-list-alt me-2"></i>Warning Status
                    </h4>
                    
                    <!-- Responsive Flood Warning Table -->
                    <?php
                    // Check if there are records to display
                    if ($result->num_rows > 0) {
                        echo '<div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Barangay</th>
                                            <th>Sitio</th>
                                            <th class="text-center">Warning Level</th>
                                            <th>Status</th>
                                            <th>Recommended Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>';
                        
                        while ($row = $result->fetch_assoc()) {
                            echo '<tr>';
                            echo '<td>' . date('M d, Y', strtotime($row['date_created'])) . '</td>';
                            echo '<td>' . ucfirst($row['barangay']) . '</td>';
                            echo '<td>' . ucfirst($row['sitio']) . '</td>';

                            // Apply background color for warning level
                            switch ($row['warning_level']) {
                                case 1:
                                    echo '<td class="text-center"><span class="badge bg-warning text-dark"><i class="fas fa-exclamation-triangle me-1"></i> Yellow Alert</span></td>';
                                    break;
                                case 2:
                                    echo '<td class="text-center"><span class="badge bg-danger"><i class="fas fa-exclamation-circle me-1"></i> Orange Alert</span></td>';
                                    break;
                                case 3:
                                    echo '<td class="text-center"><span class="badge bg-dark"><i class="fas fa-skull-crossbones me-1"></i> Red Alert</span></td>';
                                    break;
                                default:
                                    echo '<td class="text-center"><span class="badge bg-secondary">Unknown</span></td>';
                            }

                            echo '<td>' . htmlspecialchars($row['status']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['recommended_action']) . '</td>';
                            echo '</tr>';
                        }

                        echo '</tbody></table>
                              </div>';
                    } else {
                        echo '<div class="alert alert-info-custom text-center">
                                <i class="fas fa-info-circle me-2"></i> No flood warning records found for the selected criteria.
                              </div>';
                    }

                    // Close the statement and connection
                    $stmt->close();
                    $conn->close();
                    ?>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4 mb-4">
                <div class="content-card">
                    <h4 class="section-title">
                        <i class="fas fa-filter me-2"></i>Search Filters
                    </h4>
                    
                    <div class="alert-info-custom mb-4">
                         <strong><i class="fas fa-info-circle me-1"></i> Filter Data:</strong>
                         <p class="mb-0 mt-1 small">Select a specific location to view warning history.</p>
                    </div>

                    <form method="post">
                        <?php if (isset($user_role) && !empty($user_role)): ?>
                            <!-- Role specific content could go here -->
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label for="barangay" class="form-label">Barangay</label>
                            <select class="form-select" name="barangay" onchange="this.form.submit()">
                                <option value="lizada" <?php if ($selectedBarangay == 'lizada') echo 'selected'; ?>>Lizada</option>
                                <option value="daliao" <?php if ($selectedBarangay == 'daliao') echo 'selected'; ?>>Daliao</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="purok" class="form-label">Purok/Sitio</label>
                            <select class="form-select" name="purok" onchange="this.form.submit()">
                                <option value="">-- All --</option>
                                <?php
                                if ($purokResult->num_rows > 0) {
                                    while ($purokRow = $purokResult->fetch_assoc()) {
                                        $selected = $selectedPurok == $purokRow['sitio_purok'] ? 'selected' : '';
                                        echo "<option value='{$purokRow['sitio_purok']}' $selected>{$purokRow['sitio_purok']}</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        
                        <?php if (isset($debug_role)): ?>
                        <input type="hidden" name="role" value="<?php echo $debug_role; ?>" readonly>
                        <?php endif; ?>
                    </form>
                    
                    <?php 
                    // Check if user is admin before showing the button
                    if (isset($user_role) && $user_role === 'admin'): ?>
                        <div class="mt-4">
                            <a href="add_flood_warning.php" class="btn btn-primary w-100">
                                <i class="fas fa-plus me-2"></i> Add Barangay and Sitio
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <div class="container">
            &copy; 2024 Flood Resilience App. All Rights Reserved.
        </div>
    </footer>

    <!-- Scripts -->
    <!-- Scripts -->

</body>
</html>