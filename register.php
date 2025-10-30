<?php
session_start();
include 'config.php'; // Database connection

// Initialize error messages
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data and validate
    $first_name = $_POST['first_name'] ?? '';
    $middle_name = $_POST['middle_name'] ?? null; // Optional
    $last_name = $_POST['last_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $province = $_POST['province'] ?? '';
    $city = $_POST['city_municipality'] ?? '';
    $barangay = $_POST['barangay'] ?? '';
    $purok = $_POST['purok'] ?? '';

    // Validate form fields
    if (empty($first_name)) {
        $errors[] = "First name is required.";
    }

    if (empty($last_name)) {
        $errors[] = "Last name is required.";
    }

    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (empty($password)) {
        $errors[] = "Password is required.";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long.";
    }

    if (empty($province)) {
        $errors[] = "Province is required.";
    }

    if (empty($city)) {
        $errors[] = "City/Municipality is required.";
    }

    if (empty($barangay)) {
        $errors[] = "Barangay is required.";
    }

    if (empty($purok)) {
        $errors[] = "Purok is required.";
    }

    // Validate and handle profile picture upload
    $profile_picture = '';
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $file_tmp = $_FILES['profile_picture']['tmp_name'];
        $file_name = $_FILES['profile_picture']['name'];
        $file_size = $_FILES['profile_picture']['size'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        // Define allowed file extensions
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($file_ext, $allowed_extensions)) {
            $errors[] = "Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.";
        }

        // Limit file size to 2MB
        if ($file_size > 2 * 1024 * 1024) {
            $errors[] = "File size should not exceed 2MB.";
        }

        // Move the uploaded file to the server directory
        if (empty($errors)) {
            $profile_picture = 'uploads/' . uniqid('profile_', true) . '.' . $file_ext;
            move_uploaded_file($file_tmp, $profile_picture);
        }
    } else {
        $profile_picture = 'uploads/default-profile.jpg'; // Default image if no photo is uploaded
    }

    // If no errors, proceed with database insertion
    if (empty($errors)) {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Prepare SQL query
        $role = "user"; // Default role for new registrations
        $sql = "INSERT INTO users (first_name, middle_name, last_name, email, password, province, city_municipality, barangay, role, purok, profile_picture) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssssss", $first_name, $middle_name, $last_name, $email, $hashed_password, $province, $city, $barangay, $role, $purok, $profile_picture);

        if ($stmt->execute()) {
            echo "<script>alert('Registration successful!'); window.location.href = 'index.php';</script>";
            exit();
        } else {
            echo "<script>alert('Error: " . $stmt->error . "'); window.location.href = 'index.php';</script>";
        }

        $stmt->close();
    } else {
        // Combine all errors into a single alert
        $error_message = implode("\\n", $errors);
        echo "<script>alert('$error_message'); window.location.href = 'index.php';</script>";
    }

    $conn->close();
}
?>
