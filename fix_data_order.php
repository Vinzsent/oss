<?php
include('config.php');

echo "Fixing household materials data order:\n";

// Update IDs to ensure proper ordering
$conn->query('UPDATE household_materials SET id = 1 WHERE material_name = "Concrete"');
$conn->query('UPDATE household_materials SET id = 2 WHERE material_name = "Semi or Half Concrete"');
$conn->query('UPDATE household_materials SET id = 3 WHERE material_name = "Made up of Light Materials"');
$conn->query('UPDATE household_materials SET id = 4 WHERE material_name = "Salvaged House"');

echo "IDs updated. New order:\n";

$result = $conn->query("SELECT material_name, households FROM household_materials ORDER BY id");
while($row = $result->fetch_assoc()) {
    echo $row['material_name'] . ': ' . $row['households'] . "\n";
}

$conn->close();
?>
