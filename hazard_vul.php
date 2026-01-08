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
    </style>
</head>
<body>
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
</body>
</html>