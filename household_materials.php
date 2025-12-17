<?php
session_start();
include('config.php');
include('includes/nav.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Household Materials - Micro Online Synthesis System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f5f5f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .main-container {
            max-width: 1200px;
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
        
        .material-type {
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
            
            .demographic-table {
                font-size: 0.9rem;
            }
            
            .demographic-table th,
            .demographic-table td {
                padding: 8px;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="page-header">
            <h1 class="page-title">
                <i class="fas fa-home me-3"></i>Household Materials Analysis
            </h1>
            <p class="page-subtitle">Construction Materials & Ownership Types - Barangay Lizada</p>
            <div class="mt-4 text-center">
                <a href="population.php" class="back-btn">
                    <i class="fas fa-arrow-left me-2"></i>
                    Back to Population Demographics
                </a>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-9">
                <!-- Construction Materials Table -->
                <div class="table-container">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="mb-0">
                            <i class="fas fa-building me-2 text-purple"></i>
                            Number of Households according to the type of Materials used in Construction
                        </h4>
                        <button class="export-btn" onclick="exportTable('materialsTable')">
                            <i class="fas fa-download me-1"></i>Export
                        </button>
                    </div>
                    
                    <?php
                    // Fetch construction materials data
                    $materials_sql = "SELECT material_name, id, households FROM household_materials ORDER BY id";
                    $materials_result = $conn->query($materials_sql);

                    // Calculate total households
                    $total_materials_sql = "SELECT SUM(households) as total_households FROM household_materials";
                    $total_materials_result = $conn->query($total_materials_sql);
                    $materials_totals = $total_materials_result->fetch_assoc();
                    ?>
                    
                    <table class="demographic-table" id="materialsTable">
                        <thead>
                            <tr>
                                <th>Type of Materials Used in Construction</th>
                                <th>Number of Households</th>
                                <th>Percentage</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($materials_result->num_rows > 0) {
                                while($row = $materials_result->fetch_assoc()) {
                                    $percentage = $materials_totals["total_households"] > 0 ? 
                                        round(($row["households"] / $materials_totals["total_households"]) * 100, 1) : 0;
                                    
                                    echo "<tr>";
                                    echo "<td class='material-type'>" . htmlspecialchars($row["material_name"]) . "</td>";
                                    echo "<td>" . number_format((float)$row["households"]) . "</td>";
                                    echo "<td>" . $percentage . "%</td>";
                                    echo "<td>";
                                    echo "<a href='edit_material.php?id=" . $row['id'] . "' class='btn btn-sm btn-primary' style='background: #8b5cf6; border-color: #8b5cf6;'>";
                                    echo "<i class='fas fa-edit me-1'></i>Edit";
                                    echo "</a>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                                
                                // Add total row
                                echo "<tr>";
                                echo "<td class='material-type'><strong>TOTAL</strong></td>";
                                echo "<td><strong>" . number_format((float)$materials_totals["total_households"]) . "</strong></td>";
                                echo "<td><strong>100%</strong></td>";
                                echo "<td>-</td>";
                                echo "</tr>";
                            } else {
                                echo "<tr><td colspan='4' class='text-center'>No data available</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Ownership Types Table -->
                <div class="table-container">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="mb-0">
                            <i class="fas fa-key me-2 text-purple"></i>
                            Total Households by Type of Ownership
                        </h4>
                        <button class="export-btn" onclick="exportTable('ownershipTable')">
                            <i class="fas fa-download me-1"></i>Export
                        </button>
                    </div>
                    
                    <?php
                    // Fetch ownership data
                    $ownership_sql = "SELECT ownership_type, id, households FROM household_ownership ORDER BY id";
                    $ownership_result = $conn->query($ownership_sql);

                    // Calculate total households for ownership
                    $total_ownership_sql = "SELECT SUM(households) as total_households FROM household_ownership";
                    $total_ownership_result = $conn->query($total_ownership_sql);
                    $ownership_totals = $total_ownership_result->fetch_assoc();
                    ?>
                    
                    <table class="demographic-table" id="ownershipTable">
                        <thead>
                            <tr>
                                <th>Type of Ownership</th>
                                <th>Number of Households</th>
                                <th>Percentage</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($ownership_result->num_rows > 0) {
                                while($row = $ownership_result->fetch_assoc()) {
                                    $percentage = $ownership_totals["total_households"] > 0 ? 
                                        round(($row["households"] / $ownership_totals["total_households"]) * 100, 1) : 0;
                                    
                                    echo "<tr>";
                                    echo "<td class='material-type'>" . htmlspecialchars($row["ownership_type"]) . "</td>";
                                    echo "<td>" . number_format((float)$row["households"]) . "</td>";
                                    echo "<td>" . $percentage . "%</td>";
                                    echo "<td>";
                                    echo "<a href='edit_ownership.php?id=" . $row['id'] . "' class='btn btn-sm btn-primary' style='background: #8b5cf6; border-color: #8b5cf6;'>";
                                    echo "<i class='fas fa-edit me-1'></i>Edit";
                                    echo "</a>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                                
                                // Add total row
                                echo "<tr>";
                                echo "<td class='material-type'><strong>TOTAL</strong></td>";
                                echo "<td><strong>" . number_format((float)$ownership_totals["total_households"]) . "</strong></td>";
                                echo "<td><strong>100%</strong></td>";
                                echo "<td>-</td>";
                                echo "</tr>";
                            } else {
                                echo "<tr><td colspan='4' class='text-center'>No data available</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="col-lg-3">
                <div class="stats-card">
                    <h5><i class="fas fa-chart-pie me-2"></i>Materials Statistics</h5>
                    <div class="stat-item">
                        <span class="stat-label">Total Households</span>
                        <span class="stat-value"><?php echo number_format((float)$materials_totals["total_households"]); ?></span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Material Types</span>
                        <span class="stat-value"><?php echo $materials_result->num_rows; ?></span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Most Common</span>
                        <span class="stat-value">
                            <?php
                            $most_common_sql = "SELECT material_name, households FROM household_materials ORDER BY households DESC LIMIT 1";
                            $most_common_result = $conn->query($most_common_sql);
                            $most_common = $most_common_result->fetch_assoc();
                            echo htmlspecialchars($most_common['material_name'] ?? 'N/A');
                            ?>
                        </span>
                    </div>
                </div>
                
                <div class="stats-card">
                    <h5><i class="fas fa-home me-2"></i>Ownership Statistics</h5>
                    <div class="stat-item">
                        <span class="stat-label">Total Households</span>
                        <span class="stat-value"><?php echo number_format((float)$ownership_totals["total_households"]); ?></span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Ownership Types</span>
                        <span class="stat-value"><?php echo $ownership_result->num_rows; ?></span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Owned Houses</span>
                        <span class="stat-value">
                            <?php
                            $owned_sql = "SELECT households FROM household_ownership WHERE ownership_type LIKE '%Owned%' LIMIT 1";
                            $owned_result = $conn->query($owned_sql);
                            $owned = $owned_result->fetch_assoc();
                            echo number_format((float)($owned['households'] ?? 0));
                            ?>
                        </span>
                    </div>
                </div>
                
                <div class="stats-card">
                    <h5><i class="fas fa-info-circle me-2"></i>Data Information</h5>
                    <div class="stat-item">
                        <span class="stat-label">Last Updated</span>
                        <span class="stat-value">Dec 2024</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Data Source</span>
                        <span class="stat-value">Barangay Survey</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Coverage</span>
                        <span class="stat-value">100%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function editMaterialData(materialType, households) {
            // Redirect to edit page with pre-filled data
            const url = `edit_household_materials.php?edit=1&material_type=${encodeURIComponent(materialType)}&households=${households}`;
            window.location.href = url;
        }
        
        function editOwnershipData(ownershipType, households) {
            // Redirect to edit page with pre-filled data
            const url = `edit_household_ownership.php?edit=1&ownership_type=${encodeURIComponent(ownershipType)}&households=${households}`;
            window.location.href = url;
        }
        
        function exportTable(tableId) {
            const table = document.getElementById(tableId);
            let csv = [];
            
            // Get headers (exclude Action column)
            const headers = [];
            table.querySelectorAll('thead th').forEach((th, index) => {
                if (index < 3) { // Only include first 3 columns, exclude Action
                    headers.push(th.textContent.trim());
                }
            });
            csv.push(headers.join(','));
            
            // Get data rows (exclude Action column)
            table.querySelectorAll('tbody tr').forEach(tr => {
                const row = [];
                tr.querySelectorAll('td').forEach((td, index) => {
                    if (index < 3) { // Only include first 3 columns, exclude Action
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
            a.download = tableId + '_' + new Date().toISOString().split('T')[0] + '.csv';
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
                this.style.backgroundColor = '#fbbf24';
            });
        });
    </script>
</body>
</html>
