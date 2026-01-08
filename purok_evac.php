<?php
session_start();
include('config.php');

// Check authentication and get user role
$is_logged_in = isset($_SESSION['id']) && isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : null;
$is_admin = $user_role === 'admin';

// Include auth check - this will show modal if not logged in
include('includes/auth_check.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Population Demographics - Micro Online Synthesis System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f5f5f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .main-container {
            max-width: 1400px;
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
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            overflow-x: auto;
        }
        
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        .demographic-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            font-size: 0.75rem;
            table-layout: fixed;
        }
        
        .demographic-table thead {
            background-color: #8b5cf6;
            color: white;
        }
        
        .demographic-table th {
            padding: 8px 4px;
            text-align: center;
            font-weight: bold;
            font-size: 0.8rem;
            border: none;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
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
        
        .demographic-table td {
            padding: 6px 4px;
            text-align: center;
            border: 1px solid #e9d5f7;
            font-weight: 500;
            color: #1f2937;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .demographic-table tbody tr:last-child td {
            font-weight: bold;
            background-color: #e9d5f7;
            color: #6b21a8;
        }
        
        .age-bracket {
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
            
            .demographic-table {
                font-size: 0.9rem;
            }
            
            .demographic-table th,
            .demographic-table td {
                padding: 8px;
            }
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
        
        .pagination {
            display: flex;
            padding-left: 0;
            list-style: none;
            border-radius: 0.25rem;
        }
        
        .page-link {
            position: relative;
            display: block;
            color: #8b5cf6;
            text-decoration: none;
            background-color: #fff;
            border: 1px solid #dee2e6;
            transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
            padding: 0.5rem 0.75rem;
        }
        
        .page-link:hover {
            z-index: 2;
            color: #fff;
            background-color: #8b5cf6;
            border-color: #8b5cf6;
        }
        
        .page-item.active .page-link {
            z-index: 3;
            color: #fff;
            background-color: #8b5cf6;
            border-color: #8b5cf6;
        }
        
        .page-item {
            margin: 0 2px;
        }
        
        /* Column width optimizations */
        .demographic-table th:nth-child(1) { width: 100px; } /* Purok Name */
        .demographic-table th:nth-child(2) { width: 80px; } /* Total Pop Families */
        .demographic-table th:nth-child(3) { width: 80px; } /* Total Pop Persons */
        .demographic-table th:nth-child(4) { width: 90px; } /* Vulnerable Families */
        .demographic-table th:nth-child(5) { width: 90px; } /* Vulnerable Persons */
        .demographic-table th:nth-child(6) { width: 120px; } /* Plan A Center Name */
        .demographic-table th:nth-child(7) { width: 140px; } /* Plan A Address */
        .demographic-table th:nth-child(8) { width: 90px; } /* Plan A Cap Families */
        .demographic-table th:nth-child(9) { width: 90px; } /* Plan A Cap Persons */
        .demographic-table th:nth-child(10) { width: 110px; } /* To be Accom Families */
        .demographic-table th:nth-child(11) { width: 110px; } /* To be Accom Persons */
        .demographic-table th:nth-child(12) { width: 100px; } /* Not Accom Families */
        .demographic-table th:nth-child(13) { width: 100px; } /* Not Accom Persons */
        .demographic-table th:nth-child(14) { width: 120px; } /* Plan B Center Name */
        .demographic-table th:nth-child(15) { width: 140px; } /* Plan B Address */
        .demographic-table th:nth-child(16) { width: 90px; } /* Plan B Cap Families */
        .demographic-table th:nth-child(17) { width: 90px; } /* Plan B Cap Persons */
        .demographic-table th:nth-child(18) { width: 130px; } /* Not Accom AB Families */
        .demographic-table th:nth-child(19) { width: 130px; } /* Not Accom AB Persons */
        .demographic-table th:nth-child(20) { width: 120px; } /* Remarks */
        .demographic-table th:nth-child(21) { width: 60px; } /* Action */
        
        /* Custom CSS for navigation */
        .dropdown-item.active {
            background-color: #8b5cf6 !important;
            color: white !important;
            font-weight: bold;
        }
        .dropdown-item.active:hover {
            background-color: #6b21a8 !important;
            color: white !important;
        }
        /* Ensure dropdown menu is clickable and properly positioned */
        .dropdown-menu {
            z-index: 1050 !important;
            pointer-events: auto !important;
        }
        .dropdown-toggle {
            cursor: pointer !important;
        }
        .dropdown-item {
            cursor: pointer !important;
        }
        /* Prevent any overlay from blocking dropdown */
        .navbar-nav .dropdown {
            position: relative;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <a class="navbar-brand" href="index.php"><strong>Micro Online Synthesis System</strong></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="home.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="maps.php">Community Map</a>
                </li>
                <li class="nav-item dropdown">
                    <a 
                        class="nav-link dropdown-toggle" 
                        href="#" 
                        id="navbarDropdown" 
                        role="button" 
                        data-bs-toggle="dropdown" 
                        aria-expanded="false">
                        Early Warning System
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="alert.php">Alert Signal</a></li>
                        <li><a class="dropdown-item" href="hazard_vul.php">Hazard Map</a></li>
                        <li><a class="dropdown-item" href="flood_warning.php">Flood Monitoring</a></li>
                    </ul>
                </li>
                <li class="nav-item"><a class="nav-link" href="evacuation.php">Evacuation Map</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="socioDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">Socio Demographic</a>
                    <ul class="dropdown-menu" aria-labelledby="socioDropdown">
                       <li><a class="dropdown-item" href="population.php">Population Over Age</a></li>
                       <li><a class="dropdown-item" href="hazard_vul.php">Hazard Vulnerability</a></li>
                       <li><a class="dropdown-item" href="purok_demographics.php">Purok Demographics</a></li>
                       <li><a class="dropdown-item" href="purok_evac.php">Purok Evacuation</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="gallery.php">Gallery</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="iks.php">IKS</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="publications.php">Publications</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="download.php">Downloadables</a>
                </li>
                
                <?php
                // Check if user is logged in
                if (isset($_SESSION['id'])) {
                    // User is logged in, show profile picture and logout option
                    // Check if profile picture is set in the session
                    $profile_picture = isset($_SESSION['profile_picture']) && !empty($_SESSION['profile_picture']) ? $_SESSION['profile_picture'] : 'default-profile.jpg';
                    echo '<li class="nav-item">';
                    echo '<img src="uploads/' . htmlspecialchars($profile_picture) . '" alt="Profile Picture" class="rounded-circle" style="width: 40px; height: 40px;">'; // Profile picture
                    echo '</li>';
                    echo '<li class="nav-item">';
                    echo '<a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal"><i class="fas fa-sign-out-alt mr-2"></i>Log Out</a>';
                    echo '</li>';
                } else {
                    // User is not logged in, show login and sign-up options
                    echo '<li class="nav-item">';
                    echo '<a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#loginModal"><i class="fas fa-sign-out-alt mr-2"></i>Login</a>';
                    echo '</li>';
                    echo '<li class="nav-item">';
                    echo '<a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#signUpModal"><i class="fas fa-sign-out-alt mr-2"></i>Sign Up</a>';
                    echo '</li>';
                }
                ?>
            </ul>
        </div>
    </nav>
    
    <!-- Mobile Navigation Auto-Close Script -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-close navbar on mobile when clicking a link
        const navLinks = document.querySelectorAll('.navbar-nav .nav-link:not(.dropdown-toggle)');
        const navbarCollapse = document.getElementById('navbarNav');
        
        navLinks.forEach(function(link) {
            link.addEventListener('click', function() {
                if (window.innerWidth < 992 && navbarCollapse && navbarCollapse.classList.contains('show')) {
                    const bsCollapse = new bootstrap.Collapse(navbarCollapse, {
                        toggle: false
                    });
                    bsCollapse.hide();
                }
            });
        });
    });
    </script>
    
    <?php if ($is_logged_in): ?>
    <div class="main-container main-content-protected">
        <?php
        if (isset($_SESSION['success'])) {
            echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>";
            echo "<i class='fas fa-check-circle me-2'></i>" . htmlspecialchars($_SESSION['success']);
            echo "<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>";
            echo "</div>";
            unset($_SESSION['success']);
        }
        
        if (isset($_SESSION['error'])) {
            echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>";
            echo "<i class='fas fa-exclamation-triangle me-2'></i>" . htmlspecialchars($_SESSION['error']);
            echo "<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>";
            echo "</div>";
            unset($_SESSION['error']);
        }
        ?>
        <div class="page-header">
            <h1 class="page-title">
                <i class="fas fa-person-running me-3"></i>Purok Evacuation population 
            </h1>
            <p class="page-subtitle">Evacuation plan of the affected population in times of disaster or emergency.</p>
            <div class="mt-4 d-flex justify-content-center align-items-center gap-3 flex-wrap">
                <a href="purok_demographics.php" class="back-btn btn-lg px-4 py-3 shadow-sm hover-shadow transition-all text-decoration-none">
                    <i class="fas fa-arrow-left me-2"></i>
                    View Flood Data
                </a>

                <a href="household_materials.php" class="back-btn btn-lg px-4 py-3 shadow-sm hover-shadow transition-all text-decoration-none">
                    <i class="fas fa-arrow-right me-2"></i>
                    View Households Materials used in Construction
                </a>
            </div>
        </div>
        
        <!-- Table Container -->
        <div class="row">
            <div class="col-lg-12">
                <div class="table-container">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="mb-0">
                            <i class="fas fa-route me-2 text-purple"></i>Purok Evacuation Plan
                        </h4>
                    </div>
                    
                    <?php
                    // Pagination variables
                    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                    $per_page = 10; // Show 10 records per page
                    $offset = ($page - 1) * $per_page;
                    
                    // Get total records
                    $count_query = "SELECT COUNT(*) as total FROM purok_evacuation_plan";
                    $count_result = $conn->query($count_query);
                    $total_rows = $count_result->fetch_assoc()['total'];
                    $total_pages = ceil($total_rows / $per_page);
                    
                    // Query to get evacuation plan data with pagination
                    $query = "SELECT * FROM purok_evacuation_plan ORDER BY purok_name ASC LIMIT $per_page OFFSET $offset";
                    $result = $conn->query($query);
                    ?>
                    
                    <div class="table-responsive">
                        <table class="demographic-table" id="evacuationTable">
                            <thead>
                                <tr>
                                    <th>Purok Name</th>
                                    <th>Total Population (Families)</th>
                                    <th>Total Population (Persons)</th>
                                    <th>Vulnerable Population (Families)</th>
                                    <th>Vulnerable Population (Persons)</th>
                                    <th>Plan A - Center Name</th>
                                    <th>Plan A - Center Address</th>
                                    <th>Plan A - Capacity (Families)</th>
                                    <th>Plan A - Capacity (Persons)</th>
                                    <th>To be Accommodated (Families)</th>
                                    <th>To be Accommodated (Persons)</th>
                                    <th>Not Accommodated (Families)</th>
                                    <th>Not Accommodated (Persons)</th>
                                    <th>Plan B - Center Name</th>
                                    <th>Plan B - Center Address</th>
                                    <th>Plan B - Capacity (Families)</th>
                                    <th>Plan B - Capacity (Persons)</th>
                                    <th>Not Accommodated (Plan A & B) (Families)</th>
                                    <th>Not Accommodated (Plan A & B) (Persons)</th>
                                    <th>Remarks</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($result && $result->num_rows > 0) {
                                    while($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($row["purok_name"]) . "</td>";
                                        echo "<td>" . number_format($row["total_pop_families"]) . "</td>";
                                        echo "<td>" . number_format($row["total_pop_persons"]) . "</td>";
                                        echo "<td>" . number_format($row["risk_pop_families"]) . "</td>";
                                        echo "<td>" . number_format($row["risk_pop_persons"]) . "</td>";
                                        echo "<td>" . htmlspecialchars($row["plan_a_center_name"]) . "</td>";
                                        echo "<td>" . htmlspecialchars($row["plan_a_center_address"]) . "</td>";
                                        echo "<td>" . htmlspecialchars($row["plan_a_capacity_families"]) . "</td>";
                                        echo "<td>" . htmlspecialchars($row["plan_a_capacity_persons"]) . "</td>";
                                        echo "<td>" . htmlspecialchars($row["to_be_accommodated_families"]) . "</td>";
                                        echo "<td>" . htmlspecialchars($row["to_be_accommodated_persons"]) . "</td>";
                                        echo "<td>" . htmlspecialchars($row["not_accommodated_families"]) . "</td>";
                                        echo "<td>" . htmlspecialchars($row["not_accommodated_persons"]) . "</td>";
                                        echo "<td>" . htmlspecialchars($row["plan_b_center_name"]) . "</td>";
                                        echo "<td>" . htmlspecialchars($row["plan_b_center_address"]) . "</td>";
                                        echo "<td>" . htmlspecialchars($row["plan_b_capacity_families"]) . "</td>";
                                        echo "<td>" . htmlspecialchars($row["plan_b_capacity_persons"]) . "</td>";
                                        echo "<td>" . htmlspecialchars($row["not_accom_plan_ab_families"]) . "</td>";
                                        echo "<td>" . htmlspecialchars($row["not_accom_plan_ab_persons"]) . "</td>";
                                        echo "<td>" . htmlspecialchars($row["remarks"]) . "</td>";
                                        echo "<td>";
                                        echo "<button class='btn btn-sm btn-primary' onclick='editEvacuationPlan(" . $row["purok_id"] . ")' style='background: #8b5cf6; border-color: #8b5cf6;'>";
                                        echo "<i class='fas fa-edit me-1'></i>Edit";
                                        echo "</button>";
                                        echo "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='21' class='text-center'>No evacuation plan data available</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                    <nav aria-label="Page navigation" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $page - 1; ?>" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($page < $total_pages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $page + 1; ?>" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #8b5cf6; color: white;">
                    <h5 class="modal-title" id="editModalLabel">
                        <i class="fas fa-edit me-2"></i>Edit Evacuation Plan
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editForm" method="POST" action="update_evacuation_plan.php">
                    <div class="modal-body">
                        <input type="hidden" id="editId" name="purok_id">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="editPurokName" class="form-label">Purok Name</label>
                                <input type="text" class="form-control" id="editPurokName" name="purok_name">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="editTotalPopFamilies" class="form-label">Total Population (Families)</label>
                                <input type="number" class="form-control" id="editTotalPopFamilies" name="total_pop_families" min="0">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="editTotalPopPersons" class="form-label">Total Population (Persons)</label>
                                <input type="number" class="form-control" id="editTotalPopPersons" name="total_pop_persons" min="0">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="editRiskPopFamilies" class="form-label">Vulnerable Population (Families)</label>
                                <input type="number" class="form-control" id="editRiskPopFamilies" name="risk_pop_families" min="0">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="editRiskPopPersons" class="form-label">Vulnerable Population (Persons)</label>
                                <input type="number" class="form-control" id="editRiskPopPersons" name="risk_pop_persons" min="0">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="editPlanACenterName" class="form-label">Plan A - Center Name</label>
                                <input type="text" class="form-control" id="editPlanACenterName" name="plan_a_center_name">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="editPlanACenterAddress" class="form-label">Plan A - Center Address</label>
                                <input type="text" class="form-control" id="editPlanACenterAddress" name="plan_a_center_address">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="editPlanACapacityFamilies" class="form-label">Plan A - Capacity (Families)</label>
                                <input type="number" class="form-control" id="editPlanACapacityFamilies" name="plan_a_capacity_families" min="0">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="editPlanACapacityPersons" class="form-label">Plan A - Capacity (Persons)</label>
                                <input type="number" class="form-control" id="editPlanACapacityPersons" name="plan_a_capacity_persons" min="0">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="editToBeAccommodatedFamilies" class="form-label">To be Accommodated (Families)</label>
                                <input type="number" class="form-control" id="editToBeAccommodatedFamilies" name="to_be_accommodated_families" min="0">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="editToBeAccommodatedPersons" class="form-label">To be Accommodated (Persons)</label>
                                <input type="number" class="form-control" id="editToBeAccommodatedPersons" name="to_be_accommodated_persons" min="0">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="editNotAccommodatedFamilies" class="form-label">Not Accommodated (Families)</label>
                                <input type="number" class="form-control" id="editNotAccommodatedFamilies" name="not_accommodated_families" min="0">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="editNotAccommodatedPersons" class="form-label">Not Accommodated (Persons)</label>
                                <input type="number" class="form-control" id="editNotAccommodatedPersons" name="not_accommodated_persons" min="0">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="editPlanBCenterName" class="form-label">Plan B - Center Name</label>
                                <input type="text" class="form-control" id="editPlanBCenterName" name="plan_b_center_name">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="editPlanBCenterAddress" class="form-label">Plan B - Center Address</label>
                                <input type="text" class="form-control" id="editPlanBCenterAddress" name="plan_b_center_address">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="editPlanBCapacityFamilies" class="form-label">Plan B - Cap (Families)</label>
                                <input type="number" class="form-control" id="editPlanBCapacityFamilies" name="plan_b_capacity_families" min="0">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="editPlanBCapacityPersons" class="form-label">Plan B - Capacity (Persons)</label>
                                <input type="number" class="form-control" id="editPlanBCapacityPersons" name="plan_b_capacity_persons" min="0">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="editNotAccomPlanABFamilies" class="form-label">Not Accom (A&B) (Families)</label>
                                <input type="number" class="form-control" id="editNotAccomPlanABFamilies" name="not_accom_plan_ab_families" min="0">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="editNotAccomPlanABPersons" class="form-label">Not Accom (A&B) (Persons)</label>
                                <input type="number" class="form-control" id="editNotAccomPlanABPersons" name="not_accom_plan_ab_persons" min="0">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="editRemarks" class="form-label">Remarks</label>
                                <input type="text" class="form-control" id="editRemarks" name="remarks">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" style="background: #8b5cf6; border-color: #8b5cf6;">
                            <i class="fas fa-save me-1"></i>Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS (includes Popper) for dropdowns/toggles -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function editEvacuationPlan(id) {
            // Fetch evacuation plan data from the server
            fetch(`get_evacuation_plan.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Access the data object from the response
                        const evacuationData = data.data;
                        
                        // Populate the modal fields with the data
                        document.getElementById('editId').value = evacuationData.purok_id;
                        document.getElementById('editPurokName').value = evacuationData.purok_name;
                        document.getElementById('editTotalPopFamilies').value = evacuationData.total_pop_families;
                        document.getElementById('editTotalPopPersons').value = evacuationData.total_pop_persons;
                        document.getElementById('editRiskPopFamilies').value = evacuationData.risk_pop_families;
                        document.getElementById('editRiskPopPersons').value = evacuationData.risk_pop_persons;
                        document.getElementById('editPlanACenterName').value = evacuationData.plan_a_center_name;
                        document.getElementById('editPlanACenterAddress').value = evacuationData.plan_a_center_address;
                        document.getElementById('editPlanACapacityFamilies').value = evacuationData.plan_a_capacity_families;
                        document.getElementById('editPlanACapacityPersons').value = evacuationData.plan_a_capacity_persons;
                        document.getElementById('editToBeAccommodatedFamilies').value = evacuationData.to_be_accommodated_families;
                        document.getElementById('editToBeAccommodatedPersons').value = evacuationData.to_be_accommodated_persons;
                        document.getElementById('editNotAccommodatedFamilies').value = evacuationData.not_accommodated_families;
                        document.getElementById('editNotAccommodatedPersons').value = evacuationData.not_accommodated_persons;
                        document.getElementById('editPlanBCenterName').value = evacuationData.plan_b_center_name;
                        document.getElementById('editPlanBCenterAddress').value = evacuationData.plan_b_center_address;
                        document.getElementById('editPlanBCapacityFamilies').value = evacuationData.plan_b_capacity_families;
                        document.getElementById('editPlanBCapacityPersons').value = evacuationData.plan_b_capacity_persons;
                        document.getElementById('editNotAccomPlanABFamilies').value = evacuationData.not_accom_plan_ab_families;
                        document.getElementById('editNotAccomPlanABPersons').value = evacuationData.not_accom_plan_ab_persons;
                        document.getElementById('editRemarks').value = evacuationData.remarks;
                        
                        // Show the modal
                        const modal = new bootstrap.Modal(document.getElementById('editModal'));
                        modal.show();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error fetching evacuation plan data:', error);
                    alert('Error fetching evacuation plan data. Please try again.');
                });
        }
    </script>
    <?php else: ?>
    <!-- Content hidden when not logged in - auth_check.php handles the modal -->
    <div class="main-content-protected" style="display: none;"></div>
    <?php endif; ?>
</body>
</html>