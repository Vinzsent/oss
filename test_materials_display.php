<?php
include('config.php');

echo "<h2>Testing Household Materials Data Display</h2>";

// Fetch construction materials data
$materials_sql = "SELECT material_name, households FROM household_materials ORDER BY id";
$materials_result = $conn->query($materials_sql);

// Calculate total households
$total_materials_sql = "SELECT SUM(households) as total_households FROM household_materials";
$total_materials_result = $conn->query($total_materials_sql);
$materials_totals = $total_materials_result->fetch_assoc();

echo "<h3>Raw Data from Database:</h3>";
if ($materials_result->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; margin: 20px 0;'>";
    echo "<tr><th>Material Name</th><th>Households</th><th>Percentage</th></tr>";
    
    while($row = $materials_result->fetch_assoc()) {
        $percentage = $materials_totals["total_households"] > 0 ? 
            round(($row["households"] / $materials_totals["total_households"]) * 100, 1) : 0;
        
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row["material_name"]) . "</td>";
        echo "<td>" . number_format((float)$row["households"]) . "</td>";
        echo "<td>" . $percentage . "%</td>";
        echo "</tr>";
    }
    
    // Add total row
    echo "<tr style='font-weight: bold; background-color: #f0f0f0;'>";
    echo "<td>TOTAL</td>";
    echo "<td>" . number_format((float)$materials_totals["total_households"]) . "</td>";
    echo "<td>100%</td>";
    echo "</tr>";
    
    echo "</table>";
    
    echo "<h3>Statistics:</h3>";
    echo "<ul>";
    echo "<li>Total Households: " . number_format((float)$materials_totals["total_households"]) . "</li>";
    echo "<li>Material Types: " . $materials_result->num_rows . "</li>";
    
    // Most common material
    $most_common_sql = "SELECT material_name, households FROM household_materials ORDER BY households DESC LIMIT 1";
    $most_common_result = $conn->query($most_common_sql);
    $most_common = $most_common_result->fetch_assoc();
    echo "<li>Most Common: " . htmlspecialchars($most_common['material_name'] ?? 'N/A') . " (" . number_format($most_common['households'] ?? 0) . " households)</li>";
    echo "</ul>";
    
} else {
    echo "<p>No data available in household_materials table</p>";
}

$conn->close();
?>
