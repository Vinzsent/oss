<?php
// ANCHOR: File upload handler for admin users
session_start();
include('config.php');

// ANCHOR: Debug mode - remove in production
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors in JSON response
ini_set('log_errors', 1); // Log errors instead

// ANCHOR: Set JSON header
header('Content-Type: application/json');

// ANCHOR: Check if user is logged in and is admin
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// ANCHOR: Get user role
$user_id = $_SESSION['id'];
$role_query = "SELECT role FROM users WHERE id = ?";
$role_stmt = $conn->prepare($role_query);
$role_stmt->bind_param("i", $user_id);
$role_stmt->execute();
$role_result = $role_stmt->get_result();
$user_role = $role_result->fetch_assoc()['role'];

if ($user_role !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Admin access required']);
    exit();
}

// ANCHOR: Configuration for file uploads
$upload_dir = 'uploads/';
$max_file_size = 10 * 1024 * 1024; // 10MB
$allowed_types = [
    'image' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
    'document' => ['pdf', 'doc', 'docx', 'txt', 'rtf'],
    'archive' => ['zip', 'rar', '7z']
];

// ANCHOR: Create upload directory if it doesn't exist
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// ANCHOR: Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $category = $_POST['category'] ?? 'general';
    $description = $_POST['description'] ?? '';
    
    // ANCHOR: Validate file
    if ($file['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'message' => 'File upload error: ' . $file['error']]);
        exit();
    }
    
    // ANCHOR: Check file size
    if ($file['size'] > $max_file_size) {
        echo json_encode(['success' => false, 'message' => 'File too large. Maximum size is 10MB.']);
        exit();
    }
    
    // ANCHOR: Get file extension and validate type
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $file_type = 'unknown';
    
    foreach ($allowed_types as $type => $extensions) {
        if (in_array($file_extension, $extensions)) {
            $file_type = $type;
            break;
        }
    }
    
    if ($file_type === 'unknown') {
        echo json_encode(['success' => false, 'message' => 'File type not allowed.']);
        exit();
    }
    
    // ANCHOR: Generate unique filename
    $original_name = $file['name'];
    $filename = uniqid() . '_' . time() . '.' . $file_extension;
    $file_path = $upload_dir . $filename;
    
    // ANCHOR: Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $file_path)) {
        // ANCHOR: Optimize image files if they're too large (optional, don't fail upload if this fails)
        if ($file_type === 'image' && file_exists('image_optimizer.php')) {
            try {
                include_once('image_optimizer.php');
                @optimizeImage($file_path);
                
                // ANCHOR: Create thumbnail for images
                $thumbnail_path = $upload_dir . 'thumb_' . $filename;
                @createThumbnail($file_path, $thumbnail_path);
            } catch (Exception $e) {
                // ANCHOR: Log error but continue with upload
                error_log("Image optimization failed: " . $e->getMessage());
            }
        }
        // ANCHOR: Save file info to database
        $insert_query = "INSERT INTO publications (filename, original_name, file_path, file_type, file_size, category, description, uploaded_by, is_active) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)";
        $insert_stmt = $conn->prepare($insert_query);
        $insert_stmt->bind_param("ssssiisi", $filename, $original_name, $file_path, $file_type, $file['size'], $category, $description, $user_id);
        
        if ($insert_stmt->execute()) {
            echo json_encode([
                'success' => true, 
                'message' => 'File uploaded successfully',
                'file_id' => $conn->insert_id,
                'filename' => $filename,
                'original_name' => $original_name
            ]);
        } else {
            // ANCHOR: Delete file if database insert fails
            unlink($file_path);
            echo json_encode([
                'success' => false, 
                'message' => 'Database error: ' . $conn->error,
                'debug' => 'Insert query failed'
            ]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to move uploaded file']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No file uploaded']);
}

$conn->close();
?>
