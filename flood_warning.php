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
    <title>Micro OSS App</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .main-content {
            flex: 1;
        }
        table {
            width: 100%;
            margin-bottom: 1rem;
            color: #212529;
        }
        thead th {
            background-color: #f8f9fa;
        }
        footer {
            margin-top: auto;
        }
        
        /* ANCHOR: Mobile responsive table styles */
        .table-responsive {
            border-radius: 0.375rem;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        /* ANCHOR: Ensure table cells have minimum width for readability */
        .table th,
        .table td {
            min-width: 120px;
            white-space: nowrap;
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
        }
        
        /* ANCHOR: Adjust table for smaller screens */
        @media (max-width: 768px) {
            .table th,
            .table td {
                min-width: 100px;
                font-size: 0.8rem;
                padding: 0.4rem 0.5rem;
            }
            
            /* ANCHOR: Make warning level column more prominent on mobile */
            .table td:nth-child(4) {
                min-width: 80px;
                font-weight: bold;
                text-align: center;
            }
            
            /* ANCHOR: Optimize status and action columns for mobile */
            .table td:nth-child(5),
            .table td:nth-child(6) {
                min-width: 150px;
                white-space: normal;
                word-wrap: break-word;
            }
            
            /* ANCHOR: Hide date column on very small screens */
            @media (max-width: 480px) {
                .table th:nth-child(1),
                .table td:nth-child(1) {
                    display: none;
                }
            }
        }
        
        /* ANCHOR: Ensure table container is properly sized */
        .table-container {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
        }
        
        /* ANCHOR: Add horizontal scroll indicator for mobile */
        @media (max-width: 768px) {
            .table-responsive::after {
                content: "← Swipe to view more →";
                display: block;
                text-align: center;
                color: #6c757d;
                font-size: 0.75rem;
                padding: 0.5rem;
                background-color: #f8f9fa;
                border-top: 1px solid #dee2e6;
            }
            
            /* ANCHOR: Improve mobile card layout */
            .card {
                margin-bottom: 1rem;
            }
            
            /* ANCHOR: Better mobile button styling */
            .btn-block {
                width: 100%;
                margin-bottom: 0.5rem;
            }
            
            /* ANCHOR: Improve mobile form controls */
            .form-control {
                font-size: 1rem; /* Prevents zoom on iOS */
            }
        }
        
        /* ANCHOR: Extra small devices optimization */
        @media (max-width: 576px) {
            .container {
                padding-left: 10px;
                padding-right: 10px;
            }
            
            .card-body {
                padding: 1rem 0.75rem;
            }
            
            .btn {
                padding: 0.5rem 1rem;
                font-size: 0.875rem;
            }
            
            h2 {
                font-size: 1.5rem;
                margin-bottom: 1rem;
            }
            
            h4 {
                font-size: 1.25rem;
                margin-bottom: 1rem;
            }
            
            h5 {
                font-size: 1.1rem;
            }
        }
        
        /* ANCHOR: Mobile-first responsive adjustments */
        @media (max-width: 767px) {
            .main-content {
                padding-top: 1rem;
            }
            
            .mt-5 {
                margin-top: 2rem !important;
            }
            
            .card {
                border-radius: 0.5rem;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }
            
            .card-header {
                padding: 0.75rem 1rem;
                background-color: #f8f9fa;
            }
            
            .alert {
                padding: 0.75rem;
                font-size: 0.9rem;
                margin-bottom: 1rem;
            }
            
            /* Better table header visibility on mobile */
            .thead-light th {
                position: sticky;
                top: 0;
                background-color: #f8f9fa !important;
                z-index: 10;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <?php include('includes/nav.php'); ?>

    <div class="container mt-5 main-content">
        <div class="row">
            <!-- ANCHOR: Main content area - responsive column sizing -->
            <div class="col-lg-8 col-md-12 mb-4">
                
                <h2>Flood Warning Data</h2> 
                <h4>Barangay: <?php echo ucfirst($selectedBarangay); ?></h4>

                <!-- ANCHOR: Responsive Flood Warning Table -->
                <?php
                // Check if there are records to display
                if ($result->num_rows > 0) {
                    echo '<div class="table-responsive table-container">
                            <table class="table table-bordered table-striped mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Barangay</th>
                                        <th>Sitio</th>
                                        <th>Warning Level</th>
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

                        // ANCHOR: Apply background color for warning level with better mobile styling
                        switch ($row['warning_level']) {
                            case 1:
                                echo '<td class="text-center" style="background-color: #fff3cd; color: #856404; border-color: #ffeaa7; font-weight: bold;">Yellow Alert</td>';
                                break;
                            case 2:
                                echo '<td class="text-center" style="background-color: #f8d7da; color: #721c24; border-color: #f5c6cb; font-weight: bold;">Orange Alert</td>';
                                break;
                            case 3:
                                echo '<td class="text-center" style="background-color: #d4edda; color: #155724; border-color: #c3e6cb; font-weight: bold;">Red Alert</td>';
                                break;
                            default:
                                echo '<td class="text-center">Unknown</td>';
                        }

                        echo '<td>' . htmlspecialchars($row['status']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['recommended_action']) . '</td>';
                        echo '</tr>';
                    }

                    echo '</tbody></table>
                          </div>';
                } else {
                    echo '<div class="alert alert-info text-center">
                            <i class="fas fa-info-circle"></i> No flood warning records found for the selected criteria.
                          </div>';
                }

                // Close the statement and connection
                $stmt->close();
                $conn->close();
                ?>
            </div>

            <!-- ANCHOR: Sidebar - responsive column sizing -->
            <div class="col-lg-4 col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-filter"></i> Search Filters</h5>
                    </div>
                    <div class="card-body">
                        <form method="post">
                            <?php if (isset($user_role) && !empty($user_role)): ?>
                                <?php endif; ?>
                                <div class="form-group">
                                    <label for="barangay"><strong>Barangay</strong></label>
                                    <select class="form-control" name="barangay" onchange="this.form.submit()">
                                    <option value="lizada" <?php if ($selectedBarangay == 'lizada') echo 'selected'; ?>>Lizada</option>
                                    <option value="daliao" <?php if ($selectedBarangay == 'daliao') echo 'selected'; ?>>Daliao</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="purok"><strong>Purok/Sitio</strong></label>
                                <select class="form-control" name="purok" onchange="this.form.submit()">
                                    <option value="">--All--</option>
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
                            <input type="hidden" name="role" value="<?php echo $debug_role; ?>" class="form-control mb-3" readonly>
                            <?php endif; ?>
                        </form>
                        <?php 
                        // ANCHOR: Check if user is admin before showing the button
                        if (isset($user_role) && $user_role === 'admin'): ?>
                            <div class="mt-3">
                                <a href="add_flood_warning.php" class="btn btn-primary btn-block">
                                    <i class="fas fa-plus"></i> Add Barangay and Sitio
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-white mt-5 p-4 text-center">
        &copy; 2024 Flood Resilience App. All Rights Reserved.
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>