<?php
session_start();
include('../config.php');

// Check authentication and get user role
$is_logged_in = isset($_SESSION['id']) && isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : null;
$is_admin = $user_role === 'admin';

// Include auth check - this will show modal if not logged in
include('../includes/auth_check.php');

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
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
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
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
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
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
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
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
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
        <?php if (!$is_admin): ?>.demographic-table th.action-column,
        .demographic-table td.action-column {
            display: none;
        }

        <?php endif; ?>
    </style>
</head>

<body>
    <!-- Navbar -->
    <?php include('../includes/nav.php'); ?>

    <!-- Include auth check modal (though nav.php has it too, keep it if specific logic depends on it) -->
    <?php include('../includes/auth_check.php'); ?>

    <?php if ($is_logged_in): ?>
        <div class="main-container main-content-protected">
            <div class="page-header">
                <h1 class="page-title">
                    <i class="fas fa-users me-3"></i>Purok Demographics
                </h1>
                <p class="page-subtitle">Detailed Population Statistics by Purok - Barangay Lizada</p>
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
                const blob = new Blob([csvContent], {
                    type: 'text/csv'
                });
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

    <!-- Bootstrap JS -->

    <?php include('../includes/scripts.php'); ?>
    <?php include('../includes/footer.php'); ?>
</body>

</html>