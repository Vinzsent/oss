<?php
session_start();
include('config.php');
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
            padding: 40px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .demographic-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .demographic-table thead {
            background-color: #8b5cf6;
            color: white;
        }
        
        .demographic-table th {
            padding: 15px;
            text-align: center;
            font-weight: bold;
            font-size: 1.1rem;
            border: none;
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
            padding: 12px 15px;
            text-align: center;
            border: 1px solid #e9d5f7;
            font-weight: 500;
            color: #1f2937;
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
                        <li><a class="dropdown-item <?php echo basename($_SERVER['PHP_SELF']) == 'population.php' ? 'active' : ''; ?>" href="population.php">Population Over Age</a></li>
                        <li><a class="dropdown-item <?php echo basename($_SERVER['PHP_SELF']) == 'hazard_vul.php' ? 'active' : ''; ?>" href="hazard_vul.php">Hazard Vulnerability</a></li>
                        <li><a class="dropdown-item <?php echo basename($_SERVER['PHP_SELF']) == 'purok_demographics.php' ? 'active' : ''; ?>" href="purok_demographics.php">Purok Demographics</a></li>
                        <li><a class="dropdown-item <?php echo basename($_SERVER['PHP_SELF']) == 'socio.php' && isset($_GET['barangay']) && $_GET['barangay'] == 'lizada' ? 'active' : ''; ?>" href="socio.php?barangay=lizada">Lizada</a></li>
                        <li><a class="dropdown-item <?php echo basename($_SERVER['PHP_SELF']) == 'socio.php' && isset($_GET['barangay']) && $_GET['barangay'] == 'daliao' ? 'active' : ''; ?>" href="socio.php?barangay=daliao">Daliao</a></li>
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
        // Initialize Bootstrap dropdowns explicitly to ensure they work
        const dropdownElementList = document.querySelectorAll('.dropdown-toggle');
        dropdownElementList.forEach(function(dropdownToggleEl) {
            // Initialize dropdown if Bootstrap is available
            if (typeof bootstrap !== 'undefined' && bootstrap.Dropdown) {
                new bootstrap.Dropdown(dropdownToggleEl);
            }
        });
        
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
        
        // Also close when clicking dropdown items (but allow dropdown to open first)
        const dropdownItems = document.querySelectorAll('.dropdown-item');
        dropdownItems.forEach(function(item) {
            item.addEventListener('click', function() {
                if (window.innerWidth < 992 && navbarCollapse && navbarCollapse.classList.contains('show')) {
                    // Small delay to allow navigation to happen first
                    setTimeout(function() {
                        if (navbarCollapse.classList.contains('show')) {
                            const bsCollapse = new bootstrap.Collapse(navbarCollapse, {
                                toggle: false
                            });
                            bsCollapse.hide();
                        }
                    }, 100);
                }
            });
        });
    });
    </script>
    
    <div class="main-container">
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
                <i class="fas fa-exclamation-triangle me-3"></i>Hazard Vulnerability 
            </h1>
            <p class="page-subtitle">Age Distribution Analysis - Barangay Lizada</p>
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
        
        <div class="row">
            <div class="col-lg-12"> 
                <div class="table-container">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="mb-0">
                            <i class="fas fa-chart-bar me-2 text-purple"></i>
                            Age Bracket Distribution
                        </h4>
                    </div>
                    
                    <?php
                    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                    $per_page = 10;
                    $offset = ($page - 1) * $per_page;
                    
                    $count_query = "SELECT COUNT(*) as total FROM hazard";
                    $count_result = $conn->query($count_query);
                    $total_rows = $count_result->fetch_assoc()['total'];
                    $total_pages = ceil($total_rows / $per_page);
                    
                    $hazard_query = "SELECT * FROM hazard LIMIT $offset, $per_page";
                    $hazard_result = $conn->query($hazard_query);
                    $totals = array(
                        "total_female" => 0,
                        "total_male" => 0,
                        "total_population" => 0
                    );
                    $age_groups = array(
                        "youth_0_14" => 0,
                        "adults_15_64" => 0,
                        "elderly_65_plus" => 0
                    );
                    ?>
                    <table class="demographic-table" id="demographicTable">
                        <thead>
                            <tr>
                                <th>AREA</th>
                                <th>LOW FAMILY</th>
                                <th>LOW PERSON</th>
                                <th>MODERATE FAMILY</th>
                                <th>MODERATE PERSON</th>
                                <th>HIGH FAMILY</th>
                                <th>HIGH PERSON</th>
                                <th>ACTION</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($hazard_result && $hazard_result->num_rows > 0) {
                                while($row = $hazard_result->fetch_assoc()) {
                                    $bracket = isset($row["hazard"]) ? $row["hazard"] : "";
                                    $female = isset($row["female"]) ? (int)$row["female"] : 0;
                                    $male = isset($row["male"]) ? (int)$row["male"] : 0;
                                    $row_total = isset($row["total"]) && is_numeric($row["total"]) ? (int)$row["total"] : ($female + $male);

                                    $totals["total_female"] += $female;
                                    $totals["total_male"] += $male;
                                    $totals["total_population"] += $row_total;

                                    echo "<tr>";

                                    echo "<td>" . htmlspecialchars(isset($row["area"]) ? $row["area"] : "") . "</td>";
                                    echo "<td>" . number_format(isset($row["low_family"]) ? (int)$row["low_family"] : 0) . "</td>";
                                    echo "<td>" . number_format(isset($row["low_person"]) ? (int)$row["low_person"] : 0) . "</td>";
                                    echo "<td>" . number_format(isset($row["moderate_family"]) ? (int)$row["moderate_family"] : 0) . "</td>";
                                    echo "<td>" . number_format(isset($row["moderate_person"]) ? (int)$row["moderate_person"] : 0) . "</td>";
                                    echo "<td>" . number_format(isset($row["high_family"]) ? (int)$row["high_family"] : 0) . "</td>";
                                    echo "<td>" . number_format(isset($row["high_person"]) ? (int)$row["high_person"] : 0) . "</td>";
                                    echo "<td>";
                                    echo "<button class='btn btn-sm btn-primary' onclick='openEditModal(" . json_encode($row) . ")' style='background: #8b5cf6; border-color: #8b5cf6;'>";
                                    echo "<i class='fas fa-edit me-1'></i>Edit";
                                    echo "</button>";
                                    echo "</td>";
                                    echo "</tr>";
                                }

                                echo "<tr>";
                                echo "<td><strong>TOTAL</strong></td>";
                                echo "<td>-</td>";
                                echo "<td>-</td>";
                                echo "<td>-</td>";
                                echo "<td>-</td>";
                                echo "<td>-</td>";
                                echo "<td>-</td>";
                                echo "<td>-</td>";
                                echo "</tr>";
                            } else {
                                echo "<tr><td colspan='8' class='text-center'>No data available</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                    
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
                        <i class="fas fa-edit me-2"></i>Edit Hazard Data
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editForm" method="POST" action="update_hazard.php">
                    <div class="modal-body">
                        <input type="hidden" id="editId" name="id">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="editArea" class="form-label">Area</label>
                                <input type="text" class="form-control" id="editArea" name="area" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="editHazard" class="form-label">Hazard</label>
                                <input type="text" class="form-control" id="editHazard" name="hazard" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="editLowFamily" class="form-label">Low Family</label>
                                <input type="number" class="form-control" id="editLowFamily" name="low_family" min="0" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="editLowPerson" class="form-label">Low Person</label>
                                <input type="number" class="form-control" id="editLowPerson" name="low_person" min="0" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="editModerateFamily" class="form-label">Moderate Family</label>
                                <input type="number" class="form-control" id="editModerateFamily" name="moderate_family" min="0" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="editModeratePerson" class="form-label">Moderate Person</label>
                                <input type="number" class="form-control" id="editModeratePerson" name="moderate_person" min="0" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="editHighFamily" class="form-label">High Family</label>
                                <input type="number" class="form-control" id="editHighFamily" name="high_family" min="0" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="editHighPerson" class="form-label">High Person</label>
                                <input type="number" class="form-control" id="editHighPerson" name="high_person" min="0" required>
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
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function openEditModal(rowData) {
            // Populate the modal fields with the row data
            document.getElementById('editId').value = rowData.id || '';
            document.getElementById('editArea').value = rowData.area || '';
            document.getElementById('editHazard').value = rowData.hazard || '';
            document.getElementById('editLowFamily').value = rowData.low_family || 0;
            document.getElementById('editLowPerson').value = rowData.low_person || 0;
            document.getElementById('editModerateFamily').value = rowData.moderate_family || 0;
            document.getElementById('editModeratePerson').value = rowData.moderate_person || 0;
            document.getElementById('editHighFamily').value = rowData.high_family || 0;
            document.getElementById('editHighPerson').value = rowData.high_person || 0;
            
            // Show the modal
            const modal = new bootstrap.Modal(document.getElementById('editModal'));
            modal.show();
        }
        
        function exportTable() {
            const table = document.getElementById('demographicTable');
            let csv = [];
            
            // Get headers (exclude Action column)
            const headers = [];
            table.querySelectorAll('thead th').forEach((th, index) => {
                if (index < 7) { // Only include first 7 columns, exclude Action
                    headers.push(th.textContent.trim());
                }
            });
            csv.push(headers.join(','));
            
            // Get data rows (exclude Action column and TOTAL row)
            table.querySelectorAll('tbody tr').forEach(tr => {
                const row = [];
                tr.querySelectorAll('td').forEach((td, index) => {
                    if (index < 7 && !td.textContent.includes('TOTAL')) { // Only include first 7 columns, exclude Action and TOTAL
                        row.push(td.textContent.trim());
                    }
                });
                if (row.length > 0) {
                    csv.push(row.join(','));
                }
            });
            
            // Create download link
            const csvContent = csv.join('\n');
            const blob = new Blob([csvContent], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'hazard_data_' + new Date().toISOString().split('T')[0] + '.csv';
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
                this.style.backgroundColor = '#c084fc';
            });
        });
    </script>
    
    <!-- Login Modal -->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="loginModalLabel">Login</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="login.php">
                        <div class="mb-3">
                            <label for="loginEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="loginEmail" name="email" placeholder="Enter your email" required>
                        </div>
                        <div class="mb-3">
                            <label for="loginPassword" class="form-label">Password</label>
                            <input type="password" class="form-control" id="loginPassword" name="password" placeholder="Enter your password" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabbed Sign-Up Modal -->
    <div class="modal fade" id="signUpModal" tabindex="-1" aria-labelledby="signUpModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="signUpModalLabel">Sign Up</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Nav Tabs -->
                    <ul class="nav nav-tabs" id="signUpTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="personal-tab" data-bs-toggle="tab" href="#personal" role="tab" aria-controls="personal" aria-selected="true">Personal Information</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="address-tab" data-bs-toggle="tab" href="#address" role="tab" aria-controls="address" aria-selected="false">Address</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="upload-tab" data-bs-toggle="tab" href="#upload" role="tab" aria-controls="upload" aria-selected="false">Upload Photo</a>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content mt-3" id="signUpTabContent">
                        <!-- Personal Information Tab -->
                        <div class="tab-pane fade show active" id="personal" role="tabpanel" aria-labelledby="personal-tab">
                            <form id="signUpForm" method="POST" action="register.php" novalidate>
                                <div class="mb-3">
                                    <label for="signUpFirstName" class="form-label">First Name</label>
                                    <input type="text" class="form-control" id="signUpFirstName" name="first_name" placeholder="Enter your first name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="signUpMiddleName" class="form-label">Middle Name</label>
                                    <input type="text" class="form-control" id="signUpMiddleName" name="middle_name" placeholder="Enter your middle name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="signUpLastName" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" id="signUpLastName" name="last_name" placeholder="Enter your last name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="signUpEmail" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="signUpEmail" name="email" placeholder="Enter your email" required>
                                </div>
                                <div class="mb-3">
                                    <label for="signUpPassword" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="signUpPassword" name="password" placeholder="Enter your password" minlength="6" required>
                                    <div id="passwordError" class="invalid-feedback" style="display:none;">Password must be at least 6 characters long.</div>
                                </div>
                        </div>

                        <!-- Address Tab -->
                        <div class="tab-pane fade" id="address" role="tabpanel" aria-labelledby="address-tab">
                            <div class="mb-3">
                                <label for="signUpProvince" class="form-label">Province</label>
                                <select class="form-select" id="signUpProvince" name="province">
                                    <option value="">Select Province</option>
                                </select>
                                <div id="provinceError" class="invalid-feedback" style="display:none;">Province is required.</div>
                            </div>
                            <div class="mb-3">
                                <label for="signUpCity" class="form-label">City/Municipality</label>
                                <select class="form-select" id="signUpCity" name="city_municipality">
                                    <option value="">Select City/Municipality</option>
                                </select>
                                <div id="cityError" class="invalid-feedback" style="display:none;">City/Municipality is required.</div>
                            </div>
                            <div class="mb-3">
                                <label for="signUpBarangay" class="form-label">Barangay</label>
                                <select class="form-select" id="signUpBarangay" name="barangay">
                                    <option value="">Select Barangay</option>
                                </select>
                                <div id="barangayError" class="invalid-feedback" style="display:none;">Barangay is required.</div>
                            </div>
                            <div class="mb-3">
                                <label for="signUpPurok" class="form-label">Purok</label>
                                <select class="form-select" id="signUpPurok" name="purok">
                                    <option value="">Select Purok</option>
                                </select>
                                <div id="purokError" class="invalid-feedback" style="display:none;">Purok is required.</div>
                            </div>
                        </div>

                        <!-- Upload Photo Tab -->
                        <div class="tab-pane fade" id="upload" role="tabpanel" aria-labelledby="upload-tab">
                            <div class="mb-3">
                                <label for="signUpPhoto" class="form-label">Upload Photo</label>
                                <input type="file" class="form-control" id="signUpPhoto" name="photo" onchange="previewPhoto(event)" accept="image/*">
                            </div>
                            <div id="photoPreviewContainer" class="mt-3" style="display: none;">
                                <p><strong>Preview:</strong></p>
                                <img id="photoPreview" src="" alt="Photo Preview" style="max-width: 100%; height: auto; border: 1px solid #ccc;">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="signUpSubmit">Submit</button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const provinceSelect = document.getElementById('signUpProvince');
            const citySelect = document.getElementById('signUpCity');
            const barangaySelect = document.getElementById('signUpBarangay');
            const purokSelect = document.getElementById('signUpPurok');

            const provinces = {
                'Davao del Sur': ['Davao City']
            };

            const cities = {
                'Davao City': ['Daliao', 'Lizada']
            };

            const sitios = {
                'Daliao': [
                    'Nakada', 'Doña Rosa Phase 1', 'Kalayaan', 'Kalubin-an', 'Kanipaan', 'Lipadas', 'Mcleod', 'Pantalan', 'Pogi Lawis', 'Prudential', 'St. Jude'
                ],
                'Lizada': [
                    'Babisa', 'Camarin', 'Culosa', 'Curvada', 'Dacudao', 'Doña Rosa', 'Fisherman', 'Glabaca', 'Gutierez', 'JV Ferriols', 'Kasama', 'Lawis', 'Lizada Beach', 'Lizada Proper', 'Maltabis'
                ]
            };

            // Populate Provinces
            Object.keys(provinces).forEach(province => {
                const option = document.createElement('option');
                option.value = province;
                option.textContent = province;
                provinceSelect.appendChild(option);
            });

            // When Province is selected
            provinceSelect.addEventListener('change', function() {
                const selectedProvince = this.value;
                citySelect.innerHTML = `<option value="">Select City/Municipality</option>`;
                if (provinces[selectedProvince]) {
                    provinces[selectedProvince].forEach(city => {
                        const option = document.createElement('option');
                        option.value = city;
                        option.textContent = city;
                        citySelect.appendChild(option);
                    });
                }
            });

            // When City is selected
            citySelect.addEventListener('change', function() {
                const selectedCity = this.value;
                barangaySelect.innerHTML = `<option value="">Select Barangay</option>`;
                if (cities[selectedCity]) {
                    cities[selectedCity].forEach(barangay => {
                        const option = document.createElement('option');
                        option.value = barangay;
                        option.textContent = barangay;
                        barangaySelect.appendChild(option);
                    });
                }
            });

            // When Barangay is selected
            barangaySelect.addEventListener('change', function() {
                const selectedBarangay = this.value;
                purokSelect.innerHTML = `<option value="">Select Purok/Sitio</option>`;
                if (sitios[selectedBarangay]) {
                    sitios[selectedBarangay].forEach(purok => {
                        const option = document.createElement('option');
                        option.value = purok;
                        option.textContent = purok;
                        purokSelect.appendChild(option);
                    });
                }
            });
        });
    </script>

    <script>
        function previewPhoto(event) {
            const fileInput = event.target;
            const previewContainer = document.getElementById('photoPreviewContainer');
            const previewImage = document.getElementById('photoPreview');

            if (fileInput.files && fileInput.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewContainer.style.display = 'block';
                    previewImage.src = e.target.result;
                };
                reader.readAsDataURL(fileInput.files[0]);
            } else {
                previewContainer.style.display = 'none';
                previewImage.src = '';
            }
        }
    </script>

    <script>
        // Inline validation to avoid full-page alerts and keep the modal state
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('signUpForm');
            const passwordInput = document.getElementById('signUpPassword');
            const passwordError = document.getElementById('passwordError');
            const submitBtn = document.getElementById('signUpSubmit');
            const provinceSelect = document.getElementById('signUpProvince');
            const citySelect = document.getElementById('signUpCity');
            const barangaySelect = document.getElementById('signUpBarangay');
            const purokSelect = document.getElementById('signUpPurok');
            const provinceError = document.getElementById('provinceError');
            const cityError = document.getElementById('cityError');
            const barangayError = document.getElementById('barangayError');
            const purokError = document.getElementById('purokError');

            function validatePassword() {
                const isValid = (passwordInput.value || '').length >= 6;
                if (!isValid) {
                    passwordInput.classList.add('is-invalid');
                    passwordError.style.display = 'block';
                } else {
                    passwordInput.classList.remove('is-invalid');
                    passwordError.style.display = 'none';
                }
                return isValid;
            }

            function validateSelect(selectEl, errorEl) {
                const isValid = !!(selectEl.value && selectEl.value.trim() !== '');
                if (!isValid) {
                    selectEl.classList.add('is-invalid');
                    errorEl.style.display = 'block';
                } else {
                    selectEl.classList.remove('is-invalid');
                    errorEl.style.display = 'none';
                }
                return isValid;
            }

            passwordInput.addEventListener('input', validatePassword);
            provinceSelect.addEventListener('change', () => validateSelect(provinceSelect, provinceError));
            citySelect.addEventListener('change', () => validateSelect(citySelect, cityError));
            barangaySelect.addEventListener('change', () => validateSelect(barangaySelect, barangayError));
            purokSelect.addEventListener('change', () => validateSelect(purokSelect, purokError));

            form.addEventListener('submit', function (e) {
                const passwordOk = validatePassword();
                const provinceOk = validateSelect(provinceSelect, provinceError);
                const cityOk = validateSelect(citySelect, cityError);
                const barangayOk = validateSelect(barangaySelect, barangayError);
                const purokOk = validateSelect(purokSelect, purokError);

                if (!(passwordOk && provinceOk && cityOk && barangayOk && purokOk)) {
                    e.preventDefault();
                    // If address fields are invalid, switch to Address tab; otherwise keep in Personal
                    const addressInvalid = !(provinceOk && cityOk && barangayOk && purokOk);
                    if (addressInvalid) {
                        const addressTabTrigger = document.getElementById('address-tab');
                        if (addressTabTrigger && !addressTabTrigger.classList.contains('active')) {
                            addressTabTrigger.click();
                        }
                        // Focus the first invalid address field
                        if (!provinceOk) provinceSelect.focus();
                        else if (!cityOk) citySelect.focus();
                        else if (!barangayOk) barangaySelect.focus();
                        else if (!purokOk) purokSelect.focus();
                    } else {
                        const personalTabTrigger = document.getElementById('personal-tab');
                        if (personalTabTrigger && !personalTabTrigger.classList.contains('active')) {
                            personalTabTrigger.click();
                        }
                        passwordInput.focus();
                    }
                }
            });
        });
    </script>

    <!-- Logout Confirmation Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="logoutModalLabel">
                        <i class="fas fa-sign-out-alt me-2"></i>Confirm Logout
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <p class="mb-0">Are you sure you want to log out?</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                    <a href="logout.php" class="btn btn-primary">Yes</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>