<?php
// Start the session
session_start();

// Include the database connection file
include('config.php');

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate input fields
    $barangay = isset($_POST['barangay']) ? trim($_POST['barangay']) : null;
    $sitio_purok = isset($_POST['sitio']) ? trim($_POST['sitio']) : null;
    $description = isset($_POST['description']) ? trim($_POST['description']) : null;
    
    // Check for uploaded file
    if (!empty($_FILES['photo']['name'])) {
        // Get file details
        $fileName = $_FILES['photo']['name'];
        $fileTmpName = $_FILES['photo']['tmp_name'];
        $fileType = $_FILES['photo']['type'];
        $fileSize = $_FILES['photo']['size'];
        $fileError = $_FILES['photo']['error'];

        // Validate the file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($fileType, $allowedTypes)) {
            die("Unsupported file type. Please upload a JPEG, PNG, or GIF image.");
        }

        // Validate file size (e.g., 5MB limit)
        if ($fileSize > 5 * 1024 * 1024) {
            die("File size exceeds the 5MB limit.");
        }

        // Read the file content
        $photoContent = file_get_contents($fileTmpName);

        // Prepare SQL query
        $sql = "INSERT INTO flood_archive (barangay, sitio_purok, photo, description) 
                VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $barangay, $sitio_purok, $photoContent, $description);

        // Execute query and check for success
        if ($stmt->execute()) {
            echo "<script>alert('Submit successfully'); window.location.href='gallery.php';</script>";
            exit;
        } else {
            die("Error: " . $stmt->error);
        }

        // Close the statement
        $stmt->close();
    } else {
        die("No photo was uploaded.");
    }
} else {
    die("Invalid request method.");
}

// Close the database connection
$conn->close();
?>
