<?php
include('config.php');

// Create household_ownership table
$sql = "CREATE TABLE IF NOT EXISTS `household_ownership` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ownership_type` varchar(255) NOT NULL,
  `households` int(11) NOT NULL DEFAULT 0,
  `survey_year` int(4) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

if ($conn->query($sql)) {
    echo "Table household_ownership created successfully or already exists.<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// Check if data already exists
$check_sql = "SELECT COUNT(*) as count FROM household_ownership";
$result = $conn->query($check_sql);
$row = $result->fetch_assoc();

if ($row['count'] == 0) {
    // Insert the household ownership data
    $insert_sql = "INSERT INTO `household_ownership` (`ownership_type`, `households`, `survey_year`) VALUES
    ('Owned', 3953, 2025),
    ('Rented', 951, 2025),
    ('Shared with Owner', 464, 2025),
    ('Shared with Renter', 50, 2025),
    ('Informal Settler Families (ISF)', 235, 2025)";
    
    if ($conn->query($insert_sql)) {
        echo "Household ownership data inserted successfully.<br>";
    } else {
        echo "Error inserting data: " . $conn->error . "<br>";
    }
} else {
    echo "Household ownership data already exists (" . $row['count'] . " records).<br>";
}

// Display current data
echo "<br>Current household_ownership data:<br>";
$select_sql = "SELECT * FROM household_ownership ORDER BY id";
$result = $conn->query($select_sql);
while ($row = $result->fetch_assoc()) {
    echo "ID: " . $row['id'] . " - " . $row['ownership_type'] . ": " . $row['households'] . " households (Year: " . $row['survey_year'] . ")<br>";
}

$conn->close();
?>
