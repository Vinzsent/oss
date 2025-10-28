<?php
// Start session to access user data
session_start();

// Database connection
include('config.php');

// Check if a barangay and purok are selected
$selectedBarangay = isset($_POST['barangay']) ? $_POST['barangay'] : 'lizada';
$selectedPurok = isset($_POST['purok']) ? $_POST['purok'] : '';

// Pagination settings
$rows_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $rows_per_page;

// Query to fetch puroks for the selected barangay
$purokQuery = "SELECT DISTINCT sitio_purok FROM socio_data WHERE barangay = ?";
$purokStmt = $conn->prepare($purokQuery);
$purokStmt->bind_param("s", $selectedBarangay);
$purokStmt->execute();
$purokResult = $purokStmt->get_result();

// Get total count for pagination
$count_sql = "SELECT COUNT(*) as total FROM socio_data WHERE barangay = ? " . ($selectedPurok ? "AND sitio_purok = ?" : "");
$count_stmt = $conn->prepare($count_sql);

if ($selectedPurok) {
    $count_stmt->bind_param("ss", $selectedBarangay, $selectedPurok);
} else {
    $count_stmt->bind_param("s", $selectedBarangay);
}
$count_stmt->execute();
$total_rows = $count_stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $rows_per_page);

// Query to fetch data based on barangay and optionally purok with pagination
$sql = "SELECT sitio_purok, total_families, total_persons, risk_level 
        FROM socio_data 
        WHERE barangay = ? " . ($selectedPurok ? "AND sitio_purok = ?" : "") . 
        " ORDER BY sitio_purok LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);

if ($selectedPurok) {
    $stmt->bind_param("ssii", $selectedBarangay, $selectedPurok, $rows_per_page, $offset);
} else {
    $stmt->bind_param("sii", $selectedBarangay, $rows_per_page, $offset);
}
$stmt->execute();
$result = $stmt->get_result();

// Initialize totals
$total_families = 0;
$total_persons = 0;

function getUserRole($conn, $user_id) {
    $role = '';
    $query = "SELECT role FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $role = $user['role'];
        }
    }
    $stmt->close();
    return $role;
}

// Get current user's role
$user_role = '';
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true && isset($_SESSION['id'])) {
    $user_role = getUserRole($conn, $_SESSION['id']);
}

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
        html, body {
            height: 100%;
            margin: 0;
        }
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .content-wrapper {
            flex: 1 0 auto;
            padding-bottom: 60px; /* Space for footer */
        }
        footer {
            flex-shrink: 0;
            background-color: #343a40;
            color: white;
            padding: 20px 0;
            margin-top: auto;
            width: 100%;
        }
        table {
            width: 100%;
            margin-bottom: 1rem;
            color: #212529;
        }
        thead th {
            background-color: #f8f9fa;
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
            
            /* ANCHOR: Make risk level column more prominent on mobile */
            .table td:nth-child(4) {
                min-width: 80px;
                font-weight: bold;
                text-align: center;
            }
            
            /* ANCHOR: Optimize numeric columns for mobile */
            .table td:nth-child(2),
            .table td:nth-child(3) {
                min-width: 110px;
                text-align: right;
            }
            
            /* ANCHOR: Hide sitio purok column on very small screens, show abbreviated version */
            @media (max-width: 480px) {
                .table th:nth-child(1),
                .table td:nth-child(1) {
                    min-width: 80px;
                    font-size: 0.75rem;
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
            
            /* ANCHOR: Adjust header layout for mobile */
            .d-flex.justify-content-between {
                flex-direction: column;
                align-items: flex-start !important;
            }
            
            .d-flex.justify-content-between .btn {
                margin-top: 1rem;
                width: 100%;
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
            
            /* ANCHOR: Improve risk level legend for mobile */
            .alert-settings {
                padding: 1rem 0.5rem;
            }
            
            h2 {
                font-size: 1.5rem;
                margin-bottom: 0.5rem;
            }
            
            h4 {
                font-size: 1.1rem;
                margin-bottom: 0.75rem;
            }
            
            h5 {
                font-size: 1rem;
            }
            
            h6 {
                font-size: 0.95rem;
            }
        }
        
        /* ANCHOR: Mobile-first responsive adjustments */
        @media (max-width: 767px) {
            .content-wrapper {
                padding-bottom: 40px;
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
            
            /* Improve pagination on mobile */
            .pagination {
                font-size: 0.875rem;
            }
            
            .pagination .page-link {
                padding: 0.375rem 0.75rem;
            }
            
            /* Risk level legend mobile optimization */
            .d-flex.align-items-center {
                margin-bottom: 0.5rem !important;
            }
        }
    </style>
</head>
<body>
    <div class="content-wrapper">
        <!-- Navigation Bar -->
        <?php include('includes/nav.php'); ?>

        <div class="container mt-5">
        <div class="row">
            <!-- ANCHOR: Main content area - responsive column sizing -->
            <div class="col-lg-8 col-md-12 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h2 class="mb-0">Socio Demographic Data</h2>
                        <h4 class="mb-0">Barangay: <?php echo ucfirst($selectedBarangay); ?> 
                        <?php if ($selectedPurok) echo " - Purok: " . ucfirst($selectedPurok); ?></h4>
                    </div>
                    <?php if ($user_role !== 'user'): ?>
                    <a href="add_socio.php"><button class="btn btn-primary"><i class="fas fa-plus"></i> Add Purok and Sitio</button></a>
                    <?php endif; ?>
                </div>
                <!-- ANCHOR: Responsive Socio Demographic Table -->
                <div class="table-responsive table-container">
                    <table class="table table-bordered table-striped mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Sitio Purok</th>
                                <th>Total Number of Families</th>
                                <th>Total Number of Persons</th>
                                <th>Risk Level</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
            if ($result->num_rows > 0) {
                // Output data for each row
                while ($row = $result->fetch_assoc()) {
                    // ANCHOR: Determine risk level color with better mobile styling
                    $riskColor = '';
                    $riskText = '';
                    switch ($row['risk_level']) {
                        case 0:
                            $riskColor = 'background-color: #f8f9fa; color: #6c757d; border-color: #dee2e6;';
                            $riskText = 'Low';
                            break;
                        case 1:
                            $riskColor = 'background-color: #fff3cd; color: #856404; border-color: #ffeaa7;';
                            $riskText = 'Low';
                            break;
                        case 2:
                            $riskColor = 'background-color: #f8d7da; color: #721c24; border-color: #f5c6cb;';
                            $riskText = 'Medium';
                            break;
                        case 3:
                            $riskColor = 'background-color: #d4edda; color: #155724; border-color: #c3e6cb;';
                            $riskText = 'High';
                            break;
                    }

                    echo "<tr>
                    <td>" . htmlspecialchars($row['sitio_purok']) . "</td>
                    <td class='text-right'>" . number_format($row['total_families']) . "</td>
                    <td class='text-right'>" . number_format($row['total_persons']) . "</td>
                    <td class='text-center' style='$riskColor font-weight: bold;'>$riskText</td>
                    </tr>";
                    // Update totals
                    $total_families += $row['total_families'];
                    $total_persons += $row['total_persons'];
                }
            } else {
                echo "<tr><td colspan='4' class='text-center'>No data available</td></tr>";
            }
            ?>
            <tr class="table-info">
                <td><strong>Total</strong></td>
                <td class="text-right"><strong><?php echo number_format($total_families); ?></strong></td>
                <td class="text-right"><strong><?php echo number_format($total_persons); ?></strong></td>
                <td></td>
            </tr>
        </tbody>
    </table>
</div>

<?php if ($total_pages > 1): ?>
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                <a class="page-link" href="?page=<?php echo $page - 1; ?>" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
            
            <?php
                        // Show page numbers
                        $start_page = max(1, $page - 2);
                        $end_page = min($total_pages, $page + 2);
                        
                        if ($start_page > 1) {
                            echo '<li class="page-item"><a class="page-link" href="?page=1">1</a></li>';
                            if ($start_page > 2) {
                                echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                            }
                        }
                        
                        for ($i = $start_page; $i <= $end_page; $i++) {
                            $active = $i == $page ? 'active' : '';
                            echo "<li class='page-item $active'><a class='page-link' href='?page=$i'>$i</a></li>";
                        }
                        
                        if ($end_page < $total_pages) {
                            if ($end_page < $total_pages - 1) {
                                echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                            }
                            echo "<li class='page-item'><a class='page-link' href='?page=$total_pages'>$total_pages</a></li>";
                        }
                        ?>
                        
                        <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $page + 1; ?>" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    </ul>
                    <div class="text-muted">
                        Showing <?php echo min($rows_per_page, $total_rows - $offset); ?> of <?php echo $total_rows; ?> records
                    </div>
                </nav>
                <?php endif; ?>
            </div>
            <!-- ANCHOR: Sidebar - responsive column sizing -->
            <div class="col-lg-4 col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-filter"></i> Search Filters</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <strong>Filter by Barangay and Purok:</strong>
                        </div>
                        <form method="post">
                            <div class="form-group">
                                <label for="barangay"><strong>Barangay</strong></label>
                                <input type="hidden" name="page" value="1">
                            <select class="form-control" name="barangay" onchange="this.form.submit()">
                                    <option value="lizada" <?php if ($selectedBarangay == 'lizada') echo 'selected'; ?>>Lizada</option>
                                    <option value="daliao" <?php if ($selectedBarangay == 'daliao') echo 'selected'; ?>>Daliao</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="purok"><strong>Purok/Sitio</strong></label>
                                <input type="hidden" name="page" value="1">
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
                        </form>
                        
                        <!-- ANCHOR: Risk Level Legend -->
                        <div class="mt-4">
                            <h6><i class="fas fa-info-circle"></i> Risk Level Legend</h6>
                            <div class="d-flex align-items-center mb-2">
                                <div class="flex-shrink-0 me-2">
                                    <span style="background-color: #fff3cd; padding: 8px 12px; border-radius: 3px; color: #856404; font-size: 0.8rem; font-weight: bold; min-width: 50px; display: inline-block; text-align: center;">Low</span>
                                </div>
                                <div class="flex-grow-1">
                                    <small class="text-muted">Low risk areas</small>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <div class="flex-shrink-0 me-2">
                                    <span style="background-color: #f8d7da; padding: 8px 12px; border-radius: 3px; color: #721c24; font-size: 0.8rem; font-weight: bold; min-width: 50px; display: inline-block; text-align: center;">Medium</span>
                                </div>
                                <div class="flex-grow-1">
                                    <small class="text-muted">Medium risk areas</small>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 me-2">
                                    <span style="background-color: #d4edda; padding: 8px 12px; border-radius: 3px; color: #155724; font-size: 0.8rem; font-weight: bold; min-width: 50px; display: inline-block; text-align: center;">High</span>
                                </div>
                                <div class="flex-grow-1">
                                    <small class="text-muted">High risk areas</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

        </div> <!-- End of container -->
    </div> <!-- End of content-wrapper -->
    
    <footer class="bg-dark text-white p-4 text-center">
        <div class="container">
            &copy; 2024 Flood Resilience App. All Rights Reserved.
        </div>
    </footer>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
