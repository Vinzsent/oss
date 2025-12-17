<?php
include('config.php');

// Create household_materials table
$create_table_sql = "CREATE TABLE IF NOT EXISTS `household_materials` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `material_name` varchar(255) NOT NULL,
  `material_type` varchar(255) DEFAULT NULL,
  `total_households` int(11) NOT NULL DEFAULT 0,
  `survey_year` int(4) DEFAULT NULL,
  `households` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

if ($conn->query($create_table_sql)) {
    echo "Table household_materials created successfully or already exists.<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// Clear existing data to avoid duplicates
$conn->query("DELETE FROM household_materials");

// Insert the household materials data
$insert_sql = "INSERT INTO `household_materials` (`material_name`, `total_households`, `survey_year`, `households`) VALUES
('Concrete', 2713, 2025, 2713),
('Semi or Half Concrete', 1881, 2025, 1881),
('Made up of Light Materials', 1009, 2025, 1009),
('Salvaged House', 50, 2025, 50)";

if ($conn->query($insert_sql)) {
    echo "Household materials data inserted successfully.<br>";
} else {
    echo "Error inserting data: " . $conn->error . "<br>";
}

// Verify data was inserted
$verify_sql = "SELECT * FROM household_materials";
$result = $conn->query($verify_sql);
if ($result->num_rows > 0) {
    echo "<h3>Current household materials data:</h3>";
    echo "<table border='1'><tr><th>ID</th><th>Material Name</th><th>Households</th><th>Survey Year</th></tr>";
    while($row = $result->fetch_assoc()) {
        echo "<tr><td>" . $row['id'] . "</td><td>" . $row['material_name'] . "</td><td>" . $row['households'] . "</td><td>" . $row['survey_year'] . "</td></tr>";
    }
    echo "</table>";
} else {
    echo "No data found in household_materials table.<br>";
}

$conn->close();
?>
