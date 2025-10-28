<?php

// Database connection
include('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $barangay = $_POST['barangay'];
    $sitio = $_POST['sitio'];
    $warning_level = $_POST['warning_level'];
    $status = $_POST['status'];
    $recommendation_action = $_POST['recommendation_action'];


    $stmt = $conn->prepare("INSERT INTO FloodWarning 
                              (barangay, sitio, warning_level, status, recommended_action, date_created) 
                              VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("sssss", $barangay, $sitio, $warning_level, $status, $recommendation_action);
    
    if ($stmt->execute()) {
        echo '<script>alert("Flood warning data added successfully!");</script>';
        echo '<script>location.href = "flood_warning.php";</script>';
    } else {
        echo '<script>alert("Error: " . $conn->error);</script>';
        echo '<script>location.href = "add_flood_warning.php";</script>';
    }
    $stmt->close();
    
}
?>