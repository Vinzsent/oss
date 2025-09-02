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

// Query to fetch data based on barangay and optionally purok
$sql = "SELECT sitio_purok, total_families, total_persons, risk_level 
        FROM socio_data 
        WHERE barangay = ? " . ($selectedPurok ? "AND sitio_purok = ?" : "");
$stmt = $conn->prepare($sql);

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
                <h2>Socio Demographic Data</h2>
                <h4>Barangay: <?php echo ucfirst($selectedBarangay); ?> 
                <?php if ($selectedPurok) echo " - Purok: " . ucfirst($selectedPurok); ?></h4>
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
        <td><strong><?php echo $total_families; ?></strong></td>
        <td><strong><?php echo $total_persons; ?></strong></td>
        <td></td>
    </tr>
</tbody>

                </table>
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

    <footer class="bg-dark text-white mt-5 p-4 text-center">
        &copy; 2024 Flood Resilience App. All Rights Reserved.
    </footer>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
