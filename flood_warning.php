<?php
// Start session to access user data
session_start();

// Database connection
include('config.php');

// Check if user is logged in and get their role
$user_role = '';
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true && isset($_SESSION['id'])) {
    $user_id = $_SESSION['id'];
    $user_query = "SELECT role FROM users WHERE id = ?";
    $user_stmt = $conn->prepare($user_query);
    $user_stmt->bind_param("i", $user_id);
    if ($user_stmt->execute()) {
        $user_result = $user_stmt->get_result();
        if ($user_result && $user_result->num_rows > 0) {
            $user = $user_result->fetch_assoc();
            $user_role = $user['role'];
            // Debug: Show the role in the input field
            $debug_role = htmlspecialchars($user_role);
        }
    }
    $user_stmt->close();
}

// Handle form submission with redirect to prevent resubmit dialog
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $barangay = isset($_POST['barangay']) ? $_POST['barangay'] : 'lizada';
    $purok = isset($_POST['purok']) ? $_POST['purok'] : '';
    
    // Store in session and redirect
    $_SESSION['selected_barangay'] = $barangay;
    $_SESSION['selected_purok'] = $purok;
    
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

// Check if a barangay and purok are selected from session or use defaults
$selectedBarangay = isset($_SESSION['selected_barangay']) ? $_SESSION['selected_barangay'] : 'lizada';
$selectedPurok = isset($_SESSION['selected_purok']) ? $_SESSION['selected_purok'] : '';

// Query to fetch puroks for the selected barangay
$purokQuery = "SELECT DISTINCT sitio_purok FROM socio_data WHERE barangay = ?";
$purokStmt = $conn->prepare($purokQuery);
$purokStmt->bind_param("s", $selectedBarangay);
$purokStmt->execute();
$purokResult = $purokStmt->get_result();

// Query to fetch flood warning data with filtering
$sql = "SELECT * FROM FloodWarning WHERE barangay = ?";
if ($selectedPurok) {
    $sql .= " AND sitio = ?";
}
$stmt = $conn->prepare($sql);

// Bind parameters for the prepared statement
if ($selectedPurok) {
    $stmt->bind_param("ss", $selectedBarangay, $selectedPurok);
} else {
    $stmt->bind_param("s", $selectedBarangay);
}
$stmt->execute();
$result = $stmt->get_result();

// Initialize totals
$total_families = 0;
$total_persons = 0;

$user_query = "SELECT role FROM users WHERE id = ?";
$user_stmt = $conn->prepare($user_query);
$user_stmt->bind_param("i", $_SESSION['id']);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user = $user_result->fetch_assoc();

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
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .main-content {
            flex: 1;
        }
        table {
            width: 100%;
            margin-bottom: 1rem;
            color: #212529;
        }
        thead th {
            background-color: #f8f9fa;
        }
        footer {
            margin-top: auto;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <?php include('includes/nav.php'); ?>

    <div class="container mt-5 main-content">
        <div class="row">
            <div class="col-md-8">
                
                <h2>Flood Warning Data</h2> 
                <h4>Barangay: <?php echo ucfirst($selectedBarangay); ?></h4>

                <!-- Flood Warning Table -->
                <?php
                // Check if there are records to display
                if ($result->num_rows > 0) {
                    echo '<table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Barangay</th>
                                    <th>Sitio</th>
                                    <th>Warning Level</th>
                                    <th>Status</th>
                                    <th>Recommended Action</th>
                                </tr>
                            </thead>
                            <tbody>';
                    
                    while ($row = $result->fetch_assoc()) {
                        echo '<tr>';
                        echo '<td>' . $row['date_created'] . '</td>';
                        echo '<td>' . $row['barangay'] . '</td>';
                        echo '<td>' . $row['sitio'] . '</td>';

                        // Apply background color for warning level
                        switch ($row['warning_level']) {
                            case 1:
                                echo '<td style="background-color: yellow;">Yellow Alert</td>';
                                break;
                            case 2:
                                echo '<td style="background-color: orange;">Orange Alert</td>';
                                break;
                            case 3:
                                echo '<td style="background-color: red; color: white;">Red Alert</td>';
                                break;
                            default:
                                echo '<td>Unknown</td>';
                        }

                        echo '<td>' . $row['status'] . '</td>';
                        echo '<td>' . $row['recommended_action'] . '</td>';
                        echo '</tr>';
                    }

                    echo '</tbody></table>';
                } else {
                    echo 'No records found';
                }

                // Close the statement and connection
                $stmt->close();
                $conn->close();
                ?>
            </div>

            <div class="col-md-4">
                <h2>Search Filters</h2>
                <form method="post">
                    <?php if (isset($user_role) && !empty($user_role)): ?>
                        <?php endif; ?>
                        <div class="form-group">
                            <label for="barangay"><strong>Barangay</strong></label>
                            <select class="form-control" name="barangay" onchange="this.form.submit()">
                            <option value="lizada" <?php if ($selectedBarangay == 'lizada') echo 'selected'; ?>>Lizada</option>
                            <option value="daliao" <?php if ($selectedBarangay == 'daliao') echo 'selected'; ?>>Daliao</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="purok"><strong>Purok/Sitio</strong></label>
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
                    <?php if (isset($debug_role)): ?>
                    <input type="hidden" name="role" value="<?php echo $debug_role; ?>" class="form-control mb-3" readonly>
                    <?php endif; ?>
                </form>
                <?php 
                // Check if user is admin before showing the button
                if (isset($user_role) && $user_role === 'admin'): ?>
                    <a href="add_flood_warning.php" class="btn btn-primary"><i class="fas fa-plus"></i> Add Barangay and Sitio</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-white mt-5 p-4 text-center">
        &copy; 2024 Flood Resilience App. All Rights Reserved.
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>