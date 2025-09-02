<?php
// Database connection
include('config.php');

// Query to fetch data
$sql = "SELECT sitio_purok, total_families, total_persons, risk_level FROM socio_data WHERE barangay = 'Lizada'";
$result = $conn->query($sql);

// Initialize totals
$total_families = 0;
$total_persons = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barangay Data</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h2 class="text-center">Barangay Data</h2>
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
                        echo "<tr>
                            <td>{$row['sitio_purok']}</td>
                            <td>{$row['total_families']}</td>
                            <td>{$row['total_persons']}</td>
                            <td>{$row['risk_level']}</td>
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
</body>
</html>

<?php
// Close database connection
$conn->close();
?>
