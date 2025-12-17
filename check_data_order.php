<?php
include('config.php');

echo "Checking household materials data order:\n";

$result = $conn->query("SELECT material_name, households FROM household_materials ORDER BY households DESC");
while($row = $result->fetch_assoc()) {
    echo $row['material_name'] . ': ' . $row['households'] . "\n";
}

$conn->close();
?>
