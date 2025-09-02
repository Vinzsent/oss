<?php
// Start session to access user data
session_start();

// Database connection
include('config.php');

// Check if a barangay and purok are selected
$selectedBarangay = isset($_POST['barangay']) ? $_POST['barangay'] : 'lizada';
$selectedPurok = isset($_POST['purok']) ? $_POST['purok'] : '';

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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Micro OSS App</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
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
    <!-- Navigation Bar -->
    <?php include('includes/nav.php'); ?>

    <div class="container mt-5">
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
                </form>
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
