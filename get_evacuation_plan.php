<?php
session_start();
include('config.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    if ($id > 0) {
        $query = "SELECT * FROM purok_evacuation_plan WHERE purok_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            $data = $result->fetch_assoc();
            echo json_encode([
                'success' => true,
                'data' => $data
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Evacuation plan not found'
            ]);
        }
        
        $stmt->close();
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid ID'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request'
    ]);
}

$conn->close();
?>
