<?php
include('config.php');

echo "Fixing household_ownership data...<br>";

// Update the households column with the correct values from total_households
$update_sql = "UPDATE household_ownership SET households = total_households";

if ($conn->query($update_sql)) {
    echo "Household counts updated successfully.<br>";
} else {
    echo "Error updating household counts: " . $conn->error . "<br>";
}

// Display current data to verify
echo "<br>Updated household_ownership data:<br>";
$select_sql = "SELECT * FROM household_ownership ORDER BY id";
$result = $conn->query($select_sql);

while ($row = $result->fetch_assoc()) {
    echo "ID: " . $row['id'] . " - " . $row['ownership_type'] . ": " . $row['households'] . " households (Year: " . $row['survey_year'] . ")<br>";
}

// Calculate and display total
$total_sql = "SELECT SUM(households) as total FROM household_ownership";
$total_result = $conn->query($total_sql);
$total_row = $total_result->fetch_assoc();
echo "<br><strong>Total households: " . number_format((float)$total_row['total']) . "</strong><br>";

$conn->close();
echo "<br><a href='household_materials.php'>Go back to Household Materials page</a>";
?>
