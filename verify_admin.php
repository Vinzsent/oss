<?php
session_start();

// Security headers
header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// Rate limiting
if (!isset($_SESSION['admin_attempts'])) {
    $_SESSION['admin_attempts'] = 0;
    $_SESSION['last_attempt'] = time();
}

// Reset attempts after 15 minutes
if (time() - $_SESSION['last_attempt'] > 900) {
    $_SESSION['admin_attempts'] = 0;
}

// Check if too many attempts
if ($_SESSION['admin_attempts'] >= 5) {
    http_response_code(429);
    echo json_encode([
        'success' => false, 
        'message' => 'Too many failed attempts. Please try again later.',
        'lockout' => true
    ]);
    exit;
}

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['password']) || empty($input['password'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Password is required']);
    exit;
}

$password = $input['password'];

// Hash the correct password (in production, store this in a secure config file)
$correct_password_hash = password_hash('misadmin', PASSWORD_DEFAULT);

// Verify password
if (password_verify($password, $correct_password_hash)) {
    // Success - reset attempts and set session
    $_SESSION['admin_attempts'] = 0;
    $_SESSION['admin_authenticated'] = true;
    $_SESSION['admin_login_time'] = time();
    
    echo json_encode([
        'success' => true, 
        'message' => 'Authentication successful',
        'redirect' => 'admin.php'
    ]);
} else {
    // Failed attempt
    $_SESSION['admin_attempts']++;
    $_SESSION['last_attempt'] = time();
    
    $remaining_attempts = 5 - $_SESSION['admin_attempts'];
    
    echo json_encode([
        'success' => false, 
        'message' => "Invalid password. {$remaining_attempts} attempts remaining.",
        'attempts_remaining' => $remaining_attempts
    ]);
}
?>
