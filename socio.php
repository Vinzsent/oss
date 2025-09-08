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
    </style>
</head>
<body>
    <div class="content-wrapper">
        <!-- Navigation Bar -->
        <?php include('includes/nav.php'); ?>

        <div class="container mt-5">
        <div class="row">
            <div class="col-md-8">
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
                <table class="table table-bordered table-striped">
                    <thead>
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
            // Determine risk level color
            $riskColor = '';
            switch ($row['risk_level']) {
                case 0:
                    $riskColor = 'background-color: white; color: black;';
                    break;
                    case 1:
                    $riskColor = 'background-color: yellow; color: black;';
                    break;
                    case 2:
                    $riskColor = 'background-color: orange; color: white;';
                    break;
                    case 3:
                    $riskColor = 'background-color: red; color: white;';
                    break;
                }

                echo "<tr>
                <td>{$row['sitio_purok']}</td>
                <td>{$row['total_families']}</td>
                <td>{$row['total_persons']}</td>
                <td style='$riskColor'></td>
                </tr>";
            // Update totals
            $total_families += $row['total_families'];
            $total_persons += $row['total_persons'];
        }
    } else {
        echo "<tr><td colspan='4'>No data available</td></tr>";
    }
    ?>
    <tr>
        <td><strong>Total</strong></td>
        <td><strong><?php echo number_format($total_families); ?></strong></td>
        <td><strong><?php echo number_format($total_persons); ?></strong></td>
        <td></td>
    </tr>
</tbody>

</table>

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
            <div class="col-md-4">
                <h2>Search Filters</h2>
                <div class="alert alert-info">
                    <strong>Filter by Barangay and Purok:</strong>
                </div>
                <div class="alert-settings">
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
                    <legend>
                    <h3>Risk Level</h3>
                    <div style="display: flex; flex-direction: row; margin-bottom: 10px;">
                        <div style="flex: 2; display: flex; align-items: center; justify-content: center;">
                            <span style="background-color: yellow; padding: 15px; border-radius: 3px; width: 100%;"></span>
                        </div>
                        <div style="flex: 1; display: flex; align-items: center; justify-content: flex-start; padding-left: 10px;">
                            <span>Low</span>
                        </div>
                    </div>
                    <div style="display: flex; flex-direction: row; margin-bottom: 10px;">
                        <div style="flex: 2; display: flex; align-items: center; justify-content: center;">
                            <span style="background-color: orange; padding: 15px; border-radius: 3px; width: 100%;"></span>
                        </div>
                        <div style="flex: 1; display: flex; align-items: center; justify-content: flex-start; padding-left: 10px;">
                            <span>Medium</span>
                        </div>
                    </div>
                    <div style="display: flex; flex-direction: row;">
                        <div style="flex: 2; display: flex; align-items: center; justify-content: center;">
                            <span style="background-color: red; padding: 15px; border-radius: 3px; color: white; width: 100%;"></span>
                        </div>
                        <div style="flex: 1; display: flex; align-items: center; justify-content: flex-start; padding-left: 10px;">
                            <span>High</span>
                        </div>
                    </div>
                </legend>     
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
