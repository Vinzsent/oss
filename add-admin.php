<?php
session_start();

// Check if user is logged in and has admin privileges
if (!isset($_SESSION['id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['error_msg'] = "Unauthorized access. Admin privileges required.";
    header('Location: login.php');
    exit();
}

include('config.php');

// Initialize response variables
$success_msg = '';
$error_msg = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    
    // Sanitize and validate input
    $first_name = mysqli_real_escape_string($conn, trim($_POST['first_name']));
    $middle_name = mysqli_real_escape_string($conn, trim($_POST['middle_name'] ?? ''));
    $last_name = mysqli_real_escape_string($conn, trim($_POST['last_name']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = $_POST['password'];
    $province = mysqli_real_escape_string($conn, trim($_POST['province']));
    $city_municipality = mysqli_real_escape_string($conn, trim($_POST['city_municipality']));
    $barangay = mysqli_real_escape_string($conn, trim($_POST['barangay'] ?? ''));
    $purok = mysqli_real_escape_string($conn, trim($_POST['purok'] ?? ''));
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error_msg'] = "Invalid email format!";
        header('Location: admin.php');
        exit();
    }
    
    // Check if email already exists
    $check_email_sql = "SELECT id FROM users WHERE email = '$email'";
    $check_result = mysqli_query($conn, $check_email_sql);
    
    if (mysqli_num_rows($check_result) > 0) {
        $_SESSION['error_msg'] = "Email address already exists!";
        header('Location: admin.php');
        exit();
    }
    
    // Validate password strength (minimum 6 characters)
    if (strlen($password) < 6) {
        $_SESSION['error_msg'] = "Password must be at least 6 characters long!";
        header('Location: admin.php');
        exit();
    }
    
    // Validate role
    $valid_roles = ['user', 'moderator', 'admin'];
    if (!in_array($role, $valid_roles)) {
        $_SESSION['error_msg'] = "Invalid role selected!";
        header('Location: admin.php');
        exit();
    }
    
    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Set status to active by default
    $status = 'active';
    
    // Insert user into database
    $sql = "INSERT INTO users (first_name, middle_name, last_name, email, password, province, city_municipality, barangay, role, purok, status) 
            VALUES ('$first_name', '$middle_name', '$last_name', '$email', '$hashed_password', '$province', '$city_municipality', '$barangay', '$role', '$purok', '$status')";
    
    if (mysqli_query($conn, $sql)) {
        $_SESSION['success_msg'] = "User created successfully!";
        header('Location: admin.php');
        exit();
    } else {
        $_SESSION['error_msg'] = "Error creating user: " . mysqli_error($conn);
        header('Location: admin.php');
        exit();
    }
    
} else {
    // If accessed directly without POST, redirect to admin page
    header('Location: admin.php');
    exit();
}

mysqli_close($conn);
?>
