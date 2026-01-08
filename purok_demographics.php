<?php
session_start();
include('config.php');

// Check authentication and get user role
$is_logged_in = isset($_SESSION['id']) && isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : null;
$is_admin = $user_role === 'admin';

// Include auth check - this will show modal if not logged in
include('includes/auth_check.php');

// Pagination settings
$rows_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $rows_per_page;

// Initialize totals array
$totals = array(
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
    'pregnant_women' => 0
);

// Get total count for pagination
$count_query = "SELECT COUNT(*) as total FROM flood_data";
$count_result = $conn->query($count_query);
$total_rows = 0;
$total_pages = 0;

if ($count_result) {
    $total_rows = $count_result->fetch_assoc()['total'];
    $total_pages = ceil($total_rows / $rows_per_page);
}

// Calculate totals from ALL records (not just paginated ones)
$totals_query = "SELECT * FROM flood_data";
$totals_result = $conn->query($totals_query);

if ($totals_result && $totals_result->num_rows > 0) {
    while ($row = $totals_result->fetch_assoc()) {
        // Calculate totals for numeric columns only
        if (isset($row['total_families'])) $totals['total_families'] += (int)$row['total_families'];
        if (isset($row['total_persons_male'])) $totals['total_persons_male'] += (int)$row['total_persons_male'];
        if (isset($row['total_persons_female'])) $totals['total_persons_female'] += (int)$row['total_persons_female'];
        if (isset($row['infant_male'])) $totals['infant_male'] += (int)$row['infant_male'];
        if (isset($row['infant_female'])) $totals['infant_female'] += (int)$row['infant_female'];
        if (isset($row['children_male'])) $totals['children_male'] += (int)$row['children_male'];
        if (isset($row['children_female'])) $totals['children_female'] += (int)$row['children_female'];
        if (isset($row['adult_male'])) $totals['adult_male'] += (int)$row['adult_male'];
        if (isset($row['adult_female'])) $totals['adult_female'] += (int)$row['adult_female'];
        if (isset($row['elderly_male'])) $totals['elderly_male'] += (int)$row['elderly_male'];
        if (isset($row['elderly_female'])) $totals['elderly_female'] += (int)$row['elderly_female'];
        if (isset($row['pwd_male'])) $totals['pwd_male'] += (int)$row['pwd_male'];
        if (isset($row['pwd_female'])) $totals['pwd_female'] += (int)$row['pwd_female'];
        if (isset($row['sickness_male'])) $totals['sickness_male'] += (int)$row['sickness_male'];
        if (isset($row['sickness_female'])) $totals['sickness_female'] += (int)$row['sickness_female'];
        if (isset($row['pregnant_women'])) $totals['pregnant_women'] += (int)$row['pregnant_women'];
    }
}

// Query to fetch paginated demographic data
$query = "SELECT * FROM flood_data ORDER BY purok_name LIMIT $rows_per_page OFFSET $offset";
$demographic_result = $conn->query($query);

// Initialize demographic_data array
$demographic_data = array();

// Check if query was successful and fetch paginated data
if ($demographic_result && $demographic_result->num_rows > 0) {
    while ($row = $demographic_result->fetch_assoc()) {
        $demographic_data[] = $row;
    }
} else {
    // If table doesn't exist or query fails, show error message
    if ($total_rows == 0) {
        $error_message = "No demographic data found. " . ($conn->error ? "Error: " . $conn->error : "The flood_data table may not exist.");
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
            padding: 10px;
        }
        
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .page-title {
            font-size: 1.8rem;
            font-weight: bold;
            margin: 0;
            text-align: center;
        }
        
        .page-subtitle {
            font-size: 1rem;
            margin: 8px 0 0 0;
            text-align: center;
            opacity: 0.9;
        }
        
        .table-container {
            background: white;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        .demographic-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            min-width: 1200px;
            font-size: 0.85rem;
        }
        
        .demographic-table thead {
            background-color: #8b5cf6;
            color: white;
        }
        
        .demographic-table th {
            padding: 8px 4px;
            text-align: center;
            font-weight: bold;
            font-size: 0.75rem;
            border: none;
            white-space: nowrap;
            line-height: 1.2;
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
            padding: 6px 4px;
            text-align: center;
            border: 1px solid #e9d5f7;
            font-weight: 500;
            color: #1f2937;
            font-size: 0.8rem;
            line-height: 1.3;
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
            padding: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 15px;
            border-left: 4px solid #8b5cf6;
        }
        
        .stats-card h5 {
            color: #8b5cf6;
            font-weight: bold;
            margin-bottom: 10px;
            font-size: 1rem;
        }
        
        .stat-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 6px 0;
            border-bottom: 1px solid #f3f4f6;
        }
        
        .stat-item:last-child {
            border-bottom: none;
        }
        
        .stat-label {
            color: #6b7280;
            font-size: 0.8rem;
        }
        
        .stat-value {
            font-weight: bold;
            color: #1f2937;
            font-size: 0.95rem;
        }
        
        .export-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            font-size: 0.85rem;
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
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            font-size: 0.9rem;
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
            margin-top: 20px;
        }
        
        .pagination .page-link {
            color: #8b5cf6;
            border-color: #e9d5f7;
            padding: 8px 16px;
            transition: all 0.3s ease;
        }
        
        .pagination .page-link:hover {
            background-color: #8b5cf6;
            color: white;
            border-color: #8b5cf6;
        }
        
        .pagination .page-item.active .page-link {
            background-color: #8b5cf6;
            border-color: #8b5cf6;
            color: white;
        }
        
        .pagination .page-item.disabled .page-link {
            color: #6c757d;
            pointer-events: none;
            cursor: not-allowed;
            background-color: #fff;
            border-color: #dee2e6;
        }
        
        @media (max-width: 1200px) {
            .demographic-table {
                min-width: 1000px;
                font-size: 0.8rem;
            }
            
            .demographic-table th {
                font-size: 0.7rem;
                padding: 6px 3px;
            }
            
            .demographic-table td {
                font-size: 0.75rem;
                padding: 5px 3px;
            }
        }
        
        @media (max-width: 768px) {
            .main-container {
                padding: 5px;
            }
            
            .page-header {
                padding: 15px;
                margin-bottom: 15px;
            }
            
            .page-title {
                font-size: 1.4rem;
            }
            
            .page-subtitle {
                font-size: 0.85rem;
            }
            
            .table-container {
                padding: 10px;
                margin-bottom: 15px;
            }
            
            .demographic-table {
                min-width: 900px;
                font-size: 0.75rem;
            }
            
            .demographic-table th {
                font-size: 0.65rem;
                padding: 5px 2px;
            }
            
            .demographic-table td {
                font-size: 0.7rem;
                padding: 4px 2px;
            }
            
            .stats-card {
                padding: 12px;
                margin-bottom: 12px;
            }
            
            .stats-card h5 {
                font-size: 0.9rem;
                margin-bottom: 8px;
            }
            
            .stat-label {
                font-size: 0.75rem;
            }
            
            .stat-value {
                font-size: 0.85rem;
            }
            
            .back-btn {
                padding: 6px 12px;
                font-size: 0.8rem;
            }
            
            .pagination {
                flex-wrap: wrap;
                margin-top: 15px;
            }
            
            .pagination .page-link {
                padding: 4px 8px;
                font-size: 0.8rem;
            }
        }
        
        @media (max-width: 480px) {
            .demographic-table {
                min-width: 800px;
            }
            
            .page-title {
                font-size: 1.2rem;
            }
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
        /* Hide action column for non-admin users */
        <?php if (!$is_admin): ?>
        .demographic-table th.action-column,
        .demographic-table td.action-column {
            display: none;
        }
        <?php endif; ?>
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
                        <li><a class="dropdown-item <?php echo basename($_SERVER['PHP_SELF']) == 'purok_evac.php' ? 'active' : ''; ?>" href="purok_evac.php">Purok Evacuation</a></li>
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
    
    <!-- Include auth check modal -->
    <?php include('includes/auth_check.php'); ?>
    
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
    
    <?php if ($is_logged_in): ?>
    <div class="main-container main-content-protected">
        <div class="page-header">
            <h1 class="page-title">
                <i class="fas fa-users me-3"></i>Purok Demographics
            </h1>
            <p class="page-subtitle">Detailed Population Statistics by Purok - Barangay Lizada</p>
            <div class="mt-3 text-center">
                <a href="population.php" class="back-btn shadow-sm hover-shadow transition-all text-decoration-none">
                    <i class="fas fa-arrow-left me-2"></i>
                    Back to Population Demographics
                </a>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-12">
                <div class="table-container">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0" style="font-size: 1rem; font-weight: 600;">
                            <i class="fas fa-chart-bar me-2 text-purple"></i>
                            Demographic Data by Purok
                        </h5>
                        <button class="export-btn" onclick="exportTable()">
                            <i class="fas fa-download me-1"></i>Export
                        </button>
                    </div>
                    
                    <table class="demographic-table" id="demographicTable">
                        <thead>
                            <tr>
                                <th>PUROK</th>
                                <th>FAMILIES</th>
                                <th>MALE</th>
                                <th>FEMALE</th>
                                <th>INF M</th>
                                <th>INF F</th>
                                <th>CHILD M</th>
                                <th>CHILD F</th>
                                <th>ADULT M</th>
                                <th>ADULT F</th>
                                <th>ELDER M</th>
                                <th>ELDER F</th>
                                <th>PWD M</th>
                                <th>PWD F</th>
                                <th>SICK M</th>
                                <th>SICK F</th>
                                <th>PREGNANT</th>
                                <?php if ($is_admin): ?>
                                <th class="action-column">ACTION</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (!empty($demographic_data)) {
                                foreach ($demographic_data as $row) {
                                    echo "<tr>";
                                    echo "<td class='purok-name'>" . htmlspecialchars($row['purok_name'] ?? '') . "</td>";
                                    echo "<td>" . number_format($row['total_families'] ?? 0) . "</td>";
                                    echo "<td>" . number_format($row['total_persons_male'] ?? 0) . "</td>";
                                    echo "<td>" . number_format($row['total_persons_female'] ?? 0) . "</td>";
                                    echo "<td>" . number_format($row['infant_male'] ?? 0) . "</td>";
                                    echo "<td>" . number_format($row['infant_female'] ?? 0) . "</td>";
                                    echo "<td>" . number_format($row['children_male'] ?? 0) . "</td>";
                                    echo "<td>" . number_format($row['children_female'] ?? 0) . "</td>";
                                    echo "<td>" . number_format($row['adult_male'] ?? 0) . "</td>";
                                    echo "<td>" . number_format($row['adult_female'] ?? 0) . "</td>";
                                    echo "<td>" . number_format($row['elderly_male'] ?? 0) . "</td>";
                                    echo "<td>" . number_format($row['elderly_female'] ?? 0) . "</td>";
                                    echo "<td>" . number_format($row['pwd_male'] ?? 0) . "</td>";
                                    echo "<td>" . number_format($row['pwd_female'] ?? 0) . "</td>";
                                    echo "<td>" . number_format($row['sickness_male'] ?? 0) . "</td>";
                                    echo "<td>" . number_format($row['sickness_female'] ?? 0) . "</td>";
                                    echo "<td>" . number_format($row['pregnant_women'] ?? 0) . "</td>";
                                    if ($is_admin) {
                                        echo "<td class='action-column'>";
                                        echo "<button class='btn btn-sm btn-primary' onclick='editData(\"" . htmlspecialchars($row['purok_name'] ?? '') . "\")' style='background: #8b5cf6; border-color: #8b5cf6;'>";
                                        echo "<i class='fas fa-edit me-1'></i>Edit";
                                        echo "</button>";
                                        echo "</td>";
                                    }
                                    echo "</tr>";
                                }
                                
                                // Add total row
                                echo "<tr>";
                                echo "<td class='purok-name'><strong>TOTAL</strong></td>";
                                echo "<td><strong>" . number_format($totals['total_families']) . "</strong></td>";
                                echo "<td><strong>" . number_format($totals['total_persons_male']) . "</strong></td>";
                                echo "<td><strong>" . number_format($totals['total_persons_female']) . "</strong></td>";
                                echo "<td><strong>" . number_format($totals['infant_male']) . "</strong></td>";
                                echo "<td><strong>" . number_format($totals['infant_female']) . "</strong></td>";
                                echo "<td><strong>" . number_format($totals['children_male']) . "</strong></td>";
                                echo "<td><strong>" . number_format($totals['children_female']) . "</strong></td>";
                                echo "<td><strong>" . number_format($totals['adult_male']) . "</strong></td>";
                                echo "<td><strong>" . number_format($totals['adult_female']) . "</strong></td>";
                                echo "<td><strong>" . number_format($totals['elderly_male']) . "</strong></td>";
                                echo "<td><strong>" . number_format($totals['elderly_female']) . "</strong></td>";
                                echo "<td><strong>" . number_format($totals['pwd_male']) . "</strong></td>";
                                echo "<td><strong>" . number_format($totals['pwd_female']) . "</strong></td>";
                                echo "<td><strong>" . number_format($totals['sickness_male']) . "</strong></td>";
                                echo "<td><strong>" . number_format($totals['sickness_female']) . "</strong></td>";
                                echo "<td><strong>" . number_format($totals['pregnant_women']) . "</strong></td>";
                                if ($is_admin) {
                                    echo "<td class='action-column'>-</td>";
                                }
                                echo "</tr>";
                            } else {
                                // Show error message if no data
                                $colspan = $is_admin ? 18 : 17; // Number of columns
                                echo "<tr><td colspan='$colspan' class='text-center p-5'>";
                                echo "<div class='alert alert-warning' role='alert'>";
                                echo "<i class='fas fa-exclamation-triangle me-2'></i>";
                                echo isset($error_message) ? htmlspecialchars($error_message) : "No demographic data available.";
                                echo "</div>";
                                echo "</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                    
                    <?php if (!empty($demographic_data) && $total_pages > 1): ?>
                    <nav aria-label="Page navigation" class="mt-4">
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
                        <div class="text-center text-muted mt-2">
                            <small>Showing <?php echo count($demographic_data); ?> of <?php echo $total_rows; ?> records (Page <?php echo $page; ?> of <?php echo $total_pages; ?>)</small>
                        </div>
                    </nav>
                    <?php endif; ?>
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
                        <span class="stat-value"><?php echo number_format($totals['total_persons_male'] + $totals['total_persons_female']); ?></span>
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
                        <span class="stat-value"><?php echo count($demographic_data); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
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
    <?php else: ?>
    <!-- Content hidden when not logged in - auth_check.php handles the modal -->
    <div class="main-content-protected" style="display: none;"></div>
    <?php endif; ?>
</body>
</html>

