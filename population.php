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
    <title>Population Demographics - Micro Online Synthesis System</title>
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
    </style>
</head>
<body>
    <div class="main-container">
        <div class="page-header">
            <h1 class="page-title">
                <i class="fas fa-users me-3"></i>Population Demographics
            </h1>
            <p class="page-subtitle">Age Distribution Analysis - Barangay Lizada</p>
            <div class="mt-4 d-flex justify-content-center align-items-center gap-3 flex-wrap">
                <a href="purok_demographics.php" class="back-btn btn-lg px-4 py-3 shadow-sm hover-shadow transition-all text-decoration-none">
                    <i class="fas fa-arrow-left me-2"></i>
                    Purok Demographics
                </a>

                <a href="household_materials.php" class="back-btn btn-lg px-4 py-3 shadow-sm hover-shadow transition-all text-decoration-none">
                    <i class="fas fa-arrow-right me-2"></i>
                    View Households Materials used in Construction
                </a>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-9">
                <div class="table-container">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="mb-0">
                            <i class="fas fa-chart-bar me-2 text-purple"></i>
                            Age Bracket Distribution
                        </h4>
                    </div>
                    
                    <?php
                    // Fetch data from age_population table, excluding any existing TOTAL rows
                    $sql = "SELECT age_bracket, female, male, total FROM age_population WHERE age_bracket != 'TOTAL' ORDER BY id";
                    $result = $conn->query($sql);

                    // Calculate totals
                    $total_sql = "SELECT SUM(female) as total_female, SUM(male) as total_male, SUM(total) as total_population FROM age_population WHERE age_bracket != 'TOTAL'";
                    $total_result = $conn->query($total_sql);
                    $totals = $total_result->fetch_assoc();

                    // Calculate age group statistics
                    $age_groups_sql = "SELECT 
                        SUM(CASE WHEN age_bracket IN ('0-4', '5-9', '10-14') THEN total ELSE 0 END) as youth_0_14,
                        SUM(CASE WHEN age_bracket NOT IN ('0-4', '5-9', '10-14', '65-69', '70-74', '75-79', '80+') THEN total ELSE 0 END) as adults_15_64,
                        SUM(CASE WHEN age_bracket IN ('65-69', '70-74', '75-79', '80+') THEN total ELSE 0 END) as elderly_65_plus
                        FROM age_population WHERE age_bracket != 'TOTAL'";
                    $age_groups_result = $conn->query($age_groups_sql);
                    $age_groups = $age_groups_result->fetch_assoc();
                    ?>
                    <table class="demographic-table" id="demographicTable">
                        <thead>
                            <tr>
                                <th>AGE BRACKET (YEAR)</th>
                                <th>FEMALE</th>
                                <th>MALE</th>
                                <th>TOTAL</th>
                                <th>ACTION</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result->num_rows > 0) {
                                // Track unique age brackets to avoid duplicates
                                $displayed_brackets = array();
                                while($row = $result->fetch_assoc()) {
                                    $bracket = $row["age_bracket"];
                                    
                                    // Skip if this bracket was already displayed
                                    if (in_array($bracket, $displayed_brackets)) {
                                        continue;
                                    }
                                    
                                    echo "<tr>";
                                    echo "<td class='age-bracket'>" . htmlspecialchars($bracket) . "</td>";
                                    echo "<td>" . number_format($row["female"]) . "</td>";
                                    echo "<td>" . number_format($row["male"]) . "</td>";
                                    echo "<td>" . number_format($row["total"]) . "</td>";
                                    echo "<td>";
                                    echo "<button class='btn btn-sm btn-primary' onclick='editPopulationData(\"" . htmlspecialchars($bracket) . "\", " . $row["female"] . ", " . $row["male"] . ")' style='background: #8b5cf6; border-color: #8b5cf6;'>";
                                    echo "<i class='fas fa-edit me-1'></i>Edit";
                                    echo "</button>";
                                    echo "</td>";
                                    echo "</tr>";
                                    
                                    // Mark this bracket as displayed
                                    $displayed_brackets[] = $bracket;
                                }
                                
                                // Add only one total row at the end
                                echo "<tr>";
                                echo "<td class='age-bracket'><strong>TOTAL</strong></td>";
                                echo "<td><strong>" . number_format($totals["total_female"]) . "</strong></td>";
                                echo "<td><strong>" . number_format($totals["total_male"]) . "</strong></td>";
                                echo "<td><strong>" . number_format($totals["total_population"]) . "</strong></td>";
                                echo "<td>-</td>";
                                echo "</tr>";
                            } else {
                                echo "<tr><td colspan='5' class='text-center'>No data available</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="col-lg-3">
                <div class="stats-card">
                    <h5><i class="fas fa-chart-pie me-2"></i>Population Statistics</h5>
                    <div class="stat-item">
                        <span class="stat-label">Total Population</span>
                        <span class="stat-value"><?php echo number_format($totals["total_population"]); ?></span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Female Population</span>
                        <span class="stat-value"><?php echo number_format($totals["total_female"]); ?></span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Male Population</span>
                        <span class="stat-value"><?php echo number_format($totals["total_male"]); ?></span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Gender Ratio</span>
                        <span class="stat-value"><?php 
                            $ratio = $totals["total_male"] > 0 ? round(($totals["total_female"] / $totals["total_male"]) * 100, 1) : 0;
                            echo $ratio . ":100";
                        ?></span>
                    </div>
                </div>
                
                <div class="stats-card">
                    <h5><i class="fas fa-child me-2"></i>Age Groups</h5>
                    <div class="stat-item">
                        <span class="stat-label">0-14 years</span>
                        <span class="stat-value"><?php echo number_format($age_groups["youth_0_14"]); ?></span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">15-64 years</span>
                        <span class="stat-value"><?php echo number_format($age_groups["adults_15_64"]); ?></span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">65+ years</span>
                        <span class="stat-value"><?php echo number_format($age_groups["elderly_65_plus"]); ?></span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Dependency Ratio</span>
                        <span class="stat-value"><?php 
                            $working_age = $age_groups["adults_15_64"];
                            $dependents = $age_groups["youth_0_14"] + $age_groups["elderly_65_plus"];
                            $dependency_ratio = $working_age > 0 ? round(($dependents / $working_age) * 100, 1) : 0;
                            echo $dependency_ratio . "%";
                        ?></span>
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
        function editPopulationData(ageBracket, female, male) {
            // Redirect to add_population_data.php with pre-filled data
            const url = `add_population_data.php?edit=1&age_bracket=${encodeURIComponent(ageBracket)}&female=${female}&male=${male}`;
            window.location.href = url;
        }
        
        function exportTable() {
            const table = document.getElementById('demographicTable');
            let csv = [];
            
            // Get headers (exclude Action column)
            const headers = [];
            table.querySelectorAll('thead th').forEach((th, index) => {
                if (index < 4) { // Only include first 4 columns, exclude Action
                    headers.push(th.textContent.trim());
                }
            });
            csv.push(headers.join(','));
            
            // Get data rows (exclude Action column)
            table.querySelectorAll('tbody tr').forEach(tr => {
                const row = [];
                tr.querySelectorAll('td').forEach((td, index) => {
                    if (index < 4) { // Only include first 4 columns, exclude Action
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
            a.download = 'population_demographics_' + new Date().toISOString().split('T')[0] + '.csv';
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
</body>
</html>