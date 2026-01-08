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
                       <li><a class="dropdown-item" href="purok_evac.php">Purok Evacuation</a></li>
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
    </div>

    <!-- Bootstrap JS (includes Popper) for dropdowns/toggles -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>