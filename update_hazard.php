<?php
session_start();
include('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form data
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $area = isset($_POST['area']) ? trim($_POST['area']) : '';
    $hazard = isset($_POST['hazard']) ? trim($_POST['hazard']) : '';
    $low_family = isset($_POST['low_family']) ? (int)$_POST['low_family'] : 0;
    $low_person = isset($_POST['low_person']) ? (int)$_POST['low_person'] : 0;
    $moderate_family = isset($_POST['moderate_family']) ? (int)$_POST['moderate_family'] : 0;
    $moderate_person = isset($_POST['moderate_person']) ? (int)$_POST['moderate_person'] : 0;
    $high_family = isset($_POST['high_family']) ? (int)$_POST['high_family'] : 0;
    $high_person = isset($_POST['high_person']) ? (int)$_POST['high_person'] : 0;
    
    // Validate input
    if (empty($area) || empty($hazard)) {
        $_SESSION['error'] = "Area and Hazard fields are required.";
        header("Location: hazard_vul.php");
        exit();
    }
    
    if ($id > 0) {
        // Update existing record
        $query = "UPDATE hazard SET 
                  area = ?, 
                  hazard = ?, 
                  low_family = ?, 
                  low_person = ?, 
                  moderate_family = ?, 
                  moderate_person = ?, 
                  high_family = ?, 
                  high_person = ? 
                  WHERE id = ?";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssiiiiiii", $area, $hazard, $low_family, $low_person, $moderate_family, $moderate_person, $high_family, $high_person, $id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Hazard data updated successfully!";
        } else {
            $_SESSION['error'] = "Error updating hazard data: " . $conn->error;
        }
        
        $stmt->close();
    } else {
        $_SESSION['error'] = "Invalid record ID.";
    }
    
    $conn->close();
    header("Location: hazard_vul.php");
    exit();
} else {
    // Not a POST request
    $_SESSION['error'] = "Invalid request method.";
    header("Location: hazard_vul.php");
    exit();
}
?>
