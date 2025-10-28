<?php
session_start();
include 'config.php'; // Make sure this file sets up $conn

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    // Collect form data
    $id = intval($_POST['id']);
    $first_name = trim($_POST['first_name']);
    $middle_name = trim($_POST['middle_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']); // Optional
    $province = trim($_POST['province']);
    $city_municipality = trim($_POST['city_municipality']);
    $role = trim($_POST['role']);

    // Validate required fields
    if (empty($id) || empty($first_name) || empty($last_name) || empty($email) || empty($province) || empty($city_municipality) || empty($role)) {
        echo "<script>alert('Please fill in all required fields.'); window.history.back();</script>";
        exit;
    }

    // Build SQL query dynamically (if password is provided, update it)
    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET first_name=?, middle_name=?, last_name=?, email=?, password=?, province=?, city_municipality=?, role=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssi", $first_name, $middle_name, $last_name, $email, $hashed_password, $province, $city_municipality, $role, $id);
    } else {
        $sql = "UPDATE users SET first_name=?, middle_name=?, last_name=?, email=?, province=?, city_municipality=?, role=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssi", $first_name, $middle_name, $last_name, $email, $province, $city_municipality, $role, $id);
    }

    // Execute and check result
    if ($stmt->execute()) {
        echo "<script>alert('User updated successfully!'); window.location.href='admin.php';</script>";
    } else {
        echo "<script>alert('Error updating user: " . addslashes($stmt->error) . "'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "<script>alert('Invalid request.'); window.history.back();</script>";
}
?>